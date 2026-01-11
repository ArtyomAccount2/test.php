<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';

if (empty($name)) 
{
    echo json_encode(['success' => false, 'error' => 'Не указано название ключа']);
    exit();
}

try 
{
    $api_key = generate_api_key();
    $secret_key = generate_secret_key();

    $stmt = $conn->prepare("INSERT INTO api_keys (name, api_key, secret_key, status, permissions, created_at, expires_at) VALUES (?, ?, ?, 'active', 'read,write', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR))");
    $stmt->bind_param("sss", $name, $api_key, $secret_key);
    
    if ($stmt->execute()) 
    {
        echo json_encode([
            'success' => true,
            'api_key' => $api_key,
            'secret_key' => $secret_key,
            'id' => $stmt->insert_id,
            'message' => 'API ключ успешно создан'
        ]);
    } 
    else 
    {
        throw new Exception('Не удалось сохранить API ключ в базу данных');
    }
    
} 
catch (Exception $e) 
{
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function generate_api_key() 
{
    $prefix = 'sk_live_';
    $random = bin2hex(random_bytes(16));
    return $prefix . $random;
}

function generate_secret_key() 
{
    return 'sk_' . bin2hex(random_bytes(24));
}
?>