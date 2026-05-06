<?php
session_start();
header('Content-Type: application/json');
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    echo json_encode(['success' => false, 'message' => 'Не авторизован']);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id']) || !isset($input['used'])) 
{
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit();
}

$user_id = (int)$input['user_id'];
$used = (bool)$input['used'];

if ($user_id != $_SESSION['user_id']) 
{
    echo json_encode(['success' => false, 'message' => 'Доступ запрещен']);
    exit();
}

$stmt = $conn->prepare("UPDATE users SET free_spin_used = ? WHERE id_users = ?");
$stmt->bind_param("ii", $used, $user_id);

if ($stmt->execute()) 
{
    echo json_encode(['success' => true]);
} 
else 
{
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных']);
}

$stmt->close();
$conn->close();
?>