<?php
error_reporting(E_ALL);
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
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
            <a href="index.php" class="btn btn-secondary ml-3 button-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-backspace-reverse" viewBox="0 0 16 16">
                    <path d="M9.854 5.146a.5.5 0 0 1 0 .708L7.707 8l2.147 2.146a.5.5 0 0 1-.708.708L7 8.707l-2.146 2.147a.5.5 0 0 1-.708-.708L6.293 8 4.146 5.854a.5.5 0 1 1 .708-.708L7 7.293l2.146-2.147a.5.5 0 0 1 .708 0"/>
                    <path d="M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7.08a2 2 0 0 0 1.519-.698l4.843-5.651a1 1 0 0 0 0-1.302L10.6 1.7A2 2 0 0 0 9.08 1zm7.08 1a1 1 0 0 1 .76.35L14.682 8l-4.844 5.65a1 1 0 0 1-.759.35H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z"/>
                </svg>
                Назад
            </a>
        </div>
    </nav>

    <script>
        function validateForm() 
        {
            let password = document.getElementById('password').value;
            let confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) 
            {
                alert("Пароли не совпадают. Пожалуйста, повторите ввод.");
                return false;
            }
            return true;
        }
    </script>