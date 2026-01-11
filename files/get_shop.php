<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$shop_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($shop_id <= 0) 
{
    echo json_encode(['success' => false, 'error' => 'Неверный ID магазина']);
    exit();
}

try 
{
    $stmt = $conn->prepare("SELECT * FROM shops WHERE id = ?");
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($shop = $result->fetch_assoc()) 
    {
        $default_values = [
            'type' => 'branch',
            'area' => 0,
            'employees' => 0,
            'status' => 'active',
            'email' => '',
            'address' => '',
            'description' => ''
        ];

        foreach ($default_values as $key => $default_value) 
        {
            if (!isset($shop[$key]) || $shop[$key] === null) 
            {
                $shop[$key] = $default_value;
            }
        }

        $safe_shop = [];
        
        foreach ($shop as $key => $value) 
        {
            if (is_string($value)) 
            {
                $safe_shop[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } 
            else 
            {
                $safe_shop[$key] = $value;
            }
        }
        
        echo json_encode([
            'success' => true, 
            'shop' => $safe_shop
        ]);
    } 
    else 
    {
        echo json_encode([
            'success' => false, 
            'error' => 'Магазин не найден'
        ]);
    }
    
    $stmt->close();
} 
catch (Exception $e) 
{
    echo json_encode([
        'success' => false, 
        'error' => 'Ошибка базы данных: ' . $e->getMessage()
    ]);
}

$conn->close();
?>