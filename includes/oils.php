<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart']) && isset($_SESSION['loggedin'])) 
{
    $productId = $_POST['product_id'] ?? 0;
    $productName = $_POST['product_name'] ?? '';
    $productImage = $_POST['product_image'] ?? '../img/no-image.png';
    $price = $_POST['price'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    $username = $_SESSION['user'];
    $userSql = "SELECT id_users FROM users WHERE CONCAT(surname_users, ' ', name_users, ' ', patronymic_users) = ? OR person_users = ?";
    $userStmt = $conn->prepare($userSql);
    $userStmt->bind_param("ss", $username, $username);
    $userStmt->execute();
    $userResult = $userStmt->get_result();
    
    if ($userResult->num_rows > 0) 
    {
        $userData = $userResult->fetch_assoc();
        $userId = $userData['id_users'];
        
        if ($productName && $price > 0) 
        {
            $checkSql = "SELECT * FROM cart WHERE user_id = ? AND product_name = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("is", $userId, $productName);
            $checkStmt->execute();
            $existingItem = $checkStmt->get_result()->fetch_assoc();
            $checkStmt->close();
            
            if ($existingItem) 
            {
                $updateSql = "UPDATE cart SET quantity = quantity + ? WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("ii", $quantity, $existingItem['id']);
                $updateStmt->execute();
                $updateStmt->close();
                $_SESSION['success_message'] = "Товар добавлен в корзину!";
            } 
            else 
            {
                $insertSql = "INSERT INTO cart (user_id, product_id, product_name, product_image, price, quantity) VALUES (?, ?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("iissdi", $userId, $productId, $productName, $productImage, $price, $quantity);
                $insertStmt->execute();
                $insertStmt->close();
                $_SESSION['success_message'] = "Товар добавлен в корзину!";
            }
        }
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $login = $_POST['login'];
    $password = $_POST['password'];
    $redirect_url = $_POST['redirect_url'] ?? $_SERVER['REQUEST_URI'];

    if (strtolower($login) === 'admin' && strtolower($password) === 'admin') 
    {
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = 'admin';
        unset($_SESSION['login_error']);
        unset($_SESSION['error_message']);
        header("Location: ../admin.php");
        exit();
    }
    else
    {
        $stmt = $conn->prepare("SELECT * FROM users WHERE LOWER(login_users) = LOWER(?) AND LOWER(password_users) = LOWER(?)");
        $stmt->bind_param("ss", $login, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
        {
            $row = $result->fetch_assoc();
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = !empty($row['surname_users']) ? $row['surname_users'] . " " . $row['name_users'] . " " . $row['patronymic_users'] : $row['person_users'];
            unset($_SESSION['login_error']);
            unset($_SESSION['error_message']);
            header("Location: " . $redirect_url);
            exit();
        } 
        else 
        {
            $_SESSION['login_error'] = true;
            $_SESSION['error_message'] = "Неверный логин или пароль!";
            $_SESSION['form_data'] = $_POST;
            header("Location: " . $redirect_url);
            exit();
        }
    }
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$products = [
    ['id' => 1, 'title' => 'Castrol EDGE 5W-30', 'art' => '15698E4', 'volume' => '4 л', 'price' => 3890, 'stock' => true, 'hit' => true, 'brand' => 'Castrol', 'viscosity' => '5W-30', 'type' => 'Синтетическое'],
    ['id' => 2, 'title' => 'Mobil Super 3000 X1 5W-40', 'art' => '152343', 'volume' => '4 л', 'price' => 3450, 'stock' => true, 'hit' => false, 'brand' => 'Mobil', 'viscosity' => '5W-40', 'type' => 'Синтетическое'],
    ['id' => 3, 'title' => 'Liqui Moly Special Tec AA 5W-30', 'art' => '1123DE', 'volume' => '5 л', 'price' => 4210, 'stock' => true, 'hit' => true, 'brand' => 'Liqui Moly', 'viscosity' => '5W-30', 'type' => 'Синтетическое'],
    ['id' => 4, 'title' => 'Shell Helix HX7 10W-40', 'art' => '87654F', 'volume' => '4 л', 'price' => 2890, 'stock' => false, 'hit' => false, 'brand' => 'Shell', 'viscosity' => '10W-40', 'type' => 'Полусинтетическое'],
    ['id' => 5, 'title' => 'Total Quartz 9000 5W-40', 'art' => 'TQ9000', 'volume' => '5 л', 'price' => 3650, 'stock' => true, 'hit' => false, 'brand' => 'Total', 'viscosity' => '5W-40', 'type' => 'Синтетическое'],
    ['id' => 6, 'title' => 'Motul 8100 X-clean 5W-30', 'art' => 'M8100', 'volume' => '5 л', 'price' => 4890, 'stock' => true, 'hit' => true, 'brand' => 'Motul', 'viscosity' => '5W-30', 'type' => 'Синтетическое'],
    ['id' => 7, 'title' => 'ZIC X9 5W-30', 'art' => 'ZX9-5W30', 'volume' => '4 л', 'price' => 2990, 'stock' => true, 'hit' => false, 'brand' => 'ZIC', 'viscosity' => '5W-30', 'type' => 'Синтетическое'],
    ['id' => 8, 'title' => 'ELF Evolution 900 NF 5W-40', 'art' => 'ELF900', 'volume' => '5 л', 'price' => 3750, 'stock' => true, 'hit' => false, 'brand' => 'ELF', 'viscosity' => '5W-40', 'type' => 'Синтетическое'],
    ['id' => 9, 'title' => 'Castrol MAGNATEC 5W-30', 'art' => 'CAST567', 'volume' => '4 л', 'price' => 3250, 'stock' => true, 'hit' => true, 'brand' => 'Castrol', 'viscosity' => '5W-30', 'type' => 'Синтетическое'],
    ['id' => 10, 'title' => 'Mobil 1 0W-40', 'art' => 'MOB1-0W40', 'volume' => '4 л', 'price' => 4450, 'stock' => true, 'hit' => false, 'brand' => 'Mobil', 'viscosity' => '0W-40', 'type' => 'Синтетическое'],
    ['id' => 11, 'title' => 'Liqui Moly Molygen 5W-40', 'art' => 'LM-MOLY', 'volume' => '5 л', 'price' => 5120, 'stock' => true, 'hit' => true, 'brand' => 'Liqui Moly', 'viscosity' => '5W-40', 'type' => 'Синтетическое'],
    ['id' => 12, 'title' => 'Shell Helix Ultra 5W-40', 'art' => 'SHU-5W40', 'volume' => '4 л', 'price' => 3980, 'stock' => true, 'hit' => false, 'brand' => 'Shell', 'viscosity' => '5W-40', 'type' => 'Синтетическое'],
    ['id' => 13, 'title' => 'Total Quartz INEO ECS 5W-30', 'art' => 'TQ-ECS', 'volume' => '5 л', 'price' => 4120, 'stock' => false, 'hit' => false, 'brand' => 'Total', 'viscosity' => '5W-30', 'type' => 'Синтетическое'],
    ['id' => 14, 'title' => 'Motul 8100 Eco-nergy 5W-30', 'art' => 'MOT-ECO', 'volume' => '5 л', 'price' => 4670, 'stock' => true, 'hit' => true, 'brand' => 'Motul', 'viscosity' => '5W-30', 'type' => 'Синтетическое'],
    ['id' => 15, 'title' => 'ZIC X7 10W-40', 'art' => 'ZX7-10W40', 'volume' => '4 л', 'price' => 2450, 'stock' => true, 'hit' => false, 'brand' => 'ZIC', 'viscosity' => '10W-40', 'type' => 'Полусинтетическое'],
    ['id' => 16, 'title' => 'ELF Evolution 700 STI 10W-40', 'art' => 'ELF700', 'volume' => '4 л', 'price' => 2780, 'stock' => true, 'hit' => false, 'brand' => 'ELF', 'viscosity' => '10W-40', 'type' => 'Полусинтетическое'],
    ['id' => 17, 'title' => 'Castrol EDGE 0W-20', 'art' => 'CAST-0W20', 'volume' => '4 л', 'price' => 4120, 'stock' => true, 'hit' => true, 'brand' => 'Castrol', 'viscosity' => '0W-20', 'type' => 'Синтетическое'],
    ['id' => 18, 'title' => 'Mobil Super 2000 10W-40', 'art' => 'MS2000', 'volume' => '4 л', 'price' => 2670, 'stock' => true, 'hit' => false, 'brand' => 'Mobil', 'viscosity' => '10W-40', 'type' => 'Полусинтетическое'],
    ['id' => 19, 'title' => 'Liqui Moly Leichtlauf 10W-40', 'art' => 'LM-LEICHT', 'volume' => '5 л', 'price' => 3890, 'stock' => true, 'hit' => false, 'brand' => 'Liqui Moly', 'viscosity' => '10W-40', 'type' => 'Синтетическое'],
    ['id' => 20, 'title' => 'Shell Helix HX8 5W-30', 'art' => 'SH-HX8', 'volume' => '4 л', 'price' => 3450, 'stock' => true, 'hit' => true, 'brand' => 'Shell', 'viscosity' => '5W-30', 'type' => 'Синтетическое'],
    ['id' => 21, 'title' => 'Total Quartz 7000 10W-40', 'art' => 'TQ7000', 'volume' => '4 л', 'price' => 2780, 'stock' => true, 'hit' => false, 'brand' => 'Total', 'viscosity' => '10W-40', 'type' => 'Полусинтетическое'],
    ['id' => 22, 'title' => 'Motul 8100 X-clean+ 5W-30', 'art' => 'MOT-CLEAN+', 'volume' => '5 л', 'price' => 5120, 'stock' => true, 'hit' => true, 'brand' => 'Motul', 'viscosity' => '5W-30', 'type' => 'Синтетическое'],
    ['id' => 23, 'title' => 'ZIC X5 10W-40', 'art' => 'ZX5-10W40', 'volume' => '4 л', 'price' => 2230, 'stock' => true, 'hit' => false, 'brand' => 'ZIC', 'viscosity' => '10W-40', 'type' => 'Минеральное'],
    ['id' => 24, 'title' => 'ELF Evolution SXR 5W-30', 'art' => 'ELF-SXR', 'volume' => '5 л', 'price' => 3980, 'stock' => true, 'hit' => false, 'brand' => 'ELF', 'viscosity' => '5W-30', 'type' => 'Синтетическое']
];

$items_per_page = 8;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$search_query = $_GET['search'] ?? '';
$sort_type = $_GET['sort'] ?? 'default';
$brand_filter = $_GET['brand'] ?? '';
$viscosity_filter = $_GET['viscosity'] ?? '';
$type_filter = $_GET['type'] ?? '';
$volume_filter = $_GET['volume'] ?? '';

$filtered_products = $products;

if (!empty($search_query)) 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($search_query) 
    {
        return stripos($product['title'], $search_query) !== false || stripos($product['art'], $search_query) !== false || stripos($product['brand'], $search_query) !== false;
    });
}

if (!empty($brand_filter) && $brand_filter !== 'Все бренды') 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($brand_filter) 
    {
        return $product['brand'] === $brand_filter;
    });
}

