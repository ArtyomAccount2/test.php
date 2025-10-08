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
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5">
    <h1 class="text-center mb-5" style="padding-top: 85px;">Контакты</h1>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="contact-card p-4 h-100">
                <h2 class="mb-4">Контактная информация</h2>
                <div class="row">
                    <div class="contact-item mb-2 p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon bg-primary p-2 rounded-circle me-3">
                                <i class="bi bi-geo-alt-fill text-white"></i>
                            </div>
                            <div class="contact-text">
                                <h5 class="text-primary fw-bold mb-2">Адрес:</h5>
                                <p class="mb-1">г. Калининград, ул. Автомобильная, 12</p>
                                <small class="text-muted">Бесплатная парковка для клиентов</small>
                            </div>
                        </div>
                    </div>
                    <div class="contact-item mb-2 p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon bg-primary p-2 rounded-circle me-3">
                                <i class="bi bi-telephone-fill text-white"></i>
                            </div>
                            <div class="contact-text">
                                <h5 class="text-primary fw-bold mb-2">Телефоны:</h5>
                                <p class="mb-1">+7 (4012) 65-65-65 (многоканальный)</p>
                                <p class="mb-1">+7 (911) 123-45-67 (мобильный)</p>
                                <small class="text-muted">Консультация и запись на сервис</small>
                            </div>
                        </div>
                    </div>
                    <div class="contact-item mb-2 p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon bg-primary p-2 rounded-circle me-3">
                                <i class="bi bi-envelope-fill text-white"></i>
                            </div>
                            <div class="contact-text">
                                <h5 class="text-primary fw-bold mb-2">Email:</h5>
                                <p class="mb-1">
                                    <a href="mailto:info@lal-auto.ru" class="text-decoration-none text-dark">
                                        info@lal-auto.ru
                                    </a>
                                </p>
                                <p class="mb-1">
                                    <a href="mailto:sales@lal-auto.ru" class="text-decoration-none text-dark">
                                        sales@lal-auto.ru
                                    </a>
                                </p>
                                <small class="text-muted">Ответим в течение 24 часов</small>
                            </div>
                        </div>
                    </div>
                    <div class="contact-item p-3 rounded-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon bg-primary p-2 rounded-circle me-3">
                                <i class="bi bi-clock-fill text-white"></i>
                            </div>
                            <div class="contact-text">
                                <h5 class="text-primary fw-bold mb-2">Режим работы:</h5>
                                <p class="mb-1">Пн-Пт: 9:00-20:00</p>
                                <p class="mb-1">Сб-Вс: 10:00-18:00</p>
                                <small class="text-muted">Без перерыва на обед</small>
                            </div>
                        </div>
                    </div>
                    <div class="p-3 rounded-3" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left: 4px solid #ffc107;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-2 fs-5"></i>
                            <h5 class="mb-0 text-dark">Срочная помощь</h5>
                        </div>
                        <p class="small mb-2">Экстренные случаи и техническая поддержка</p>
                        <div class="d-grid gap-2">
                            <a href="tel:+79111234567" class="btn btn-warning btn-sm">
                                <i class="bi bi-telephone-fill me-1"></i>+7 (911) 123-45-67
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="contact-card p-4 h-100">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary p-3 rounded-circle me-3">
                        <i class="bi bi-chat-dots-fill text-white fs-4"></i>
                    </div>
                    <h2 class="mb-0 text-primary">Обратная связь</h2>
                </div>
                <form id="contactForm" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>Ваше имя
                            </label>
                            <input type="text" class="form-control form-control-lg" id="name" placeholder="Иван Иванов" required>
                            <div class="invalid-feedback">Пожалуйста, введите ваше имя</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1"></i>Email
                            </label>
                            <input type="email" class="form-control form-control-lg" id="email" placeholder="example@mail.ru" required>
                            <div class="invalid-feedback">Пожалуйста, введите корректный email</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">
                            <i class="bi bi-phone me-1"></i>Телефон
                        </label>
                        <input type="tel" class="form-control form-control-lg" id="phone" placeholder="+7 (900) 123-45-67">
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i>Тема обращения
                        </label>
                        <select class="form-select form-select-lg" id="subject" required>
                            <option value="" selected disabled>Выберите тему</option>
                            <option value="general">Общий вопрос</option>
                            <option value="product">Вопрос по товару</option>
                            <option value="service">Запись на сервис</option>
                            <option value="complaint">Жалоба</option>
                            <option value="cooperation">Сотрудничество</option>
                        </select>
                        <div class="invalid-feedback">Пожалуйста, выберите тему</div>
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label fw-semibold">
                            <i class="bi bi-chat-text me-1"></i>Сообщение
                        </label>
                        <textarea class="form-control form-control-lg" id="message" rows="5" 
                                placeholder="Опишите ваш вопрос подробнее..." required></textarea>
                        <div class="invalid-feedback">Пожалуйста, напишите ваше сообщение</div>
                        <div class="form-text">Максимум 500 символов</div>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="consent" required>
                        <label class="form-check-label small" for="consent">
                            Я согласен на обработку персональных данных
                        </label>
                        <div class="invalid-feedback">Необходимо ваше согласие</div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                        <i class="bi bi-send me-2"></i>Отправить сообщение
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <h2 class="text-center mb-4">Как нас найти</h2>
        <div class="map-container">
            <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A1234567890abcdef&amp;source=constructor" width="100%" height="500" frameborder="0"></iframe>
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