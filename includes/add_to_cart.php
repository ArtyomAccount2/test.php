<?php
session_start();
require_once("../config/link.php");
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Ошибка', 'cart_count' => 0];

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
$stmt->close();

if (!$userData) 
{
    $response['message'] = 'Пользователь не найден';
    echo json_encode($response);
    exit();
}

$userId = $userData['id_users'];
$category_product_id = isset($_POST['category_product_id']) ? intval($_POST['category_product_id']) : 0;
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$product_name = $_POST['product_name'] ?? '';
$product_image = $_POST['product_image'] ?? '../img/no-image.png';
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$product_type = $_POST['product_type'] ?? 'part';

if (empty($product_name) || $price <= 0) 
{
    $response['message'] = 'Недостаточно данных о товаре';
    echo json_encode($response);
    exit();
}

if ($category_product_id > 0) 
{
    $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND category_product_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $userId, $category_product_id);
    $check_stmt->execute();
    $existing = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if ($existing) 
    {
        $new_quantity = $existing['quantity'] + $quantity;
        $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_quantity, $existing['id']);
        $update_stmt->execute();
        $update_stmt->close();
        $response['success'] = true;
        $response['message'] = 'Количество товара обновлено';
    } 
    else 
    {
        $insert_sql = "INSERT INTO cart (user_id, category_product_id, product_name, product_image, price, quantity, product_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iissdis", $userId, $category_product_id, $product_name, $product_image, $price, $quantity, $product_type);
        
        if ($insert_stmt->execute()) 
        {
            $response['success'] = true;
            $response['message'] = 'Товар добавлен в корзину';
        }
        $insert_stmt->close();
    }
} 
else if ($product_id > 0) 
{
    $check_sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $userId, $product_id);
    $check_stmt->execute();
    $existing = $check_stmt->get_result()->fetch_assoc();
    $check_stmt->close();
    
    if ($existing) 
    {
        $new_quantity = $existing['quantity'] + $quantity;
        $update_sql = "UPDATE cart SET quantity = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $new_quantity, $existing['id']);
        $update_stmt->execute();
        $update_stmt->close();
        $response['success'] = true;
        $response['message'] = 'Количество товара обновлено';
    } 
    else 
    {
        $insert_sql = "INSERT INTO cart (user_id, product_id, product_name, product_image, price, quantity, product_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iissdis", $userId, $product_id, $product_name, $product_image, $price, $quantity, $product_type);
        
        if ($insert_stmt->execute()) 
        {
            $response['success'] = true;
            $response['message'] = 'Товар добавлен в корзину';
        }
        $insert_stmt->close();
    }
}
else 
{
    $response['message'] = 'Не указан ID товара';
    echo json_encode($response);
    exit();
}

if ($response['success']) 
{
    $count_sql = "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $userId);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result()->fetch_assoc();
    $count_stmt->close();
    
    $response['cart_count'] = $count_result['total'] ?? 0;
} 
else 
{
    $response['message'] = 'Ошибка при добавлении товара: ' . $conn->error;
}

echo json_encode($response);
?>