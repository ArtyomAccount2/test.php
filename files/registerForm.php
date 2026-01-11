<?php
error_reporting(E_ALL);
require_once("../config/link.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $patronymic = $_POST['patronymic'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $discountCardNumber = $_POST['discountCardNumber'] ?? null;
    $region = $_POST['region'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $user_type = 'physical';

   $sql = "INSERT INTO users (surname_users, name_users, patronymic_users, login_users, password_users, email_users, discountСardNumber_users, region_users, city_users, address_users, phone_users, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt === false)
    {
        die("Ошибка подготовки запроса: " . mysqli_error($conn));
    }
    
    $stmt->bind_param("ssssssssssss", $surname, $username, $patronymic, $login, $hashed_password, $email, $discountCardNumber, $region, $city, $address, $phone, $user_type);

    if ($stmt->execute()) 
    {
        header("Location: ../index.php");
        exit();
    } 
    else 
    {
        echo "Ошибка регистрации: " . $stmt->error;
    }
}
else 
{
    echo "Неверный метод запроса.";
}
$conn->close();
?>