<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

$filename = $_POST['filename'] ?? $_GET['file'] ?? '';

if (empty($filename)) 
{
    echo json_encode(['success' => false, 'error' => 'Файл не указан']);
    exit();
}

if (!preg_match('/^(backup_|uploaded_backup_)[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{2}-[0-9]{2}-[0-9]{2}\.(sql|gz)$/', $filename)) 
{
    echo json_encode(['success' => false, 'error' => 'Некорректное имя файла: ' . htmlspecialchars($filename)]);
    exit();
}

$filepath = '../backups/' . $filename;

if (!file_exists($filepath)) 
{
    $filepath = '../backups/uploads/' . $filename;

    if (!file_exists($filepath)) 
    {
        echo json_encode(['success' => false, 'error' => 'Файл не найден: ' . htmlspecialchars($filename)]);
        exit();
    }
}

$file_size = filesize($filepath);
$max_file_size = 50 * 1024 * 1024;

if ($file_size > $max_file_size) 
{
    echo json_encode(['success' => false, 'error' => 'Файл слишком большой (' . format_size($file_size) . '). Максимальный размер: ' . format_size($max_file_size)]);
    exit();
}

set_time_limit(300);
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

header('Content-Type: application/json');

try 
{
    $backup_result = create_quick_backup();
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $sql_content = '';
    
    if ($extension === 'gz') 
    {
        if (!function_exists('gzopen')) 
        {
            throw new Exception('Функция gzopen не доступна. Установите расширение zlib.');
        }
        
        $sql_content = gzdecode(file_get_contents($filepath));

        if ($sql_content === false) 
        {
            throw new Exception('Не удалось распаковать gzip файл');
        }
    } 
    else 
    {
        $sql_content = file_get_contents($filepath);
    }
    
    if (empty($sql_content)) 
    {
        throw new Exception('Файл резервной копии пуст или поврежден');
    }
    
    if (!is_valid_sql_backup($sql_content)) 
    {
        throw new Exception('Файл не является валидной SQL резервной копией AutoShop');
    }

    $conn->autocommit(FALSE);
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("SET NAMES utf8mb4");

    $queries = split_sql_queries_improved($sql_content);
    $total_queries = count($queries);
    $executed_queries = 0;
    $errors = [];
    
    foreach ($queries as $index => $query) 
    {
        $query = trim($query);

        if (empty($query) || strpos($query, '--') === 0 || strpos($query, '/*') === 0) 
        {
            continue;
        }

        if (preg_match('/^SET\s+/i', $query)) 
        {
            continue;
        }
        
        try 
        {
            if (!$conn->query($query)) 
            {
                $error_msg = $conn->error;
                $short_query = strlen($query) > 100 ? substr($query, 0, 100) . '...' : $query;
                
                $errors[] = [
                    'query_num' => $index + 1,
                    'query' => $short_query,
                    'error' => $error_msg
                ];
                
                if (strpos($error_msg, 'syntax') !== false || strpos($error_msg, 'parse') !== false || strpos($error_msg, 'table') !== false && strpos($error_msg, 'exist') !== false) 
                {
                    $conn->rollback();

                    if ($backup_result['success'] && file_exists($backup_result['backup_file'])) 
                    {
                        restore_from_backup($backup_result['backup_file']);
                    }
                    
                    $conn->autocommit(TRUE);
                    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
                    
                    throw new Exception("Критическая ошибка SQL (запрос #" . ($index + 1) . "): " . $error_msg);
                }

                continue;
            }
            
            $executed_queries++;

            if ($executed_queries % 100 == 0) 
            {
                $conn->commit();
                $conn->begin_transaction();
            }
            
        } 
        catch (Exception $e) 
        {
            $errors[] = [
                'query_num' => $index + 1,
                'query' => substr($query, 0, 100) . '...',
                'error' => $e->getMessage()
            ];
        }
    }

    $conn->commit();
    $conn->autocommit(TRUE);
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    if (!empty($errors))
    {
        $error_count = count($errors);
        $success_rate = round((($executed_queries - $error_count) / $executed_queries) * 100, 2);

        error_log("Восстановление завершено с ошибками. Успешно: $executed_queries/$total_queries, успешность: {$success_rate}%");
        
        foreach ($errors as $error) 
        {
            error_log("Ошибка #{$error['query_num']}: {$error['error']}");
        }
    }

    $log_stmt = $conn->prepare("INSERT INTO backup_logs (filename, file_size, status, action, details, created_at) VALUES (?, ?, 'restored', 'restore', ?, NOW())");
    $details = "Выполнено запросов: $executed_queries/$total_queries";

    if (!empty($errors)) 
    {
        $details .= ", ошибок: " . count($errors);
    }

    $log_stmt->bind_param("sis", $filename, $file_size, $details);
    $log_stmt->execute();

    if ($backup_result['success'] && file_exists($backup_result['backup_file'])) 
    {
        @unlink($backup_result['backup_file']);
    }

    clear_system_cache();
    
    echo json_encode([
        'success' => true,
        'message' => "База данных успешно восстановлена из резервной копии.",
        'details' => [
            'filename' => $filename,
            'file_size' => format_size($file_size),
            'queries_executed' => $executed_queries,
            'total_queries' => $total_queries,
            'errors_count' => count($errors),
            'success_rate' => $total_queries > 0 ? round(($executed_queries / $total_queries) * 100, 2) . '%' : '0%'
        ],
        'warnings' => !empty($errors) ? array_slice($errors, 0, 5) : []
    ]);
    
} 
catch (Exception $e) 
{
    if (isset($conn) && method_exists($conn, 'rollback')) 
    {
        try 
        {
            $conn->rollback();
            $conn->autocommit(TRUE);
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        } 
        catch (Exception $rollback_error) 
        {
            // Игнорируем ошибки отката
        }
    }

    error_log("Ошибка восстановления: " . $e->getMessage());
    
    if (isset($filename)) 
    {
        try 
        {
            $log_stmt = $conn->prepare("INSERT INTO backup_logs (filename, status, error_message, action, created_at) VALUES (?, 'failed', ?, 'restore', NOW())");
            $error_msg = substr($e->getMessage(), 0, 500);
            $log_stmt->bind_param("ss", $filename, $error_msg);
            $log_stmt->execute();
        } 
        catch (Exception $log_error) 
        {
            // Игнорируем ошибки логирования
        }
    }
    
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'filename' => $filename
    ]);
}

