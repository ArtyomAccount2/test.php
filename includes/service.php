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
    <title>Автосервис - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/service-styles.css">
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

        let serviceItems = document.querySelectorAll('.service-item');
        serviceItems.forEach(item => {
            item.addEventListener('click', function() 
            {
                let serviceName = this.querySelector('h6').textContent;
                let serviceSelect = document.getElementById('service');

                for (let option of serviceSelect.options) 
                {
                    if (option.text.includes(serviceName)) 
                    {
                        serviceSelect.value = option.value;
                        break;
                    }
                }

                document.getElementById('serviceForm').scrollIntoView({ behavior: 'smooth' });
            });
        });

        let serviceForm = document.getElementById('serviceForm');
        serviceForm.addEventListener('submit', function(e)
        {
            e.preventDefault();

            if (this.checkValidity()) 
            {
                let submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Заявка отправлена!';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    this.reset();
                    submitBtn.innerHTML = '<i class="bi bi-calendar-check me-2"></i>Записаться онлайн';
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-primary');
                    submitBtn.disabled = false;
                }, 3000);
            }
        });

        let today = new Date().toISOString().split('T')[0];
        document.getElementById('date').min = today;

        function equalizeCardHeights() 
        {
            let cards = document.querySelectorAll('.service-main-card');
            let maxHeight = 0;
            
            cards.forEach(card => {
                card.style.height = 'auto';
                let height = card.offsetHeight;

                if (height > maxHeight) 
                {
                    maxHeight = height;
                }
            });
            
            cards.forEach(card => {
                card.style.height = maxHeight + 'px';
            });
        }
        
        window.addEventListener('load', equalizeCardHeights);
        window.addEventListener('resize', equalizeCardHeights);
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5">
    <div class="hero-section text-center mb-5" style="padding-top: 85px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Профессиональный автосервис</h1>
        <p class="lead text-muted mb-4">Комплексное обслуживание и ремонт автомобилей любой сложности</p>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="service-main-card h-100">
                <div class="service-card h-100">
                    <div class="card-header-custom bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="header-icon me-3">
                                <i class="bi bi-tools"></i>
                            </div>
                            <h2 class="mb-0">Наши услуги</h2>
                        </div>
                    </div>
                    <div class="card-body-custom p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="service-item h-100" data-service="diagnostics">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-primary me-3">
                                            <i class="bi bi-search text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Компьютерная диагностика</h6>
                                            <p class="small text-muted mb-2">Полная проверка систем автомобиля</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 1500₽</span>
                                        <span class="service-time small text-muted">20-40 мин</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100" data-service="maintenance">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-success me-3">
                                            <i class="bi bi-gear-fill text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Техобслуживание</h6>
                                            <p class="small text-muted mb-2">Регулярное ТО по регламенту</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 3000₽</span>
                                        <span class="service-time small text-muted">1-2 часа</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100" data-service="engine">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-danger me-3">
                                            <i class="bi bi-lightning-charge text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Ремонт двигателя</h6>
                                            <p class="small text-muted mb-2">Диагностика и ремонт ДВС</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 5000₽</span>
                                        <span class="service-time small text-muted">2-6 часов</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100" data-service="suspension">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-warning me-3">
                                            <i class="bi bi-car-front text-dark"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Ходовая часть</h6>
                                            <p class="small text-muted mb-2">Ремонт подвески и рулевого управления</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 2500₽</span>
                                        <span class="service-time small text-muted">1-3 часа</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100" data-service="electronics">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-info me-3">
                                            <i class="bi bi-cpu text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Автоэлектрика</h6>
                                            <p class="small text-muted mb-2">Диагностика и ремонт электронных систем</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 2000₽</span>
                                        <span class="service-time small text-muted">1-4 часа</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="service-item h-100" data-service="tires">
                                    <div class="d-flex align-items-start mb-2">
                                        <div class="service-icon bg-secondary me-3">
                                            <i class="bi bi-circle text-white"></i>
                                        </div>
                                        <div class="service-content">
                                            <h6 class="mb-1">Шиномонтаж</h6>
                                            <p class="small text-muted mb-2">Балансировка и сезонная смена шин</p>
                                        </div>
                                    </div>
                                    <div class="service-meta">
                                        <span class="service-price text-primary fw-bold">от 1000₽</span>
                                        <span class="service-time small text-muted">30-60 мин</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="service-alert mt-4">
                            <div class="alert alert-warning mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-lightning-charge-fill me-3 fs-5"></i>
                                    <div>
                                        <strong>Срочный выездной ремонт!</strong><br>
                                        <small>Выезд мастера в течение 60 минут по Калининграду</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="express-services mt-4">
                            <h6 class="mb-3"><i class="bi bi-clock-fill text-primary me-2"></i>Экспресс-услуги</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="express-service">
                                        <span class="service-name">Замена АКБ</span>
                                        <span class="service-duration">30 мин</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="express-service">
                                        <span class="service-name">Замена ламп</span>
                                        <span class="service-duration">15 мин</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="express-service">
                                        <span class="service-name">Диагностика</span>
                                        <span class="service-duration">20 мин</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="express-service">
                                        <span class="service-name">Шиномонтаж</span>
                                        <span class="service-duration">45 мин</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="service-main-card h-100">
                <div class="service-card h-100">
                    <div class="card-header-custom bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="header-icon me-3">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <h2 class="mb-0">Онлайн-запись</h2>
                        </div>
                    </div>
                    <div class="card-body-custom p-4">
                        <div class="special-offer mb-4">
                            <div class="alert alert-info mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-gift-fill me-3"></i>
                                    <div>
                                        <strong>Скидка 10% при онлайн-записи!</strong><br>
                                        <small>Действует на все виды диагностики</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form id="serviceForm" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="bi bi-person me-1"></i>Ваше имя
                                    </label>
                                    <input type="text" class="form-control" id="name" placeholder="Иван Иванов" required>
                                    <div class="invalid-feedback">Пожалуйста, введите ваше имя</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold">
                                        <i class="bi bi-phone me-1"></i>Телефон
                                    </label>
                                    <input type="tel" class="form-control" id="phone" placeholder="+7 (900) 123-45-67" required pattern="[\+\-\d\s\(\)]{10,}">
                                    <div class="invalid-feedback">Пожалуйста, введите корректный телефон</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="car" class="form-label fw-semibold">
                                    <i class="bi bi-car-front me-1"></i>Автомобиль
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="car" placeholder="Марка, модель, год" required>
                                </div>
                                <div class="invalid-feedback">Пожалуйста, укажите автомобиль</div>
                            </div>
                            <div class="mb-3">
                                <label for="service" class="form-label fw-semibold">
                                    <i class="bi bi-tools me-1"></i>Услуга
                                </label>
                                <select class="form-select" id="service" required>
                                    <option value="" selected disabled>Выберите услугу</option>
                                    <option value="diagnostics">Компьютерная диагностика (от 1500₽)</option>
                                    <option value="maintenance">Техническое обслуживание (от 3000₽)</option>
                                    <option value="engine">Ремонт двигателя (от 5000₽)</option>
                                    <option value="suspension">Ремонт ходовой части (от 2500₽)</option>
                                    <option value="electronics">Автоэлектрика (от 2000₽)</option>
                                    <option value="tires">Шиномонтаж (от 1000₽)</option>
                                    <option value="other">Консультация специалиста</option>
                                </select>
                                <div class="invalid-feedback">Пожалуйста, выберите услугу</div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date" class="form-label fw-semibold">
                                        <i class="bi bi-calendar me-1"></i>Дата
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar-date"></i>
                                        </span>
                                        <input type="date" class="form-control" id="date" required>
                                    </div>
                                    <div class="invalid-feedback">Пожалуйста, выберите дату</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="time" class="form-label fw-semibold">
                                        <i class="bi bi-clock me-1"></i>Время
                                    </label>
                                    <select class="form-select" id="time" required>
                                        <option value="" selected disabled>Выберите время</option>
                                        <option value="09:00">09:00</option>
                                        <option value="10:00">10:00</option>
                                        <option value="11:00">11:00</option>
                                        <option value="12:00">12:00</option>
                                        <option value="13:00">13:00</option>
                                        <option value="14:00">14:00</option>
                                        <option value="15:00">15:00</option>
                                        <option value="16:00">16:00</option>
                                        <option value="17:00">17:00</option>
                                        <option value="18:00">18:00</option>
                                    </select>
                                    <div class="invalid-feedback">Пожалуйста, выберите время</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label fw-semibold">
                                    <i class="bi bi-chat-text me-1"></i>Описание проблемы
                                </label>
                                <textarea class="form-control" id="message" rows="2" placeholder="Опишите симптомы или проблему с автомобилем..."></textarea>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="consent" required>
                                <label class="form-check-label small" for="consent">
                                    Согласен на обработку персональных данных
                                </label>
                                <div class="invalid-feedback">Необходимо ваше согласие</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="bi bi-calendar-check me-2"></i>Записаться на сервис
                            </button>
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Подтверждение записи поступит в течение 15 минут
                                </small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="service-advantages mb-5">
        <h2 class="text-center mb-4">Преимущества нашего сервиса</h2>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h6>Сертифицированные мастера</h6>
                    <p class="small text-muted mb-0">Специалисты с опытом работы от 5 лет</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h6>Гарантия 12 месяцев</h6>
                    <p class="small text-muted mb-0">На все виды работ и запчасти</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h6>Прозрачные цены</h6>
                    <p class="small text-muted mb-0">Фиксированная стоимость без доплат</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h6>Срочный ремонт</h6>
                    <p class="small text-muted mb-0">Выезд мастера в течение часа</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>