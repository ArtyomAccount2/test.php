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

<div class="container mt-5 text-center">
    <h1>Административная панель</h1>
    <a href="../files/logout.php" class="btn btn-secondary mb-3">Назад</a>
    
    <h2>Список пользователей</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
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
                            <a class="btn btn-warning btn-sm my-1" href="files/edit_user.php?id=<?= $user['id_users'] ?>">Редактировать</a>
                            <a class="btn btn-danger btn-sm my-1" href="files/delete_user.php?id=<?= $user['id_users'] ?>">Удалить</a>
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