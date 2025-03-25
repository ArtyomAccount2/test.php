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
    $telephone = $_POST["telephone"];
    $phone = $_POST['phone'];
    $organizationType = $_POST['organizationType'];

    $sql = "INSERT INTO `users` (organization_users, TIN_users, person_users, login_users, password_users, email_users, discountСardNumber_users, region_users, city_users, address_users, telephone_users, phone_users, organizationType_users) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    
    if ($stmt === false)
    {
        die("Ошибка подготовки запроса: " . mysqli_error($conn));
    }
    
    $stmt->bind_param("sssssssssssss", $organization, $inn, $person, $login, $password, $email, $discountCardNumber, $region, $city, $address, $telephone, $phone, $organizationType);

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