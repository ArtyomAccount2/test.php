<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$key = $data['key'] ?? '';

if (empty($key)) 
{
    echo json_encode(['success' => false, 'error' => 'Не указан API ключ']);
    exit();
}

try 
{
    $stmt = $conn->prepare("UPDATE api_keys SET status = 'revoked', revoked_at = NOW() WHERE api_key = ?");
    $stmt->bind_param("s", $key);
    
    if ($stmt->execute()) 
    {
        if ($stmt->affected_rows > 0) 
        {
            echo json_encode([
                'success' => true,
                'message' => 'API ключ успешно отозван'
            ]);
        } 
        else 
        {
            echo json_encode([
                'success' => false,
                'error' => 'API ключ не найден'
            ]);
        }
    } 
    else 
    {
        throw new Exception('Не удалось отозвать API ключ');
    }
    
} 
catch (Exception $e) 
{
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>