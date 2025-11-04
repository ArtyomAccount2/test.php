<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $login = $_POST['login'];
    $password = $_POST['password'];
    $redirect_url = $_POST['redirect_url'] ?? $_SERVER['REQUEST_URI'];

    if (strtolower($login) === 'admin' && strtolower($password) === 'admin') 
    {
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = 'admin';
        unset($_SESSION['login_error']);
        unset($_SESSION['error_message']);
        header("Location: ../admin.php");
        exit();
    }
    else
    {
        $stmt = $conn->prepare("SELECT * FROM users WHERE LOWER(login_users) = LOWER(?) AND LOWER(password_users) = LOWER(?)");
        $stmt->bind_param("ss", $login, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
        {
            $row = $result->fetch_assoc();
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = !empty($row['surname_users']) ? $row['surname_users'] . " " . $row['name_users'] . " " . $row['patronymic_users'] : $row['person_users'];
            unset($_SESSION['login_error']);
            unset($_SESSION['error_message']);
            header("Location: " . $redirect_url);
            exit();
        } 
        else 
        {
            $_SESSION['login_error'] = true;
            $_SESSION['error_message'] = "Неверный логин или пароль!";
            $_SESSION['form_data'] = $_POST;
            header("Location: " . $redirect_url);
            exit();
        }
    }
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вакансии - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/vacancies-styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        <?php 
        if (isset($_SESSION['login_error'])) 
        { 
        ?>
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();

            <?php unset($_SESSION['login_error']); ?>
        <?php 
        } 
        ?>

        let fileInput = document.getElementById('vacancyFile');
        let fileUploadBtn = document.querySelector('.file-upload-btn');
        
        if(fileInput && fileUploadBtn) 
        {
            fileInput.addEventListener('change', function(e) 
            {
                if(this.files && this.files[0]) 
                {
                    let fileName = this.files[0].name;
                    fileUploadBtn.innerHTML = `
                        <i class="bi bi-file-earmark-check fs-3 text-success"></i>
                        <p class="file-name mb-1">${fileName}</p>
                        <small class="text-muted">Нажмите для изменения файла</small>
                    `;
                    fileUploadBtn.classList.add('file-selected');
                }
            });

            fileUploadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fileInput.click();
            });
        }

        let form = document.getElementById('vacancyForm');

        if(form) 
        {
            form.addEventListener('submit', function(e) 
            {
                e.preventDefault();

                if(this.checkValidity()) 
                {
                    let submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Отклик отправлен!';
                    submitBtn.classList.remove('btn-primary');
                    submitBtn.classList.add('btn-success');
                    submitBtn.disabled = true;
                    
                    setTimeout(() => {
                        let modal = bootstrap.Modal.getInstance(document.getElementById('responseModal'));
                        modal.hide();
                        
                        this.reset();
                        fileUploadBtn.innerHTML = `
                            <i class="bi bi-cloud-arrow-up fs-3"></i>
                            <p class="mb-1">Перетащите файл сюда или нажмите для выбора</p>
                            <small class="text-muted">Форматы: PDF, DOC, DOCX (до 5MB)</small>
                        `;
                        fileUploadBtn.classList.remove('file-selected');
                        
                        submitBtn.innerHTML = '<i class="bi bi-send me-2"></i>Отправить отклик';
                        submitBtn.classList.remove('btn-success');
                        submitBtn.classList.add('btn-primary');
                        submitBtn.disabled = false;
                    }, 2000);
                }
            });
        }

        function equalizeVacancyHeights() 
        {
            let vacancyCards = document.querySelectorAll('.vacancy-card-main');
            let maxHeight = 0;
            
            vacancyCards.forEach(card => {
                card.style.height = 'auto';
            });

            if (window.innerWidth > 768) 
            {
                vacancyCards.forEach(card => {
                    let height = card.offsetHeight;
                    
                    if (height > maxHeight) 
                    {
                        maxHeight = height;
                    }
                });
                
                vacancyCards.forEach(card => {
                    card.style.height = maxHeight + 'px';
                });
            }
        }

        window.addEventListener('load', equalizeVacancyHeights);
        window.addEventListener('resize', equalizeVacancyHeights);
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5">
    <div class="vacancies-hero text-center mb-5" style="padding-top: 85px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Карьера в Лал-Авто</h1>
        <p class="lead text-muted mb-4">Присоединяйтесь к команде профессионалов и развивайтесь вместе с нами</p>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="vacancy-card-main h-100">
                <div class="vacancy-card">
                    <div class="vacancy-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <h3 class="mb-2">Менеджер по продажам автозапчастей</h3>
                            <span class="badge bg-primary">Актуально</span>
                        </div>
                        <div class="vacancy-meta">
                            <div class="meta-item">
                                <i class="bi bi-geo-alt"></i>
                                <span>г. Калининград</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-cash-stack"></i>
                                <span>от 50 000 ₽ + бонусы</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-briefcase"></i>
                                <span>Опыт от 1 года</span>
                            </div>
                        </div>
                    </div>
                    <div class="vacancy-body">
                        <div class="vacancy-section">
                            <h5><i class="bi bi-list-check text-primary me-2"></i>Обязанности:</h5>
                            <ul>
                                <li>Консультирование клиентов по ассортименту</li>
                                <li>Подбор запчастей по VIN и каталогам</li>
                                <li>Оформление заказов и работа с 1С</li>
                                <li>Ведение базы клиентов</li>
                                <li>Работа с входящими и исходящими звонками</li>
                            </ul>
                        </div>
                        <div class="vacancy-section">
                            <h5><i class="bi bi-person-check text-primary me-2"></i>Требования:</h5>
                            <ul>
                                <li>Опыт работы в продажах от 1 года</li>
                                <li>Знание автомобилей и запчастей</li>
                                <li>Умение работать в команде</li>
                                <li>Грамотная речь и клиентоориентированность</li>
                            </ul>
                        </div>
                        <div class="vacancy-section">
                            <h5><i class="bi bi-star text-primary me-2"></i>Условия:</h5>
                            <ul>
                                <li>Официальное трудоустройство</li>
                                <li>График 5/2 с 9:00 до 18:00</li>
                                <li>Обучение и стажировка</li>
                                <li>Карьерный рост и премии</li>
                            </ul>
                        </div>
                    </div>
                    <div class="vacancy-footer">
                        <button class="btn btn-primary w-100 py-2" data-bs-toggle="modal" data-bs-target="#responseModal">
                            <i class="bi bi-envelope me-2"></i>Откликнуться на вакансию
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="vacancy-card-main h-100">
                <div class="vacancy-card">
                    <div class="vacancy-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <h3 class="mb-2">Автомеханик</h3>
                            <span class="badge bg-primary">Актуально</span>
                        </div>
                        <div class="vacancy-meta">
                            <div class="meta-item">
                                <i class="bi bi-geo-alt"></i>
                                <span>г. Калининград</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-cash-stack"></i>
                                <span>от 65 000 ₽</span>
                            </div>
                            <div class="meta-item">
                                <i class="bi bi-briefcase"></i>
                                <span>Опыт от 3 лет</span>
                            </div>
                        </div>
                    </div>
                    <div class="vacancy-body">
                        <div class="vacancy-section">
                            <h5><i class="bi bi-list-check text-primary me-2"></i>Обязанности:</h5>
                            <ul>
                                <li>Диагностика и ремонт автомобилей</li>
                                <li>Техническое обслуживание</li>
                                <li>Работа с клиентами</li>
                                <li>Ведение документации</li>
                                <li>Шиномонтажные работы</li>
                            </ul>
                        </div>
                        <div class="vacancy-section">
                            <h5><i class="bi bi-person-check text-primary me-2"></i>Требования:</h5>
                            <ul>
                                <li>Опыт работы автомехаником от 3 лет</li>
                                <li>Знание устройства автомобилей</li>
                                <li>Навыки работы с диагностическим оборудованием</li>
                                <li>Ответственность и аккуратность</li>
                            </ul>
                        </div>
                        <div class="vacancy-section">
                            <h5><i class="bi bi-star text-primary me-2"></i>Условия:</h5>
                            <ul>
                                <li>Официальное трудоустройство</li>
                                <li>График 2/2 с 9:00 до 20:00</li>
                                <li>Современное оборудование</li>
                                <li>Премии за качество работы</li>
                            </ul>
                        </div>
                    </div>
                    <div class="vacancy-footer">
                        <button class="btn btn-primary w-100 py-2" data-bs-toggle="modal" data-bs-target="#responseModal">
                            <i class="bi bi-envelope me-2"></i>Откликнуться на вакансию
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="employer-benefits">
        <div class="benefits-card">
            <div class="benefits-header text-center mb-4">
                <h2 class="text-primary mb-3"><i class="bi bi-building me-3"></i>Почему стоит работать у нас?</h2>
                <p class="text-muted">Мы создаем комфортные условия для профессионального роста и развития</p>
            </div>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="benefit-content">
                        <h5>Конкурентная зарплата</h5>
                        <p class="mb-0">Стабильные выплаты и бонусы за результаты работы</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <div class="benefit-content">
                        <h5>Карьерный рост</h5>
                        <p class="mb-0">Возможности для профессионального и карьерного развития</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="benefit-content">
                        <h5>Дружный коллектив</h5>
                        <p class="mb-0">Работа в команде опытных профессионалов</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <div class="benefit-content">
                        <h5>Обучение</h5>
                        <p class="mb-0">Корпоративные тренинги и курсы повышения квалификации</p>
                    </div>
                </div>
            </div>
            <div class="benefits-footer text-center mt-4">
                <h4 class="mb-3">Не нашли подходящую вакансию?</h4>
                <p class="text-muted mb-3">Отправьте свое резюме и мы рассмотрим вас, когда появится подходящая позиция</p>
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#responseModal">
                    <i class="bi bi-file-earmark-person me-2"></i>Отправить резюме
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Отклик на вакансию</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="vacancyForm" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vacancyPosition" class="form-label fw-semibold">Вакансия<span class="text-danger">*</span></label>
                            <select class="form-select" id="vacancyPosition" required>
                                <option value="" selected disabled>Выберите вакансию</option>
                                <option value="Менеджер по продажам">Менеджер по продажам автозапчастей</option>
                                <option value="Автомеханик">Автомеханик</option>
                                <option value="Другая">Другая вакансия</option>
                            </select>
                            <div class="invalid-feedback">Пожалуйста, выберите вакансию</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vacancyName" class="form-label fw-semibold">ФИО<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vacancyName" placeholder="Иванов Иван Иванович" required>
                            <div class="invalid-feedback">Пожалуйста, введите ваше ФИО</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vacancyPhone" class="form-label fw-semibold">Телефон<span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="vacancyPhone" placeholder="+7 (900) 123-45-67" required>
                            <div class="invalid-feedback">Пожалуйста, введите корректный телефон</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="vacancyEmail" class="form-label fw-semibold">Email<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="vacancyEmail" placeholder="example@mail.ru" required>
                            <div class="invalid-feedback">Пожалуйста, введите корректный email</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="vacancyMessage" class="form-label fw-semibold">Сопроводительное письмо</label>
                        <textarea class="form-control" id="vacancyMessage" rows="3" placeholder="Расскажите о своем опыте и почему вы хотите работать у нас"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Резюме<span class="text-danger">*</span></label>
                        <div class="file-upload">
                            <label for="vacancyFile" class="file-upload-btn">
                                <i class="bi bi-cloud-arrow-up fs-3"></i>
                                <p class="mb-1">Перетащите файл сюда или нажмите для выбора</p>
                                <small class="text-muted">Форматы: PDF, DOC, DOCX (до 5MB)</small>
                            </label>
                            <input type="file" class="form-control d-none" id="vacancyFile" accept=".pdf,.doc,.docx" required>
                            <div class="invalid-feedback">Пожалуйста, прикрепите резюме</div>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="vacancyAgree" required>
                        <label class="form-check-label small" for="vacancyAgree">Я согласен на обработку персональных данных</label>
                        <div class="invalid-feedback">Необходимо ваше согласие</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="vacancyForm" class="btn btn-primary w-100">
                    <i class="bi bi-send me-2"></i>Отправить отклик
                </button>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    var responseModal = document.getElementById('responseModal');

    if (responseModal) 
    {
        responseModal.addEventListener('show.bs.modal', function () 
        {
            setTimeout(function() 
            {
                var modalInstance = bootstrap.Modal.getInstance(responseModal);
            }, 10);
        });
    }
});
</script>
</body>
</html>