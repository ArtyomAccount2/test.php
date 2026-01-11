<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $id = (int)$_POST['news_id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $status = $_POST['status'];
    $published_at = $status == 'published' ? date('Y-m-d') : null;
    
    if (!empty($title) && !empty($content)) 
    {
        $stmt = $conn->prepare("UPDATE news SET title = ?, content = ?, status = ?, published_at = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $title, $content, $status, $published_at, $id);
        
        if ($stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Новость обновлена';
        }
    }
}

header("Location: ../admin.php?section=news");
exit();
?>