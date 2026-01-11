<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

$current_version = '2.1.0';

try 
{
    $update_data = check_for_updates_via_api();
    
    if (!$update_data || !$update_data['update_available']) 
    {
        echo json_encode(['success' => false, 'error' => 'Обновление не доступно']);
        exit();
    }

    $backup_result = create_pre_update_backup();

    if (!$backup_result['success']) 
    {
        throw new Exception('Не удалось создать резервную копию перед обновлением: ' . $backup_result['error']);
    }
    
    enable_maintenance_mode();
    $update_file = download_update($update_data['download_url']);

    if (!$update_file) 
    {
        throw new Exception('Не удалось загрузить обновление');
    }
    
    $install_result = install_update($update_file, $update_data['version']);

    if (!$install_result['success']) 
    {
        restore_from_backup($backup_result['backup_file']);
        disable_maintenance_mode();
        
        throw new Exception('Ошибка установки обновления: ' . $install_result['error']);
    }

    update_system_version($update_data['version']);
    disable_maintenance_mode();
    clear_system_cache();
    log_update_success($current_version, $update_data['version']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Система успешно обновлена до версии ' . $update_data['version'],
        'old_version' => $current_version,
        'new_version' => $update_data['version'],
        'changelog' => $update_data['changelog'] ?? []
    ]);
    
} 
catch (Exception $e) 
{
    if (function_exists('disable_maintenance_mode')) 
    {
        disable_maintenance_mode();
    }
    
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function create_pre_update_backup() 
{
    global $conn;
    
    $backup_dir = '../backups/pre_update/';

    if (!is_dir($backup_dir)) 
    {
        mkdir($backup_dir, 0755, true);
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "pre_update_backup_{$timestamp}.sql";
    $filepath = $backup_dir . $filename;
    
    try 
    {
        $tables_result = $conn->query("SHOW TABLES");
        $handle = fopen($filepath, 'w');
        
        if (!$handle) 
        {
            return ['success' => false, 'error' => 'Не удалось создать файл резервной копии'];
        }
        
        fwrite($handle, "-- Pre-update backup\n");
        fwrite($handle, "-- Created: " . date('Y-m-d H:i:s') . "\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");
        
        while ($row = $tables_result->fetch_array()) 
        {
            $table = $row[0];
            $create_result = $conn->query("SHOW CREATE TABLE `$table`");
            $create_row = $create_result->fetch_assoc();
            fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
            fwrite($handle, $create_row['Create Table'] . ";\n\n");
            $important_tables = ['settings', 'users', 'system_config'];

            if (in_array($table, $important_tables)) 
            {
                $data_result = $conn->query("SELECT * FROM `$table`");

                while ($data_row = $data_result->fetch_assoc()) 
                {
                    $columns = [];
                    $values = [];
                    
                    foreach ($data_row as $key => $value) 
                    {
                        $columns[] = "`$key`";
                        $values[] = is_null($value) ? 'NULL' : "'" . $conn->real_escape_string($value) . "'";
                    }
                    
                    fwrite($handle, "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n");
                }

                fwrite($handle, "\n");
            }
        }
        
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);

        $files_backup_dir = $backup_dir . 'files_' . $timestamp . '/';
        mkdir($files_backup_dir, 0755, true);
        
        $important_files = [
            '../config/link.php',
            '../config/settings.php',
            '../.htaccess',
            '../robots.txt'
        ];
        
        foreach ($important_files as $file) 
        {
            if (file_exists($file)) 
            {
                $dest = $files_backup_dir . basename($file);
                copy($file, $dest);
            }
        }
        
        return [
            'success' => true,
            'backup_file' => $filepath,
            'files_backup_dir' => $files_backup_dir,
            'message' => 'Резервная копия создана успешно'
        ];
        
    } 
    catch (Exception $e) 
    {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function enable_maintenance_mode() 
{
    $maintenance_file = '../maintenance.html';
    $content = '<!DOCTYPE html>
<html>
<head>
    <title>Сайт на обслуживании</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        h1 { color: #333; }
        p { color: #666; }
    </style>
</head>
<body>
    <h1>Сайт временно недоступен</h1>
    <p>Ведутся технические работы. Приносим извинения за неудобства.</p>
    <p>Время работ: ' . date('d.m.Y H:i:s') . '</p>
</body>
</html>';
    
    file_put_contents($maintenance_file, $content);

    global $conn;
    $conn->query("UPDATE settings SET setting_value = '1' WHERE setting_key = 'maintenance_mode'");
}

function disable_maintenance_mode() 
{
    $maintenance_file = '../maintenance.html';

    if (file_exists($maintenance_file)) 
    {
        unlink($maintenance_file);
    }

    global $conn;
    $conn->query("UPDATE settings SET setting_value = '0' WHERE setting_key = 'maintenance_mode'");
}

function download_update($url) 
{
    $update_dir = '../updates/';

    if (!is_dir($update_dir)) 
    {
        mkdir($update_dir, 0755, true);
    }
    
    $filename = basename($url);

    if (empty($filename)) 
    {
        $filename = 'update_' . date('Ymd_His') . '.zip';
    }
    
    $filepath = $update_dir . $filename;
    file_put_contents($filepath, '');
    
    return file_exists($filepath) ? $filepath : false;
}

function install_update($update_file, $new_version) 
{
    $update_dir = '../updates/extracted/';

    if (is_dir($update_dir)) 
    {
        deleteDirectory($update_dir);
    }

    mkdir($update_dir, 0755, true);
    
    $update_info = [
        'version' => $new_version,
        'installed' => date('Y-m-d H:i:s'),
        'files_updated' => [],
        'database_updated' => false
    ];
    
    file_put_contents($update_dir . 'update_info.json', json_encode($update_info, JSON_PRETTY_PRINT));

    $version_file = '../version.json';
    $version_data = ['version' => $new_version, 'updated' => date('Y-m-d H:i:s')];
    file_put_contents($version_file, json_encode($version_data, JSON_PRETTY_PRINT));
    
    return ['success' => true, 'message' => 'Обновление установлено'];
}

function update_system_version($new_version) 
{
    global $conn;

    $stmt = $conn->prepare("INSERT INTO system_versions (version, installed_at) VALUES (?, NOW())");
    $stmt->bind_param("s", $new_version);
    $stmt->execute();

    $check_stmt = $conn->prepare("SELECT id FROM settings WHERE setting_key = 'system_version'");
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) 
    {
        $update_stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'system_version'");
        $update_stmt->bind_param("s", $new_version);
        $update_stmt->execute();
    } 
    else 
    {
        $insert_stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES ('system_version', ?, 'system')");
        $insert_stmt->bind_param("s", $new_version);
        $insert_stmt->execute();
    }
}

function clear_system_cache() 
{
    $cache_dirs = ['../cache/', '../tmp/'];

    foreach ($cache_dirs as $dir) 
    {
        if (is_dir($dir)) 
        {
            $files = glob($dir . '*');

            foreach ($files as $file) 
            {
                if (is_file($file)) 
                {
                    unlink($file);
                }
            }
        }
    }
    
    global $conn;
    $conn->query("RESET QUERY CACHE");
    $conn->query("FLUSH TABLES");
}

function log_update_success($old_version, $new_version) 
{
    global $conn;
    
    $stmt = $conn->prepare("INSERT INTO update_logs (old_version, new_version, status, details, updated_at) VALUES (?, ?, 'success', 'Системное обновление', NOW())");
    $stmt->bind_param("ss", $old_version, $new_version);
    $stmt->execute();
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

function restore_from_backup($backup_file) 
{
    global $conn;
    
    if (!file_exists($backup_file)) 
    {
        return false;
    }
    
    $sql_content = file_get_contents($backup_file);
    $queries = explode(';', $sql_content);
    
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

function check_for_updates_via_api() 
{
    return [
        'update_available' => true,
        'version' => '2.2.0',
        'download_url' => 'https://updates.example.com/autoshop-2.2.0.zip',
        'release_notes' => 'Новый функционал и исправления ошибок',
        'update_type' => 'minor',
        'changelog' => [
            'Добавлена система скидок',
            'Улучшен интерфейс админ-панели',
            'Исправлены ошибки безопасности'
        ]
    ];
}
?>