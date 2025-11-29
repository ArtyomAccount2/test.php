<?php
error_reporting(E_ALL);
session_start();
require_once("config/link.php");

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
        header("Location: admin.php");
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

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

function getBrandsData() 
{
    return [
        ['name' => 'Acura', 'image' => 'img/Stamps/Acura.png', 'search_term' => 'acura'],
        ['name' => 'Aixam', 'image' => 'img/Stamps/Aixam.png', 'search_term' => 'aixam'],
        ['name' => 'Alfa Romeo', 'image' => 'img/Stamps/Alfa Romeo.png', 'search_term' => 'alfa romeo'],
        ['name' => 'Aston Martin', 'image' => 'img/Stamps/Aston Martin.png', 'search_term' => 'aston martin'],
        ['name' => 'Audi', 'image' => 'img/Stamps/Audi.png', 'search_term' => 'audi'],
        ['name' => 'BMW', 'image' => 'img/Stamps/BMW.png', 'search_term' => 'bmw'],
        ['name' => 'Bentley', 'image' => 'img/Stamps/Bentley.png', 'search_term' => 'bentley'],
        ['name' => 'Buick', 'image' => 'img/Stamps/Buick.png', 'search_term' => 'buick'],
        ['name' => 'Cadillac', 'image' => 'img/Stamps/Cadillac.png', 'search_term' => 'cadillac'],
        ['name' => 'Chevrolet', 'image' => 'img/Stamps/Chevrolet.png', 'search_term' => 'chevrolet'],
        ['name' => 'Chrysler', 'image' => 'img/Stamps/Chrysler.png', 'search_term' => 'chrysler'],
        ['name' => 'Dodge', 'image' => 'img/Stamps/Dodge.png', 'search_term' => 'dodge'],
        ['name' => 'Fiat', 'image' => 'img/Stamps/Fiat.png', 'search_term' => 'fiat'],
        ['name' => 'Ford', 'image' => 'img/Stamps/Ford.png', 'search_term' => 'ford'],
        ['name' => 'Gaz', 'image' => 'img/Stamps/Gaz.png', 'search_term' => 'gaz'],
        ['name' => 'Honda', 'image' => 'img/Stamps/Honda.png', 'search_term' => 'honda'],
        ['name' => 'Hummer', 'image' => 'img/Stamps/Hummer.png', 'search_term' => 'hummer'],
        ['name' => 'Hyundai', 'image' => 'img/Stamps/Hyundai.png', 'search_term' => 'hyundai'],
        ['name' => 'Infiniti', 'image' => 'img/Stamps/Infiniti.png', 'search_term' => 'infiniti'],
        ['name' => 'Jaguar', 'image' => 'img/Stamps/Jaguar.png', 'search_term' => 'jaguar'],
        ['name' => 'Jeep', 'image' => 'img/Stamps/Jeep.png', 'search_term' => 'jeep'],
        ['name' => 'Kia', 'image' => 'img/Stamps/Kia.png', 'search_term' => 'kia'],
        ['name' => 'Lada', 'image' => 'img/Stamps/Lada.png', 'search_term' => 'lada'],
        ['name' => 'Lamborghini', 'image' => 'img/Stamps/Lamborghini.png', 'search_term' => 'lamborghini'],
        ['name' => 'Lancia', 'image' => 'img/Stamps/Lancia.png', 'search_term' => 'lancia'],
        ['name' => 'Land Rover', 'image' => 'img/Stamps/Land Rover.png', 'search_term' => 'land rover'],
        ['name' => 'Lexus', 'image' => 'img/Stamps/Lexus.png', 'search_term' => 'lexus'],
        ['name' => 'Lotus', 'image' => 'img/Stamps/Lotus.png', 'search_term' => 'lotus']
    ];
}

