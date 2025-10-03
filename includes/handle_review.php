<?php
session_start();
require_once("../config/link.php");

header('Content-Type: application/json');

if ($_POST['action'] == 'add_review') 
{
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $rating = (int)$_POST['rating'];
    $text = trim($_POST['text']);

    if (empty($name) || empty($text) || $rating < 1 || $rating > 5) 
    {
        echo json_encode(['success' => false, 'message' => 'Пожалуйста, заполните все обязательные поля']);
        exit;
    }
    
    $stmt = $conn->prepare("INSERT INTO reviews (name, email, rating, text, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("ssis", $name, $email, $rating, $text);
    
    if ($stmt->execute()) 
    {
        echo json_encode(['success' => true, 'message' => 'Отзыв успешно отправлен на модерацию']);
    } 
    else 
    {
        echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении отзыва']);
    }
    
    $stmt->close();
}
?>