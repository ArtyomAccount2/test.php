<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit();
}

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM users WHERE id_users = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) 
{
    foreach ($user as $key => $value) 
    {
        if (is_string($value)) 
        {
            $user[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    
    echo json_encode(['success' => true, 'user' => $user]);
} 
else 
{
    echo json_encode(['success' => false, 'error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>