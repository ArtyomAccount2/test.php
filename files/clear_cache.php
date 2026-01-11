<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

$type = $_GET['type'] ?? 'all';
$message = '';

try 
{
    if ($type == 'cache' || $type == 'all') 
    {
        $cache_dirs = [
            '../cache/',
            '../tmp/',
            '../uploads/cache/'
        ];
        
        foreach ($cache_dirs as $cache_dir) 
        {
            if (is_dir($cache_dir)) 
            {
                $files = glob($cache_dir . '*');
                $deleted = 0;

                foreach ($files as $file)
                {
                    if (is_file($file) && !is_dir($file)) 
                    {
                        if (unlink($file)) 
                        {
                            $deleted++;
                        }
                    } 
                    else if (is_dir($file) && basename($file) != '.' && basename($file) != '..') 
                    {
                        deleteDirectory($file);
                        $deleted++;
                    }
                }

                if ($deleted > 0) 
                {
                    $message .= "Очищено $deleted файлов/папок из $cache_dir. ";
                }
            }
        }

        $session_dir = session_save_path();

        if (empty($session_dir)) 
        {
            $session_dir = sys_get_temp_dir();
        }
        
        $session_files = glob($session_dir . '/sess_*');
        $old_time = time() - (3600 * 24);
        $deleted_sessions = 0;
        
        foreach ($session_files as $session_file) 
        {
            if (filemtime($session_file) < $old_time) 
            {
                if (unlink($session_file)) 
                {
                    $deleted_sessions++;
                }
            }
        }
        
        if ($deleted_sessions > 0) 
        {
            $message .= "Удалено $deleted_sessions старых сессий. ";
        }
    }

    if ($type == 'logs' || $type == 'all') 
    {
        $log_files = [
            '../logs/system.log',
            '../logs/error.log',
            '../logs/access.log'
        ];
        
        $cleared_logs = 0;

        foreach ($log_files as $log_file) 
        {
            if (file_exists($log_file)) 
            {
                $lines = file($log_file);

                if (count($lines) > 1000) 
                {
                    $recent_lines = array_slice($lines, -1000);

                    if (file_put_contents($log_file, implode('', $recent_lines)))
                    {
                        $cleared_logs++;
                        $message .= "Очищен лог-файл: " . basename($log_file) . ". ";
                    }
                }
                else 
                {
                    if (file_put_contents($log_file, '')) 
                    {
                        $cleared_logs++;
                        $message .= "Очищен лог-файл: " . basename($log_file) . ". ";
                    }
                }
            }
        }

        $log_tables = ['error_logs', 'access_logs', 'debug_logs'];

        foreach ($log_tables as $table) 
        {
            $check_table = $conn->query("SHOW TABLES LIKE '$table'");

            if ($check_table->num_rows > 0) 
            {
                $conn->query("DELETE FROM $table WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
                $message .= "Очищена таблица логов: $table. ";
            }
        }
    }

    if ($type == 'cache' || $type == 'all') 
    {
        $conn->query("RESET QUERY CACHE");
        $conn->query("FLUSH TABLES");
        $conn->query("DROP TEMPORARY TABLE IF EXISTS temp_products, temp_users, temp_orders");
        
        $message .= "Кэш базы данных очищен. ";
    }
    
    echo json_encode(['success' => true, 'message' => trim($message)]);
    
} 
catch (Exception $e) 
{
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function deleteDirectory($dir) 
{
    if (!file_exists($dir)) 
    {
        return true;
    }
    
    if (!is_dir($dir)) 
    {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) 
    {
        if ($item == '.' || $item == '..') 
        {
            continue;
        }
        
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) 
        {
            return false;
        }
    }
    
    return rmdir($dir);
}
?>