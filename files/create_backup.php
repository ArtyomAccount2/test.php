<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

set_time_limit(300);
ini_set('memory_limit', '512M');

try 
{
    $backup_dir = '../backups/';

    if (!is_dir($backup_dir)) 
    {
        if (!mkdir($backup_dir, 0755, true)) 
        {
            throw new Exception('Не удалось создать директорию для резервных копий');
        }
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "backup_{$timestamp}.sql";
    $filepath = $backup_dir . $filename;
    $handle = fopen($filepath, 'w');

    if (!$handle) 
    {
        throw new Exception('Не удалось создать файл резервной копии');
    }

    fwrite($handle, "-- AutoShop Database Backup\n");
    fwrite($handle, "-- Date: " . date('Y-m-d H:i:s') . "\n");
    fwrite($handle, "-- PHP Version: " . phpversion() . "\n");
    fwrite($handle, "-- MySQL Version: " . $conn->server_version . "\n\n");
    fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

    $tables_result = $conn->query("SHOW TABLES");
    $tables = [];
    
    while ($row = $tables_result->fetch_array()) 
    {
        $tables[] = $row[0];
    }
    
    $total_tables = count($tables);
    $processed_tables = 0;
    
    foreach ($tables as $table) 
    {
        $create_result = $conn->query("SHOW CREATE TABLE `$table`");
        $create_row = $create_result->fetch_assoc();
        
        fwrite($handle, "--\n");
        fwrite($handle, "-- Структура таблицы `$table`\n");
        fwrite($handle, "--\n\n");
        fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
        fwrite($handle, $create_row['Create Table'] . ";\n\n");

        $data_result = $conn->query("SELECT * FROM `$table`");
        $total_rows = $data_result->num_rows;
        
        if ($total_rows > 0) 
        {
            fwrite($handle, "--\n");
            fwrite($handle, "-- Дамп данных таблицы `$table`\n");
            fwrite($handle, "-- Всего записей: $total_rows\n");
            fwrite($handle, "--\n\n");
            
            $inserted_rows = 0;
            $batch_size = 1000;
            $batch_values = [];
            
            while ($row = $data_result->fetch_assoc()) 
            {
                $columns = [];
                $values = [];
                
                foreach ($row as $key => $value) 
                {
                    $columns[] = "`$key`";

                    if (is_null($value)) 
                    {
                        $values[] = 'NULL';
                    } 
                    else 
                    {
                        $values[] = "'" . $conn->real_escape_string($value) . "'";
                    }
                }
                
                $batch_values[] = "(" . implode(', ', $values) . ")";
                $inserted_rows++;

                if (count($batch_values) >= $batch_size || $inserted_rows == $total_rows) 
                {
                    $sql = "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES \n" . implode(",\n", $batch_values) . ";\n\n";
                    fwrite($handle, $sql);
                    $batch_values = [];
                }
            }
        }
        
        $processed_tables++;

        if ($total_tables > 10 && $processed_tables % ceil($total_tables / 10) == 0) 
        {
            $progress = round(($processed_tables / $total_tables) * 100);
        }
    }
    
    fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
    fwrite($handle, "-- End of backup\n");
    fclose($handle);
    
    $file_size = filesize($filepath);

    $log_stmt = $conn->prepare("INSERT INTO backup_logs (filename, file_size, status, created_at) VALUES (?, ?, 'success', NOW())");
    $log_stmt->bind_param("si", $filename, $file_size);
    $log_stmt->execute();

    $backup_info = [
        'filename' => $filename,
        'size' => format_size($file_size),
        'tables' => $total_tables,
        'date' => date('d.m.Y H:i:s'),
        'path' => $filepath
    ];
    
    file_put_contents($backup_dir . 'last_backup.json', json_encode($backup_info, JSON_PRETTY_PRINT));
    
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'filesize' => format_size($file_size),
        'size_bytes' => $file_size,
        'date' => date('d.m.Y H:i:s'),
        'tables' => $total_tables,
        'message' => "Резервная копия успешно создана. Обработано таблиц: $total_tables"
    ]);
    
} 
catch (Exception $e) 
{
    if (isset($filename)) 
    {
        $log_stmt = $conn->prepare("INSERT INTO backup_logs (filename, status, error_message, created_at) VALUES (?, 'failed', ?, NOW())");
        $error_msg = $e->getMessage();
        $log_stmt->bind_param("ss", $filename, $error_msg);
        $log_stmt->execute();
    }
    
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function format_size($bytes) 
{
    if ($bytes == 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>