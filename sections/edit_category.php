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
    $id = (int)$_POST['category_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (!empty($name)) 
    {
        $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        
        if ($stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Категория обновлена';
        } 
        else 
        {
            $_SESSION['error_message'] = 'Ошибка при обновлении категории';
        }
    }
    
    header("Location: ../admin.php?section=products_categories");
    exit();
}

header("Location: ../admin.php?section=products_categories");
exit();
?>