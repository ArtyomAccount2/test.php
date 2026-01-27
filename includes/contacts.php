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
    <title>Контакты - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/contacts-styles.css">
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

        let contactForm = document.getElementById('contactForm');

        contactForm.addEventListener('submit', function(e) 
        {
            e.preventDefault();
            if (this.checkValidity()) 
            {
                let submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Сообщение отправлено!';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    this.reset();
                    submitBtn.innerHTML = '<i class="bi bi-send me-2"></i>Отправить сообщение';
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-primary');
                    submitBtn.disabled = false;
                }, 3000);
            }
        });

        let messageTextarea = document.getElementById('message');
        let charCounter = document.getElementById('charCounter');
        
        messageTextarea.addEventListener('input', function() 
        {
            let remaining = 500 - this.value.length;
            charCounter.textContent = remaining + ' символов осталось';
            
            if (remaining < 50) 
            {
                charCounter.className = 'form-text text-warning';
            } 
            else 
            {
                charCounter.className = 'form-text text-muted';
            }
            
            if (remaining < 0) 
            {
                this.value = this.value.substring(0, 500);
            }
        });

        function equalizeCardHeights() 
        {
            let cards = document.querySelectorAll('.contact-main-card');
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
        <h1 class="display-5 fw-bold text-primary mb-3">Контакты</h1>
        <p class="lead text-muted mb-4">Свяжитесь с нами удобным для вас способом</p>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="contact-main-card h-100">
                <div class="contact-card h-100">
                    <div class="card-header-custom bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="header-icon me-3">
                                <i class="bi bi-info-circle"></i>
                            </div>
                            <h4 class="mb-0">Контактная информация</h4>
                        </div>
                    </div>
                    <div class="card-body-custom p-4">
                        <div class="contact-items">
                            <div class="contact-item">
                                <div class="d-flex align-items-start">
                                    <div class="contact-icon bg-primary me-3">
                                        <i class="bi bi-geo-alt-fill text-white"></i>
                                    </div>
                                    <div class="contact-text">
                                        <h6 class="text-primary fw-bold mb-2">Адрес:</h6>
                                        <p class="mb-1">г. Калининград, ул. Автомобильная, 12</p>
                                        <small class="text-muted">Бесплатная парковка для клиентов</small>
                                    </div>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="d-flex align-items-start">
                                    <div class="contact-icon bg-primary me-3">
                                        <i class="bi bi-telephone-fill text-white"></i>
                                    </div>
                                    <div class="contact-text">
                                        <h6 class="text-primary fw-bold mb-2">Телефоны:</h6>
                                        <p class="mb-1">+7 (4012) 65-65-65 (многоканальный)</p>
                                        <p class="mb-1">+7 (911) 123-45-67 (мобильный)</p>
                                        <small class="text-muted">Консультация и запись на сервис</small>
                                    </div>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="d-flex align-items-start">
                                    <div class="contact-icon bg-primary me-3">
                                        <i class="bi bi-envelope-fill text-white"></i>
                                    </div>
                                    <div class="contact-text">
                                        <h6 class="text-primary fw-bold mb-2">Email:</h6>
                                        <p class="mb-1">
                                            <a href="mailto:info@lal-auto.ru" class="text-decoration-none">
                                                info@lal-auto.ru
                                            </a>
                                        </p>
                                        <p class="mb-1">
                                            <a href="mailto:sales@lal-auto.ru" class="text-decoration-none">
                                                sales@lal-auto.ru
                                            </a>
                                        </p>
                                        <small class="text-muted">Ответим в течение 24 часов</small>
                                    </div>
                                </div>
                            </div>
                            <div class="contact-item">
                                <div class="d-flex align-items-start">
                                    <div class="contact-icon bg-primary me-3">
                                        <i class="bi bi-clock-fill text-white"></i>
                                    </div>
                                    <div class="contact-text">
                                        <h6 class="text-primary fw-bold mb-2">Режим работы:</h6>
                                        <p class="mb-1">Пн-Пт: 9:00-20:00</p>
                                        <p class="mb-1">Сб-Вс: 10:00-18:00</p>
                                        <small class="text-muted">Без перерыва на обед</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="contact-main-card h-100">
                <div class="contact-card h-100">
                    <div class="card-header-custom bg-primary text-white">
                        <div class="d-flex align-items-center">
                            <div class="header-icon me-3">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <h4 class="mb-0">Обратная связь</h4>
                        </div>
                    </div>
                    <div class="card-body-custom p-4">
                        <form id="contactForm" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="bi bi-person me-1"></i>Ваше имя<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" placeholder="Иван Иванов" required>
                                    <div class="invalid-feedback">Пожалуйста, введите ваше имя</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="bi bi-envelope me-1"></i>Email<span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" placeholder="example@mail.ru" required>
                                    <div class="invalid-feedback">Пожалуйста, введите корректный email</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">
                                    <i class="bi bi-phone me-1"></i>Телефон
                                </label>
                                <input type="tel" class="form-control" id="phone" placeholder="+7 (900) 123-45-67">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label fw-semibold">
                                    <i class="bi bi-tag me-1"></i>Тема обращения<span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="subject" required>
                                    <option value="" selected disabled>Выберите тему</option>
                                    <option value="general">Общий вопрос</option>
                                    <option value="product">Вопрос по товару</option>
                                    <option value="service">Запись на сервис</option>
                                    <option value="complaint">Жалоба</option>
                                    <option value="cooperation">Сотрудничество</option>
                                </select>
                                <div class="invalid-feedback">Пожалуйста, выберите тему</div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label fw-semibold">
                                    <i class="bi bi-chat-text me-1"></i>Сообщение<span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="message" rows="4" placeholder="Опишите ваш вопрос подробнее..." required maxlength="500"></textarea>
                                <div class="invalid-feedback">Пожалуйста, напишите ваше сообщение</div>
                                <div id="charCounter" class="form-text text-muted">500 символов осталось</div>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="consent" required>
                                <label class="form-check-label small" for="consent">
                                    Согласен на обработку персональных данных
                                </label>
                                <div class="invalid-feedback">Необходимо ваше согласие</div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                <i class="bi bi-send me-2"></i>Отправить сообщение
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="map-section mb-5">
        <h2 class="text-center mb-4">Как нас найти</h2>
        <div class="map-container">
            <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A1234567890abcdef&amp;source=constructor" width="100%" height="400" frameborder="0"></iframe>
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