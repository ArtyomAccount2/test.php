<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

$query = "SELECT * FROM shops ORDER BY region, name";
$result = $conn->query($query);

$filename = 'shops_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

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
    'Дата создания'
], ';');

while ($shop = $result->fetch_assoc()) 
{
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
        date('d.m.Y H:i', strtotime($shop['created_at']))
    ], ';');
}

fclose($output);
$conn->close();
?>