if (!empty($viscosity_filter) && $viscosity_filter !== 'Все') 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($viscosity_filter) 
    {
        return $product['viscosity'] === $viscosity_filter;
    });
}

if (!empty($type_filter) && $type_filter !== 'Все') 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($type_filter) 
    {
        return $product['type'] === $type_filter;
    });
}

if (!empty($volume_filter) && $volume_filter !== 'Все') 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($volume_filter) 
    {
        return $product['volume'] === $volume_filter;
    });
}

$filtered_products = array_values($filtered_products);

switch ($sort_type) 
{
    case 'price_asc':
        usort($filtered_products, function($a, $b) 
        {
            return $a['price'] - $b['price'];
        });
        break;
    case 'price_desc':
        usort($filtered_products, function($a, $b) 
        {
            return $b['price'] - $a['price'];
        });
        break;
    case 'name':
        usort($filtered_products, function($a, $b) 
        {
            return strcmp($a['title'], $b['title']);
        });
        break;
    case 'popular':
        usort($filtered_products, function($a, $b) 
        {
            if ($a['hit'] == $b['hit']) 
            {
                return 0;
            }

            return $a['hit'] ? -1 : 1;
        });
        break;
    default:
        break;
}

$total_items = count($filtered_products);
$total_pages = ceil($total_items / $items_per_page);
$start_index = ($current_page - 1) * $items_per_page;
$end_index = min($start_index + $items_per_page, $total_items);

