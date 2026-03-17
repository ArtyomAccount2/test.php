<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");
require_once("../config/check_auth.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    if (isset($_POST['login']) && isset($_POST['password'])) 
    {
        $login = $_POST['login'];
        $password = $_POST['password'];

        if (strtolower($login) === 'admin' && strtolower($password) === 'admin') 
        {
            $_SESSION['login_error'] = true;
            $_SESSION['error_message'] = "Неверный логин или пароль!";
            header("Location: " . $_SERVER['REQUEST_URI']);
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
                $_SESSION['user_id'] = $row['id_users'];
                unset($_SESSION['login_error']);
                unset($_SESSION['error_message']);
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            } 
            else 
            {
                $_SESSION['login_error'] = true;
                $_SESSION['error_message'] = "Неверный логин или пароль!";
                $_SESSION['form_data'] = $_POST;
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    }

    if (isset($_POST['contact_submit'])) 
    {
        $form_token = md5(serialize($_POST));
        
        if (isset($_SESSION['last_contact_form_token']) && $_SESSION['last_contact_form_token'] === $form_token) 
        {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = $_POST['subject'] ?? '';
        $message = trim($_POST['message'] ?? '');
        $consent = isset($_POST['consent']) ? 1 : 0;
        
        $errors = [];
        
        if (empty($name)) 
        {
            $errors[] = "Пожалуйста, введите ваше имя";
        }
        
        if (empty($email)) 
        {
            $errors[] = "Пожалуйста, укажите email";
        } 
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $errors[] = "Пожалуйста, введите корректный email";
        }
        
        if (!empty($phone) && !preg_match('/^[\+\-\d\s\(\)]{10,}$/', $phone)) 
        {
            $errors[] = "Пожалуйста, введите корректный номер телефона";
        }
        
        if (empty($subject)) 
        {
            $errors[] = "Пожалуйста, выберите тему обращения";
        }
        
        if (empty($message)) 
        {
            $errors[] = "Пожалуйста, напишите ваше сообщение";
        } 
        else if (strlen($message) > 500) 
        {
            $errors[] = "Сообщение не должно превышать 500 символов";
        }
        
        if (!$consent) 
        {
            $errors[] = "Необходимо ваше согласие на обработку персональных данных";
        }

        if (empty($errors)) 
        {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $insert_query = "INSERT INTO contact_messages (name, email, phone, subject, message, status, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, 'new', ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sssssss", $name, $email, $phone, $subject, $message, $ip_address, $user_agent);
            
            if ($stmt->execute()) 
            {
                $_SESSION['last_contact_form_token'] = $form_token;
                $_SESSION['show_contact_success_modal'] = true;

                $admin_email = "admin@lal-auto.ru";
                $email_subject = "Новое сообщение с сайта: $subject";
                $email_message = "Имя: $name\n";
                $email_message .= "Email: $email\n";
                $email_message .= "Телефон: $phone\n";
                $email_message .= "Тема: $subject\n";
                $email_message .= "Сообщение:\n$message\n";
                $email_message .= "IP: $ip_address\n";
                
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } 
            else 
            {
                $error_message = "Произошла ошибка при отправке сообщения: " . $conn->error;
            }
            $stmt->close();
        } 
        else 
        {
            $error_message = implode("<br>", $errors);
        }
    }
}

$show_success_modal = false;

if (isset($_SESSION['show_contact_success_modal']) && $_SESSION['show_contact_success_modal'] === true) 
{
    $show_success_modal = true;
    unset($_SESSION['show_contact_success_modal']);
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
    <link rel="icon" href="../img/iconAuto.png" type="image/png" height="32">
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

        if (!empty($error_message))
        {
        ?>
            setTimeout(function() 
            {
                alert('<?= addslashes($error_message) ?>');
            }, 100);
        <?php 
        }

        if ($show_success_modal)
        {
        ?>
            setTimeout(function() 
            {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            }, 100);
        <?php 
        }
        ?>

        let contactForm = document.getElementById('contactForm');

        if (contactForm) 
        {
            contactForm.addEventListener('submit', function(e) 
            {
                if (!this.checkValidity()) 
                {
                    e.preventDefault();
                    e.stopPropagation();
                }

                this.classList.add('was-validated');
            });
        }

        let messageTextarea = document.getElementById('message');
        let charCounter = document.getElementById('charCounter');
        
        if (messageTextarea && charCounter) 
        {
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
        }

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

        let phoneInput = document.getElementById('phone');

        if (phoneInput) 
        {
            phoneInput.addEventListener('input', function() 
            {
                this.value = this.value.replace(/[^\d\s\(\)\+\-]/g, '');
            });
        }

        let loginModal = document.getElementById('loginModal');

        if (loginModal) 
        {
            loginModal.addEventListener('show.bs.modal', function() 
            {
                let loginForm = document.querySelector('#loginModal form');

                if (loginForm) 
                {
                    loginForm.reset();
                }
            });
        }
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
    <?php 
    if (!empty($error_message))
    {
    ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
    }
    ?>
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
                        <form id="contactForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="needs-validation" novalidate>
                            <input type="hidden" name="contact_submit" value="1">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">
                                        <i class="bi bi-person me-1"></i>Ваше имя<span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Иван Иванов" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                    <div class="invalid-feedback">Пожалуйста, введите ваше имя</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">
                                        <i class="bi bi-envelope me-1"></i>Email<span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="example@mail.ru" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                    <div class="invalid-feedback">Пожалуйста, введите корректный email</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label fw-semibold">
                                    <i class="bi bi-phone me-1"></i>Телефон
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="+7 (900) 123-45-67" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label fw-semibold">
                                    <i class="bi bi-tag me-1"></i>Тема обращения<span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="subject" name="subject" required>
                                    <option value="" <?php echo empty($_POST['subject']) ? 'selected' : ''; ?> disabled>Выберите тему</option>
                                    <option value="general" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'general') ? 'selected' : ''; ?>>Общий вопрос</option>
                                    <option value="product" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'product') ? 'selected' : ''; ?>>Вопрос по товару</option>
                                    <option value="service" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'service') ? 'selected' : ''; ?>>Запись на сервис</option>
                                    <option value="complaint" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'complaint') ? 'selected' : ''; ?>>Жалоба</option>
                                    <option value="cooperation" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'cooperation') ? 'selected' : ''; ?>>Сотрудничество</option>
                                </select>
                                <div class="invalid-feedback">Пожалуйста, выберите тему</div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label fw-semibold">
                                    <i class="bi bi-chat-text me-1"></i>Сообщение<span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Опишите ваш вопрос подробнее..." required maxlength="500"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                <div class="invalid-feedback">Пожалуйста, напишите ваше сообщение</div>
                                <div id="charCounter" class="form-text text-muted">500 символов осталось</div>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="consent" name="consent" required <?php echo (isset($_POST['consent']) || empty($_POST)) ? 'checked' : ''; ?>>
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

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle-fill me-2"></i>Сообщение отправлено
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Ваше сообщение успешно отправлено! Мы ответим вам в ближайшее время.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
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