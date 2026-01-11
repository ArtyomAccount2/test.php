<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

$filename = $_GET['filename'] ?? '';

if (empty($filename)) 
{
    echo json_encode(['success' => false, 'error' => 'Файл не указан']);
    exit();
}

if (!preg_match('/^(backup_|uploaded_backup_)[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{2}-[0-9]{2}-[0-9]{2}\.(sql|gz)$/', $filename)) 
{
    echo json_encode(['success' => false, 'error' => 'Некорректное имя файла']);
    exit();
}

$filepath = '../backups/' . $filename;

if (!file_exists($filepath)) 
{
    $filepath = '../backups/uploads/' . $filename;

    if (!file_exists($filepath)) 
    {
        echo json_encode(['success' => false, 'error' => 'Файл не найден']);
        exit();
    }
}

$file_size = filesize($filepath);
$max_size = 50 * 1024 * 1024;

if ($file_size > $max_size) 
{
    echo json_encode([
        'success' => false,
        'error' => 'Файл слишком большой (' . format_size($file_size) . '). Максимальный размер: ' . format_size($max_size),
        'size' => $file_size
    ]);

    exit();
}

if (!is_readable($filepath)) 
{
    echo json_encode(['success' => false, 'error' => 'Нет прав на чтение файла']);
    exit();
}

$free_space = disk_free_space(dirname($filepath));
$required_space = $file_size * 3;

if ($free_space < $required_space) 
{
    echo json_encode([
        'success' => false,
        'error' => 'Недостаточно свободного места на диске. Требуется: ' . format_size($required_space) . ', доступно: ' . format_size($free_space)
    ]);

    exit();
}

$tables_result = $conn->query("SHOW TABLES");
$current_tables = [];

while ($row = $tables_result->fetch_array()) 
{
    $current_tables[] = $row[0];
}

echo json_encode([
    'success' => true,
    'filename' => $filename,
    'file_size' => $file_size,
    'file_size_formatted' => format_size($file_size),
    'current_tables_count' => count($current_tables),
    'free_space' => format_size($free_space),
    'checks' => [
        'file_exists' => true,
        'file_readable' => true,
        'size_ok' => $file_size <= $max_size,
        'enough_space' => $free_space >= $required_space
    ]
]);

function format_size($bytes) 
{
    if ($bytes == 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>