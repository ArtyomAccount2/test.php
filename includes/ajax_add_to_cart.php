<?php
session_start();
require_once("../config/link.php");

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') 
{
    die('Access denied');
}

$response = ['success' => false, 'message' => 'Ошибка', 'cart_count' => 0];

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    $response['message'] = 'Требуется авторизация';
    echo json_encode($response);
    exit();
}

$username = $_SESSION['user'];
$sql = "SELECT * FROM users WHERE CONCAT(surname_users, ' ', name_users, ' ', patronymic_users) = ? OR person_users = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$userData) 
{
    $response['message'] = 'Пользователь не найден';
    echo json_encode($response);
    exit();
}

$userId = $userData['id_users'];
$productId = isset($_POST['product_id']) && $_POST['product_id'] > 0 ? intval($_POST['product_id']) : null;
$categoryProductId = isset($_POST['category_product_id']) && $_POST['category_product_id'] > 0 ? intval($_POST['category_product_id']) : null;
$productName = $_POST['product_name'] ?? '';
$productImage = $_POST['product_image'] ?? 'no-image.png';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$productType = $_POST['product_type'] ?? 'part';

if ($userId && $productName && $price > 0) 
{
    if ($categoryProductId) 
    {
        $checkSql = "SELECT * FROM cart WHERE user_id = ? AND category_product_id = ? AND product_name = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("iis", $userId, $categoryProductId, $productName);
    } 
    else 
    {
        $checkSql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND product_name = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("iis", $userId, $productId, $productName);
    }
    
    $checkStmt->execute();
    $existingItem = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();
    
    if ($existingItem) 
    {
        $updateSql = "UPDATE cart SET quantity = quantity + ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $quantity, $existingItem['id']);

        if ($updateStmt->execute()) 
        {
            $response['success'] = true;
            $response['message'] = "Товар добавлен в корзину!";
        }

        $updateStmt->close();
    } 
    else 
    {
        if ($categoryProductId) 
        {
            $insertSql = "INSERT INTO cart (user_id, category_product_id, product_name, product_image, price, quantity, product_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("iissdis", $userId, $categoryProductId, $productName, $productImage, $price, $quantity, $productType);
        } 
        else 
        {
            $insertSql = "INSERT INTO cart (user_id, product_id, product_name, product_image, price, quantity, product_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("iissdis", $userId, $productId, $productName, $productImage, $price, $quantity, $productType);
        }

        if ($insertStmt->execute()) 
        {
            $response['success'] = true;
            $response['message'] = "Товар добавлен в корзину!";
        }

        $insertStmt->close();
    }

    $countSql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("i", $userId);
    $countStmt->execute();
    $countResult = $countStmt->get_result()->fetch_assoc();
    $countStmt->close();
    
    $response['cart_count'] = $countResult['total'] ?? 0;
} 
else 
{
    $response['message'] = 'Недостаточно данных о товаре';
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>