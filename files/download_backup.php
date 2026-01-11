<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

$filename = $_GET['file'] ?? '';

if (empty($filename)) 
{
    header("Location: ../admin.php?section=settings&tab=backup&error=Файл не указан");
    exit();
}

if (!preg_match('/^backup_[0-9]{4}-[0-9]{2}-[0-9]{2}_[0-9]{2}-[0-9]{2}-[0-9]{2}\.(sql|gz)$/', $filename)) 
{
    header("Location: ../admin.php?section=settings&tab=backup&error=Некорректное имя файла");
    exit();
}

$filepath = '../backups/' . $filename;

if (!file_exists($filepath) || !is_file($filepath)) 
{
    header("Location: ../admin.php?section=settings&tab=backup&error=Файл не найден");
    exit();
}

$extension = pathinfo($filename, PATHINFO_EXTENSION);
$mime_types = [
    'sql' => 'application/sql',
    'gz' => 'application/gzip'
];

$mime_type = $mime_types[$extension] ?? 'application/octet-stream';

$log_stmt = $conn->prepare("UPDATE backup_logs SET downloads = downloads + 1, last_download = NOW() WHERE filename = ?");
$log_stmt->bind_param("s", $filename);
$log_stmt->execute();

header('Content-Description: File Transfer');
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

ob_clean();
flush();

readfile($filepath);
exit;
?>