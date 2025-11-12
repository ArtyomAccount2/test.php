<?php
session_start();
require_once("../config/link.php");

header('Content-Type: application/json');

file_put_contents('debug.log', "=== REVIEW SUBMISSION START ===\n", FILE_APPEND);
file_put_contents('debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
{
    file_put_contents('debug.log', "ERROR: Not POST method\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Метод не поддерживается']);
    exit;
}

if (!isset($_POST['action'])) 
{
    file_put_contents('debug.log', "ERROR: No action specified\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Не указано действие']);
    exit;
}

if ($_POST['action'] == 'add_review') 
{
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    
    file_put_contents('debug.log', "Processing review: name=$name, email=$email, rating=$rating, text=" . substr($text, 0, 50) . "...\n", FILE_APPEND);

    if (empty($name)) 
    {
        file_put_contents('debug.log', "ERROR: Empty name\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Пожалуйста, укажите ваше имя']);
        exit;
    }
    
    if (empty($text)) 
    {
        file_put_contents('debug.log', "ERROR: Empty text\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Пожалуйста, напишите текст отзыва']);
        exit;
    }
    
    if ($rating < 1 || $rating > 5) 
    {
        file_put_contents('debug.log', "ERROR: Invalid rating: $rating\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Пожалуйста, поставьте оценку от 1 до 5 звезд']);
        exit;
    }

    if (strlen($name) > 100) 
    {
        echo json_encode(['success' => false, 'message' => 'Имя не должно превышать 100 символов']);
        exit;
    }
    
    if (strlen($text) > 1000) 
    {
        echo json_encode(['success' => false, 'message' => 'Текст отзыва не должен превышать 1000 символов']);
        exit;
    }
    
    if (!empty($email) && strlen($email) > 255) 
    {
        echo json_encode(['success' => false, 'message' => 'Email не должен превышать 255 символов']);
        exit;
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
        echo json_encode(['success' => false, 'message' => 'Укажите корректный email адрес']);
        exit;
    }

    try 
    {
        if (!$conn) 
        {
            throw new Exception('No database connection');
        }
        
        $stmt = $conn->prepare("INSERT INTO reviews (name, email, rating, text, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        
        if (!$stmt) 
        {
            throw new Exception('Prepare failed: ' . $conn->error);
        }

        $bind_result = $stmt->bind_param("ssis", $name, $email, $rating, $text);

        if (!$bind_result) 
        {
            throw new Exception('Bind failed: ' . $stmt->error);
        }

        $execute_result = $stmt->execute();

        if (!$execute_result) 
        {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        if ($stmt->affected_rows > 0) 
        {
            file_put_contents('debug.log', "SUCCESS: Review saved to database\n", FILE_APPEND);
            echo json_encode([
                'success' => true, 
                'message' => 'Спасибо за ваш отзыв! После модерации он будет опубликован.'
            ]);
        } 
        else 
        {
            throw new Exception('No rows affected');
        }
        
        $stmt->close();
        
    } 
    catch (Exception $e) 
    {
        file_put_contents('debug.log', "DATABASE ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        echo json_encode([
            'success' => false, 
            'message' => 'Ошибка базы данных: ' . $e->getMessage()
        ]);
    }
} 
else 
{
    file_put_contents('debug.log', "ERROR: Unknown action: " . $_POST['action'] . "\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Неизвестное действие']);
}

file_put_contents('debug.log', "=== REVIEW SUBMISSION END ===\n\n", FILE_APPEND);
?>