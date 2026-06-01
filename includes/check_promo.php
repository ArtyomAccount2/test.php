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
$code = $input['code'] ?? '';

if (empty($code)) 
{
    echo json_encode(['success' => false, 'message' => 'Введите промокод']);
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM promo_codes WHERE code = ? AND user_id = ? AND is_used = 0 AND expires_at > NOW()");
$stmt->bind_param("si", $code, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$promo = $result->fetch_assoc();
$stmt->close();

if ($promo) 
{
    echo json_encode([
        'success' => true,
        'discount' => $promo['discount'],
        'type' => $promo['type'],
        'code' => $promo['code']
    ]);
} 
else 
{
    echo json_encode(['success' => false, 'message' => 'Промокод недействителен или уже использован']);
}
?>