<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    header("Location: ../index.php");
    exit();
}

ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

$check_table = $conn->query("SHOW TABLES LIKE 'users'");

if ($check_table->num_rows == 0) 
{
    die("Таблица users не существует");
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="users_' . date('Y-m-d') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, [
    'ID',
    'Логин',
    'Фамилия',
    'Имя',
    'Отчество',
    'Email',
    'Телефон',
    'Тип пользователя',
    'Организация',
    'ИНН',
    'Регион',
    'Город',
    'Адрес',
    'Номер карты скидок',
], ';');

$user_types = [
    'physical' => 'Физическое лицо',
    'legal' => 'Юридическое лицо',
    'entrepreneur' => 'Индивидуальный предприниматель'
];

$stmt = $conn->prepare("SELECT * FROM users WHERE login_users != 'admin' ORDER BY id_users DESC");
$stmt->execute();
$result = $stmt->get_result();

while ($user = $result->fetch_assoc()) 
{
    $login = strip_tags($user['login_users'] ?? '');
    $surname = strip_tags($user['surname_users'] ?? '');
    $name = strip_tags($user['name_users'] ?? '');
    $patronymic = strip_tags($user['patronymic_users'] ?? '');
    $email = strip_tags($user['email_users'] ?? '');
    $phone = strip_tags($user['phone_users'] ?? '');
    $organization = strip_tags($user['organization_users'] ?? '');
    $tin = strip_tags($user['TIN_users'] ?? '');
    $region = strip_tags($user['region_users'] ?? '');
    $city = strip_tags($user['city_users'] ?? '');
    $address = strip_tags($user['address_users'] ?? '');
    $discount_card = strip_tags($user['discountСardNumber_users'] ?? '');
    
    fputcsv($output, [
        $user['id_users'],
        $login,
        $surname,
        $name,
        $patronymic,
        $email,
        $phone,
        $user_types[$user['user_type'] ?? 'physical'],
        $organization,
        $tin,
        $region,
        $city,
        $address,
        $discount_card,
    ], ';');
}

fclose($output);
$stmt->close();
$conn->close();
exit();
?>