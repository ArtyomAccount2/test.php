<?php
session_start();
require_once("../config/link.php");

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'unread_count' => 0
];

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    $response['message'] = 'Требуется авторизация';
    echo json_encode($response);
    exit();
}

$username = $_SESSION['user'];
$sql = "SELECT id_users FROM users WHERE CONCAT(surname_users, ' ', name_users, ' ', patronymic_users) = ? OR person_users = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$userId = $userData['id_users'] ?? 0;
$stmt->close();

if (!$userId) 
{
    $response['message'] = 'Пользователь не найден';
    echo json_encode($response);
    exit();
}

$action = $_POST['action'] ?? '';
$notificationId = $_POST['notification_id'] ?? 0;

if ($action === 'mark_as_read' && $notificationId) 
{
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notificationId, $userId);
    
    if ($stmt->execute()) 
    {
        $response['success'] = true;
        $response['message'] = 'Уведомление отмечено как прочитанное';
    }

    $stmt->close();
    
} 
else if ($action === 'delete' && $notificationId) 
{
    $sql = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notificationId, $userId);
    
    if ($stmt->execute()) 
    {
        $response['success'] = true;
        $response['message'] = 'Уведомление удалено';
    }

    $stmt->close();
}

$sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$response['unread_count'] = $row['count'] ?? 0;
$stmt->close();

echo json_encode($response);
?>