$show_pagination = $total_pages > 1;

$current_page_products = array_slice($filtered_products, $start_index, $items_per_page);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Масла и тех. жидкости - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/notifications-styles.css">
    <link rel="stylesheet" href="../css/oils-styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        <?php 
        if (isset($_SESSION['login_error'])) 
        { 
        ?>
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();

            <?php unset($_SESSION['login_error']); ?>
        <?php 
        } 
        ?>
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5 pt-4">
    <div class="row mb-4 align-items-center" style="padding-top: 75px;">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold text-primary mb-3">Масла и технические жидкости</h1>
            <p class="lead text-muted mb-4">Широкий ассортимент качественных масел и жидкостей для вашего автомобиля</p>
            <?php 
            if (!empty($search_query) || !empty($brand_filter) || !empty($viscosity_filter) || !empty($type_filter) || !empty($volume_filter))
            {
                $filters_applied = [];
                if (!empty($search_query)) $filters_applied[] = 'поиск: "' . htmlspecialchars($search_query) . '"';
                if (!empty($brand_filter) && $brand_filter !== 'Все бренды') $filters_applied[] = 'бренд: ' . htmlspecialchars($brand_filter);
                if (!empty($viscosity_filter) && $viscosity_filter !== 'Все') $filters_applied[] = 'вязкость: ' . htmlspecialchars($viscosity_filter);
                if (!empty($type_filter) && $type_filter !== 'Все') $filters_applied[] = 'тип: ' . htmlspecialchars($type_filter);
                if (!empty($volume_filter) && $volume_filter !== 'Все') $filters_applied[] = 'объем: ' . htmlspecialchars($volume_filter);
            ?>
                <p class="text-muted mt-2">
                    Найдено <?php echo $total_items; ?> товаров 
                    <?php 
                    if (!empty($filters_applied))
                    {
                    ?>
                        (<?php echo implode(', ', $filters_applied); ?>)
                    <?php 
                    }    
                    ?>
                    <?php 
                    if (!empty($search_query) || !empty($brand_filter) || !empty($viscosity_filter) || !empty($type_filter) || !empty($volume_filter)) 
                    {
                    ?>
                        <a href="oils.php?sort=default&page=1" class="btn btn-sm btn-outline-secondary ms-2">Показать все</a>
                    <?php 
                    } 
                    ?>
                </p>
            <?php 
            } 
            ?>
        </div>
        <div class="col-md-4 text-md-end">
            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#oilSelectorModal">
                <i class="bi bi-question-circle me-2"></i>Подобрать масло
            </button>
         </div>
    </div>
    <div class="oil-categories mb-5">
        <h2 class="mb-4"><i class="bi bi-filter-square"></i> Категории</h2>
        <div class="row g-4">
            <?php
            $base_url = '../files/categories/';

            $categories = [
                ['icon' => 'bi-droplet', 'title' => 'Моторные масла', 'count' => '124 товара', 'color' => 'dark', 'link' => $base_url . 'motor-oils.php?sort=default&page=1'],
                ['icon' => 'bi-gear', 'title' => 'Трансмиссионные масла', 'count' => '56 товаров', 'color' => 'success', 'link' => $base_url . 'transmission-oils.php?sort=default&page=1'],
                ['icon' => 'bi-snow', 'title' => 'Тормозные жидкости', 'count' => '23 товара', 'color' => 'info', 'link' => $base_url . 'brake-fluids.php?sort=default&page=1'],
                ['icon' => 'bi-water', 'title' => 'Охлаждающие жидкости', 'count' => '34 товара', 'color' => 'primary', 'link' => $base_url . 'cooling-fluids.php?sort=default&page=1'],
                ['icon' => 'bi-wind', 'title' => 'Жидкости ГУР', 'count' => '18 товаров', 'color' => 'dark', 'link' => $base_url . 'power-steering-fluids.php?sort=default&page=1'],
                ['icon' => 'bi-droplet-half', 'title' => 'Антифризы', 'count' => '42 товара', 'color' => 'secondary', 'link' => $base_url . 'antifreeze.php?sort=default&page=1'],
                ['icon' => 'bi-brightness-high', 'title' => 'Специальные жидкости', 'count' => '31 товар', 'color' => 'warning', 'link' => $base_url . 'special-fluids.php?sort=default&page=1'],
                ['icon' => 'bi-archive', 'title' => 'Комплекты', 'count' => '15 товаров', 'color' => 'primary', 'link' => $base_url . 'kits.php?sort=default&page=1']
            ];
            
            foreach ($categories as $category) 
            {
                echo '
                <div class="col-lg-3 col-md-6">
                    <div class="category-card card h-100 shadow">
                        <div class="card-body text-center">
                            <div class="category-icon text-'.$category['color'].' mb-3">
                                <i class="bi '.$category['icon'].'" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="category-title card-title">'.$category['title'].'</h5>
                            <div class="category-count text-muted small">'.$category['count'].'</div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 text-center">
                            <a href="'.$category['link'].'" class="btn btn-sm btn-outline-'.$category['color'].' stretched-link">Смотреть</a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
    <div class="filter-section mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-funnel"></i> Фильтры</h2>
            <div>
                <a href="?" class="btn btn-sm btn-outline-secondary me-2" id="resetFilters">Сбросить все</a>
                <button class="btn btn-sm btn-primary" type="button" id="applyFiltersBtn">Применить фильтры</button>
            </div>
        </div>
        <form method="GET" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="filter-group mb-3">
                        <label class="form-label filter-title">Бренд</label>
                        <select class="form-select filter-select" name="brand">
                            <option value="">Все бренды</option>
                            <option value="Castrol" <?php echo $brand_filter === 'Castrol' ? 'selected' : ''; ?>>Castrol</option>
                            <option value="Mobil" <?php echo $brand_filter === 'Mobil' ? 'selected' : ''; ?>>Mobil</option>
                            <option value="Liqui Moly" <?php echo $brand_filter === 'Liqui Moly' ? 'selected' : ''; ?>>Liqui Moly</option>
                            <option value="Shell" <?php echo $brand_filter === 'Shell' ? 'selected' : ''; ?>>Shell</option>
                            <option value="Total" <?php echo $brand_filter === 'Total' ? 'selected' : ''; ?>>Total</option>
                            <option value="Motul" <?php echo $brand_filter === 'Motul' ? 'selected' : ''; ?>>Motul</option>
                            <option value="ZIC" <?php echo $brand_filter === 'ZIC' ? 'selected' : ''; ?>>ZIC</option>
                            <option value="ELF" <?php echo $brand_filter === 'ELF' ? 'selected' : ''; ?>>ELF</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="filter-group mb-3">
                        <label class="form-label filter-title">Вязкость</label>
                        <select class="form-select filter-select" name="viscosity">
                            <option value="">Все</option>
                            <option value="0W-20" <?php echo $viscosity_filter === '0W-20' ? 'selected' : ''; ?>>0W-20</option>
                            <option value="0W-30" <?php echo $viscosity_filter === '0W-30' ? 'selected' : ''; ?>>0W-30</option>
                            <option value="0W-40" <?php echo $viscosity_filter === '0W-40' ? 'selected' : ''; ?>>0W-40</option>
                            <option value="5W-30" <?php echo $viscosity_filter === '5W-30' ? 'selected' : ''; ?>>5W-30</option>
                            <option value="5W-40" <?php echo $viscosity_filter === '5W-40' ? 'selected' : ''; ?>>5W-40</option>
                            <option value="10W-40" <?php echo $viscosity_filter === '10W-40' ? 'selected' : ''; ?>>10W-40</option>
                            <option value="15W-40" <?php echo $viscosity_filter === '15W-40' ? 'selected' : ''; ?>>15W-40</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="filter-group mb-3">
                        <label class="form-label filter-title">Тип</label>
                        <select class="form-select filter-select" name="type">
                            <option value="">Все</option>
                            <option value="Синтетическое" <?php echo $type_filter === 'Синтетическое' ? 'selected' : ''; ?>>Синтетическое</option>
                            <option value="Полусинтетическое" <?php echo $type_filter === 'Полусинтетическое' ? 'selected' : ''; ?>>Полусинтетическое</option>
                            <option value="Минеральное" <?php echo $type_filter === 'Минеральное' ? 'selected' : ''; ?>>Минеральное</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="filter-group mb-3">
                        <label class="form-label filter-title">Объем</label>
                        <select class="form-select filter-select" name="volume">
                            <option value="">Все</option>
                            <option value="1 л" <?php echo $volume_filter === '1 л' ? 'selected' : ''; ?>>1 л</option>
                            <option value="4 л" <?php echo $volume_filter === '4 л' ? 'selected' : ''; ?>>4 л</option>
                            <option value="5 л" <?php echo $volume_filter === '5 л' ? 'selected' : ''; ?>>5 л</option>
                            <option value="20 л" <?php echo $volume_filter === '20 л' ? 'selected' : ''; ?>>20 л</option>
                            <option value="60 л" <?php echo $volume_filter === '60 л' ? 'selected' : ''; ?>>60 л</option>
                        </select>
                    </div>
                </div>
            </div>
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
            <input type="hidden" name="sort" value="<?php echo $sort_type; ?>">
            <input type="hidden" name="page" value="1">
        </form>
    </div>
    <div class="products-section mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-box-seam"></i> Товары <span class="badge bg-secondary"><?php echo $total_items; ?></span></h2>
            <div class="d-flex">
                <div class="dropdown me-2">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php
                        $sort_labels = [
                            'default' => 'По умолчанию',
                            'popular' => 'По популярности',
                            'price_asc' => 'По цене (возрастание)',
                            'price_desc' => 'По цене (убывание)',
                            'name' => 'По названию'
                        ];
                        echo $sort_labels[$sort_type] ?? 'Сортировка';
                        ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(['page' => 1, 'sort' => 'default']); ?>">По умолчанию</a></li>
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(['page' => 1, 'sort' => 'popular']); ?>">По популярности</a></li>
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(['page' => 1, 'sort' => 'price_asc']); ?>">По цене (возрастание)</a></li>
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(['page' => 1, 'sort' => 'price_desc']); ?>">По цене (убывание)</a></li>
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(['page' => 1, 'sort' => 'name']); ?>">По названию</a></li>
                    </ul>
                </div>
                <form method="GET" class="d-flex">
                    <div class="input-group" style="width: 200px;">
                        <input type="text" class="form-control" name="search" placeholder="Поиск..." value="<?php echo htmlspecialchars($search_query); ?>">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                        <?php 
                        if (!empty($search_query))
                        {
                        ?>
                            <a href="?<?php echo buildQueryString(['search' => '', 'page' => 1]); ?>" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                        <?php
                        }
                        ?>
                    </div>
                    <input type="hidden" name="sort" value="<?php echo $sort_type; ?>">
                    <input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand_filter); ?>">
                    <input type="hidden" name="viscosity" value="<?php echo htmlspecialchars($viscosity_filter); ?>">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type_filter); ?>">
                    <input type="hidden" name="volume" value="<?php echo htmlspecialchars($volume_filter); ?>">
                    <input type="hidden" name="page" value="1">
                </form>
            </div>
        </div>
        <?php 
        if (empty($current_page_products))
        {
        ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> По вашему запросу ничего не найдено.
                <a href="oils.php?sort=default&page=1" class="btn btn-sm btn-outline-primary ms-2">Показать все товары</a>
            </div>
        <?php 
        }
        else
        {
        ?>
            <div class="row g-4">
                <?php
                foreach ($current_page_products as $product) 
                {
                    echo '
                    <div class="col-lg-3 col-md-6">
                        <div class="product-card card h-100">
                            '.($product['hit'] ? '<span class="badge bg-danger position-absolute top-0 start-0 m-2">Хит</span>' : '').'
                            <img src="../img/no-image.png" class="product-img card-img-top p-3" alt="'.$product['title'].'">
                            <div class="card-body">
                                <h5 class="product-title card-title">'.$product['title'].'</h5>
                                <p class="product-meta text-muted small mb-2">Арт. '.$product['art'].', '.$product['volume'].'</p>
                                <h4 class="product-price mb-3">'.number_format($product['price'], 0, '', ' ').' ₽</h4>
                                <p class="product-stock '.($product['stock'] ? 'text-success' : 'text-danger').' mb-3">
                                    <i class="bi '.($product['stock'] ? 'bi-check-circle' : 'bi-x-circle').'"></i> 
                                    '.($product['stock'] ? 'В наличии' : 'Нет в наличии').'
                                </p>
                                <div class="product-actions d-grid gap-2">';
                                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                    {
                                        echo '
                                        <form method="POST" class="add-to-cart-form">
                                            <input type="hidden" name="product_id" value="'.$product['id'].'">
                                            <input type="hidden" name="product_name" value="'.htmlspecialchars($product['title']).'">
                                            <input type="hidden" name="product_image" value="../img/no-image.png">
                                            <input type="hidden" name="price" value="'.$product['price'].'">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" name="add_to_cart" class="btn btn-sm w-100 '.($product['stock'] ? 'btn-primary' : 'btn-outline-secondary disabled').' add-to-cart-btn">
                                                <span class="btn-text">
                                                    <i class="bi bi-cart-plus"></i> В корзину
                                                </span>
                                            </button>
                                        </form>';
                                    }
                                    else
                                    {
                                        echo '
                                        <button class="btn btn-sm '.($product['stock'] ? 'btn-primary' : 'btn-outline-secondary disabled').'" data-bs-toggle="modal" data-bs-target="#loginModal">
                                            <i class="bi bi-cart-plus"></i> В корзину
                                        </button>';
                                    }
                                    echo '
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-info-circle"></i> Подробнее
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>
            <?php 
            if ($show_pagination)
            { 
            ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo buildQueryString(['page' => $current_page - 1]); ?>" tabindex="-1" aria-disabled="true">Назад</a>
                    </li>
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $start_page + 4);
                    
                    if ($end_page - $start_page < 4) 
                    {
                        $start_page = max(1, $end_page - 4);
                    }
                    
                    for ($page = $start_page; $page <= $end_page; $page++)
                    { 
                    ?>
                        <li class="page-item <?php echo $page == $current_page ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo buildQueryString(['page' => $page]); ?>"><?php echo $page; ?></a>
                        </li>
                    <?php 
                    } 
                    ?>
                    <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo buildQueryString(['page' => $current_page + 1]); ?>">Вперед</a>
                    </li>
                </ul>
            </nav>
            <div class="text-center text-muted mt-3">
                Страница <?php echo $current_page; ?> из <?php echo $total_pages; ?> | Показано <?php echo count($current_page_products); ?> из <?php echo $total_items; ?> товаров
            </div>
            <?php 
            }
            else
            { 
            ?>
                <?php 
                if ($total_items > 0) 
                {
                ?>
                <div class="text-center text-muted mt-3">
                    Показано <?php echo $total_items; ?> товаров
                </div>
                <?php 
                } 
                ?>
            <?php 
            } 
            ?>
        <?php 
        } 
        ?>
    </div>
