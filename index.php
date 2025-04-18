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
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = $login;

            header("Location: index.php");
            exit();
        } 
        else 
        {
            $error_message = "Неверный логин или пароль!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лал-Авто - Автозапчасти</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-xl navbar-light bg-light shadow-sm fixed-top">
        <a class="navbar-brand" href="#"><img src="img/Auto.png" alt="Лал-Авто" height="75"></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="#">Торговые марки</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="#">Поддержка сайта</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="#">Новости компании</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="#">Оплата и доставка</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Меню
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">Автосервис</a>
                        <a class="dropdown-item" href="#">Ассортимент</a>
                        <a class="dropdown-item" href="#">Масла и тех. жидкости</a>
                        <a class="dropdown-item" href="#">Аксессуары</a>
                        <a class="dropdown-item" href="#">Покупателям</a>
                        <a class="dropdown-item" href="#">Поставщикам</a>
                        <a class="dropdown-item" href="#">Вакансии</a>
                        <a class="dropdown-item" href="#">Контакты</a>
                        <a class="dropdown-item" href="#">Отзывы</a>
                    </div>
                </li>
            </ul>
            <form id="catalogSearchForm" class="form-inline my-2 my-lg-0">
                <input class="form-control mr-2 button-link" type="search" placeholder="Поиск по каталогу" aria-label="Search" id="catalogSearchInput">
                <button class="btn btn-outline-primary my-2 my-sm-0 button-link" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                    </svg>
                    Найти
                </button>
            </form>
            <div class="ml-3">
                <?php 
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
                {
                ?>
                    <form action="files/logout.php" method="POST" class="d-inline">
                        <button type="submit" class="btn btn-secondary button-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                            </svg> 
                            Выйти
                        </button>
                    </form>
                    <button class="btn btn-info button-link" data-toggle="modal" data-target="#accountModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                        </svg>
                        Личный Кабинет
                    </button>
                <?php 
                } 
                else 
                {
                ?>
                    <a href="#" class="btn btn-primary button-link" data-toggle="modal" data-target="#loginModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                            <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                        </svg>
                        Войти
                    </a>
                    <a href="#" class="btn btn-primary button-link" data-toggle="modal" data-target="#registerModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-r-circle" viewBox="0 0 16 16">
                            <path d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.5 4.002h3.11c1.71 0 2.741.973 2.741 2.46 0 1.138-.667 1.94-1.495 2.24L11.5 12H9.98L8.52 8.924H6.836V12H5.5zm1.335 1.09v2.777h1.549c.995 0 1.573-.463 1.573-1.36 0-.913-.596-1.417-1.537-1.417z"/>
                        </svg>
                        Зарегистрироваться
                    </a>
                <?php
                }
                ?>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="loginModalLabel">Авторизация</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/">
                        <div class="form-group">
                            <label for="username">Логин</label>
                            <input type="text" name="login" class="form-control" id="username" placeholder="Введите логин" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Пароль</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Введите пароль" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" name="rememberMe" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                        </div>
                        <?php 
                        if (!empty($error_message))
                        {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message; ?>
                            </div>
                        <?php  
                        }
                        ?>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                                <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                            </svg>
                            Войти
                        </button>
                        <a href="#" class="btn btn-link">Забыли пароль?</a>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="accountModal" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel" aria-hidden="true" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="accountModalLabel" style="font-weight: bold; color: #007bff;">Личный Кабинет</h5>
                </div>
                <div class="modal-body">
                    <p style="font-size: 1.1em;">Добро пожаловать, <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>!</p>
                    <p>Здесь вы можете управлять своими данными, просмотреть заказы и т.д.</p>
                    <ul class="list-unstyled">
                        <li><a href="#" class="link-opacity-100" style="color: #007bff;">Мои Заказы</a></li>
                        <li><a href="#" class="link-opacity-100" style="color: #007bff;">Редактировать Профиль</a></li>
                        <li><a href="#" class="link-opacity-100" style="color: #007bff;">Настройки Уведомлений</a></li>
                        <li><a href="#" class="link-opacity-100" style="color: #007bff;">Изменить Пароль</a></li>
                        <li><a href="#" class="link-opacity-100" style="color: #007bff;">История Платежей</a></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="carouselExample" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/1.jpg" class="d-block w-100" alt="Слайд 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5 id="slider_body">Лучшие автозапчасти</h5>
                    <p id="slider_body">Найдите запчасти для вашего автомобиля.</p>
                    <p><a href="#aboutUs" class="text-light link-opacity-100 link_body">Перейти на следующий экран</a></p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/2.jpg" class="d-block w-100" alt="Слайд 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5 id="slider_body">Качество и надежность</h5>
                    <p id="slider_body">Мы предлагаем только проверенные запчасти.</p>
                    <p><a href="#aboutUs" class="text-light link-opacity-100 link_body">Перейти на следующий экран</a></p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/3.jpeg" class="d-block w-100" alt="Слайд 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5 id="slider_body">Быстрая доставка</h5>
                    <p id="slider_body">Получите свои запчасти в кратчайшие сроки.</p>
                    <p><a href="#aboutUs" class="text-light link-opacity-100 link_body">Перейти на следующий экран</a></p>
                </div>
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Предыдущий</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Следующий</span>
        </a>
    </div>

    <section class="about-us" id="aboutUs">
        <h2 class="text-center">О НАС</h2>
        <p class="lead">Лал-Авто - это ведущий поставщик автозапчастей и услуг в области автомобильного сервиса. Мы стремимся предоставить нашим клиентам только качественные товары и услуги, соответствующие самым высоким стандартам.</p>
        <p class="lead">Наша компания предлагает широкий ассортимент автозапчастей для различных марок и моделей автомобилей, включая как оригинальные, так и высококачественные аналоги. Мы сотрудничаем с проверенными производителями, что гарантирует надежность и долговечность нашей продукции.</p>
        <p class="lead">Кроме того, Лал-Авто предоставляет полный спектр услуг в области автомобильного сервиса. Наша команда квалифицированных специалистов готова предложить диагностику, ремонт и техническое обслуживание автомобилей любой сложности. Мы используем только современное оборудование и передовые технологии, что позволяет нам обеспечивать высокий уровень сервиса.</p>
        <p class="lead">Мы ценим каждого клиента и стремимся к долгосрочным отношениям, поэтому всегда готовы предложить индивидуальный подход, гибкие условия сотрудничества и конкурентоспособные цены. Наша цель – сделать ваш автомобиль безопасным и надежным, а ваше время – ценным.</p>
        <p class="lead">Выбирая Лал-Авто, вы выбираете качество, надежность и профессионализм. Мы уверены, что с нами ваш автомобиль будет в надежных руках!</p>
        <h2 class="text-center m-5 lead-text">Почему выбирают нас?</h2>
        <ul class="list-unstyled">
            <li>✔️ Широкий ассортимент запчастей для различных марок автомобилей.</li>
            <li>✔️ Конкурентоспособные цены.</li>
            <li>✔️ Быстрая доставка и удобные способы оплаты.</li>
            <li>✔️ Профессиональная консультация и поддержка клиентов.</li>
            <li>✔️ Гарантия качества на все наши товары.</li>
        </ul>
        <div class="row text-center">
            <div class="col-md-4">
                <div class="about-card">
                    <img src="img/slider-1.png" alt="Качество" class="img-fluid rounded mb-3">
                    <h5>Качество</h5>
                    <p>Мы работаем только с проверенными производителями.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="about-card">
                    <img src="img/slider-2.png" alt="Доставка" class="img-fluid rounded mb-3">
                    <h5>Доставка</h5>
                    <p>Быстрая и надежная доставка по всей территории.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="about-card">
                    <img src="img/slider-3.png" alt="Поддержка" class="img-fluid rounded mb-3">
                    <h5>Поддержка</h5>
                    <p>Наша команда готова помочь вам в любое время.</p>
                </div>
            </div>
        </div>
        <div class="text-center">
            <a href="#specialOffer" class="btn btn-primary mt-4">Перейти на следующий экран</a>
        </div>
    </section>

    <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="registerModalLabel">Регистрация</h5>
                </div>
                <div class="modal-body text-center">
                    <a href="individuel.php" type="button" class="btn btn-primary mb-2" id="individualsBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-add" viewBox="0 0 16 16">
                            <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                            <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
                        </svg>
                        Физические лица
                    </a>
                    <div id="individualsInfo" class="registration-info">
                        <p>- если Вы - физическое лицо, пройдите регистрацию. Регистрация возможна как при наличии карты скидок, так и при её отсутствии.</p>
                    </div>
                    <a href="legalEntity.php" type="button" class="btn btn-primary mb-2" id="legalEntitiesBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-add" viewBox="0 0 16 16">
                            <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                            <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
                        </svg>
                        Юридические лица и ИП
                    </a>
                    <div id="legalEntitiesInfo" class="registration-info">
                        <p>- если Вы - представитель организации, учреждения, предприятия или фирмы, заполните данную форму регистрации.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                        </svg>
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="container my-5 text-center d-flex align-items-center flex-column" id="specialOffer">
        <h2 class="text-center mb-4">Специальное предложение!</h2>
        <p class="lead mb-4">Не упустите шанс! Скидка 20% на определенные запчасти.</p>

        <div class="row d-flex justify-content-center">
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card shadow-sm h-100">
                    <img src="img/SpareParts/image1.png" alt="Коленчатый вал">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Коленчатый вал</h5>
                        <p class="card-text">Теперь всего за <span class="text-danger font-weight-bold">20% скидки</span>!</p>
                        <a href="#" class="btn btn-primary mt-auto">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card shadow-sm h-100">
                    <img class="mt-3 mb-2" src="img/SpareParts/image2.png" alt="Прокладки двигателя">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Прокладки двигателя</h5>
                        <p class="card-text">Теперь всего за <span class="text-danger font-weight-bold">20% скидки</span>!</p>
                        <a href="#" class="btn btn-primary mt-auto">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card shadow-sm h-100">
                    <img class="mt-3 mb-2" src="img/SpareParts/image3.png" alt="Топливный насос">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Топливный насос</h5>
                        <p class="card-text">Теперь всего за <span class="text-danger font-weight-bold">20% скидки</span>!</p>
                        <a href="#" class="btn btn-primary mt-auto">Подробнее</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card shadow-sm h-100">
                    <img class="mt-2 mb-2" src="img/SpareParts/image4.png" alt="Распределительный вал">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title big-text">Распределительный вал</h5>
                        <p class="card-text">Теперь всего за <span class="text-danger font-weight-bold">20% скидки</span>!</p>
                        <a href="#" class="btn btn-primary mt-auto">Подробнее</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="countdown mb-4">
            <div class="row text-center">
                <div class="col">
                    <span id="days">00</span>
                    <div>Дней</div>
                </div>
                <div class="col">
                    <span id="hours">00</span>
                    <div>Часов</div>
                </div>
                <div class="col">
                    <span id="minutes">00</span>
                    <div>Минут</div>
                </div>
                <div class="col">
                    <span id="seconds">00</span>
                    <div>Секунд</div>
                </div>
            </div>
        </div>

        <p class="lead">Спешите, предложение ограничено по времени!</p>

        <div class="text-center">
            <a href="#nextSection" class="btn btn-primary mt-4">Перейти на следующий экран</a>
        </div>
    </section>

    <section class="container my-5 text-center" id="nextSection">
        <h2 class="text-center">Поиск по марке</h2>
        <input type="text" id="brandSearch" placeholder="Поиск марки" class="form-control mb-4 w-100">
        <div class="row mx-auto align-items-center">
            <div id="carBrandsList" class="col overflow-hidden" style="max-height: 300px; overflow-y: auto;">
                <div id="no-results-brands" style="display: none;">Ничего не найдено!</div>
                <div class="row flex-nowrap scrollable" id="carBrandsBlock">
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Acura.png" class="card-img-top" alt="Марка 1">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Acura</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Aixam.png" class="card-img-top" alt="Марка 2">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Aixam</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Alfa Romeo.png" class="card-img-top" alt="Марка 3">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Alfa Romeo</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Aston Martin.png" class="card-img-top" alt="Марка 4">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Aston Martin</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Audi.png" class="card-img-top" alt="Марка 5">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Audi</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Bentley.png" class="card-img-top" alt="Марка 6">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Bentley</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/BMW.png" class="card-img-top" alt="Марка 7">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">BMW</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Buick.png" class="card-img-top" alt="Марка 8">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Buick</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Cadillac.png" class="card-img-top" alt="Марка 9">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Cadillac</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Chevrolet.png" class="card-img-top" alt="Марка 10">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Chevrolet</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Chrysler.png" class="card-img-top" alt="Марка 11">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Chrysler</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Dodge.png" class="card-img-top" alt="Марка 12">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Dodge</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Fiat.png" class="card-img-top" alt="Марка 13">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Fiat</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Ford.png" class="card-img-top" alt="Марка 14">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Ford</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Gaz.png" class="card-img-top" alt="Марка 15">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Gaz</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Honda.png" class="card-img-top" alt="Марка 16">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Honda</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Hummer.png" class="card-img-top" alt="Марка 17">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Hummer</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Hyundai.png" class="card-img-top" alt="Марка 18">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Hyundai</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Infiniti.png" class="card-img-top" alt="Марка 19">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Infiniti</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Jaguar.png" class="card-img-top" alt="Марка 20">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Jaguar</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Jeep.png" class="card-img-top" alt="Марка 21">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Jeep</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Kia.png" class="card-img-top" alt="Марка 22">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Kia</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lada.png" class="card-img-top" alt="Марка 23">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lada</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lamborghini.png" class="card-img-top" alt="Марка 24">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lamborghini</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lancia.png" class="card-img-top" alt="Марка 25">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lancia</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Land Rover.png" class="card-img-top" alt="Марка 26">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Land Rover</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lexus.png" class="card-img-top" alt="Марка 27">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lexus</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lotus.png" class="card-img-top" alt="Марка 28">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lotus</h6>
                                <a href="#" class="btn btn-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scrollbar" id="carBrandsScrollbar">
            <div class="scrollbar-thumb"></div>
        </div>
    </section>

    <section class="container my-5 text-center">
        <h2 class="text-center">Поиск по запчастям</h2>
        <input type="text" id="partsSearch" placeholder="Поиск запчасти" class="form-control mb-4 w-100">
        <div class="row mx-auto align-items-center">
            <div id="popularParts" class="col overflow-hidden" style="max-height: 300px; overflow-y: auto;">
                <div id="no-results-parts" style="display: none;">Ничего не найдено!</div>
                <div class="row flex-nowrap scrollable" id="partsContainer">
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image1.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Коленчатый вал</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image2.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Прокладки двигателя</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image3.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Топливный насос</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image4.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title big-text-card">Распределительный вал</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image5.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Тормозной цилиндр</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image6.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Тормозные колодки</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image7.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Стабилизатор</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image8.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Тормозные суппорта</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image9.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Топливный фильтр</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image10.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Тормозные диски</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image11.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Цапфа</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image12.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Сальники</h6>
                                <a href="#" class="btn btn-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scrollbar" id="popularPartsScrollbar">
            <div class="scrollbar-thumb"></div>
        </div>
    </section>
</div>
    <footer class="text-center py-4">
        <div class="container">
            <p>© 2025 Лал-Авто. Все права защищены.</p>
            <p>Контактный телефон: <a href="#">+7 (4012) 65-65-65</a></p>
            <p>
                <a href="#">Политика конфиденциальности</a> | 
                <a href="#">Условия использования</a>
            </p>
            <div class="d-flex justify-content-center mt-3">
                <a href="https://vk.com/lalauto?ysclid=m91623ocq3201359667"><img class="mx-1 small-img navbar-brand" src="img/image 33.png" alt=""></a>
                <a href="https://t.me/s/lalauto"><img class="mx-1 small-img navbar-brand" src="img/image 32.png" alt=""></a>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>