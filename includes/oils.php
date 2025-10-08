<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
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
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Масла и тех. жидкости</li>
        </ol>
    </nav>
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="mb-0">Масла и технические жидкости</h1>
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
                        <a href="?" class="btn btn-sm btn-outline-secondary ms-2">Показать все</a>
                    <?php 
                    } 
                    ?>
                </p>
            <?php 
            } 
            ?>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#oilSelectorModal">
                <i class="bi bi-question-circle"></i> Подобрать масло
            </button>
        </div>
    </div>
    <div class="oil-categories mb-5">
        <h2 class="mb-4"><i class="bi bi-filter-square"></i> Категории</h2>
        <div class="row g-4">
            <?php
            $base_url = '../files/categories/';

            $categories = [
                ['icon' => 'bi-droplet', 'title' => 'Моторные масла', 'count' => '124 товара', 'color' => 'dark', 'link' => $base_url . 'motor-oils.php'],
                ['icon' => 'bi-gear', 'title' => 'Трансмиссионные масла', 'count' => '56 товаров', 'color' => 'success', 'link' => $base_url . 'transmission-oils.php'],
                ['icon' => 'bi-snow', 'title' => 'Тормозные жидкости', 'count' => '23 товара', 'color' => 'info', 'link' => $base_url . 'brake-fluids.php'],
                ['icon' => 'bi-water', 'title' => 'Охлаждающие жидкости', 'count' => '34 товара', 'color' => 'primary', 'link' => $base_url . 'cooling-fluids.php'],
                ['icon' => 'bi-wind', 'title' => 'Жидкости ГУР', 'count' => '18 товаров', 'color' => 'dark', 'link' => $base_url . 'power-steering-fluids.php'],
                ['icon' => 'bi-droplet-half', 'title' => 'Антифризы', 'count' => '42 товара', 'color' => 'secondary', 'link' => $base_url . 'antifreeze.php'],
                ['icon' => 'bi-brightness-high', 'title' => 'Специальные жидкости', 'count' => '31 товар', 'color' => 'warning', 'link' => $base_url . 'special-fluids.php'],
                ['icon' => 'bi-archive', 'title' => 'Комплекты', 'count' => '15 товаров', 'color' => 'primary', 'link' => $base_url . 'kits.php']
            ];
            
            foreach ($categories as $category) 
            {
                echo '
                <div class="col-md-3 col-6">
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
                <button class="btn btn-sm btn-primary" id="applyFilters">Применить фильтры</button>
            </div>
        </div>
        <form method="GET" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="filter-group mb-3">
                        <label class="form-label filter-title">Бренд</label>
                        <select class="form-select" name="brand">
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
                        <select class="form-select" name="viscosity">
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
                        <select class="form-select" name="type">
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
                        <select class="form-select" name="volume">
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
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(1, $search_query, $sort_type, $brand_filter, $viscosity_filter, $type_filter, $volume_filter, 'default'); ?>">По умолчанию</a></li>
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(1, $search_query, $sort_type, $brand_filter, $viscosity_filter, $type_filter, $volume_filter, 'popular'); ?>">По популярности</a></li>
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(1, $search_query, $sort_type, $brand_filter, $viscosity_filter, $type_filter, $volume_filter, 'price_asc'); ?>">По цене (возрастание)</a></li>
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(1, $search_query, $sort_type, $brand_filter, $viscosity_filter, $type_filter, $volume_filter, 'price_desc'); ?>">По цене (убывание)</a></li>
                        <li><a class="dropdown-item" href="?<?php echo buildQueryString(1, $search_query, $sort_type, $brand_filter, $viscosity_filter, $type_filter, $volume_filter, 'name'); ?>">По названию</a></li>
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
                            <a href="?<?php echo buildQueryString(1, '', $sort_type, $brand_filter, $viscosity_filter, $type_filter, $volume_filter); ?>" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                        <?php
                        }
                        ?>
                    </div>
                    <input type="hidden" name="sort" value="<?php echo $sort_type; ?>">
                    <input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand_filter); ?>">
                    <input type="hidden" name="viscosity" value="<?php echo htmlspecialchars($viscosity_filter); ?>">
                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($type_filter); ?>">
                    <input type="hidden" name="volume" value="<?php echo htmlspecialchars($volume_filter); ?>">
                </form>
            </div>
        </div>
        <?php 
        if (empty($current_page_products))
        {
        ?>
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> По вашему запросу ничего не найдено.
                <a href="?" class="btn btn-sm btn-outline-primary ms-2">Показать все товары</a>
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
                    <div class="col-lg-3 col-md-4 col-6">
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
                                <div class="product-actions d-grid gap-2">
                                    <button class="btn btn-sm '.($product['stock'] ? 'btn-primary' : 'btn-outline-secondary disabled').'">
                                        <i class="bi bi-cart-plus"></i> В корзину
                                    </button>
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
                        <a class="page-link" href="?<?php echo buildQueryString($current_page - 1, $search_query, $sort_type, $brand_filter, $viscosity_filter, $type_filter, $volume_filter); ?>" tabindex="-1" aria-disabled="true">Назад</a>
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
                            <a class="page-link" href="?<?php echo buildQueryString($page, $search_query, $sort_type, $brand_filter, $viscosity_filter, $type_filter, $volume_filter); ?>"><?php echo $page; ?></a>
                        </li>
                    <?php 
                    } 
                    ?>
                    <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo buildQueryString($current_page + 1, $search_query, $sort_type, $brand_filter, $viscosity_filter, $type_filter, $volume_filter); ?>">Вперед</a>
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
    <div class="specs-section mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-card-list"></i> Спецификации масел</h2>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#specsTable">
                <i class="bi bi-arrows-collapse"></i> Свернуть/развернуть
            </button>
        </div>
        <div class="collapse show" id="specsTable">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="bi bi-card-checklist"></i> Спецификация</th>
                            <th><i class="bi bi-clipboard-data"></i> Описание</th>
                            <th><i class="bi bi-gear"></i> Применение</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>API SN/CF</strong></td>
                            <td>Для бензиновых и дизельных двигателей</td>
                            <td>Современные двигатели</td>
                        </tr>
                        <tr>
                            <td><strong>ACEA A3/B4</strong></td>
                            <td>Для высоконагруженных двигателей</td>
                            <td>Европейские автомобили</td>
                        </tr>
                        <tr>
                            <td><strong>VW 502.00/505.00</strong></td>
                            <td>Для двигателей VW, Audi, Skoda, Seat</td>
                            <td>Германские автомобили</td>
                        </tr>
                        <tr>
                            <td><strong>BMW Longlife-01</strong></td>
                            <td>Для двигателей BMW с увеличенным интервалом замены</td>
                            <td>BMW, Mini</td>
                        </tr>
                        <tr>
                            <td><strong>MB-Approval 229.5</strong></td>
                            <td>Для двигателей Mercedes-Benz</td>
                            <td>Mercedes-Benz</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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
                <form>
                    <div class="mb-3">
                        <label class="form-label">Марка автомобиля</label>
                        <select class="form-select">
                            <option selected disabled>Выберите марку</option>
                            <option>Audi</option>
                            <option>BMW</option>
                            <option>Mercedes-Benz</option>
                            <option>Volkswagen</option>
                            <option>Toyota</option>
                            <option>Honda</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Модель</label>
                        <select class="form-select" disabled>
                            <option selected disabled>Сначала выберите марку</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Год выпуска</label>
                        <select class="form-select" disabled>
                            <option selected disabled>Сначала выберите модель</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Двигатель</label>
                        <select class="form-select" disabled>
                            <option selected disabled>Сначала выберите год выпуска</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" disabled>Подобрать</button>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<?php