function split_sql_queries_improved($sql) 
{
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

    $queries = [];
    $current = '';
    $in_string = false;
    $string_char = '';
    $escape_next = false;
    
    for ($i = 0; $i < strlen($sql); $i++) 
    {
        $char = $sql[$i];
        
        if ($escape_next) 
        {
            $current .= $char;
            $escape_next = false;

            continue;
        }
        
        if ($char === '\\') 
        {
            $escape_next = true;
            $current .= $char;

            continue;
        }
        
        if (($char === "'" || $char === '"' || $char === '`') && !$in_string) 
        {
            $in_string = true;
            $string_char = $char;
        } 
        else if ($char === $string_char && $in_string) 
        {
            $in_string = false;
            $string_char = '';
        }
        
        if ($char === ';' && !$in_string) 
        {
            $queries[] = trim($current);
            $current = '';
        } 
        else 
        {
            $current .= $char;
        }
    }

    if (!empty(trim($current))) 
    {
        $queries[] = trim($current);
    }
    
    return array_filter($queries, function($query) 
    {
        return !empty(trim($query));
    });
}

function is_valid_sql_backup($content) 
{
    $signatures = [
        'CREATE TABLE',
        'INSERT INTO',
        'DROP TABLE IF EXISTS',
        '-- AutoShop Database Backup',
        '-- MySQL dump'
    ];
    
    foreach ($signatures as $signature) 
    {
        if (stripos($content, $signature) !== false) 
        {
            return true;
        }
    }
    
    return false;
}

function create_quick_backup() 
{
    global $conn;
    
    $backup_dir = '../backups/temp/';

    if (!is_dir($backup_dir)) 
    {
        if (!mkdir($backup_dir, 0755, true)) 
        {
            return ['success' => false, 'error' => 'Не удалось создать временную директорию'];
        }
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "pre_restore_backup_{$timestamp}.sql";
    $filepath = $backup_dir . $filename;
    
    try 
    {
        $handle = fopen($filepath, 'w');

        if (!$handle) 
        {
            return ['success' => false, 'error' => 'Не удалось создать файл резервной копии'];
        }
        
        fwrite($handle, "-- Pre-restore backup\n");
        fwrite($handle, "-- Created: " . date('Y-m-d H:i:s') . "\n\n");

        $important_tables = ['settings', 'users', 'products', 'categories', 'orders'];
        $tables_result = $conn->query("SHOW TABLES");
        
        while ($row = $tables_result->fetch_array()) 
        {
            $table = $row[0];

            if (in_array($table, $important_tables)) 
            {
                $create_result = $conn->query("SHOW CREATE TABLE `$table`");

                if ($create_result && $create_row = $create_result->fetch_assoc()) 
                {
                    fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
                    fwrite($handle, $create_row['Create Table'] . ";\n\n");
                    $data_result = $conn->query("SELECT * FROM `$table`");

                    if ($data_result && $data_result->num_rows > 0) 
                    {
                        while ($data_row = $data_result->fetch_assoc()) 
                        {
                            $columns = [];
                            $values = [];
                            
                            foreach ($data_row as $key => $value) 
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
                            
                            fwrite($handle, "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n");
                        }
                        
                        fwrite($handle, "\n");
                    }
                }
            }
        }
        
        fclose($handle);
        
        return [
            'success' => true,
            'backup_file' => $filepath,
            'message' => 'Резервная копия создана успешно'
        ];
        
    } 
    catch (Exception $e) 
    {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function restore_from_backup($backup_file) 
{
    global $conn;
    
    if (!file_exists($backup_file)) 
    {
        return false;
    }
    
    try 
    {
        $sql_content = file_get_contents($backup_file);
        $queries = split_sql_queries_improved($sql_content);
        
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        
        foreach ($queries as $query) 
        {
            $query = trim($query);

            if (!empty($query)) 
            {
                $conn->query($query);
            }
        }
        
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        return true;
        
    } 
    catch (Exception $e) 
    {
        error_log("Ошибка восстановления из резервной копии: " . $e->getMessage());
        return false;
    }
}

function clear_system_cache() 
{
    global $conn;
    @$conn->query("FLUSH TABLES");
    @$conn->query("RESET QUERY CACHE");
}

function format_size($bytes) 
{
    if ($bytes == 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>