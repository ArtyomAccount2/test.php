<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    header("Location: ../index.php");
    exit();
}

ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

$query = "SELECT * FROM shops ORDER BY region, name";
$result = $conn->query($query);

if (!$result) 
{
    die("Ошибка запроса: " . $conn->error);
}

$filename = 'shops_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, [
    'ID', 
    'Название', 
    'Тип', 
    'Регион', 
    'Телефон', 
    'Email', 
    'Адрес', 
    'Площадь (м²)', 
    'Сотрудников', 
    'Статус',
    'Услуги',
    'Парковка',
    'Расписание',
    'Дата создания'
], ';');

$type_labels = [
    'main' => 'Основной',
    'branch' => 'Филиал', 
    'partner' => 'Партнёрский'
];

$status_labels = [
    'active' => 'Активен',
    'inactive' => 'Неактивен',
    'maintenance' => 'На обслуживании'
];

while ($shop = $result->fetch_assoc()) 
{
    $services = '';

    if (!empty($shop['services'])) 
    {
        $services_array = explode(',', $shop['services']);
        $services = implode(', ', $services_array);
    }

    $created_at = '';

    if (!empty($shop['created_at'])) 
    {
        $created_at = date('d.m.Y H:i', strtotime($shop['created_at']));
    }

    fputcsv($output, [
        $shop['id'],
        $shop['name'],
        $type_labels[$shop['type'] ?? 'branch'],
        $shop['region'],
        $shop['phone'],
        $shop['email'] ?: '',
        $shop['address'],
        $shop['area'] ?: '0',
        $shop['employees'] ?: '0',
        $status_labels[$shop['status'] ?? 'active'],
        $services,
        $shop['parking'] ?: '',
        $shop['schedule'] ?: '',
        $created_at
    ], ';');
}

fclose($output);
$conn->close();
exit();
?>