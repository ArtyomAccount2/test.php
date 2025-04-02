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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
</head>
<body>

<div class="container mt-5 text-center">
    <h1>Административная панель</h1>
    <a href="index.php" class="btn btn-secondary mb-3">Назад</a>
    
    <h2>Список пользователей</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Логин</th>
                <th>Email</th>
                <th>Телефон</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            foreach ($users as $user) 
            {
            ?>
                <tr>
                    <td><?= isset($user['surname_users']) ? htmlspecialchars($user['surname_users']) : 'Не указано' ?></td>
                    <td><?= isset($user['name_users']) ? htmlspecialchars($user['name_users']) : 'Не указано' ?></td>
                    <td><?= isset($user['patronymic_users']) ? htmlspecialchars($user['patronymic_users']) : 'Не указано' ?></td>
                    <td><?= isset($user['login_users']) ? htmlspecialchars($user['login_users']) : 'Не указано' ?></td>
                    <td><?= isset($user['email_users']) ? htmlspecialchars($user['email_users']) : 'Не указано' ?></td>
                    <td><?= isset($user['phone_users']) ? htmlspecialchars($user['phone_users']) : 'Не указано' ?></td>
                    <td>
                        <a class="btn btn-warning mb-3" href="files/edit_user.php?id=<?= $user['id_users'] ?>">Редактировать</a>
                        <a class="btn btn-danger mb-3" href="files/delete_user.php?id=<?= $user['id_users'] ?>">Удалить</a>
                    </td>
                </tr>
            <?php 
            }
            ?>
        </tbody>
    </table>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>