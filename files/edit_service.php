<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = isset($_POST['price']) ? floatval(str_replace(',', '.', $_POST['price'])) : 0;
    $duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    
    $errors = [];
    
    if (empty($name)) 
    {
        $errors[] = 'Название услуги обязательно для заполнения';
    }
    
    if (empty($category)) 
    {
        $errors[] = 'Категория обязательна для заполнения';
    }
    
    if ($price <= 0) 
    {
        $errors[] = 'Цена должна быть больше 0';
    }
    
    if (empty($errors)) 
    {
        try 
        {
            $check_stmt = $conn->prepare("SELECT id FROM services WHERE id = ?");
            $check_stmt->bind_param("i", $service_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) 
            {
                $stmt = $conn->prepare("UPDATE services SET name = ?, category = ?, price = ?, duration = ?, description = ?, status = ? WHERE id = ?");
                $stmt->bind_param("ssdissi", $name, $category, $price, $duration, $description, $status, $service_id);
                
                if ($stmt->execute()) 
                {
                    $success_message = 'Услуга успешно обновлена';

                    $user_id = $_SESSION['user_id'] ?? 0;
                    $log_stmt = $conn->prepare("INSERT INTO action_logs (user_id, action, description, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
                    $action = 'service_updated';
                    $log_description = "Обновлена услуга: $name (ID: $service_id)";
                    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
                    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                    $log_stmt->bind_param("issss", $user_id, $action, $log_description, $ip_address, $user_agent);
                    $log_stmt->execute();
                    $log_stmt->close();
                } 
                else 
                {
                    $error_message = 'Ошибка при обновлении услуги: ' . $conn->error;
                }
                
                $stmt->close();
            } 
            else 
            {
                $stmt = $conn->prepare("INSERT INTO services (name, category, price, duration, description, status) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssdiss", $name, $category, $price, $duration, $description, $status);
                
                if ($stmt->execute()) 
                {
                    $service_id = $conn->insert_id;
                    $success_message = 'Услуга успешно создана';
                } 
                else 
                {
                    $error_message = 'Ошибка при создании услуги: ' . $conn->error;
                }
                
                $stmt->close();
            }
            
            $check_stmt->close();
        } 
        catch (Exception $e) 
        {
            $error_message = 'Ошибка базы данных: ' . $e->getMessage();
        }
    } 
    else 
    {
        $error_message = implode('<br>', $errors);
    }

    if (!empty($success_message)) 
    {
        $_SESSION['success_message'] = $success_message;
    }
    
    if (!empty($error_message)) 
    {
        $_SESSION['error_message'] = $error_message;
    }
    
    $redirect_url = "../admin.php?section=service&page=" . $page;

    if (isset($_SESSION['service_filters'])) 
    {
        $filters = $_SESSION['service_filters'];

        if (!empty($filters['search'])) 
        {
            $redirect_url .= "&search=" . urlencode($filters['search']);
        }

        if (!empty($filters['category'])) 
        {
            $redirect_url .= "&category=" . urlencode($filters['category']);
        }

        if (!empty($filters['status_filter'])) 
        {
            $redirect_url .= "&status_filter=" . urlencode($filters['status_filter']);
        }
    }
    
    header("Location: " . $redirect_url);
    exit();
} 
else 
{
    header("Location: ../admin.php?section=service");
    exit();
}
?>