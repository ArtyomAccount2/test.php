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
            header("Location: ../index.php");
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

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поддержка сайта - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/support-styles.css">
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

<div class="container my-5 pt-4">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="mb-3" style="padding-top: 60px;">Поддержка сайта</h1>
            <p class="lead">Помощь в работе с сайтом и технические вопросы</p>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="support-card">
                <h3 class="mb-4"><i class="bi bi-question-circle"></i> Частые вопросы</h3>
                <div class="accordion" id="supportAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#supportOne">
                                Как восстановить пароль?
                            </button>
                        </h2>
                        <div id="supportOne" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                На странице входа нажмите ссылку "Забыли пароль?". Введите email, указанный при регистрации, и следуйте инструкциям в письме.
                            </div>
                        </div>
                    </div>
                    <!-- Другие вопросы -->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="support-card">
                <h3 class="mb-4"><i class="bi bi-chat-left-text"></i> Обратная связь</h3>
                <form id="supportForm">
                    <div class="mb-3">
                        <label for="supportName" class="form-label">Ваше имя *</label>
                        <input type="text" class="form-control" id="supportName" required>
                    </div>
                    <div class="mb-3">
                        <label for="supportEmail" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="supportEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="supportTopic" class="form-label">Тема *</label>
                        <select class="form-select" id="supportTopic" required>
                            <option value="" selected disabled>Выберите тему</option>
                            <option>Техническая проблема</option>
                            <option>Вопрос по работе сайта</option>
                            <option>Предложение по улучшению</option>
                            <option>Другое</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="supportMessage" class="form-label">Сообщение *</label>
                        <textarea class="form-control" id="supportMessage" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="supportScreenshot" class="form-label">Скриншот (если требуется)</label>
                        <input type="file" class="form-control" id="supportScreenshot" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-send"></i> Отправить сообщение
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="support-contacts">
                <h3 class="mb-4 text-center"><i class="bi bi-headset"></i> Контакты поддержки</h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="contact-method">
                            <div class="contact-icon bg-primary">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <h4>Телефон</h4>
                            <p>+7 (4012) 65-65-67</p>
                            <small>Пн-Пт: 9:00-18:00</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-method">
                            <div class="contact-icon bg-success">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <h4>Email</h4>
                            <p>support@lal-auto.ru</p>
                            <small>Ответ в течение 24 часов</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-method">
                            <div class="contact-icon bg-warning">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <h4>Онлайн-чат</h4>
                            <p>Доступен в рабочее время</p>
                            <button class="btn btn-sm btn-outline-warning">Открыть чат</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>