</div>

<div class="modal fade" id="oilSelectorModal" tabindex="-1" aria-labelledby="oilSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="oilSelectorModalLabel"><i class="bi bi-question-circle"></i> Подбор масла</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="oilSelectorForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Марка автомобиля</label>
                            <select class="form-select" id="carBrand" name="brand" required>
                                <option value="" selected disabled>Выберите марку</option>
                                <option value="Audi">Audi</option>
                                <option value="BMW">BMW</option>
                                <option value="Mercedes-Benz">Mercedes-Benz</option>
                                <option value="Volkswagen">Volkswagen</option>
                                <option value="Toyota">Toyota</option>
                                <option value="Honda">Honda</option>
                                <option value="Ford">Ford</option>
                                <option value="Chevrolet">Chevrolet</option>
                                <option value="Nissan">Nissan</option>
                                <option value="Hyundai">Hyundai</option>
                                <option value="Kia">Kia</option>
                                <option value="Mazda">Mazda</option>
                                <option value="Subaru">Subaru</option>
                                <option value="Lexus">Lexus</option>
                                <option value="Volvo">Volvo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Модель</label>
                            <select class="form-select" id="carModel" name="model" disabled required>
                                <option value="" selected disabled>Сначала выберите марку</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Год выпуска</label>
                            <select class="form-select" id="carYear" name="year" disabled required>
                                <option value="" selected disabled>Сначала выберите модель</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Двигатель (объем)</label>
                            <select class="form-select" id="carEngine" name="engine" disabled required>
                                <option value="" selected disabled>Сначала выберите год выпуска</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Тип двигателя</label>
                            <select class="form-select" id="engineType" name="engine_type">
                                <option value="Бензиновый">Бензиновый</option>
                                <option value="Дизельный">Дизельный</option>
                                <option value="Гибридный">Гибридный</option>
                                <option value="Электрический">Электрический</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Тип масла</label>
                            <select class="form-select" id="oilType" name="oil_type">
                                <option value="Синтетическое">Синтетическое</option>
                                <option value="Полусинтетическое">Полусинтетическое</option>
                                <option value="Минеральное">Минеральное</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div id="selectionResults" class="mt-4" style="display: none;">
                    <div class="alert alert-success">
                        <h5><i class="bi bi-check-circle"></i> Подходящие масла найдены!</h5>
                        <p>Для вашего автомобиля <span id="selectedCarInfo"></span> рекомендуем следующие масла:</p>
                    </div>
                    <div id="recommendedOils" class="row g-3"></div>
                </div>
                <div id="noResults" class="alert alert-warning mt-4" style="display: none;">
                    <i class="bi bi-exclamation-triangle"></i> Для указанных параметров не найдено подходящих масел. 
                    <a href="oils.php?sort=default&page=1" class="alert-link">Посмотреть все масла</a>
                </div>
                <div id="errorMessage" class="alert alert-danger mt-4" style="display: none;">
                    <i class="bi bi-x-circle"></i> Произошла ошибка при подборе. Попробуйте еще раз.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" id="findOilBtn" disabled>
                    <span id="findOilText">Подобрать масло</span>
                    <span id="findOilLoading" style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Подбираем...
                    </span>
                </button>
                <button type="button" class="btn btn-outline-primary" id="resetSelectionBtn" style="display: none;">
                    <i class="bi bi-arrow-counterclockwise"></i> Новый подбор
                </button>
            </div>
        </div>
    </div>
