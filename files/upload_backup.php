<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);

if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] != UPLOAD_ERR_OK) 
{
    $error_message = 'Файл не был загружен';

    if (isset($_FILES['backup_file']['error'])) 
    {
        switch ($_FILES['backup_file']['error']) 
        {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_message = 'Файл слишком большой. Максимальный размер: 100MB';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message = 'Файл был загружен только частично';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message = 'Файл не был выбран';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error_message = 'Отсутствует временная папка';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error_message = 'Не удалось записать файл на диск';
                break;
            case UPLOAD_ERR_EXTENSION:
                $error_message = 'Расширение PHP остановило загрузку файла';
                break;
        }
    }
    
    echo json_encode(['success' => false, 'error' => $error_message]);
    exit();
}

$allowed_extensions = ['sql', 'gz', 'zip'];
$max_file_size = 100 * 1024 * 1024;

$file_name = $_FILES['backup_file']['name'];
$file_size = $_FILES['backup_file']['size'];
$file_tmp = $_FILES['backup_file']['tmp_name'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if (!in_array($file_ext, $allowed_extensions)) 
{
    echo json_encode(['success' => false, 'error' => 'Недопустимый формат файла. Разрешены только .sql, .gz, .zip']);
    exit();
}

if ($file_size > $max_file_size) 
{
    echo json_encode(['success' => false, 'error' => 'Файл слишком большой (' . format_size($file_size) . '). Максимальный размер: ' . format_size($max_file_size)]);
    exit();
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file_tmp);
finfo_close($finfo);

$allowed_mime_types = [
    'application/sql',
    'application/x-sql',
    'text/plain',
    'text/x-sql',
    'application/gzip',
    'application/x-gzip',
    'application/zip',
    'application/x-zip-compressed',
    'application/x-zip'
];

if (!in_array($mime_type, $allowed_mime_types)) 
{
    echo json_encode(['success' => false, 'error' => 'Недопустимый тип файла: ' . $mime_type]);
    exit();
}

$timestamp = date('Y-m-d_H-i-s');
$safe_name = preg_replace('/[^a-zA-Z0-9\._-]/', '_', pathinfo($file_name, PATHINFO_FILENAME));
$new_filename = "uploaded_backup_{$safe_name}_{$timestamp}.{$file_ext}";
$upload_dir = '../backups/uploads/';

if (!is_dir($upload_dir)) 
{
    if (!mkdir($upload_dir, 0755, true)) 
    {
        echo json_encode(['success' => false, 'error' => 'Не удалось создать директорию для загрузки']);
        exit();
    }
}

$destination = $upload_dir . $new_filename;

if (move_uploaded_file($file_tmp, $destination)) 
{
    chmod($destination, 0644);

    if (!verify_backup_file($destination, $file_ext)) 
    {
        unlink($destination);
        echo json_encode(['success' => false, 'error' => 'Файл поврежден или не является корректной резервной копией']);
        exit();
    }

    try 
    {
        $log_stmt = $conn->prepare("INSERT INTO backup_logs (filename, file_size, status, source, created_at) VALUES (?, ?, 'uploaded', 'user_upload', NOW())");
        $log_stmt->bind_param("si", $new_filename, $file_size);
        $log_stmt->execute();
    } 
    catch (Exception $e) 
    {
        error_log("Ошибка логирования загрузки: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'filename' => $new_filename,
        'original_name' => $file_name,
        'size' => format_size($file_size),
        'size_bytes' => $file_size,
        'message' => 'Файл успешно загружен и проверен'
    ]);
} 
else 
{
    echo json_encode(['success' => false, 'error' => 'Не удалось сохранить файл. Проверьте права на запись в директорию backups/uploads/']);
}

function verify_backup_file($filepath, $extension) 
{
    $max_check_size = 10 * 1024 * 1024;
    $file_size = filesize($filepath);
    $check_size = min($file_size, $max_check_size);
    
    if ($extension === 'gz') 
    {
        if (!function_exists('gzopen')) 
        {
            return false;
        }

        $handle = fopen($filepath, 'rb');
        $header = fread($handle, 2);
        fclose($handle);
        
        if ($header !== "\x1f\x8b") 
        {
            return false;
        }

        $gz = @gzopen($filepath, 'r');

        if (!$gz) 
        {
            return false;
        }

        $content = gzread($gz, 5000);
        gzclose($gz);
        
        return strpos($content, 'CREATE TABLE') !== false || strpos($content, 'INSERT INTO') !== false || strpos($content, 'DROP TABLE') !== false;
        
    } 
    else if ($extension === 'sql') 
    {
        $handle = fopen($filepath, 'r');
        $content = fread($handle, $check_size);
        fclose($handle);
        
        return strpos($content, 'CREATE TABLE') !== false || strpos($content, 'INSERT INTO') !== false || strpos($content, 'DROP TABLE') !== false;
        
    } 
    else if ($extension === 'zip') 
    {
        if (!class_exists('ZipArchive')) 
        {
            return false;
        }
        
        $zip = new ZipArchive();

        if ($zip->open($filepath) !== true) 
        {
            return false;
        }
        
        $has_sql = false;

        for ($i = 0; $i < $zip->numFiles; $i++) 
        {
            $filename = $zip->getNameIndex($i);

            if (pathinfo($filename, PATHINFO_EXTENSION) === 'sql') 
            {
                $has_sql = true;
                break;
            }
        }
        
        $zip->close();
        return $has_sql;
    }
    
    return false;
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