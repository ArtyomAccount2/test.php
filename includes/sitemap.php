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
    <title>Карта сайта - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/sitemap-styles.css">
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
        <div class="col-12 text-center">
            <h1 class="mb-3" style="padding-top: 60px;">Карта сайта</h1>
            <p class="lead">Полная структура сайта для удобной навигации</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="sitemap-content">
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-house"></i> Главные страницы</h3>
                    <ul class="sitemap-list">
                        <li><a href="../index.php">Главная страница</a></li>
                        <li><a href="shops.php">Магазины</a></li>
                        <li><a href="service.php">Автосервис</a></li>
                        <li><a href="assortment.php">Ассортимент</a></li>
                        <li><a href="contacts.php">Контакты</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-cart"></i> Каталог и покупки</h3>
                    <ul class="sitemap-list">
                        <li><a href="oils.php">Масла и тех. жидкости</a></li>
                        <li><a href="accessories.php">Аксессуары</a></li>
                        <li><a href="brands.php">Торговые марки</a></li>
                        <li><a href="delivery.php">Оплата и доставка</a></li>
                        <li><a href="customers.php">Покупателям</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-info-circle"></i> Информация</h3>
                    <ul class="sitemap-list">
                        <li><a href="about.php">О компании</a></li>
                        <li><a href="news.php">Новости</a></li>
                        <li><a href="reviews.php">Отзывы</a></li>
                        <li><a href="vacancies.php">Вакансии</a></li>
                        <li><a href="requisites.php">Реквизиты</a></li>
                        <li><a href="suppliers.php">Поставщикам</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-shield"></i> Правовая информация</h3>
                    <ul class="sitemap-list">
                        <li><a href="privacy.php">Политика конфиденциальности</a></li>
                        <li><a href="terms.php">Условия использования</a></li>
                        <li><a href="return.php">Возврат и обмен</a></li>
                        <li><a href="guarantee.php">Гарантия</a></li>
                    </ul>
                </div>
                <div class="sitemap-section">
                    <h3 class="mb-4"><i class="bi bi-question-circle"></i> Помощь и поддержка</h3>
                    <ul class="sitemap-list">
                        <li><a href="faq.php">Частые вопросы (FAQ)</a></li>
                        <li><a href="support.php">Поддержка сайта</a></li>
                        <li><a href="api.php">API для разработчиков</a></li>
                    </ul>
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