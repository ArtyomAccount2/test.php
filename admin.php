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
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin-styles.css">
</head>
<body>

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="img/Auto.png" alt="Лал-Авто" height="75"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Меню
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
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
                <a href="../files/logout.php" class="btn btn-secondary ms-3 button-link">
                    <i class="bi bi-box-arrow-right"></i>  
                    Выйти
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid admin-container">
        <h1 class="mb-4 text-center" style="padding-top: 50px">Административная панель</h1>
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
                                <div class="d-flex flex-column gap-1">
                                    <a class="btn btn-primary btn-sm" href="files/edit_user.php?id=<?= $user['id_users'] ?>">
                                        <i class="bi bi-pen"></i>
                                        Редактировать
                                    </a>
                                    <a class="btn btn-danger btn-sm" href="files/delete_user.php?id=<?= $user['id_users'] ?>">
                                        <i class="bi bi-trash3"></i>
                                        Удалить
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>