function getPartsData() 
{
    return [
        ['name' => 'Коленчатый вал', 'image' => 'img/SpareParts/image1.png', 'category' => 'двигатель', 'search_term' => 'коленчатый вал'],
        ['name' => 'Прокладки двигателя', 'image' => 'img/SpareParts/image2.png', 'category' => 'двигатель', 'search_term' => 'прокладки двигателя'],
        ['name' => 'Топливный насос', 'image' => 'img/SpareParts/image3.png', 'category' => 'двигатель', 'search_term' => 'топливный насос'],
        ['name' => 'Распределительный вал', 'image' => 'img/SpareParts/image4.png', 'category' => 'двигатель', 'search_term' => 'распределительный вал'],
        ['name' => 'Тормозной цилиндр', 'image' => 'img/SpareParts/image5.png', 'category' => 'тормозная система', 'search_term' => 'тормозной цилиндр'],
        ['name' => 'Тормозные колодки', 'image' => 'img/SpareParts/image6.png', 'category' => 'тормозная система', 'search_term' => 'тормозные колодки'],
        ['name' => 'Стабилизатор', 'image' => 'img/SpareParts/image7.png', 'category' => 'ходовая часть', 'search_term' => 'стабилизатор'],
        ['name' => 'Тормозные суппорта', 'image' => 'img/SpareParts/image8.png', 'category' => 'тормозная система', 'search_term' => 'тормозные суппорта'],
        ['name' => 'Топливный фильтр', 'image' => 'img/SpareParts/image9.png', 'category' => 'фильтры', 'search_term' => 'топливный фильтр'],
        ['name' => 'Тормозные диски', 'image' => 'img/SpareParts/image10.png', 'category' => 'тормозная система', 'search_term' => 'тормозные диски'],
        ['name' => 'Цапфа', 'image' => 'img/SpareParts/image11.png', 'category' => 'ходовая часть', 'search_term' => 'цапфа'],
        ['name' => 'Сальники', 'image' => 'img/SpareParts/image12.png', 'category' => 'двигатель', 'search_term' => 'сальники']
    ];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лал-Авто - Автозапчасти</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/index-styles.css">
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

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-xl navbar-light bg-light shadow-sm fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="img/Auto.png" alt="Лал-Авто" height="75"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link text-dark dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Навигация</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#carouselExample">Слайдер</a></li>
                            <li><a class="dropdown-item" href="#aboutUs">О Нас</a></li>
                            <li><a class="dropdown-item" href="#specialOffer">Колесо Фортуны</a></li>
                            <li><a class="dropdown-item" href="#nextSection">Поиск по марке автомобиля</a></li>
                            <li><a class="dropdown-item" href="#nextSection2">Популярные запчасти</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link text-dark dropdown-toggle" href="#" id="navbarDropdownMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">Меню</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenu">
                            <li><a class="dropdown-item" href="includes/shops.php">Магазины</a></li>
                            <li><a class="dropdown-item" href="includes/service.php">Автосервис</a></li>
                            <li><a class="dropdown-item" href="includes/assortment.php">Ассортимент</a></li>
                            <li><a class="dropdown-item" href="includes/oils.php">Масла и тех. жидкости</a></li>
                            <li><a class="dropdown-item" href="includes/accessories.php">Аксессуары</a></li>
                            <li><a class="dropdown-item" href="includes/customers.php">Покупателям</a></li>
                            <li><a class="dropdown-item" href="includes/requisites.php">Реквизиты</a></li>
                            <li><a class="dropdown-item" href="includes/suppliers.php">Поставщикам</a></li>
                            <li><a class="dropdown-item" href="includes/vacancies.php">Вакансии</a></li>
                            <li><a class="dropdown-item" href="includes/contacts.php">Контакты</a></li>
                            <li><a class="dropdown-item" href="includes/reviews.php">Отзывы</a></li>
                            <li><a class="dropdown-item" href="includes/delivery.php">Оплата и доставка</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="includes/brands.php">Торговые марки</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="includes/support.php">Поддержка сайта</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="includes/news.php">Новости компании</a>
                    </li>
                </ul>
                <form id="catalogSearchForm" class="d-flex align-items-center me-3">
                    <div class="input-group">
                        <input class="form-control me-2 search-input" type="search" placeholder="Поиск по каталогу" aria-label="Search" id="catalogSearchInput">
                        <button class="btn btn-outline-primary button-link search-button" type="submit">
                            <i class="bi bi-search"></i>
                            <span class="search-text">Найти</span>
                        </button>
                    </div>
                </form>
                <div class="ms-xl-3 ms-lg-2 ms-md-1">
                    <?php 
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
                    {
                    ?>
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <p class="mb-0 text-center text-md-end me-md-2" style="font-size: 0.9em; white-space: nowrap;">
                                <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>
                            </p>
                            <a href="profile.php" class="profile-button w-md-auto text-decoration-none">
                                <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                            </a>
                        </div>
                    <?php 
                    } 
                    else 
                    {
                    ?>
                        <div class="d-flex flex-wrap flex-md-nowrap">
                            <a href="#" class="btn btn-primary button-link w-md-auto mx-1" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-box-arrow-in-right"></i> Войти
                            </a>
                            <a href="#" class="btn btn-primary button-link w-md-auto" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="bi bi-r-circle"></i> Зарегистрироваться
                            </a>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="loginModalLabel">Авторизация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/">
                        <div class="mb-3">
                            <label for="username" class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" id="username" placeholder="Введите логин" required value="<?= htmlspecialchars($form_data['login'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Введите пароль" required value="<?= htmlspecialchars($form_data['password'] ?? '') ?>">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="rememberMe" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                        </div>
                        <?php 
                        if (isset($_SESSION['error_message'])) 
                        {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($_SESSION['error_message']); ?>
                            </div>
                        <?php 
                            unset($_SESSION['error_message']);
                        }
                        ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Войти
                        </button>
                        <a href="#" class="btn btn-link">Забыли пароль?</a>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="carousel-image-container">
                    <img src="img/1.jpg" class="carousel-image" alt="Лучшие автозапчасти">
                    <div class="carousel-overlay"></div>
                </div>
                <div class="carousel-caption">
                    <div class="container">
                        <div class="caption-content">
                            <h1 class="caption-title animate-slide-down">Лучшие автозапчасти</h1>
                            <p class="caption-text animate-slide-up">Более 50,000 качественных запчастей для вашего автомобиля</p>
                            <div class="caption-features animate-fade-in">
                                <div class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Гарантия качества</span>
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Быстрая доставка</span>
                                </div>
                                <div class="feature-item">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <span>Оригинальные бренды</span>
                                </div>
                            </div>
                            <div class="caption-actions animate-slide-up">
                                <a href="includes/assortment.php" class="btn btn-primary btn-lg me-3">
                                    <i class="bi bi-cart-plus me-2"></i>Перейти к покупкам
                                </a>
                                <a href="#aboutUs" class="btn btn-outline-light btn-lg">
                                    <i class="bi bi-info-circle me-2"></i>Узнать больше
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="carousel-image-container">
                    <img src="img/2.jpg" class="carousel-image" alt="Качество и надежность">
                    <div class="carousel-overlay"></div>
                </div>
                <div class="carousel-caption">
                    <div class="container">
                        <div class="caption-content">
                            <h1 class="caption-title animate-slide-down">Качество и надежность</h1>
                            <p class="caption-text animate-slide-up">Только проверенные производители с гарантией</p>
                            <div class="stats-container animate-fade-in">
                                <div class="stat-card">
                                    <div class="stat-number">100+</div>
                                    <div class="stat-label">Брендов</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number">12</div>
                                    <div class="stat-label">Лет на рынке</div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-number">50000+</div>
                                    <div class="stat-label">Товаров</div>
                                </div>
                            </div>
                            <div class="caption-actions animate-slide-up">
                                <a href="includes/brands.php" class="btn btn-primary btn-lg me-3">
                                    <i class="bi bi-tags me-2"></i>Все бренды
                                </a>
                                <a href="#nextSection" class="btn btn-outline-light btn-lg">
                                    <i class="bi bi-search me-2"></i>Найти запчасть
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="carousel-image-container">
                    <img src="img/3.jpeg" class="carousel-image" alt="Доставка по России">
                    <div class="carousel-overlay"></div>
                </div>
                <div class="carousel-caption">
                    <div class="container">
                        <div class="caption-content">
                            <h1 class="caption-title animate-slide-down">Доставка по России</h1>
                            <p class="caption-text animate-slide-up">Быстрая доставка в любой регион страны</p>
                            <div class="stats-container animate-fade-in">
                                <div class="stat-card">
                                    <i class="bi bi-truck"></i>
                                    <div>
                                        <div class="stat-number">1-3 дня</div>
                                        <div class="stat-label">по всей России</div>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <i class="bi bi-geo-alt"></i>
                                    <div>
                                        <div class="stat-number">Самовывоз</div>
                                        <div class="stat-label">из 5 магазинов</div>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <i class="bi bi-credit-card"></i>
                                    <div>
                                        <div class="stat-number">Оплата</div>
                                        <div class="stat-label">любым способом</div>
                                    </div>
                                </div>
                            </div>
                            <div class="caption-actions animate-slide-up">
                                <a href="includes/delivery.php" class="btn btn-primary btn-lg me-3">
                                    <i class="bi bi-truck me-2"></i>Условия доставки
                                </a>
                                <a href="includes/shops.php" class="btn btn-outline-light btn-lg">
                                    <i class="bi bi-shop me-2"></i>Наши магазины
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Предыдущий</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Следующий</span>
        </button>
    </div>

    <section id="aboutUs" class="py-5 position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100">
            <div class="about-bg-circle circle-1"></div>
            <div class="about-bg-circle circle-2"></div>
        </div>
        <div class="container position-relative">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="display-4 fw-bold mb-3 text-gradient-primary">Лал-Авто - ваш надежный партнер</h2>
                <div class="divider mx-auto bg-primary" style="width: 80px; height: 4px;"></div>
                <p class="lead mt-3">Качество, проверенное временем</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="about-card h-100">
                        <div class="about-card-icon">
                            <i class="bi bi-star-fill" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="h4 mb-3">Качество</h3>
                        <p>Мы сотрудничаем только с проверенными мировыми производителями автозапчастей, гарантируя оригинальность и долговечность каждой детали.</p>
                        <div class="about-card-number">01</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="about-card h-100">
                        <div class="about-card-icon">
                            <i class="bi bi-truck" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="h4 mb-3">Доставка</h3>
                        <p>Собственная логистическая служба обеспечивает быструю доставку в любой регион. 95% заказов доставляются в течение 1-3 дней.</p>
                        <div class="about-card-number">02</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="about-card h-100">
                        <div class="about-card-icon">
                            <i class="bi bi-info-circle-fill" style="font-size: 2rem;"></i>
                        </div>
                        <h3 class="h4 mb-3">Поддержка</h3>
                        <p>Наши специалисты готовы помочь с подбором запчастей 24/7. Среднее время ответа на запрос - 15 минут.</p>
                        <div class="about-card-number">03</div>
                    </div>
                </div>
            </div>
            <div class="stats-section mt-5 py-4" data-aos="fade-up">
                <div class="row g-3 text-center">
                    <div class="col-6 col-md-3">
                        <div class="stat-item">
                            <div class="stat-number" data-count="12">21</div>
                            <div class="stat-label">Лет на рынке</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-item">
                            <div class="stat-number" data-count="50000">500+</div>
                            <div class="stat-label">Товаров</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-item">
                            <div class="stat-number" data-count="100">39+</div>
                            <div class="stat-label">Брендов</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-item">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">Поддержка</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="100">
                <a href="includes/about.php" class="btn btn-primary btn-lg px-4 py-2 btn-hover-effect">
                    <span>Подробнее о компании</span>
                </a>
            </div>
        </div>
    </section>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="registerModalLabel">Регистрация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <a href="individuel.php" type="button" class="btn btn-primary mb-2" id="individualsBtn">
                        <i class="bi bi-person-add"></i> Физические лица
                    </a>
                    <div id="individualsInfo" class="registration-info">
                        <p>- если Вы - физическое лицо, пройдите регистрацию. Регистрация возможна как при наличии карты скидок, так и при её отсутствии.</p>
                    </div>
                    <a href="legalEntity.php" type="button" class="btn btn-primary mb-2" id="legalEntitiesBtn">
                        <i class="bi bi-person-add"></i> Юридические лица и ИП
                    </a>
                    <div id="legalEntitiesInfo" class="registration-info">
                        <p>- если Вы - представитель организации, учреждения, предприятия или фирмы, заполните данную форму регистрации.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="container my-5 text-center" id="specialOffer">
        <h2 class="text-center mb-4">Колесо Фортуны</h2>
        <p class="lead mb-4">Крутите колесо и получите специальное предложение!</p>
        <br>
        <div class="wheel-container mb-4">
            <canvas id="wheelCanvas" width="300" height="300"></canvas>
            <div class="wheel-pointer"></div>
        </div>
        <button id="spinButton" class="btn btn-primary btn-lg mb-3">Крутить колесо!</button>        
        <div id="resultContainer" class="alert alert-success" style="display: none;">
            <h4 id="resultText"></h4>
            <p id="resultDescription" class="mb-0"></p>
        </div>
        <div id="purchaseCounter" class="alert alert-info" style="display: none;">
            <p style="margin-top: 15px;">До следующего вращения осталось: <span id="purchasesLeft">10</span> покупок</p>
        </div>
    </section>

    <div class="modal fade" id="wheelResultModal" tabindex="-1" aria-labelledby="wheelResultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content prize-modal-content">
                <div id="confetti-container"></div>
                
                <div class="modal-header prize-modal-header justify-content-center position-relative">
                    <div class="prize-ribbon position-absolute">
                        <i class="bi bi-gift-fill" style="color: #333;"></i>
                    </div>
                    <h3 class="modal-title w-100 text-center prize-title" id="wheelResultModalLabel">
                        <span class="prize-congrats">Поздравляем!</span>
                    </h3>
                    <button type="button" class="btn-close prize-close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white; font-size: 1.2rem;">×</span>
                    </button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="prize-icon-container mb-3">
                        <div class="prize-icon-wrapper">
                            <i class="bi bi-trophy-fill prize-icon" id="modalPrizeIcon"></i>
                        </div>
                    </div>
                    <h4 class="prize-result-text mb-2" id="modalResultText"></h4>
                    <p class="prize-description mb-3" id="modalResultDescription"></p>
                    <div class="prize-code-container bg-light rounded p-3 mb-3" id="prizeCodeSection" style="display: none;">
                        <p class="mb-1 small text-muted">Ваш промокод:</p>
                        <div class="d-flex align-items-center justify-content-center">
                            <code class="promo-code fs-5 fw-bold me-2" id="modalPromoCode"></code>
                            <button class="btn btn-sm btn-outline-secondary copy-btn" data-bs-toggle="tooltip" title="Скопировать">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                        <small class="text-muted">Действителен 7 дней</small>
                    </div>
                    <div class="prize-actions mt-4">
                        <button type="button" class="btn btn-primary btn-lg prize-action-btn me-2" id="usePrizeBtn">
                            <i class="bi bi-cart-check me-2"></i>Использовать сейчас
                        </button>
                        <button type="button" class="btn btn-outline-secondary prize-action-btn" data-bs-dismiss="modal">
                            <i class="bi bi-clock me-2"></i>Воспользоваться позже
                        </button>
                    </div>
                </div>
                <div class="modal-footer justify-content-center border-top-0 pt-0">
                    <div class="prize-timer text-center w-100">
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                role="progressbar" style="width: 100%" id="prizeTimerBar"></div>
                        </div>
                        <small class="text-muted">Это окно закроется автоматически через <span id="prizeTimer">10</span> сек.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="container-fluid" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9f9ff 100%); position: relative; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);">
            <div class="position-absolute top-0 start-0 w-100 h-100">
                <div class="about-bg-circle circle-1"></div>
                <div class="about-bg-circle circle-2"></div>
            </div>
            <div class="container">
            <section class="container my-5" id="nextSection">
                <div class="search-section">
                    <div class="section-header text-center mb-5">
                        <h2 class="mb-3">Найдите запчасти для вашего автомобиля</h2>
                        <p class="lead text-muted">Более 50,000 оригинальных запчастей в наличии</p>
                        <div class="search-container position-relative mx-auto" style="max-width: 600px;">
                            <input type="text" id="brandSearch" placeholder="Введите марку автомобиля..." class="form-control form-control-lg search-input">
                            <button class="btn btn-link search-clear" type="button" style="display: none;">
                                <i class="bi bi-x"></i>
                            </button>
                            <i class="bi bi-search search-icon"></i>
                        </div>
                    </div>
                    <div class="brands-container">
                        <div class="position-relative">
                            <div class="section-subheader d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Популярные марки</h4>
                                <a href="includes/brands.php" class="btn btn-outline-primary btn-sm">
                                    Все марки <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="scrollable-container-wrapper">
                                <div id="carBrandsList" class="scrollable-container">
                                    <div id="no-results-brands" class="no-results-message">
                                        <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Марка не найдена</p>
                                        <small class="text-muted">Попробуйте изменить запрос или посмотреть все марки</small>
                                    </div>
                                    <div class="scrollable" id="carBrandsBlock">
                                        <?php
                                        $brands = getBrandsData();
                                        foreach ($brands as $brand) 
                                        {
                                            echo '
                                            <div class="scrollable-item">
                                                <div class="card brand-card h-100" data-brand="' . $brand['search_term'] . '">
                                                    <div class="card-img-container">
                                                        <img src="' . $brand['image'] . '" class="card-img-top" alt="' . $brand['name'] . '">
                                                        <div class="card-overlay">
                                                            <button class="btn btn-primary select-brand-btn" data-brand="' . $brand['search_term'] . '">
                                                                Выбрать <i class="bi bi-chevron-right ms-1"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <h6 class="card-title mb-0">' . $brand['name'] . '</h6>
                                                    </div>
                                                </div>
                                            </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <button class="scroll-button scroll-left" aria-label="Прокрутить влево">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button class="scroll-button scroll-right" aria-label="Прокрутить вправо">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="container my-5" id="nextSection2">
                <div class="search-section">
                    <div class="section-header text-center mb-5">
                        <h2 class="mb-3">Популярные автозапчасти</h2>
                        <p class="lead text-muted">Качественные комплектующие от проверенных производителей</p>
                        <div class="search-container position-relative mx-auto" style="max-width: 600px;">
                            <input type="text" id="partsSearch" placeholder="Найдите нужную запчасть..." class="form-control form-control-lg search-input">
                            <button class="btn btn-link search-clear" type="button" style="display: none;">
                                <i class="bi bi-x"></i>
                            </button>
                            <i class="bi bi-search search-icon"></i>
                        </div>
                    </div>
                    <div class="parts-container">
                        <div class="position-relative">
                            <div class="section-subheader d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Часто покупаемые запчасти</h4>
                                <a href="includes/assortment.php" class="btn btn-outline-primary btn-sm">
                                    Весь каталог <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                            <div class="scrollable-container-wrapper">
                                <div id="popularParts" class="scrollable-container">
                                    <div id="no-results-parts" class="no-results-message">
                                        <i class="bi bi-exclamation-circle" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Запчасть не найдена</p>
                                        <small class="text-muted">Попробуйте изменить запрос или посмотреть весь каталог</small>
                                    </div>
                                    <div class="scrollable" id="partsContainer">
                                        <?php
                                        $parts = getPartsData();
                                        foreach ($parts as $part) 
                                        {
                                            $categoryDisplay = getCategoryDisplayName($part['category']);
                                            echo '
                                            <div class="scrollable-item">
                                                <div class="card part-card h-100" data-part="' . $part['search_term'] . '" data-category="' . $part['category'] . '">
                                                    <div class="card-img-container">
                                                        <img src="' . $part['image'] . '" class="card-img-top" alt="' . $part['name'] . '">
                                                        <div class="card-badge">' . $categoryDisplay . '</div>
                                                        <div class="card-overlay">
                                                            <button class="btn btn-primary details-part-btn" data-part="' . $part['search_term'] . '" data-category="' . $part['category'] . '">
                                                                Подробнее <i class="bi bi-arrow-right ms-1"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <h6 class="card-title">' . $part['name'] . '</h6>
                                                        <div class="card-features">
                                                            <span class="feature-item">
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                                В наличии
                                                            </span>
                                                            <span class="feature-item">
                                                                <i class="bi bi-truck text-primary"></i>
                                                                Доставка 1-3 дня
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <button class="scroll-button scroll-left" aria-label="Прокрутить влево">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <button class="scroll-button scroll-right" aria-label="Прокрутить вправо">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
</div>

<footer class="footer-section bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <p class="mb-3">Лал-Авто - ваш надежный партнер в мире автозапчастей с 2010 года. Мы предлагаем оригинальные и качественные автокомплектующие от ведущих мировых производителей с гарантией и профессиональной поддержкой.</p>
                <div class="contact-info">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-telephone me-2"></i>
                        <a href="tel:+74012656565" class="text-white">+7 (4012) 65-65-65</a>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-envelope me-2"></i>
                        <a href="mailto:info@lal-auto.ru" class="text-white">info@lal-auto.ru</a>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt me-2"></i>
                        <span>г. Калининград, ул. Автомобильная, 12</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase mb-4 text-center">Быстрые ссылки</h5>
                <ul class="list-unstyled text-center">
                    <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">Главная</a></li>
                    <li class="mb-2"><a href="includes/shops.php" class="text-white text-decoration-none">Магазины</a></li>
                    <li class="mb-2"><a href="includes/service.php" class="text-white text-decoration-none">Автосервис</a></li>
                    <li class="mb-2"><a href="includes/assortment.php" class="text-white text-decoration-none">Ассортимент</a></li>
                    <li class="mb-2"><a href="includes/news.php" class="text-white text-decoration-none">Новости</a></li>
                    <li class="mb-2"><a href="includes/contacts.php" class="text-white text-decoration-none">Контакты</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase mb-4 text-center">Информация</h5>
                <ul class="list-unstyled text-center">
                    <li class="mb-2"><a href="includes/privacy.php" class="text-white text-decoration-none">Политика конфиденциальности</a></li>
                    <li class="mb-2"><a href="includes/terms.php" class="text-white text-decoration-none">Условия использования</a></li>
                    <li class="mb-2"><a href="includes/delivery.php" class="text-white text-decoration-none">Оплата и доставка</a></li>
                    <li class="mb-2"><a href="includes/return.php" class="text-white text-decoration-none">Возврат и обмен</a></li>
                    <li class="mb-2"><a href="includes/vacancies.php" class="text-white text-decoration-none">Вакансии</a></li>
                    <li class="mb-2"><a href="includes/suppliers.php" class="text-white text-decoration-none">Поставщикам</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-uppercase mb-4 text-center">Мы в соцсетях</h5>
                <div class="social-links mb-4 text-center">
                    <a href="https://vk.com/lalauto" class="text-white me-3" target="_blank">
                        <img  src="img/image 33.png" alt="VK" width="32" height="32">
                    </a>
                    <a href="https://t.me/s/lalauto" class="text-white" target="_blank">
                        <img src="img/image 32.png" alt="Telegram" width="32" height="32">
                    </a>
                </div>
                <h5 class="text-uppercase mb-3 text-center">Подписаться на новости</h5>
                <form class="subscribe-form px-3 px-md-0">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Ваш email" aria-label="Ваш email">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-envelope-arrow-up"></i>
                        </button>
                    </div>
                </form>
                <div class="payment-methods mt-3 text-center">
                    <h6 class="mb-2">Способы оплаты:</h6>
                    <div class="d-flex justify-content-center">
                        <img src="img/1.png" alt="Visa" class="me-2" width="40">
                        <img src="img/2.png" alt="Mastercard" class="me-2" width="40">
                        <img src="img/3.png" alt="МИР" width="40">
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-4 bg-light">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0">© 2025 Лал-Авто. Все права защищены.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="includes/sitemap.php" class="text-white text-decoration-none me-3">Карта сайта</a>
                <a href="includes/api.php" class="text-white text-decoration-none">Разработчикам API</a>
            </div>
        </div>
    </div>
</footer>

<div id="cookieConsent" class="cookie-consent" style="display: none;">
    <div class="cookie-container">
        <div class="cookie-content">
            <p class="cookie-text">
                Наш сайт использует cookies и сохраняет ваши персональные данные. 
                Нажимая <strong>"Согласен"</strong> вы принимаете условия обработки 
                персональных данных согласно нашей 
                <a href="includes/privacy.php" class="cookie-link">Политике Конфиденциальности</a>.
            </p>
            <div class="cookie-buttons">
                <button id="cookieAccept" class="btn btn-primary btn-sm cookie-btn">
                    Согласен
                </button>
                <button id="cookieReject" class="btn btn-outline-secondary btn-sm cookie-btn">
                    Не согласен
                </button>
            </div>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="files/app.js"></script>
</body>
</html>

<?php
function getCategoryDisplayName($category) 
{
    $categoryMap = [
        'двигатель' => 'Двигатель',
        'топливная система' => 'Топливная система', 
        'тормозная система' => 'Тормозная система',
        'подвеска' => 'Подвеска',
        'фильтры' => 'Фильтры',
        'ходовая часть' => 'Ходовая часть',
        'уплотнения' => 'Уплотнения'
    ];
    
    return isset($categoryMap[$category]) ? $categoryMap[$category] : $category;
}
?>