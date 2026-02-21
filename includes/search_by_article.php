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

$sql = "SELECT id, name as title, product_type, article, art, 'products' as source_table FROM products WHERE status = 'available' AND (article = ? OR art = ?)";
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
        'product_name' => $product['title'],
        'product_type' => $product_type,
        'article' => $product['article'] ?: $product['art'],
        'source_table' => $product['source_table'],
        'message' => 'Товар найден'
    ]);
    $stmt->close();
    $conn->close();
    exit();
}

$stmt->close();

$sql = "SELECT id, title, category_type, art, 'category_products' as source_table FROM category_products WHERE art = ? AND stock = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $article);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) 
{
    $product = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'found' => true,
        'product_id' => $product['id'],
        'product_name' => $product['title'],
        'product_type' => $product['category_type'],
        'article' => $product['art'],
        'source_table' => $product['source_table'],
        'message' => 'Товар найден'
    ]);
} 
else 
{
    echo json_encode([
        'success' => true,
        'found' => false,
        'message' => 'Товар с таким артикулом не найден или отсутствует в наличии'
    ]);
}

$stmt->close();
$conn->close();
?>