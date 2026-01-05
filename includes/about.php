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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
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

        let statNumbers = document.querySelectorAll('.stat-number');

        statNumbers.forEach(stat => {
            let finalValue = parseInt(stat.textContent);
            let currentValue = 0;
            let increment = finalValue / 50;
            
            let timer = setInterval(() => {
                currentValue += increment;

                if (currentValue >= finalValue) 
                {
                    currentValue = finalValue;
                    clearInterval(timer);
                }

                stat.textContent = Math.floor(currentValue);
            }, 30);
        });

        let observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        let observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) 
                {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.about-card, .stat-item, .feature-item, .timeline-item, .value-card').forEach(el => {
            observer.observe(el);
        });
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<section class="about-hero-section" style="margin-top: 80px;">
    <div class="container">
        <div class="row align-items-center container-hero">
            <div class="col-lg-6 mb-5 mb-lg-0 container-hero-badge">
                <div class="hero-badge mb-4">С 2010 года</div>
                <h1 class="hero-title mb-4">
                    <span class="text-primary">Лал-Авто</span> - ваш надежный партнер в мире автозапчастей
                </h1>
                <p class="hero-subtitle mb-5">
                    Мы не просто продаем запчасти - мы обеспечиваем уверенность в каждой поездке. 
                    Более 13 лет доверия тысяч автомобилистов по всей России.
                </p>
                <div class="hero-actions">
                    <a href="../includes/assortment.php" class="btn btn-primary btn-lg px-4 py-3 me-3 mb-3">
                        <i class="bi bi-search me-2"></i>Найти запчасти
                    </a>
                    <a href="#stats" class="btn btn-outline-primary btn-lg px-4 py-3 mb-3">
                        <i class="bi bi-graph-up me-2"></i>Наши достижения
                    </a>
                </div>
            </div>
            <div class="col-lg-6 container-hero-badge">
                <div class="hero-image-container">
                    <img src="../img/company.jpg" alt="Лал-Авто" class="hero-image img-fluid rounded-3">
                    <div class="image-overlay"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="stats" class="about-stats-section">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title mb-3">Цифры, которые говорят сами за себя</h2>
            <p class="section-subtitle">Мы гордимся тем, что делаем, и можем это подтвердить</p>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="stat-number mb-2">13</div>
                    <div class="stat-label">Лет успешной работы</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="stat-number mb-2">5000</div>
                    <div class="stat-label">Позиций в каталоге</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-award"></i>
                    </div>
                    <div class="stat-number mb-2">50</div>
                    <div class="stat-label">Официальных брендов</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-number mb-2">10000</div>
                    <div class="stat-label">Довольных клиентов</div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-features-section">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title mb-3">Преимущества, которые вы оцените</h2>
            <p class="section-subtitle">Мы делаем покупку автозапчастей простой и надежной</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-item about-card">
                    <div class="feature-icon mb-4">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h3 class="feature-title mb-3">Гарантированное качество</h3>
                    <p class="feature-description">
                        Каждая запчасть проходит тройной контроль качества. 
                        Оригинальные комплектующие с гарантией от 1 года.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="feature-item about-card">
                    <div class="feature-icon mb-4">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h3 class="feature-title mb-3">Молниеносная доставка</h3>
                    <p class="feature-description">
                        Доставка по городу за 2 часа, 5 пунктов самовывоза. 
                        Отправка по РФ в день заказа.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="feature-item about-card">
                    <div class="feature-icon mb-4">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <h3 class="feature-title mb-3">Экспертная поддержка</h3>
                    <p class="feature-description">
                        Специалисты с опытом от 5 лет. Круглосуточная поддержка 
                        и бесплатная консультация.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-history-section">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title mb-3">Наш путь к успеху</h2>
            <p class="section-subtitle">От небольшого склада до ведущего поставщика автозапчастей</p>
        </div>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-year">2010</div>
                <div class="timeline-content">
                    <h4 class="mb-2">Основание компании</h4>
                    <p class="mb-0">Открытие первого склада с 200 позициями запчастей для отечественных автомобилей</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2014</div>
                <div class="timeline-content">
                    <h4 class="mb-2">Первый магазин</h4>
                    <p class="mb-0">Открытие розничного магазина и начало работы с европейскими производителями</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2018</div>
                <div class="timeline-content">
                    <h4 class="mb-2">Цифровизация</h4>
                    <p class="mb-0">Запуск интернет-магазина и системы онлайн-заказов</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-year">2023</div>
                <div class="timeline-content">
                    <h4 class="mb-2">Официальный дилер</h4>
                    <p class="mb-0">Стали официальным дилером 15+ мировых брендов автозапчастей</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-values-section">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title mb-3">Наши ценности</h2>
            <p class="section-subtitle">Принципы, которые лежат в основе нашей работы</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="value-card about-card">
                    <div class="value-icon mb-3">
                        <i class="bi bi-heart-fill"></i>
                    </div>
                    <h5 class="mb-3">Клиент на первом месте</h5>
                    <p class="mb-0">Каждый клиент для нас уникален. Мы не просто продаем запчасти - мы решаем проблемы.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="value-card about-card">
                    <div class="value-icon mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="mb-3">Честность и прозрачность</h5>
                    <p class="mb-0">Никаких скрытых платежей. Цена на сайте - окончательная цена.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="value-card about-card">
                    <div class="value-icon mb-3">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <h5 class="mb-3">Инновации</h5>
                    <p class="mb-0">Постоянно внедряем новые технологии для вашего удобства.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="value-card about-card">
                    <div class="value-icon mb-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <h5 class="mb-3">Командный дух</h5>
                    <p class="mb-0">Наша сила - в слаженной работе профессиональной команды.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-cta-section">
    <div class="container text-center">
        <h2 class="cta-title mb-4">Готовы обновить ваше авто?</h2>
        <p class="cta-subtitle mb-5">Более 5000 качественных запчастей уже ждут вас в каталоге</p>
        <div class="cta-actions">
            <a href="../includes/assortment.php" class="btn btn-primary btn-lg px-5 py-3 me-3 mb-3">
                <i class="bi bi-arrow-right me-2"></i>Перейти в каталог
            </a>
            <a href="tel:+78001234567" class="btn btn-outline-primary btn-lg px-5 py-3 mb-3">
                <i class="bi bi-telephone me-2"></i>8-800-123-45-67
            </a>
        </div>
    </div>
</section>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>