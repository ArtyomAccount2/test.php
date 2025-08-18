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
    <title>Условия использования - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/terms-styles.css">
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
            <h1 class="mb-3" style="padding-top: 60px;">Условия использования сайта</h1>
            <p class="lead">Правила и условия работы с сайтом lal-auto.ru</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="terms-content">
                <section class="mb-5">
                    <h3 class="mb-3">1. Общие положения</h3>
                    <p>1.1. Настоящие Условия использования (далее — Условия) регулируют отношения между ООО "Лал-Авто" (далее — Компания) и пользователями сайта lal-auto.ru (далее — Сайт).</p>
                    <p>1.2. Используя Сайт, вы соглашаетесь с настоящими Условиями. Если вы не согласны с Условиями, пожалуйста, не используйте Сайт.</p>
                    <p>1.3. Компания оставляет за собой право изменять Условия в любое время без предварительного уведомления. Новая редакция Условий вступает в силу с момента ее размещения на Сайте.</p>
                </section>
                
                <section class="mb-5">
                    <h3 class="mb-3">2. Использование сайта</h3>
                    <p>2.1. Сайт предназначен для ознакомления с товарами и услугами Компании, оформления заказов и получения информации.</p>
                    <p>2.2. Запрещается:</p>
                    <ul>
                        <li>Использовать Сайт в незаконных целях</li>
                        <li>Размещать вредоносный код или пытаться нарушить работу Сайта</li>
                        <li>Копировать контент без разрешения</li>
                        <li>Использовать автоматизированные системы для сбора данных</li>
                    </ul>
                </section>
                <!-- Другие разделы условий -->
                <section class="mb-5">
                    <h3 class="mb-3">7. Контакты</h3>
                    <p>По всем вопросам, связанным с настоящими Условиями, обращайтесь:</p>
                    <ul>
                        <li>Email: <a href="mailto:info@lal-auto.ru">info@lal-auto.ru</a></li>
                        <li>Телефон: +7 (4012) 65-65-65</li>
                        <li>Адрес: 236022, г. Калининград, ул. Автомобильная, д. 12</li>
                    </ul>
                </section>
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