<?php
session_start();
require_once("../config/link.php");

if (!isset($_GET['file']) || empty($_GET['file'])) 
{
    die('Файл не указан');
}

$allowed_files = [
    'garanty_talon' => 'Гарантийный_талон.pdf',
    'garanty_rules' => 'Правила_гарантийного_обслуживания.pdf',
    'claim_form' => 'Бланк_рекламации.pdf'
];

$file_key = $_GET['file'];

if (!array_key_exists($file_key, $allowed_files)) 
{
    die('Неверный запрос');
}

$file_path = __DIR__ . '/../uploads/docs/' . $file_key . '.pdf';
$file_name = $allowed_files[$file_key];

if (!file_exists($file_path)) 
{
    $html_path = __DIR__ . '/../uploads/docs/' . $file_key . '.html';

    if (file_exists($html_path)) 
    {
        $file_path = $html_path;
        $file_name = str_replace('.pdf', '.html', $file_name);
    } 
    else 
    {
        die('Файл не найден');
    }
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

readfile($file_path);
exit;