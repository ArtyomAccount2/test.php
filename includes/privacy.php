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
    <title>Политика конфиденциальности - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/privacy-styles.css">
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
            <h1 class="mb-3" style="padding-top: 60px;">Политика конфиденциальности</h1>
            <p class="lead">Последнее обновление: 31 июля 2025 года</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="privacy-content">
                <section class="mb-5">
                    <h3 class="mb-3">1. Общие положения</h3>
                    <p>1.1. Настоящая Политика конфиденциальности (далее — Политика) разработана в соответствии с законодательством Российской Федерации и определяет порядок обработки персональных данных и меры по обеспечению безопасности персональных данных в ООО "Лал-Авто" (далее — Компания).</p>
                    <p>1.2. Компания обязуется сохранять конфиденциальность полученных персональных данных и обеспечивать их защиту при обработке.</p>
                </section>
                <section class="mb-5">
                    <h3 class="mb-3">2. Какие данные мы собираем</h3>
                    <p>2.1. При регистрации на сайте, оформлении заказа, подписке на рассылку или заполнении форм обратной связи мы можем запросить у вас следующую информацию:</p>
                    <ul>
                        <li>Фамилия, имя, отчество</li>
                        <li>Контактный телефон</li>
                        <li>Адрес электронной почты</li>
                        <li>Адрес доставки</li>
                        <li>Данные об автомобиле (марка, модель, год выпуска)</li>
                    </ul>
                    <p>2.2. Мы также автоматически собираем техническую информацию при посещении сайта (IP-адрес, данные cookies, тип браузера и т.д.).</p>
                </section>
                <!-- Другие разделы политики -->
                <section class="mb-5">
                    <h3 class="mb-3">7. Контакты</h3>
                    <p>Если у вас есть вопросы относительно нашей Политики конфиденциальности, пожалуйста, свяжитесь с нами:</p>
                    <ul>
                        <li>Email: <a href="mailto:privacy@lal-auto.ru">privacy@lal-auto.ru</a></li>
                        <li>Телефон: +7 (4012) 65-65-65 (доб. 123)</li>
                        <li>Почтовый адрес: 236022, г. Калининград, ул. Автомобильная, д. 12, ООО "Лал-Авто"</li>
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