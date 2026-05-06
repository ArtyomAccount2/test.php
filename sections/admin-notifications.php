<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) 
{
    case 'get_notifications':
        $stmt = $conn->prepare("SELECT * FROM admin_notifications ORDER BY created_at DESC LIMIT 20");
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = [];

        while ($row = $result->fetch_assoc()) 
        {
            $notifications[] = $row;
        }

        $stmt = $conn->prepare("SELECT COUNT(*) as unread FROM admin_notifications WHERE is_read = 0");
        $stmt->execute();
        $unread_count = $stmt->get_result()->fetch_assoc()['unread'];
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unread_count
        ]);

        break;
        
    case 'mark_read':
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id) 
        {
            $stmt = $conn->prepare("UPDATE admin_notifications SET is_read = 1 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }

        echo json_encode(['success' => true]);
        break;
        
    case 'mark_all_read':
        $stmt = $conn->prepare("UPDATE admin_notifications SET is_read = 1");
        $stmt->execute();
        echo json_encode(['success' => true]);
        break;
        
    case 'add_notification':
        $title = isset($_POST['title']) ? $_POST['title'] : '';
        $message = isset($_POST['message']) ? $_POST['message'] : '';
        $type = isset($_POST['type']) ? $_POST['type'] : 'info';
        
        if ($title && $message) 
        {
            $stmt = $conn->prepare("INSERT INTO admin_notifications (title, message, type, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $title, $message, $type);
            $stmt->execute();
            echo json_encode(['success' => true]);
        } 
        else 
        {
            echo json_encode(['success' => false, 'error' => 'Missing fields']);
        }
        
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>