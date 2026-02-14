<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");

header('Content-Type: application/json');

$article = isset($_GET['article']) ? trim($_GET['article']) : '';

if (empty($article)) 
{
    echo json_encode([
        'success' => false,
        'found' => false,
        'message' => 'Артикул не указан'
    ]);
    exit();
}

$sql = "SELECT id, name, product_type, article, art FROM products WHERE status = 'available' AND (article = ? OR art = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $article, $article);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) 
{
    $product = $result->fetch_assoc();
    $product_type = $product['product_type'] ?? 'part';
    
    echo json_encode([
        'success' => true,
        'found' => true,
        'product_id' => $product['id'],
        'product_name' => $product['name'],
        'product_type' => $product_type,
        'article' => $product['article'] ?: $product['art'],
        'message' => 'Товар найден'
    ]);
} else {
    echo json_encode([
        'success' => true,
        'found' => false,
        'message' => 'Товар с таким артикулом не найден'
    ]);
}

$stmt->close();
$conn->close();
?>