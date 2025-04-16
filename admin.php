<?php
session_start();
require_once("config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM `users` WHERE `login_users` != 'admin'");
$stmt->execute();
$result = $stmt->get_result();
$users = [];

while ($row = $result->fetch_assoc()) 
{
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Административная панель</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        table 
        {
            font-size: 0.8rem;
        }
        th, td 
        {
            padding: 0.3rem;
        }
        .table-responsive 
        {
            overflow-x: auto;
        }
    </style>
</head>
<body>

<div class="flex-grow-1">
    
<div class="container-fluid mt-5">
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
            <a href="../files/logout.php" class="btn btn-secondary ml-3 button-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                </svg>  
                Выйти
            </a>
        </div>
    </nav>

    <h1 class="mb-4 text-center" style="padding-top: 90px;" >Административная панель</h1>
    
    <div class="table-responsive text-center">
        <table class="table table-bordered w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Фамилия</th>
                    <th>Имя</th>
                    <th>Отчество</th>
                    <th>Логин</th>
                    <th>Пароль</th>
                    <th>Email</th>
                    <th>Номер карты скидок</th>
                    <th>Регион</th>
                    <th>Город</th>
                    <th>Адрес</th>
                    <th>Мобильный телефон</th>
                    <th>ИНН</th>
                    <th>Имя контактного лица</th>
                    <th>Название организации</th>
                    <th>Тип организации</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($users as $user) 
                {
                ?>
                    <tr>
                    <td><?= isset($user['id_users']) ? htmlspecialchars($user['id_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['surname_users']) ? htmlspecialchars($user['surname_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['name_users']) ? htmlspecialchars($user['name_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['patronymic_users']) ? htmlspecialchars($user['patronymic_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['login_users']) ? htmlspecialchars($user['login_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['password_users']) ? htmlspecialchars($user['password_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['email_users']) ? htmlspecialchars($user['email_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['discountСardNumber_users']) ? htmlspecialchars($user['discountСardNumber_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['region_users']) ? htmlspecialchars($user['region_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['city_users']) ? htmlspecialchars($user['city_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['address_users']) ? htmlspecialchars($user['address_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['phone_users']) ? htmlspecialchars($user['phone_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['TIN_users']) ? htmlspecialchars($user['TIN_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['person_users']) ? htmlspecialchars($user['person_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['organization_users']) ? htmlspecialchars($user['organization_users']) : 'Не указано' ?></td>
                        <td><?= isset($user['organizationType_users']) ? htmlspecialchars($user['organizationType_users']) : 'Не указано' ?></td>
                        <td>
                            <a class="btn btn-primary btn-sm my-1" href="files/edit_user.php?id=<?= $user['id_users'] ?>">Редактировать</a>
                            <a class="btn btn-primary btn-sm my-1" href="files/delete_user.php?id=<?= $user['id_users'] ?>">Удалить</a>
                        </td>
                    </tr>
                <?php 
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>