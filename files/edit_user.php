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
    $surname = !empty($_POST['surname']) ? $_POST['surname'] : null;
    $name = !empty($_POST['name']) ? $_POST['name'] : null;
    $patronymic = !empty($_POST['patronymic']) ? $_POST['patronymic'] : null;
    $login = $_POST['login'];
    $password = $_POST['password'];
    $email = !empty($_POST['email']) ? $_POST['email'] : null;
    $discountCardNumber = !empty($_POST['discountCardNumber']) ? $_POST['discountCardNumber'] : null;
    $region = !empty($_POST['region']) ? $_POST['region'] : null;
    $city = !empty($_POST['city']) ? $_POST['city'] : null;
    $address = !empty($_POST['address']) ? $_POST['address'] : null;
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
    $inn = !empty($_POST['TIN']) ? $_POST['TIN'] : null;
    $person = !empty($_POST['person']) ? $_POST['person'] : null;
    $organization = !empty($_POST['organization']) ? $_POST['organization'] : null;
    $organizationType = !empty($_POST['organizationType']) ? $_POST['organizationType'] : null;

    $stmt = $conn->prepare("UPDATE users SET surname_users=?, name_users=?, patronymic_users=?, login_users=?, password_users=?, email_users=?, discountСardNumber_users=?, region_users=?, city_users=?, address_users=?, phone_users=?, TIN_users=?, person_users=?, organization_users=?, organizationType_users=? WHERE id_users=?");
    $stmt->bind_param("sssssssssssssssi", $surname, $name, $patronymic, $login, $password, $email, $discountCardNumber, $region, $city, $address, $phone, $inn, $person, $organization, $organizationType, $id);
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
            <input type="text" name="surname" class="form-control" id="surname" value="<?= htmlspecialchars($user['surname_users']) ?>">
        </div>
        <div class="form-group">
            <label for="name">Имя</label>
            <input type="text" name="name" class="form-control" id="name" value="<?= htmlspecialchars($user['name_users']) ?>">
        </div>
        <div class="form-group">
            <label for="patronymic">Отчество</label>
            <input type="text" name="patronymic" class="form-control" id="patronymic" value="<?= htmlspecialchars($user['patronymic_users']) ?>">
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
            <input type="email" name="email" class="form-control" id="email" value="<?= htmlspecialchars($user['email_users']) ?>">
        </div>
        <div class="form-group">
            <label for="phone">Телефон</label>
            <input type="text" name="phone" class="form-control" id="phone" value="<?= htmlspecialchars($user['phone_users']) ?>">
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
            <input type="text" name="region" class="form-control" id="region" value="<?= htmlspecialchars($user['region_users']) ?>">
        </div>
        <div class="form-group">
            <label for="city">Город</label>
            <input type="text" name="city" class="form-control" id="city" value="<?= htmlspecialchars($user['city_users']) ?>">
        </div>
        <div class="form-group">
            <label for="address">Адрес</label>
            <input type="text" name="address" class="form-control" id="address" value="<?= htmlspecialchars($user['address_users']) ?>">
        </div>
        <div class="form-group">
            <label for="person">Физическое лицо/Юридическое лицо</label>
            <input type="text" name="person" class="form-control" id="person" value="<?= htmlspecialchars($user['person_users']) ?>">
        </div>
        <div class="form-group">
            <label for="organization">Организация</label>
            <input type="text" name="organization" class="form-control" id="organization" value="<?= htmlspecialchars($user['organization_users']) ?>">
        </div>
        <div class="form-group">
            <label for="organizationType">Тип организации</label>
            <input type="text" name="organizationType" class="form-control" id="organizationType" value="<?= htmlspecialchars($user['organizationType_users']) ?>">
        </div>

        <button type="submit" class="btn btn-primary mt-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-down" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1z"/>
                <path fill-rule="evenodd" d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708z"/>
            </svg>
            Сохранить
        </button>
        <a href="../admin.php" class="btn btn-secondary mt-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-square" viewBox="0 0 16 16">
                <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
            </svg>
            Отмена
        </a>
    </form>
</div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>