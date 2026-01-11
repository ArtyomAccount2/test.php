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

$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($service_id <= 0) 
{
    echo json_encode(['success' => false, 'error' => 'Неверный ID услуги']);
    exit();
}

try 
{
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($service = $result->fetch_assoc()) 
    {
        $safe_service = [];
        
        foreach ($service as $key => $value) 
        {
            if (is_string($value)) 
            {
                $safe_service[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } 
            else 
            {
                $safe_service[$key] = $value;
            }
        }
        
        echo json_encode([
            'success' => true, 
            'service' => $safe_service
        ]);
    } 
    else 
    {
        echo json_encode([
            'success' => false, 
            'error' => 'Услуга не найдена'
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