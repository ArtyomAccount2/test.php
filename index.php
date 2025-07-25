<?php
error_reporting(E_ALL);
session_start();
require_once("config/link.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $login = $_POST['login'];
    $password = $_POST['password'];

    if ($login === 'admin' && $password === 'admin') 
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
        $stmt = $conn->prepare("SELECT * FROM users WHERE login_users = ? AND password_users = ?");
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
            header("Location: index.php");
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
    <title>Лал-Авто - Автозапчасти</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
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
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Навигация
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#carouselExample">Слайдер</a></li>
                            <li><a class="dropdown-item" href="#aboutUs">О Нас</a></li>
                            <li><a class="dropdown-item" href="#specialOffer">Колесо Фортуны</a></li>
                            <li><a class="dropdown-item" href="#nextSection">Поиск по марке и по запчастям</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Меню
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenu">
                            <li><a class="dropdown-item" href="#">Магазины</a></li>
                            <li><a class="dropdown-item" href="#">Автосервис</a></li>
                            <li><a class="dropdown-item" href="#">Ассортимент</a></li>
                            <li><a class="dropdown-item" href="#">Масла и тех. жидкости</a></li>
                            <li><a class="dropdown-item" href="#">Аксессуары</a></li>
                            <li><a class="dropdown-item" href="#">Покупателям</a></li>
                            <li><a class="dropdown-item" href="#">Реквизиты</a></li>
                            <li><a class="dropdown-item" href="#">Поставщикам</a></li>
                            <li><a class="dropdown-item" href="#">Вакансии</a></li>
                            <li><a class="dropdown-item" href="#">Контакты</a></li>
                            <li><a class="dropdown-item" href="#">Отзывы</a></li>
                            <li><a class="dropdown-item" href="#">Оплата и доставка</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#">Торговые марки</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#">Поддержка сайта</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#">Новости компании</a>
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
                            <button class="profile-button w-md-auto" data-bs-toggle="modal" data-bs-target="#accountModal">
                                <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                            </button>
                        </div>
                    <?php 
                    } 
                    else 
                    {
                    ?>
                        <div class="d-flex flex-wrap flex-md-nowrap">
                            <a href="#" class="btn btn-primary button-link w-md-auto mx-1" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Войти
                            </a>
                            <a href="#" class="btn btn-primary button-link w-md-auto" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="bi bi-r-circle"></i>
                                Зарегистрироваться
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
                            <i class="bi bi-box-arrow-in-right"></i>
                            Войти
                        </button>
                        <a href="#" class="btn btn-link">Забыли пароль?</a>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="user-card text-center bg-light p-3 rounded mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="modal-title fw-bold text-primary">Личный кабинет</h4>
                                <p class="text-muted mb-0">Добро пожаловать, дорогой пользователь!</p>
                            </div>
                        </div>
                    </div>
                    <div class="account-menu">
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded">
                            <div class="icon-wrapper bg-primary-light mb-3 me-3">
                                <i class="bi bi-cart3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6>Мои заказы</h6>
                                <p class="text-muted">Просмотр истории заказов</p>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded">
                            <div class="icon-wrapper bg-primary-light mb-3 me-3">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6>Профиль</h6>
                                <p class="text-muted">Редактирование личных данных</p>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded">
                            <div class="icon-wrapper bg-primary-light mb-3 me-3">
                                <i class="bi bi-bell"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6>Уведомления</h6>
                                <p class="text-muted">Настройка оповещений</p>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded">
                            <div class="icon-wrapper bg-primary-light mb-3 me-3">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6>Безопасность</h6>
                                <p class="text-muted">Смена пароля и защита</p>
                            </div>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <form action="files/logout.php" method="POST" class="w-50">
                        <button type="submit" class="btn btn-outline-danger btn-block">
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Выйти из аккаунта
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary btn-block w-25" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/1.jpg" class="d-block w-100" alt="Слайд 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5 id="slider_body">Лучшие автозапчасти</h5>
                    <p id="slider_body">Найдите запчасти для вашего автомобиля.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/2.jpg" class="d-block w-100" alt="Слайд 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5 id="slider_body">Качество и надежность</h5>
                    <p id="slider_body">Мы предлагаем только проверенные запчасти.</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/3.jpeg" class="d-block w-100" alt="Слайд 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5 id="slider_body">Быстрая доставка</h5>
                    <p id="slider_body">Получите свои запчасти в кратчайшие сроки.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Предыдущий</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
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
                <a href="#" class="btn btn-primary btn-lg px-4 py-2 btn-hover-effect">
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
                        <i class="bi bi-person-add"></i>
                        Физические лица
                    </a>
                    <div id="individualsInfo" class="registration-info">
                        <p>- если Вы - физическое лицо, пройдите регистрацию. Регистрация возможна как при наличии карты скидок, так и при её отсутствии.</p>
                    </div>
                    <a href="legalEntity.php" type="button" class="btn btn-primary mb-2" id="legalEntitiesBtn">
                        <i class="bi bi-person-add"></i>
                        Юридические лица и ИП
                    </a>
                    <div id="legalEntitiesInfo" class="registration-info">
                        <p>- если Вы - представитель организации, учреждения, предприятия или фирмы, заполните данную форму регистрации.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        Закрыть
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-2">
                <div class="modal-header justify-content-center">
                    <h4 class="modal-title w-100 text-center" id="wheelResultModalLabel">Вы получаете!</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h4 id="modalResultText" class="mb-3"></h4>
                    <p id="modalResultDescription" class="mb-0"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        Закрыть
                    </button>
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
                <div class="section-header text-center mb-5">
                    <h2 class="mb-3">Поиск по марке автомобиля</h2>
                    <p class="lead text-muted">Найдите запчасти для вашего автомобиля</p>
                    <div class="search-container position-relative mx-auto" style="max-width: 500px;">
                        <input type="text" id="brandSearch" placeholder="Начните вводить марку..." class="form-control form-control-lg search-input">
                        <button class="btn btn-link search-clear" type="button" style="display: none;">
                            <i class="bi bi-x"></i>
                        </button>
                        <i class="bi bi-search search-icon"></i>
                    </div>
                </div>
                <div class="brands-container">
                    <div class="position-relative">
                        <div id="carBrandsList" class="scrollable-container">
                            <div id="no-results-brands" class="no-results-message">
                                <i class="bi bi-exclamation-circle" style="font-size: 1.5rem;"></i>
                                <p>Ничего не найдено. Попробуйте изменить запрос</p>
                            </div>
                            <div class="scrollable" id="carBrandsBlock">
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
            </section>

            <section class="container my-5" id="nextSection2">
                <div class="section-header text-center mb-5">
                    <h2 class="mb-3">Популярные запчасти</h2>
                    <p class="lead text-muted">Широкий ассортимент качественных автозапчастей</p>
                    <div class="search-container position-relative mx-auto" style="max-width: 500px;">
                        <input type="text" id="partsSearch" placeholder="Найдите нужную запчасть..." class="form-control form-control-lg search-input">
                        <button class="btn btn-link search-clear" type="button" style="display: none;">
                            <i class="bi bi-x"></i>
                        </button>
                        <i class="bi bi-search search-icon"></i>
                    </div>
                </div>
                <div class="parts-container">
                    <div class="position-relative">
                        <div id="popularParts" class="scrollable-container">
                            <div id="no-results-parts" class="no-results-message">
                                <i class="bi bi-exclamation-circle" style="font-size: 1.5rem;"></i>
                                <p>Ничего не найдено. Попробуйте изменить запрос</p>
                            </div>
                            <div class="scrollable" id="partsContainer">
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
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Главная</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Магазины</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Автосервис</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Ассортимент</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Новости</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Контакты</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase mb-4 text-center">Информация</h5>
                <ul class="list-unstyled text-center">
                    <li class="mb-2"><a href="privacy.php" class="text-white text-decoration-none">Политика конфиденциальности</a></li>
                    <li class="mb-2"><a href="terms.php" class="text-white text-decoration-none">Условия использования</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Оплата и доставка</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Возврат и обмен</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Вакансии</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Поставщикам</a></li>
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
                <a href="#" class="text-white text-decoration-none me-3">Карта сайта</a>
                <a href="#" class="text-white text-decoration-none">Разработчикам API</a>
            </div>
        </div>
    </div>
</footer>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="files/app.js"></script>
</body>
</html>