</div>
<div id="cartNotification" class="notification">
    <i class="bi bi-check-circle-fill"></i>
    <span>Товар добавлен в корзину!</span>
</div>

<?php 
    require_once("footer.php"); 
?>

<?php
function buildQueryString($newParams = []) 
{
    $params = array_merge($_GET, $newParams);
    
    foreach ($params as $key => $value) 
    {
        if ($value === '') 
        {
            unset($params[$key]);
        }
    }
    
    return http_build_query($params);
}
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let carData = {
        "Audi": {
            "A4": {
                "years": ["2015-2020", "2020-2023"],
                "engines": {
                    "2015-2020": ["1.4 TSI", "2.0 TDI", "2.0 TFSI"],
                    "2020-2023": ["2.0 TFSI mild hybrid", "3.0 TDI"]
                }
            },
            "A6": {
                "years": ["2018-2022", "2022-2024"],
                "engines": {
                    "2018-2022": ["2.0 TDI", "3.0 TDI", "2.0 TFSI"],
                    "2022-2024": ["3.0 TFSI", "3.0 TDI e-tron"]
                }
            },
            "Q5": {
                "years": ["2017-2021", "2021-2024"],
                "engines": {
                    "2017-2021": ["2.0 TDI", "2.0 TFSI"],
                    "2021-2024": ["2.0 TFSI", "3.0 TFSI"]
                }
            }
        },
        "BMW": {
            "3 Series": {
                "years": ["2019-2022", "2022-2024"],
                "engines": {
                    "2019-2022": ["2.0 320i", "3.0 330i", "2.0 320d"],
                    "2022-2024": ["2.0 320i", "3.0 M340i"]
                }
            },
            "5 Series": {
                "years": ["2017-2020", "2020-2023"],
                "engines": {
                    "2017-2020": ["2.0 520i", "3.0 530d", "2.0 520d"],
                    "2020-2023": ["2.0 520i", "3.0 540i"]
                }
            },
            "X5": {
                "years": ["2018-2023"],
                "engines": {
                    "2018-2023": ["3.0 xDrive30d", "4.4 xDrive50i", "3.0 xDrive40i"]
                }
            }
        },
        "Mercedes-Benz": {
            "C-Class": {
                "years": ["2014-2018", "2018-2021", "2021-2024"],
                "engines": {
                    "2014-2018": ["C 180", "C 200", "C 220 d"],
                    "2018-2021": ["C 200", "C 300", "C 220 d"],
                    "2021-2024": ["C 200", "C 300 e", "C 220 d"]
                }
            },
            "E-Class": {
                "years": ["2016-2020", "2020-2023"],
                "engines": {
                    "2016-2020": ["E 200", "E 300", "E 220 d"],
                    "2020-2023": ["E 200", "E 350 e", "E 220 d"]
                }
            }
        },
        "Toyota": {
            "Camry": {
                "years": ["2017-2021", "2021-2024"],
                "engines": {
                    "2017-2021": ["2.5 Hybrid", "3.5 V6"],
                    "2021-2024": ["2.5 Hybrid", "2.5 Dynamic Force"]
                }
            },
            "RAV4": {
                "years": ["2018-2022", "2022-2024"],
                "engines": {
                    "2018-2022": ["2.0", "2.5 Hybrid"],
                    "2022-2024": ["2.0", "2.5 Hybrid", "2.5 Plug-in Hybrid"]
                }
            },
            "Corolla": {
                "years": ["2018-2020", "2020-2023"],
                "engines": {
                    "2018-2020": ["1.6", "1.8 Hybrid"],
                    "2020-2023": ["1.8 Hybrid", "2.0 Hybrid"]
                }
            }
        },
        "Volkswagen": {
            "Golf": {
                "years": ["2012-2017", "2017-2020", "2020-2024"],
                "engines": {
                    "2012-2017": ["1.2 TSI", "1.4 TSI", "1.6 TDI"],
                    "2017-2020": ["1.0 TSI", "1.5 TSI", "2.0 TDI"],
                    "2020-2024": ["1.0 TSI", "1.5 eTSI", "2.0 TDI"]
                }
            },
            "Passat": {
                "years": ["2014-2019", "2019-2023"],
                "engines": {
                    "2014-2019": ["1.4 TSI", "2.0 TSI", "2.0 TDI"],
                    "2019-2023": ["1.4 TSI", "2.0 TSI", "2.0 TDI"]
                }
            },
            "Tiguan": {
                "years": ["2016-2020", "2020-2024"],
                "engines": {
                    "2016-2020": ["1.4 TSI", "2.0 TSI", "2.0 TDI"],
                    "2020-2024": ["1.4 TSI", "2.0 TSI", "2.0 TDI"]
                }
            }
        }
    };

    let oilRecommendations = {
        "Audi_A4_1.4 TSI": ["5W-30", "5W-40"],
        "Audi_A4_2.0 TFSI": ["5W-30", "5W-40"],
        "Audi_A4_2.0 TDI": ["5W-30", "5W-40"],
        "BMW_3 Series_2.0 320i": ["5W-30", "0W-30"],
        "BMW_3 Series_2.0 320d": ["5W-30", "5W-40"],
        "Mercedes-Benz_C-Class_C 200": ["5W-30", "5W-40"],
        "Toyota_Camry_2.5 Hybrid": ["0W-20", "5W-30"],
        "Volkswagen_Golf_1.4 TSI": ["5W-30", "5W-40"],
        "default": ["5W-30", "5W-40", "10W-40"]
    };

    let carBrandSelect = document.getElementById('carBrand');
    let carModelSelect = document.getElementById('carModel');
    let carYearSelect = document.getElementById('carYear');
    let carEngineSelect = document.getElementById('carEngine');
    let findOilBtn = document.getElementById('findOilBtn');
    let resetSelectionBtn = document.getElementById('resetSelectionBtn');

    carBrandSelect.addEventListener('change', function() 
    {
        let brand = this.value;

        carModelSelect.innerHTML = '<option value="" selected disabled>Выберите модель</option>';
        carYearSelect.innerHTML = '<option value="" selected disabled>Сначала выберите модель</option>';
        carEngineSelect.innerHTML = '<option value="" selected disabled>Сначала выберите год выпуска</option>';
        carModelSelect.disabled = !brand;
        carYearSelect.disabled = true;
        carEngineSelect.disabled = true;
        findOilBtn.disabled = true;
        
        if (brand && carData[brand]) 
        {
            carModelSelect.disabled = false;

            Object.keys(carData[brand]).forEach(model => {
                let option = document.createElement('option');
                option.value = model;
                option.textContent = model;
                carModelSelect.appendChild(option);
            });
        }
    });
    
    carModelSelect.addEventListener('change', function() 
    {
        let brand = carBrandSelect.value;
        let model = this.value;

        carYearSelect.innerHTML = '<option value="" selected disabled>Выберите год выпуска</option>';
        carEngineSelect.innerHTML = '<option value="" selected disabled>Сначала выберите год выпуска</option>';
        carYearSelect.disabled = !model;
        carEngineSelect.disabled = true;
        findOilBtn.disabled = true;

        if (brand && model && carData[brand][model]) 
        {
            carYearSelect.disabled = false;

            carData[brand][model].years.forEach(year => {
                let option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                carYearSelect.appendChild(option);
            });
        }
    });

    carYearSelect.addEventListener('change', function() 
    {
        let brand = carBrandSelect.value;
        let model = carModelSelect.value;
        let year = this.value;
        
        carEngineSelect.innerHTML = '<option value="" selected disabled>Выберите двигатель</option>';
        carEngineSelect.disabled = !year;
        findOilBtn.disabled = true;

        if (brand && model && year && carData[brand][model].engines[year]) 
        {
            carEngineSelect.disabled = false;

            carData[brand][model].engines[year].forEach(engine => {
                let option = document.createElement('option');
                option.value = engine;
                option.textContent = engine;
                carEngineSelect.appendChild(option);
            });
        }
    });

    carEngineSelect.addEventListener('change', function() 
    {
        findOilBtn.disabled = !this.value;
    });

    findOilBtn.addEventListener('click', function() 
    {
        findRecommendedOils();
    });

    resetSelectionBtn.addEventListener('click', function() 
    {
        resetOilSelection();
    });
    
    function findRecommendedOils() 
    {
        let brand = carBrandSelect.value;
        let model = carModelSelect.value;
        let engine = carEngineSelect.value;
        let year = carYearSelect.value;
        let oilType = document.getElementById('oilType').value;
        
        if (!brand || !model || !engine || !year) 
        {
            showNotification('Заполните все поля', 'error');
            return;
        }
        
        document.getElementById('findOilText').style.display = 'none';
        document.getElementById('findOilLoading').style.display = 'inline';
        findOilBtn.disabled = true;

        document.getElementById('selectionResults').style.display = 'none';
        document.getElementById('noResults').style.display = 'none';
        document.getElementById('errorMessage').style.display = 'none';

        setTimeout(() => {
            let key = `${brand}_${model}_${engine}`.replace(/\s+/g, '_');
            let viscosities = oilRecommendations[key] || oilRecommendations.default;
            let products = <?php echo json_encode($products); ?>;

            let recommendedProducts = products.filter(product => {
                let viscosityMatch = viscosities.includes(product.viscosity);
                let typeMatch = oilType === 'Синтетическое' || product.type === oilType;

                return viscosityMatch && typeMatch && product.stock;
            });

            recommendedProducts.sort((a, b) => {
                if (a.hit !== b.hit) 
                {
                    return a.hit ? -1 : 1;
                }

                return a.price - b.price;
            });

            updateSelectionResults(brand, model, engine, year, recommendedProducts);

            document.getElementById('findOilText').style.display = 'inline';
            document.getElementById('findOilLoading').style.display = 'none';
            findOilBtn.disabled = false;
            resetSelectionBtn.style.display = 'inline-block';
        }, 1500);
    }

    function updateSelectionResults(brand, model, engine, year, recommendedProducts) 
    {
        document.getElementById('selectedCarInfo').textContent = `${brand} ${model} (${year}), ${engine}`;
        
        let resultsContainer = document.getElementById('recommendedOils');
        resultsContainer.innerHTML = '';
        
        if (recommendedProducts.length === 0) 
        {
            document.getElementById('noResults').style.display = 'block';
        } 
        else 
        {
            recommendedProducts.slice(0, 6).forEach(product => {
                let productHtml = `
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="card-title mb-2">${product.title}</h6>
                                ${product.hit ? '<span class="badge bg-danger">Хит</span>' : ''}
                            </div>
                            <p class="text-muted small mb-2">${product.brand} • ${product.viscosity} • ${product.type}</p>
                            <p class="text-muted small mb-2">${product.volume}</p>
                            <h5 class="text-primary mb-3">${product.price.toLocaleString('ru-RU')} ₽</h5>
                            <div class="d-grid gap-2">
                                <button class="btn btn-sm btn-outline-primary add-from-selection" 
                                        data-id="${product.id}"
                                        data-name="${product.title}"
                                        data-price="${product.price}">
                                    <i class="bi bi-cart-plus"></i> В корзину
                                </button>
                                <a href="oils.php?search=${encodeURIComponent(product.title)}&page=1" 
                                   class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-info-circle"></i> Подробнее
                                </a>
                            </div>
                        </div>
                    </div>
                </div>`;
                resultsContainer.innerHTML += productHtml;
            });
            
            document.getElementById('selectionResults').style.display = 'block';

            document.querySelectorAll('.add-from-selection').forEach(button => {
                button.addEventListener('click', function() 
                {
                    let productId = this.getAttribute('data-id');
                    let productName = this.getAttribute('data-name');
                    let productPrice = this.getAttribute('data-price');
                    
                    addToCartFromSelection(productId, productName, productPrice, this);
                });
            });

            if (recommendedProducts.length > 6) 
            {
                resultsContainer.innerHTML += `
                <div class="col-12">
                    <div class="text-center mt-3">
                        <a href="oils.php?brand=${encodeURIComponent(recommendedProducts[0].brand)}&viscosity=${encodeURIComponent(recommendedProducts[0].viscosity)}&type=${encodeURIComponent(recommendedProducts[0].type)}&page=1" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-list"></i> Показать все ${recommendedProducts.length} рекомендованных масел
                        </a>
                    </div>
                </div>`;
            }
        }

        document.getElementById('selectionResults').scrollIntoView({ behavior: 'smooth' });
    }

    function addToCartFromSelection(productId, productName, productPrice, button) 
    {
        let originalText = button.innerHTML;
        
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Добавляем...';
        
        let formData = new FormData();

        formData.append('product_id', productId);
        formData.append('product_name', productName);
        formData.append('product_image', '../img/no-image.png');
        formData.append('price', productPrice);
        formData.append('quantity', '1');
        formData.append('add_to_cart', '1');
        
        fetch('oils.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(() => {
            showNotification('Масло добавлено в корзину!', 'success');
            updateCartCounter();
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Ошибка при добавлении в корзину', 'error');
        })
        .finally(() => {
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 1500);
        });
    }

    function resetOilSelection() 
    {
        document.getElementById('oilSelectorForm').reset();

        carBrandSelect.selectedIndex = 0;
        carModelSelect.innerHTML = '<option value="" selected disabled>Сначала выберите марку</option>';
        carYearSelect.innerHTML = '<option value="" selected disabled>Сначала выберите модель</option>';
        carEngineSelect.innerHTML = '<option value="" selected disabled>Сначала выберите год выпуска</option>';

        carModelSelect.disabled = true;
        carYearSelect.disabled = true;
        carEngineSelect.disabled = true;

        document.getElementById('selectionResults').style.display = 'none';
        document.getElementById('noResults').style.display = 'none';
        document.getElementById('errorMessage').style.display = 'none';

        findOilBtn.disabled = true;
        resetSelectionBtn.style.display = 'none';

        carBrandSelect.focus();
    }
    
    document.getElementById('oilSelectorModal').addEventListener('hidden.bs.modal', function() 
    {
        resetOilSelection();
    });

    document.getElementById('applyFiltersBtn').addEventListener('click', function() 
    {
        let brand = document.querySelector('select[name="brand"]').value;
        let viscosity = document.querySelector('select[name="viscosity"]').value;
        let type = document.querySelector('select[name="type"]').value;
        let volume = document.querySelector('select[name="volume"]').value;
        let search = document.querySelector('input[name="search"]').value;
        let sort = document.querySelector('input[name="sort"]').value;
        let params = new URLSearchParams();

        if (search) 
        {
            params.set('search', search);
        }

        if (sort && sort !== 'default') 
        {
            params.set('sort', sort);
        }

        if (brand) 
        {
            params.set('brand', brand);
        }

        if (viscosity) 
        {
            params.set('viscosity', viscosity);
        }

        if (type) 
        {
            params.set('type', type);
        }

        if (volume) 
        {
            params.set('volume', volume);
        }
        
        params.set('page', '1');
        window.location.href = '?' + params.toString();
    });

    document.getElementById('resetFilters').addEventListener('click', function(e) 
    {
        e.preventDefault();

        let params = new URLSearchParams();
        let search = document.querySelector('input[name="search"]').value;
        let sort = document.querySelector('input[name="sort"]').value;
        
        if (search) 
        {
            params.set('search', search);
        }

        if (sort && sort !== 'default') 
        {
            params.set('sort', sort);
        }

        params.set('sort', 'default'); 
        params.set('page', '1'); 
        window.location.href = '?' + params.toString();
    });

    document.querySelector('.btn-outline-danger')?.addEventListener('click', function(e) 
    {
        e.preventDefault();

        let params = new URLSearchParams();
        let brand = document.querySelector('select[name="brand"]').value;
        let viscosity = document.querySelector('select[name="viscosity"]').value;
        let type = document.querySelector('select[name="type"]').value;
        let volume = document.querySelector('select[name="volume"]').value;
        let sort = document.querySelector('input[name="sort"]').value;
        
        if (brand) 
        {
            params.set('brand', brand);
        }

        if (viscosity) 
        {
            params.set('viscosity', viscosity);
        }

        if (type) 
        {
            params.set('type', type);
        }

        if (volume) 
        {
            params.set('volume', volume);
        }

        if (sort && sort !== 'default') 
        {
            params.set('sort', sort);
        }

        params.set('page', '1');
        window.location.href = '?' + params.toString();
    });

    let addToCartForms = document.querySelectorAll('.add-to-cart-form');
    
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) 
        {
            e.preventDefault();
            
            let submitButton = this.querySelector('.add-to-cart-btn');
            
            if (!submitButton || submitButton.disabled) 
            {
                return;
            }
            
            let originalHtml = submitButton.innerHTML;
            let originalDisabled = submitButton.disabled;
            
            submitButton.classList.add('btn-loading');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="btn-text">Добавляем...</span>';
            
            showNotification('Товар добавляется...', 'info');
            
            let formData = new FormData(this);
            
            fetch('oils.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(() => {
                showNotification('Товар добавлен в корзину!', 'success');
                updateCartCounter();
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка сети', 'error');
            })
            .finally(() => {
                setTimeout(() => {
                    submitButton.classList.remove('btn-loading');
                    submitButton.disabled = originalDisabled;
                    submitButton.innerHTML = originalHtml;
                }, 1500);
            });
        });
    });
    
    function showNotification(message, type = 'success') 
    {
        let notification = document.getElementById('cartNotification');
        
        if (!notification) 
        {
            notification = document.createElement('div');
            notification.id = 'cartNotification';
            notification.className = 'notification';
            document.body.appendChild(notification);
        }
        
        let icon = 'bi-check-circle-fill';
        let bgColor = '#28a745';
        let textColor = 'white';
        
        if (type === 'error') 
        {
            icon = 'bi-exclamation-triangle-fill';
            bgColor = '#dc3545';
        } 
        else if (type === 'info') 
        {
            icon = 'bi-info-circle-fill';
            bgColor = '#17a2b8';
        }
        
        notification.innerHTML = `<i class="bi ${icon}"></i><span>${message}</span>`;
        notification.style.background = bgColor;
        notification.style.color = textColor;
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
    
    function updateCartCounter() {
        let cartCounter = document.getElementById('cartCounter');
        
        if (cartCounter) 
        {
            let currentCount = parseInt(cartCounter.textContent) || 0;
            cartCounter.textContent = currentCount + 1;
            
            cartCounter.style.transform = 'scale(1.3)';
            
            setTimeout(() => {
                cartCounter.style.transform = 'scale(1)';
            }, 300);
        }
    }
    
    <?php 
    if (isset($_SESSION['success_message']))
    {
    ?>
        showNotification('<?= $_SESSION['success_message'] ?>', 'success');
        <?php unset($_SESSION['success_message']); ?>
    <?php 
    }
    ?>
});
</script>
</body>
</html>