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

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

$redirect_url = "../admin.php?section=news&page=" . $page;

if (isset($_SESSION['news_filters'])) 
{
    $filters = $_SESSION['news_filters'];

    if (!empty($filters['search'])) 
    {
        $redirect_url .= "&search=" . urlencode($filters['search']);
    }

    if (!empty($filters['status_filter'])) 
    {
        $redirect_url .= "&status_filter=" . urlencode($filters['status_filter']);
    }
}

header("Location: " . $redirect_url);
exit();
?>