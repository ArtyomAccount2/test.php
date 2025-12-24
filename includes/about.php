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
                    <img src="../img/company.jpg" alt="Лал-Авто" class="img-fluid rounded">
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
                <div class="stat-label">Лет опыта</div>
            </div>
            <div class="col-md-3 stat-item">
                <div class="stat-number">5000+</div>
                <div class="stat-label">Товаров</div>
            </div>
            <div class="col-md-3 stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">Брендов</div>
            </div>
            <div class="col-md-3 stat-item">
                <div class="stat-number">10000+</div>
                <div class="stat-label">Клиентов</div>
            </div>
        </div>
    </div>
</section>

<section class="about-features">
    <div class="container">
        <h2 class="section-title text-center">Почему выбирают нас</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="feature-item w-100">
                    <div class="feature-icon">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h3>Гарантия качества</h3>
                    <p>Все запчасти проходят многоуровневую проверку качества. Гарантия на все товары от 1 года</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="feature-item w-100">
                    <div class="feature-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h3>Быстрая доставка</h3>
                    <p>Доставка по городу за 2 часа, по области - 1-2 дня. Самовывоз из 5 пунктов выдачи</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 d-flex">
                <div class="feature-item w-100">
                    <div class="feature-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h3>Экспертная поддержка</h3>
                    <p>Наши специалисты с опытом от 5 лет помогут подобрать оптимальное решение</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-history">
    <div class="container">
        <h2 class="section-title text-center">Наш путь к успеху</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-year">2010</div>
                <div class="timeline-content">
                    <h3>Начало пути</h3>
                    <p>Основание компании с небольшим складом запчастей для отечественных автомобилей</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2014</div>
                <div class="timeline-content">
                    <h3>Расширение</h3>
                    <p>Открытие первого розничного магазина и начало работы с европейскими брендами</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2018</div>
                <div class="timeline-content">
                    <h3>Цифровизация</h3>
                    <p>Запуск интернет-магазина и системы онлайн-заказов</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2023</div>
                <div class="timeline-content">
                    <h3>Лидерство</h3>
                    <p>Стали официальным дилером 10+ мировых брендов</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-values">
    <div class="container">
        <h2 class="section-title text-center">Наши ценности</h2>
        <div class="row g-4">
            <div class="col-lg-4 col-md-12 d-flex">
                <div class="value-card w-100">
                    <i class="bi bi-heart-fill text-primary mb-3"></i>
                    <h4>Клиентоориентированность</h4>
                    <p>Клиент всегда на первом месте. Мы стремимся превзойти ожидания каждого покупателя</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="value-card w-100">
                    <i class="bi bi-shield-check text-primary mb-3"></i>
                    <h4>Надежность</h4>
                    <p>Мы гарантируем качество каждой детали и выполняем обещания в срок</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="value-card w-100">
                    <i class="bi bi-arrow-repeat text-primary mb-3"></i>
                    <h4>Развитие</h4>
                    <p>Постоянно улучшаем сервис, расширяем ассортимент и внедряем новые технологии</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-cta">
    <div class="container text-center">
        <h2>Готовы найти нужные запчасти?</h2>
        <p class="lead">Наш каталог содержит более 5000 позиций от ведущих производителей</p>
        <a href="../includes/assortment.php" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-arrow-right me-2"></i>Перейти в каталог
        </a>
    </div>
</section>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>