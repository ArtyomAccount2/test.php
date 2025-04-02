<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM `users` WHERE `id_users` = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: ../admin.php");
exit();
?>