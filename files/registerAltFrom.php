<?php
error_reporting(E_ALL);
require_once("../config/link.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $organization = $_POST['organization'];
    $inn = $_POST['TIN'];
    $person = $_POST['person'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $discountCardNumber = $_POST['discountCardNumber'] ?? null;
    $region = $_POST['region'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $organizationType = $_POST['organizationType'];
    $user_type = 'legal';

    $sql = "INSERT INTO users (login_users, password_users, email_users, discountСardNumber_users, region_users, city_users, address_users, phone_users, TIN_users, person_users, organization_users, organizationType_users, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt === false)
    {
        die("Ошибка подготовки запроса: " . mysqli_error($conn));
    }
    
    $stmt->bind_param("sssssssssssss", $login, $password, $email, $discountCardNumber, $region, $city, $address, $phone, $TIN, $person, $organization, $organizationType, $user_type);

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