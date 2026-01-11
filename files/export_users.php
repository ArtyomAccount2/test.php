<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=users_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'ID',
    'Логин',
    'Фамилия',
    'Имя',
    'Отчество',
    'Email',
    'Телефон',
    'Тип',
    'Организация',
    'ИНН',
    'Регион',
    'Город',
    'Адрес',
    'Карта скидок',
    'Дата регистрации'
], ';');

$stmt = $conn->prepare("SELECT * FROM users WHERE login_users != 'admin' ORDER BY id_users DESC");
$stmt->execute();
$result = $stmt->get_result();

while ($user = $result->fetch_assoc()) 
{
    fputcsv($output, [
        $user['id_users'],
        $user['login_users'],
        $user['surname_users'] ?? '',
        $user['name_users'] ?? '',
        $user['patronymic_users'] ?? '',
        $user['email_users'] ?? '',
        $user['phone_users'] ?? '',
        $user['user_type'] ?? 'physical',
        $user['organization_users'] ?? '',
        $user['TIN_users'] ?? '',
        $user['region_users'] ?? '',
        $user['city_users'] ?? '',
        $user['address_users'] ?? '',
        $user['discountСardNumber_users'] ?? '',
        $user['created_at'] ?? ''
    ], ';');
}

fclose($output);
exit();
?>