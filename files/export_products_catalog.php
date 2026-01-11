<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? 'all';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';
$quantity_filter = $_GET['quantity_filter'] ?? '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) 
{
    $where_conditions[] = "(name LIKE ? OR description LIKE ? OR article LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= 'sss';
}

if ($category_filter !== 'all') 
{
    $where_conditions[] = "category = ?";
    $params[] = $category_filter;
    $types .= 's';
}

if (!empty($price_min) && is_numeric($price_min)) 
{
    $where_conditions[] = "price >= ?";
    $params[] = $price_min;
    $types .= 'd';
}

if (!empty($price_max) && is_numeric($price_max)) 
{
    $where_conditions[] = "price <= ?";
    $params[] = $price_max;
    $types .= 'd';
}

if (!empty($quantity_filter)) 
{
    switch ($quantity_filter) 
    {
        case 'available':
            $where_conditions[] = "quantity > 0";
            break;
        case 'low':
            $where_conditions[] = "quantity BETWEEN 1 AND 10";
            break;
        case 'out_of_stock':
            $where_conditions[] = "quantity = 0";
            break;
    }
}

$where_sql = '';
if (!empty($where_conditions)) 
{
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=products_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'ID',
    'Название товара',
    'Артикул',
    'Категория',
    'Описание',
    'Цена (₽)',
    'Количество',
    'Статус',
    'Дата добавления'
], ';');

$query = "SELECT * FROM products $where_sql ORDER BY id DESC";
$stmt = $conn->prepare($query);

if (!empty($params)) 
{
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

function getStatusText($status) 
{
    $statuses = [
        'available' => 'В наличии',
        'low' => 'Мало',
        'out_of_stock' => 'Нет в наличии'
    ];
    
    return $statuses[$status] ?? $status;
}

while ($product = $result->fetch_assoc()) 
{
    fputcsv($output, [
        $product['id'],
        html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8'),
        $product['article'] ?? '',
        $product['category'] ?? '',
        html_entity_decode(substr($product['description'] ?? '', 0, 500), ENT_QUOTES, 'UTF-8'),
        number_format($product['price'], 2, '.', ''),
        $product['quantity'] ?? 0,
        getStatusText($product['status'] ?? 'available'),
        $product['created_at'] ?? ''
    ], ';');
}

fclose($output);
exit();
?>