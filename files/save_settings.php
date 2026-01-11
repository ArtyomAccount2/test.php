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
    $group = $_POST['group'] ?? 'general';
    $success = true;
    $errors = [];

    foreach ($_POST as $key => $value) 
    {
        if (strpos($key, 'setting_') === 0) 
        {
            $setting_key = substr($key, 8);
            $check_stmt = $conn->prepare("SELECT id FROM settings WHERE setting_key = ?");
            $check_stmt->bind_param("s", $setting_key);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) 
            {
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
                $stmt->bind_param("ss", $value, $setting_key);
            } 
            else 
            {
                $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $setting_key, $value, $group);
            }
            
            if (!$stmt->execute()) 
            {
                $success = false;
                $errors[] = "Ошибка при сохранении настройки: $setting_key";
            }
        }
    }

    if ($success) 
    {
        $_SESSION['success_message'] = 'Настройки успешно сохранены';
    } 
    else 
    {
        $_SESSION['error_message'] = 'Произошли ошибки при сохранении: ' . implode(', ', $errors);
    }
    
    header("Location: ../admin.php?section=settings");
    exit();
}

header("Location: ../admin.php?section=settings");
exit();
?>