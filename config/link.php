<?php
error_reporting(E_ALL);

$conn = mysqli_connect("127.0.0.1", "root", "", "CourseWork");

if ($conn->connect_error) 
{
    die("Ошибка подключения: " . $conn->connect_error);
}
?>