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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
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
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5" style="padding-top: 75px;">
    <div class="row mb-5">
        <div class="col-12">
            <div class="vacancies-intro">
                <h2 class="text-center mb-4">Наши вакансии</h2>
                <p class="text-center">Мы предлагаем стабильную работу в дружном коллективе, конкурентную зарплату и возможности для профессионального роста.</p>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="vacancy-card">
                <div class="vacancy-header">
                    <h3>Менеджер по продажам автозапчастей</h3>
                    <span class="badge bg-primary">Актуально</span>
                </div>
                <div class="vacancy-meta">
                    <div class="meta-item">
                        <i class="bi bi-geo-alt"></i> г. Калининград
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-cash-stack"></i> от 50 000 ₽ + бонусы
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-briefcase"></i> Опыт от 1 года
                    </div>
                </div>
                <div class="vacancy-body">
                    <div class="vacancy-description">
                        <h5>Обязанности:</h5>
                        <ul>
                            <li>Консультирование клиентов по ассортименту</li>
                            <li>Подбор запчастей по VIN и каталогам</li>
                            <li>Оформление заказов и работа с 1С</li>
                            <li>Ведение базы клиентов</li>
                            <li>Работа с входящими и исходящими звонками</li>
                            <li>Участие в инвентаризациях</li>
                            <li>Взаимодействие с отделом закупок</li>
                            <li>Контроль выполнения заказов</li>
                        </ul>
                        <h5>Требования:</h5>
                        <ul>
                            <li>Опыт работы в продажах от 1 года</li>
                            <li>Знание автомобилей и запчастей</li>
                            <li>Умение работать в команде</li>
                            <li>Грамотная речь</li>
                            <li>Клиентоориентированность</li>
                            <li>Стрессоустойчивость</li>
                            <li>Обучаемость</li>
                        </ul>
                        <h5>Условия:</h5>
                        <ul>
                            <li>Официальное трудоустройство</li>
                            <li>График 5/2 с 9:00 до 18:00</li>
                            <li>Обучение и стажировка</li>
                            <li>Карьерный рост</li>
                            <li>Корпоративные мероприятия</li>
                            <li>Оформление по ТК РФ</li>
                            <li>Премии по результатам работы</li>
                        </ul>
                    </div>
                </div>
                <div class="vacancy-footer">
                    <button class="btn btn-primary btn-lg w-100 py-3 fw-bold" data-bs-toggle="modal" data-bs-target="#responseModal">
                        <i class="bi bi-envelope"></i> Откликнуться на вакансию
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="vacancy-card">
                <div class="vacancy-header">
                    <h3>Автомеханик</h3>
                    <span class="badge bg-primary">Актуально</span>
                </div>
                <div class="vacancy-meta">
                    <div class="meta-item">
                        <i class="bi bi-geo-alt"></i> г. Калининград
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-cash-stack"></i> от 65 000 ₽
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-briefcase"></i> Опыт от 3 лет
                    </div>
                </div>
                <div class="vacancy-body">
                    <div class="vacancy-description">
                        <h5>Обязанности:</h5>
                        <ul>
                            <li>Диагностика и ремонт автомобилей</li>
                            <li>Техническое обслуживание</li>
                            <li>Работа с клиентами</li>
                            <li>Ведение документации</li>
                            <li>Замена технических жидкостей</li>
                            <li>Ремонт ходовой части</li>
                            <li>Диагностика электронных систем</li>
                            <li>Шиномонтажные работы</li>
                        </ul>
                        <h5>Требования:</h5>
                        <ul>
                            <li>Опыт работы автомехаником от 3 лет</li>
                            <li>Знание устройства автомобилей</li>
                            <li>Навыки работы с диагностическим оборудованием</li>
                            <li>Ответственность и аккуратность</li>
                            <li>Наличие собственного инструмента</li>
                            <li>Опыт работы с иномарками</li>
                            <li>Техническое образование</li>
                        </ul>
                        <h5>Условия:</h5>
                        <ul>
                            <li>Официальное трудоустройство</li>
                            <li>График 2/2 с 9:00 до 20:00</li>
                            <li>Современное оборудование</li>
                            <li>Премии за качество работы</li>
                            <li>Оплачиваемые больничные</li>
                            <li>Стабильная заработная плата</li>
                            <li>Комфортные условия труда</li>
                        </ul>
                    </div>
                </div>
                <div class="vacancy-footer">
                    <button class="btn btn-primary btn-lg w-100 py-3 fw-bold" data-bs-toggle="modal" data-bs-target="#responseModal">
                        <i class="bi bi-envelope"></i> Откликнуться на вакансию
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="about-employer p-4 mb-2 text-center">
                <h2 class="mb-4"><i class="bi bi-building"></i> Почему стоит работать у нас?</h2>
                <div class="benefits-grid">
                    <div class="benefit-item">
                        <i class="bi bi-currency-dollar"></i>
                        <h5>Конкурентная зарплата</h5>
                        <p>Стабильные выплаты и бонусы за результаты</p>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-graph-up"></i>
                        <h5>Карьерный рост</h5>
                        <p>Возможности для профессионального развития</p>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-people"></i>
                        <h5>Дружный коллектив</h5>
                        <p>Работа в команде профессионалов</p>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-book"></i>
                        <h5>Обучение</h5>
                        <p>Корпоративные тренинги и курсы</p>
                    </div>
                </div>
                <div class="mt-5">
                    <h4>Не нашли подходящую вакансию?</h4>
                    <p>Отправьте свое резюме на <a href="mailto:hr@lal-auto.ru">hr@lal-auto.ru</a> и мы рассмотрим вас, когда появится подходящая позиция.</p>
                    <button class="btn btn-outline-primary mt-3" data-bs-toggle="modal" data-bs-target="#responseModal">
                        <i class="bi bi-file-earmark-person"></i> Отправить резюме
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Отклик на вакансию</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="vacancyForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="vacancyPosition" class="form-label">Вакансия<span class="text-danger">*</span></label>
                        <select class="form-select" id="vacancyPosition" required>
                            <option value="" selected disabled>Выберите вакансию</option>
                            <option value="Менеджер по продажам">Менеджер по продажам автозапчастей</option>
                            <option value="Автомеханик">Автомеханик</option>
                            <option value="Другая">Другая вакансия</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="vacancyName" class="form-label">ФИО<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vacancyName" required>
                    </div>
                    <div class="mb-3">
                        <label for="vacancyPhone" class="form-label">Телефон<span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="vacancyPhone" placeholder="+7 (___) ___-__-__" required>
                    </div>
                    <div class="mb-3">
                        <label for="vacancyEmail" class="form-label">Email<span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="vacancyEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="vacancyMessage" class="form-label">Сопроводительное письмо</label>
                        <textarea class="form-control" id="vacancyMessage" rows="3" placeholder="Расскажите о своем опыте и почему вы хотите работать у нас"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Резюме<span class="text-danger">*</span></label>
                        <div class="file-upload">
                            <label for="vacancyFile" class="file-upload-btn">
                                <i class="bi bi-cloud-arrow-up fs-3"></i>
                                <p class="mt-2">Перетащите файл сюда или нажмите для выбора</p>
                                <small class="text-muted">Форматы: PDF, DOC, DOCX (до 5MB)</small>
                            </label>
                            <input type="file" class="form-control d-none" id="vacancyFile" accept=".pdf,.doc,.docx" required>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="vacancyAgree" required>
                        <label class="form-check-label" for="vacancyAgree">Я согласен на обработку персональных данных</label>
                    </div>
                </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-send"></i> Отправить отклик
                </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let fileInput = document.getElementById('vacancyFile');
    let fileUploadBtn = document.querySelector('.file-upload-btn');
    
    if(fileInput) 
    {
        fileInput.addEventListener('change', function(e) 
        {
            if(this.files && this.files[0]) 
            {
                let fileName = this.files[0].name;
                fileUploadBtn.innerHTML = `
                    <i class="bi bi-file-earmark-check fs-3 text-success"></i>
                    <p class="mt-2">${fileName}</p>
                    <small class="text-muted">Нажмите для изменения файла</small>
                `;
            }
        });
    }
    
    let form = document.getElementById('vacancyForm');

    if(form) 
    {
        form.addEventListener('submit', function(e) 
        {
            e.preventDefault();

            alert('Ваш отклик успешно отправлен! Мы свяжемся с вами в ближайшее время.');

            let modal = bootstrap.Modal.getInstance(document.getElementById('responseModal'));
            modal.hide();

            form.reset();
            fileUploadBtn.innerHTML = `
                <i class="bi bi-cloud-arrow-up fs-3"></i>
                <p class="mt-2">Перетащите файл сюда или нажмите для выбора</p>
                <small class="text-muted">Форматы: PDF, DOC, DOCX (до 5MB)</small>
            `;
        });
    }
});
</script>
</body>
</html>