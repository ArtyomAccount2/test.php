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

        let animatedElements = document.querySelectorAll('.feature-item-compact, .timeline-item-compact, .value-card-compact');

        let elementObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) 
                {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    elementObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        animatedElements.forEach(el => elementObserver.observe(el));
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
            <div class="col-lg-6 order-one">
                <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill">С 2010 года</span>
                <h1 class="about-title">
                    <span class="text-primary">Лал-Авто</span> - ваш надежный партнер в мире автозапчастей
                </h1>
                <p class="about-subtitle">Мы не просто продаем запчасти - мы обеспечиваем уверенность в каждой поездке. Более 13 лет доверия тысяч автомобилистов.</p>
                <div class="d-flex justify-content-between flex-wrap gap-3 mt-4">
                    <a href="../includes/assortment.php" class="btn btn-primary btn-lg px-4 py-2 weight-45">
                        <i class="bi bi-search me-2"></i>Найти запчасти
                    </a>
                    <a href="#stats" class="btn btn-outline-primary btn-lg px-4 py-2 weight-45">
                        <i class="bi bi-graph-up me-2"></i>Наши достижения
                    </a>
                </div>
            </div>
            <div class="col-lg-6 order-two">
                <div class="about-hero-image">
                    <img src="../img/company.jpg" alt="Лал-Авто" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </div>
</section>

<section id="stats" class="about-stats compact-stats">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Цифры, которые говорят сами за себя</h2>
            <p class="text-muted mb-4">Мы гордимся тем, что делаем, и можем это подтвердить</p>
        </div>
        <div class="row">
            <div class="col-md-3 col-6 stat-item-compact">
                <div class="stat-number-compact">13+</div>
                <div class="stat-label-compact">Лет успешной работы</div>
            </div>
            <div class="col-md-3 col-6 stat-item-compact">
                <div class="stat-number-compact">5000+</div>
                <div class="stat-label-compact">Позиций в каталоге</div>
            </div>
            <div class="col-md-3 col-6 stat-item-compact">
                <div class="stat-number-compact">50+</div>
                <div class="stat-label-compact">Официальных брендов</div>
            </div>
            <div class="col-md-3 col-6 stat-item-compact">
                <div class="stat-number-compact">10000+</div>
                <div class="stat-label-compact">Довольных клиентов</div>
            </div>
        </div>
    </div>
</section>

<section class="about-features compact-features">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Преимущества, которые вы оцените</h2>
            <p class="text-muted mb-4">Мы делаем покупку автозапчастей простой и надежной</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="feature-item-compact w-100">
                    <div class="feature-icon-compact">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h3>Гарантированное качество</h3>
                    <p class="small">Каждая запчасть проходит тройной контроль качества. Оригинальные комплектующие с гарантией от 1 года.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="feature-item-compact w-100">
                    <div class="feature-icon-compact">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h3>Молниеносная доставка</h3>
                    <p class="small">Доставка по городу за 2 часа, 5 пунктов самовывоза. Отправка по РФ в день заказа.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 d-flex">
                <div class="feature-item-compact w-100">
                    <div class="feature-icon-compact">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <h3>Экспертная поддержка</h3>
                    <p class="small">Специалисты с опытом от 5 лет. Круглосуточная поддержка и бесплатная консультация.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-history">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Наш путь к успеху</h2>
            <p class="text-muted mb-4">От небольшого склада до ведущего поставщика автозапчастей</p>
        </div>
        <div class="timeline-compact">
            <div class="timeline-item-compact">
                <div class="timeline-year-compact">2010</div>
                <div class="timeline-content-compact">
                    <h4>Основание компании</h4>
                    <p class="small">Открытие первого склада с 200 позициями запчастей для отечественных автомобилей</p>
                </div>
            </div>
            <div class="timeline-item-compact">
                <div class="timeline-year-compact">2014</div>
                <div class="timeline-content-compact">
                    <h4>Первый магазин</h4>
                    <p class="small">Открытие розничного магазина и начало работы с европейскими производителями</p>
                </div>
            </div>
            <div class="timeline-item-compact">
                <div class="timeline-year-compact">2018</div>
                <div class="timeline-content-compact">
                    <h4>Цифровизация</h4>
                    <p class="small">Запуск интернет-магазина и системы онлайн-заказов</p>
                </div>
            </div>
            <div class="timeline-item-compact">
                <div class="timeline-year-compact">2023</div>
                <div class="timeline-content-compact">
                    <h4>Официальный дилер</h4>
                    <p class="small">Стали официальным дилером 15+ мировых брендов автозапчастей</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-values compact-values">
    <div class="container">
        <div class="section-header text-center mb-4">
            <h2 class="section-title">Наши ценности</h2>
            <p class="text-muted mb-4">Принципы, которые лежат в основе нашей работы</p>
        </div>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="value-card-compact w-100">
                    <i class="bi bi-heart-fill text-primary mb-2"></i>
                    <h5>Клиент на первом месте</h5>
                    <p class="small">Каждый клиент для нас уникален. Мы не просто продаем запчасти - мы решаем проблемы.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="value-card-compact w-100">
                    <i class="bi bi-shield-check text-primary mb-2"></i>
                    <h5>Честность и прозрачность</h5>
                    <p class="small">Никаких скрытых платежей. Цена на сайте - окончательная цена.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="value-card-compact w-100">
                    <i class="bi bi-lightbulb text-primary mb-2"></i>
                    <h5>Инновации</h5>
                    <p class="small">Постоянно внедряем новые технологии для вашего удобства.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="value-card-compact w-100">
                    <i class="bi bi-people text-primary mb-2"></i>
                    <h5>Командный дух</h5>
                    <p class="small">Наша сила - в слаженной работе профессиональной команды.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-cta">
    <div class="container text-center">
        <h2>Готовы обновить ваше авто?</h2>
        <p class="lead">Более 5000 качественных запчастей уже ждут вас в каталоге</p>
        <div class="d-flex flex-wrap gap-3 justify-content-center">
            <a href="../includes/assortment.php" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-arrow-right me-2"></i>Перейти в каталог
            </a>
            <a href="tel:+78001234567" class="btn btn-outline-primary btn-lg px-5">
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