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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
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
            <form class="form-inline my-2" id="catalogSearchForm">
                <input class="form-control mr-2" type="search" placeholder="Поиск по каталогу" aria-label="Search" id="catalogSearchInput">
                <button class="btn btn-outline-primary my-2" type="submit">Найти</button>
            </form>
            <div class="ml-auto">
                <?php 
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
                {
                ?>
                    <form action="files/logout.php" method="POST" class="d-inline">
                        <button type="submit" class="btn btn-secondary">Выйти</button>
                    </form>
                <?php 
                } 
                else 
                {
                ?>
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#loginModal">Войти</a>
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#registerModal">Зарегистрироваться</a>
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
                        {?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_message; ?>
                            </div>
                        <?php  
                        }
                        ?>
                        <button type="submit" class="btn btn-primary">Войти</button>
                        <a href="#" class="btn btn-link">Забыли пароль?</a>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
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
                    <a href="#aboutUs" class="btn btn-primary">Перейти на следующий экран</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/2.jpg" class="d-block w-100" alt="Слайд 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5 id="slider_body">Качество и надежность</h5>
                    <p id="slider_body">Мы предлагаем только проверенные запчасти.</p>
                    <a href="#aboutUs" class="btn btn-primary">Перейти на следующий экран</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="img/3.jpeg" class="d-block w-100" alt="Слайд 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5 id="slider_body">Быстрая доставка</h5>
                    <p id="slider_body">Получите свои запчасти в кратчайшие сроки.</p>
                    <a href="#aboutUs" class="btn btn-primary">Перейти на следующий экран</a>
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
        <h3 class="text-center m-5 lead-text">Почему выбирают нас?</h3>
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
            <a href="#nextSection" class="btn btn-primary mt-4">Перейти на следующий экран</a>
        </div>
    </section>

    <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="registerModalLabel">Регистрация</h5>
                </div>
                <div class="modal-body text-center">
                    <a href="individuel.php" type="button" class="btn btn-primary mb-2" id="individualsBtn">Физические лица</a>
                    <div id="individualsInfo" class="registration-info">
                        <p>- если Вы - физическое лицо, пройдите регистрацию. Регистрация возможна как при наличии карты скидок, так и при её отсутствии.</p>
                    </div>
                    <a href="legalEntity.php" type="button" class="btn btn-primary mb-2" id="legalEntitiesBtn">Юридические лица и ИП</a>
                    <div id="legalEntitiesInfo" class="registration-info">
                        <p>- если Вы - представитель организации, учреждения, предприятия или фирмы, заполните данную форму регистрации.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</a>
                </div>
            </div>
        </div>
    </div>

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
                                <h6 class="card-title">Распределительный вал</h6>
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
            <p>Контактный телефон: +7 (4012) 65-65-65</p>
            <p><a href="#">Политика конфиденциальности</a> | <a href="#">Условия использования</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>