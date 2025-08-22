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
    <title>О компании - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/about-styles.css">
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

        <section class="about-hero" style="margin-top: 100px;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="about-title">О компании Лал-Авто</h1>
                        <p class="about-subtitle">Ваш надежный партнер в мире автозапчастей с 2010 года</p>
                        <p class="about-description">
                            Мы специализируемся на поставках оригинальных и качественных автозапчастей 
                            от ведущих мировых производителей. Наша миссия - обеспечить автовладельцев 
                            надежными комплектующими по доступным ценам.
                        </p>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-hero-image">
                            <img src="../img/no-image.png" alt="Лал-Авто" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-features">
            <div class="container">
                <h2 class="section-title text-center">Наши преимущества</h2>
                <div class="row">
                    <div class="col-md-4 feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-award"></i>
                        </div>
                        <h3>Качество</h3>
                        <p>Только оригинальные запчасти от проверенных производителей с гарантией качества</p>
                    </div>
                    <div class="col-md-4 feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h3>Доставка</h3>
                        <p>Быстрая доставка по всему региону. 95% заказов доставляются в течение 1-3 дней</p>
                    </div>
                    <div class="col-md-4 feature-item">
                        <div class="feature-icon">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h3>Поддержка</h3>
                        <p>Квалифицированные специалисты готовы помочь с подбором запчастей 24/7</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-history">
            <div class="container">
                <h2 class="section-title">Наша история</h2>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-year">2010</div>
                        <div class="timeline-content">
                            <h3>Основание компании</h3>
                            <p>Начало работы с небольшим ассортиментом запчастей для отечественных автомобилей</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-year">2013</div>
                        <div class="timeline-content">
                            <h3>Расширение ассортимента</h3>
                            <p>Добавление запчастей для иномарок и открытие первого автосервиса</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-year">2018</div>
                        <div class="timeline-content">
                            <h3>Запуск интернет-магазина</h3>
                            <p>Создание современного онлайн-каталога с возможностью заказа через сайт</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-year">2023</div>
                        <div class="timeline-content">
                            <h3>Лидер рынка</h3>
                            <p>Становление одним из крупнейших поставщиков автозапчастей в регионе</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-stats">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 stat-item">
                        <div class="stat-number">13+</div>
                        <div class="stat-label">Лет на рынке</div>
                    </div>
                    <div class="col-md-3 stat-item">
                        <div class="stat-number">50000+</div>
                        <div class="stat-label">Товаров в каталоге</div>
                    </div>
                    <div class="col-md-3 stat-item">
                        <div class="stat-number">100+</div>
                        <div class="stat-label">Брендов партнеров</div>
                    </div>
                    <div class="col-md-3 stat-item">
                        <div class="stat-number">10000+</div>
                        <div class="stat-label">Довольных клиентов</div>
                    </div>
                </div>
            </div>
        </section>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>