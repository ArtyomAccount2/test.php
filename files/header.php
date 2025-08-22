<?php
error_reporting(E_ALL);
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
</head>
<body>

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top header-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="img/Auto.png" alt="Лал-Авто" height="75"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link text-dark dropdown-toggle" href="#" id="navbarDropdownMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Меню
                        </a>
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
                <a href="index.php" class="btn btn-secondary ms-3 button-link header-back-button">
                    <i class="bi bi-backspace-reverse"></i>
                    Назад
                </a>
            </div>
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