function buildQueryString($page, $search, $sort, $brand, $viscosity, $type, $volume, $new_sort = null) 
{
    $params = [];

    if ($page > 1) 
    {
        $params['page'] = $page;
    }

    if (!empty($search)) 
    {
        $params['search'] = $search;
    }

    if (!empty($new_sort)) 
    {
        $params['sort'] = $new_sort;
    } 
    else if (!empty($sort) && $sort !== 'default') 
    {
        $params['sort'] = $sort;
    }

    if (!empty($brand)) 
    {
        $params['brand'] = $brand;
    }

    if (!empty($viscosity)) 
    {
        $params['viscosity'] = $viscosity;
    }

    if (!empty($type)) 
    {
        $params['type'] = $type;
    }

    if (!empty($volume)) 
    {
        $params['volume'] = $volume;
    }

    return http_build_query($params);
}
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    document.getElementById('applyFilters').addEventListener('click', function() 
    {
        document.getElementById('filterForm').submit();
    });

    document.getElementById('resetFilters').addEventListener('click', function(e) 
    {
        e.preventDefault();
        window.location.href = '?';
    });

    document.querySelectorAll('#filterForm select').forEach(select => {
        select.addEventListener('change', function() 
        {
            document.getElementById('filterForm').submit();
        });
    });

    document.querySelector('.btn-outline-danger')?.addEventListener('click', function(e) 
    {
        e.preventDefault();
        window.location.href = '?' + new URLSearchParams({
            sort: '<?php echo $sort_type; ?>',
            brand: '<?php echo $brand_filter; ?>',
            viscosity: '<?php echo $viscosity_filter; ?>',
            type: '<?php echo $type_filter; ?>',
            volume: '<?php echo $volume_filter; ?>'
        }).toString();
    });
});
</script>
</body>
</html>