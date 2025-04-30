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
            $row = $result->fetch_assoc();
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = !empty($row['surname_users']) ? $row['surname_users'] . " " . $row['name_users'] . " " . $row['patronymic_users'] : $row['person_users'];

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
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Навигация
                    </a>
                    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#carouselExample">Слайдер</a>
                        <a class="dropdown-item" href="#aboutUs">О Нас</a>
                        <a class="dropdown-item" href="#specialOffer">Колесо Фортуны</a>
                        <a class="dropdown-item" href="#nextSection">Поиск по марке и по запчастям</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Меню
                    </a>
                    <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">Магазины</a>
                        <a class="dropdown-item" href="#">Автосервис</a>
                        <a class="dropdown-item" href="#">Ассортимент</a>
                        <a class="dropdown-item" href="#">Масла и тех. жидкости</a>
                        <a class="dropdown-item" href="#">Аксессуары</a>
                        <a class="dropdown-item" href="#">Покупателям</a>
                        <a class="dropdown-item" href="#">Реквизиты</a>
                        <a class="dropdown-item" href="#">Поставщикам</a>
                        <a class="dropdown-item" href="#">Вакансии</a>
                        <a class="dropdown-item" href="#">Контакты</a>
                        <a class="dropdown-item" href="#">Отзывы</a>
                        <a class="dropdown-item" href="#">Оплата и доставка</a>
                    </div>
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
            <form id="catalogSearchForm" class="form-inline my-2 my-lg-0 d-flex flex-nowrap align-items-center">
                <div class="input-group flex-nowrap">
                    <input class="form-control mr-sm-2 search-input" type="search" placeholder="Поиск по каталогу" aria-label="Search" id="catalogSearchInput">
                    <div class="input-group-append">
                        <button class="btn btn-outline-primary my-2 my-sm-0 button-link search-button" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                            <span class="search-text">Найти</span>
                        </button>
                    </div>
                </div>
            </form>
            <div class="ml-xl-3 ml-lg-2 ml-md-1">
                <?php 
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
                {
                ?>
                    <div class="d-flex flex-column flex-md-row align-items-center">
                        <p class="mb-0 text-center text-md-right mr-md-2" style="font-size: 0.9em; white-space: nowrap;">
                            <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>
                        </p>
                        <button class="profile-button w-md-auto" data-toggle="modal" data-target="#accountModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="48" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                            </svg>
                        </button>
                    </div>
                <?php 
                } 
                else 
                {
                ?>
                    <div class="d-flex flex-wrap flex-md-nowrap">
                        <a href="#" class="btn btn-primary button-link w-md-auto mx-1" data-toggle="modal" data-target="#loginModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0z"/>
                                <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                            </svg>
                            Войти
                        </a>
                        <a href="#" class="btn btn-primary button-link w-md-auto" data-toggle="modal" data-target="#registerModal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-r-circle" viewBox="0 0 16 16">
                                <path d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.5 4.002h3.11c1.71 0 2.741.973 2.741 2.46 0 1.138-.667 1.94-1.495 2.24L11.5 12H9.98L8.52 8.924H6.836V12H5.5zm1.335 1.09v2.777h1.549c.995 0 1.573-.463 1.573-1.36 0-.913-.596-1.417-1.537-1.417z"/>
                            </svg>
                            Зарегистрироваться
                        </a>
                    </div>
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

    <div class="modal fade" id="accountModal" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="user-card text-center bg-light p-3 rounded mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h4 class="modal-title font-weight-bold text-primary">Личный кабинет</h4>
                                <p class="text-muted mb-0">Добро пожаловать, дорогой пользователь!</p>
                            </div>
                        </div>
                    </div>
                    <div class="account-menu">
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded">
                            <div class="icon-wrapper bg-primary-light mb-3 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#007bff" class="bi bi-cart3" viewBox="0 0 16 16">
                                    <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l.84 4.479 9.144-.459L13.89 4zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                                </svg>
                            </div>
                            <div class="flex-grow-1">
                                <h6>Мои заказы</h6>
                                <p class="text-muted">Просмотр истории заказов</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#6c757d" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </a>
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded">
                            <div class="icon-wrapper bg-primary-light mb-3 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#007bff" class="bi bi-person" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                </svg>
                            </div>
                            <div class="flex-grow-1">
                                <h6>Профиль</h6>
                                <p class="text-muted">Редактирование личных данных</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#6c757d" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </a>
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded">
                            <div class="icon-wrapper bg-primary-light mb-3 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#007bff" class="bi bi-bell" viewBox="0 0 16 16">
                                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                                </svg>
                            </div>
                            <div class="flex-grow-1">
                                <h6>Уведомления</h6>
                                <p class="text-muted">Настройка оповещений</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#6c757d" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </a>
                        <a href="#" class="menu-item d-flex align-items-center p-3 rounded">
                            <div class="icon-wrapper bg-primary-light mb-3 mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#007bff" class="bi bi-shield-lock" viewBox="0 0 16 16">
                                    <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56"/>
                                    <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415"/>
                                </svg>
                            </div>
                            <div class="flex-grow-1">
                                <h6>Безопасность</h6>
                                <p class="text-muted">Смена пароля и защита</p>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#6c757d" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <form action="files/logout.php" method="POST" class="w-50">
                        <button type="submit" class="btn btn-outline-danger btn-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right mr-2" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                            </svg>
                            Выйти из аккаунта
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-secondary btn-block w-25" data-dismiss="modal">
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
        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Предыдущий</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Следующий</span>
        </a>
    </div>

    <div id="aboutUs" class="py-5 about-us">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 font-weight-bold mb-3">О Нас</h2>
                <div class="divider mx-auto bg-primary" style="width: 80px; height: 4px;"></div>
            </div> 
            <p class="lead text-center">Лал-Авто — ведущий поставщик автозапчастей и автоуслуг. Предлагаем оригинальные и качественные запчасти для всех марок, сотрудничая с проверенными производителями. Наши специалисты выполняют диагностику, ремонт и обслуживание с современным оборудованием. Ценим клиентов, предлагаем индивидуальный подход и доступные цены.</p>
            <br>
            <div class="text-center mb-5">
                <h2 class="display-4 font-weight-bold mb-3">Почему выбирают нас?</h2>
                <div class="divider mx-auto bg-primary" style="width: 80px; height: 4px;"></div>
            </div> 
            <ul class="list-unstyled">
                <li class="list-connect">✓ Широкий ассортимент запчастей для различных марок автомобилей.</li>
                <li class="list-connect">✓ Конкурентоспособные цены.</li>
                <li class="list-connect">✓ Быстрая доставка и удобные способы оплаты.</li>
                <li class="list-connect">✓ Профессиональная консультация и поддержка клиентов.</li>
                <li class="list-connect">✓ Гарантия качества на все наши товары.</li>
            </ul>
            <br>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="about-card p-4 h-100 text-center">
                        <div class="icon-box mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#007bff" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </div>
                        <h3 class="h4 mb-3">Качество</h3>
                        <p class="mb-0">Мы работаем только с проверенными производителями</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="about-card p-4 h-100 text-center">
                        <div class="icon-box mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#007bff" viewBox="0 0 16 16">
                                <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                            </svg>
                        </div>
                        <h3 class="h4 mb-3">Доставка</h3>
                        <p class="mb-0">Быстрая и надежная доставка по всей территории страны гарантирована</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="about-card p-4 h-100 text-center">
                        <div class="icon-box mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="#007bff" viewBox="0 0 16 16">
                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm8.93 4.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM8 5.5a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
                            </svg>
                        </div>
                        <h3 class="h4 mb-3">Поддержка</h3>
                        <p class="mb-0">Наша команда готова помочь вам в любое время, гарантируя качественную поддержку</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="#" class="btn btn-primary btn-lg px-4">Подробнее о компании</a>
            </div>
        </div>
    </div>

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

    <div class="modal fade" id="wheelResultModal" tabindex="-1" role="dialog" aria-labelledby="wheelResultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content p-2">
                <div class="modal-header justify-content-center">
                    <h4 class="modal-title w-100 text-center" id="wheelResultModalLabel">Вы получаете!</h4>
                </div>
                <div class="modal-body text-center">
                    <h4 id="modalResultText" class="mb-3"></h4>
                    <p id="modalResultDescription" class="mb-0"></p>
                </div>
                <div class="modal-footer justify-content-center">
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
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Aixam.png" class="card-img-top" alt="Марка 2">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Aixam</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Alfa Romeo.png" class="card-img-top" alt="Марка 3">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Alfa Romeo</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Aston Martin.png" class="card-img-top" alt="Марка 4">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Aston Martin</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Audi.png" class="card-img-top" alt="Марка 5">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Audi</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Bentley.png" class="card-img-top" alt="Марка 6">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Bentley</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/BMW.png" class="card-img-top" alt="Марка 7">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">BMW</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Buick.png" class="card-img-top" alt="Марка 8">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Buick</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Cadillac.png" class="card-img-top" alt="Марка 9">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Cadillac</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Chevrolet.png" class="card-img-top" alt="Марка 10">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Chevrolet</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Chrysler.png" class="card-img-top" alt="Марка 11">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Chrysler</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Dodge.png" class="card-img-top" alt="Марка 12">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Dodge</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Fiat.png" class="card-img-top" alt="Марка 13">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Fiat</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Ford.png" class="card-img-top" alt="Марка 14">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Ford</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Gaz.png" class="card-img-top" alt="Марка 15">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Gaz</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Honda.png" class="card-img-top" alt="Марка 16">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Honda</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Hummer.png" class="card-img-top" alt="Марка 17">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Hummer</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Hyundai.png" class="card-img-top" alt="Марка 18">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Hyundai</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Infiniti.png" class="card-img-top" alt="Марка 19">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Infiniti</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Jaguar.png" class="card-img-top" alt="Марка 20">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Jaguar</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Jeep.png" class="card-img-top" alt="Марка 21">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Jeep</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Kia.png" class="card-img-top" alt="Марка 22">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Kia</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lada.png" class="card-img-top" alt="Марка 23">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lada</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lamborghini.png" class="card-img-top" alt="Марка 24">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lamborghini</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lancia.png" class="card-img-top" alt="Марка 25">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lancia</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Land Rover.png" class="card-img-top" alt="Марка 26">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Land Rover</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lexus.png" class="card-img-top" alt="Марка 27">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lexus</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/Stamps/Lotus.png" class="card-img-top" alt="Марка 28">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Lotus</h6>
                                <a href="#" class="btn btn-outline-primary">Выбрать</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scrollbar scrollable" id="carBrandsScrollbar">
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
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image2.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Прокладки двигателя</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image3.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Топливный насос</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image4.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title big-text-card">Распределительный вал</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image5.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Тормозной цилиндр</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image6.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Тормозные колодки</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image7.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Стабилизатор</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image8.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Тормозные суппорта</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image9.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Топливный фильтр</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image10.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Тормозные диски</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image11.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Цапфа</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4 scrollable-item">
                        <div class="card shadow-sm h-100">
                            <img src="img/SpareParts/image12.png" class="card-img-top">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <h6 class="card-title">Сальники</h6>
                                <a href="#" class="btn btn-outline-primary">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="scrollbar scrollable" id="popularPartsScrollbar">
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
    <script src="files/app.js"></script>
</body>
</html>