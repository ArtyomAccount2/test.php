<?php
session_start();
header('Content-Type: application/json');
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    echo json_encode(['success' => false, 'message' => 'Не авторизован']);
    exit();
}

$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("SELECT free_spin_used FROM users WHERE id_users = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) 
{
    echo json_encode(['success' => false, 'message' => 'Пользователь не найден']);
    exit();
}

$canSpin = false;
$spinType = '';

if ($user['free_spin_used'] == 0) 
{
    $canSpin = true;
    $spinType = 'free';
} 
else 
{
    $stmt = $conn->prepare("SELECT COUNT(*) as purchase_count FROM orders WHERE user_id = ? AND order_date > (SELECT MAX(created_at) FROM wheel_spins WHERE user_id = ?)");
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $purchaseResult = $stmt->get_result();
    $purchaseData = $purchaseResult->fetch_assoc();
    $stmt->close();

    $stmt = $conn->prepare("SELECT purchases_required FROM wheel_spins WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $lastSpin = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $requiredPurchases = $lastSpin ? ($lastSpin['purchases_required'] ?? 10) : 10;
    
    if ($purchaseData['purchase_count'] >= $requiredPurchases) 
    {
        $canSpin = true;
        $spinType = 'regular';
    }
}

if (!$canSpin) 
{
    echo json_encode(['success' => false, 'message' => 'Недостаточно покупок для вращения']);
    exit();
}

$segments = [
    ['text' => 'Неудача', 'discount' => 0, 'type' => 'failure', 'description' => 'Попробуйте в следующий раз!'],
    ['text' => '10%', 'discount' => 10, 'type' => 'discount', 'description' => 'Скидка 10% на следующий заказ'],
    ['text' => 'Бесп. доставка', 'discount' => 0, 'type' => 'free_shipping', 'description' => 'Бесплатная доставка следующего заказа!'],
    ['text' => '15%', 'discount' => 15, 'type' => 'discount', 'description' => 'Скидка 15% на следующий заказ'],
    ['text' => '5%', 'discount' => 5, 'type' => 'discount', 'description' => 'Скидка 5% на следующий заказ'],
    ['text' => 'Неудача', 'discount' => 0, 'type' => 'failure', 'description' => 'Попробуйте в следующий раз!'],
    ['text' => '10%', 'discount' => 10, 'type' => 'discount', 'description' => 'Скидка 10% на следующий заказ'],
    ['text' => 'Бесп. доставка', 'discount' => 0, 'type' => 'free_shipping', 'description' => 'Бесплатная доставка следующего заказа!'],
    ['text' => 'Неудача', 'discount' => 0, 'type' => 'failure', 'description' => 'Попробуйте в следующий раз!'],
    ['text' => '5%', 'discount' => 5, 'type' => 'discount', 'description' => 'Скидка 5% на следующий заказ']
];

$winIndex = mt_rand(0, count($segments) - 1);
$prize = $segments[$winIndex];
$promoCode = null;

if ($prize['type'] !== 'failure') 
{
    $promoCode = generatePromoCode($prize['text'], $user_id);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
    $stmt = $conn->prepare("INSERT INTO promo_codes (user_id, code, discount, type, expires_at, is_used) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->bind_param("issis", $user_id, $promoCode, $prize['discount'], $prize['type'], $expiresAt);
    $stmt->execute();
    $stmt->close();
}

$purchasesRequired = ($spinType == 'free') ? 10 : 10;
$stmt = $conn->prepare("INSERT INTO wheel_spins (user_id, prize_text, prize_type, discount_value, promo_code, purchases_required, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$stmt->bind_param("issisi", $user_id, $prize['text'], $prize['type'], $prize['discount'], $promoCode, $purchasesRequired);
$stmt->execute();
$stmt->close();

if ($spinType == 'free') 
{
    $stmt = $conn->prepare("UPDATE users SET free_spin_used = 1 WHERE id_users = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

echo json_encode([
    'success' => true,
    'winIndex' => $winIndex,
    'prize' => $prize,
    'promoCode' => $promoCode,
    'spinType' => $spinType,
    'purchasesRequired' => $purchasesRequired
]);

function generatePromoCode($prizeText, $userId) 
{
    $prefix = strpos($prizeText, 'доставка') !== false ? 'FREE' : 'DISCOUNT';
    $randomNum = mt_rand(1000, 9999);
    $userIdHash = substr(md5($userId), 0, 4);
    return strtoupper($prefix . $randomNum . $userIdHash);
}
?>