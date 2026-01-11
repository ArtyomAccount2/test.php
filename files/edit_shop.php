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
    $shop_id = isset($_POST['shop_id']) ? (int)$_POST['shop_id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? 'branch';
    $region = $_POST['region'] ?? '';

    if ($region === 'other' && isset($_POST['other_region'])) 
    {
        $region = trim($_POST['other_region']);
    }
    
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $area = isset($_POST['area']) ? floatval(str_replace(',', '.', $_POST['area'])) : 0;
    $employees = isset($_POST['employees']) ? intval($_POST['employees']) : 0;
    $status = $_POST['status'] ?? 'active';
    $description = trim($_POST['description'] ?? '');

    $errors = [];
    
    if (empty($name)) 
    {
        $errors[] = 'Название магазина обязательно для заполнения';
    }
    
    if (empty($region)) 
    {
        $errors[] = 'Регион обязателен для заполнения';
    }
    
    if (empty($phone)) 
    {
        $errors[] = 'Телефон обязателен для заполнения';
    }
    
    if (empty($address)) 
    {
        $errors[] = 'Адрес обязателен для заполнения';
    }
    
    if (empty($errors)) 
    {
        try 
        {
            $check_stmt = $conn->prepare("SELECT id FROM shops WHERE id = ?");
            $check_stmt->bind_param("i", $shop_id);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) 
            {
                $stmt = $conn->prepare("UPDATE shops SET name = ?, type = ?, region = ?, phone = ?, email = ?, address = ?, area = ?, employees = ?, status = ?, description = ? WHERE id = ?");
                $stmt->bind_param("ssssssdisss", $name, $type, $region, $phone, $email, $address, $area, $employees, $status, $description, $shop_id);
                
                if ($stmt->execute()) 
                {
                    $success_message = 'Магазин успешно обновлен';
                } 
                else 
                {
                    $error_message = 'Ошибка при обновлении магазина: ' . $conn->error;
                }
                
                $stmt->close();
            } 
            else 
            {
                $stmt = $conn->prepare("INSERT INTO shops (name, type, region, phone, email, address, area, employees, status, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssdiss", $name, $type, $region, $phone, $email, $address, $area, $employees, $status, $description);
                
                if ($stmt->execute()) 
                {
                    $shop_id = $conn->insert_id;
                    $success_message = 'Магазин успешно создан';
                } 
                else 
                {
                    $error_message = 'Ошибка при создании магазина: ' . $conn->error;
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
    
    header("Location: ../admin.php?section=shops");
    exit();
} 
else 
{
    header("Location: ../admin.php?section=shops");
    exit();
}
?>