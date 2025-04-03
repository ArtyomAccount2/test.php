<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $id = $_POST['id'];
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $discountCardNumber = $_POST['discountCardNumber'] ?? null;
    $region = $_POST['region'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $inn = $_POST['TIN'];
    $person = $_POST['person'];
    $organization = $_POST['organization'];
    $organizationType = $_POST['organizationType'];

    $stmt = $conn->prepare("UPDATE users SET surname_users=?, name_users=?, patronymic_users=?, login_users=?, password_users=?, email_users=?, discountСardNumber_users=?, region_users=?, city_users=?, address_users=?, phone_users=?, TIN_users=?, person_users=?, organization_users=?, organizationType_users=? WHERE id=?");
    $stmt->bind_param("ssssssssssssssi", $surname, $name, $patronymic, $login, $password, $email, $discountCardNumber, $region, $city, $address, $phone, $inn, $person, $organization, $organizationType, $id);
    $stmt->execute();

    header("Location: ../admin.php");
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM `users` WHERE `id_users` = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body 
        {
            font-family: 'Montserrat', sans-serif;
        }
        .form-group 
        {
            margin-bottom: 0.5rem;
        }
        .btn 
        {
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1>Редактирование пользователя</h1>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= $user['id_users'] ?>">
        
        <div class="form-group">
            <label for="surname">Фамилия</label>
            <input type="text" name="surname" class="form-control" id="surname" value="<?= htmlspecialchars($user['surname_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="name">Имя</label>
            <input type="text" name="name" class="form-control" id="name" value="<?= htmlspecialchars($user['name_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="patronymic">Отчество</label>
            <input type="text" name="patronymic" class="form-control" id="patronymic" value="<?= htmlspecialchars($user['patronymic_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="login">Логин</label>
            <input type="text" name="login" class="form-control" id="login" value="<?= htmlspecialchars($user['login_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" name="password" class="form-control" id="password" value="<?= htmlspecialchars($user['password_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" id="email" value="<?= htmlspecialchars($user['email_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="text" name="phone" class="form-control" id="phone" value="<?= htmlspecialchars($user['phone_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="TIN">ИНН</label>
            <input type="text" name="TIN" class="form-control" id="TIN" value="<?= htmlspecialchars($user['TIN_users']) ?>">
        </div>
        <div class="form-group">
            <label for="discountCardNumber">Номер карты скидок</label>
            <input type="text" name="discountCardNumber" class="form-control" id="discountCardNumber" value="<?= htmlspecialchars($user['discountСardNumber_users']) ?>">
        </div>
        <div class="form-group">
            <label for="region">Регион</label>
            <input type="text" name="region" class="form-control" id="region" value="<?= htmlspecialchars($user['region_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="city">Город</label>
            <input type="text" name="city" class="form-control" id="city" value="<?= htmlspecialchars($user['city_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Адрес</label>
            <input type="text" name="address" class="form-control" id="address" value="<?= htmlspecialchars($user['address_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="person">Физическое лицо/Юридическое лицо</label>
            <input type="text" name="person" class="form-control" id="person" value="<?= htmlspecialchars($user['person_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="organization">Организация</label>
            <input type="text" name="organization" class="form-control" id="organization" value="<?= htmlspecialchars($user['organization_users']) ?>" required>
        </div>
        <div class="form-group">
            <label for="organizationType">Тип организации</label>
            <input type="text" name="organizationType" class="form-control" id="organizationType" value="<?= htmlspecialchars($user['organizationType_users']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="../admin.php" class="btn btn-secondary">Отмена</a>
    </form>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
</body>
</html>