<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    
    if (!empty($name)) 
    {
        if ($category_id > 0) 
        {
            $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $description, $category_id);
            
            if ($stmt->execute()) 
            {
                $_SESSION['success_message'] = 'Категория обновлена';
            }
            else 
            {
                $_SESSION['error_message'] = 'Ошибка при обновлении категории';
            }
        } 
        else 
        {
            $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            
            if ($stmt->execute()) 
            {
                $_SESSION['success_message'] = 'Категория добавлена';
            }
            else 
            {
                $_SESSION['error_message'] = 'Ошибка при добавлении категории';
            }
        }

        header("Location: ../admin.php?section=products_categories&page=" . $page);
        exit();
    }
    else 
    {
        $_SESSION['error_message'] = 'Название категории обязательно';
        header("Location: ../admin.php?section=products_categories&page=" . $page);
        exit();
    }
}
else 
{
    header("Location: ../admin.php?section=products_categories");
    exit();
}
?>