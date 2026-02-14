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
    $productType = $_POST['product_type'] ?? 'accessory';

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
            $checkSql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND product_name = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("iis", $userId, $productId, $productName);
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
                $insertSql = "INSERT INTO cart (user_id, product_id, product_name, product_image, price, quantity, product_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("iissdis", $userId, $productId, $productName, $productImage, $price, $quantity, $productType);
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

$sql = "SELECT id, name, description, category, price, old_price, quantity, article, image, badge, status, brand, hit FROM products WHERE product_type = 'accessory' AND status = 'available' ORDER BY id";

$result = $conn->query($sql);
$all_products = [];

if ($result->num_rows > 0) 
{
    while ($row = $result->fetch_assoc()) 
    {
        $all_products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'brand' => $row['brand'] ?? 'Лал-Авто',
            'price' => (float)$row['price'],
            'old_price' => (float)($row['old_price'] ?? 0),
            'rating' => 4.5,
            'badge' => !empty($row['badge']) ? $row['badge'] : '',
            'badge_text' => $row['badge'] == 'danger' ? 'Акция' : ($row['badge'] == 'success' ? 'Новинка' : ($row['badge'] == 'info' ? 'Хит' : '')),
            'category' => $row['category'] ?? 'Аксессуары',
            'hit' => $row['hit'] ?? 0,
            'image' => $row['image'],
            'article' => $row['article']
        ];
    }
}

$search_term = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$brand_filter = isset($_GET['brand']) ? $_GET['brand'] : '';
$min_price = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? intval($_GET['max_price']) : 0;

$filtered_products = $all_products;

if ($search_term !== '' || $category_filter !== '' || $brand_filter !== '' || $min_price > 0 || $max_price > 0) 
{
    $filtered_products = array_filter($all_products, function($product) use ($search_term, $category_filter, $brand_filter, $min_price, $max_price) 
    {
        $matches_search = $search_term === '' || strpos(strtolower($product['name']), $search_term) !== false || strpos(strtolower($product['brand']), $search_term) !== false;
        $matches_category = $category_filter === '' || $product['category'] === $category_filter;
        $matches_brand = $brand_filter === '' || $product['brand'] === $brand_filter;
        $matches_min_price = $min_price === 0 || $product['price'] >= $min_price;
        $matches_max_price = $max_price === 0 || $product['price'] <= $max_price;
        
        return $matches_search && $matches_category && $matches_brand && $matches_min_price && $matches_max_price;
    });
    
    $filtered_products = array_values($filtered_products);
}

$sort = $_GET['sort'] ?? 'popular';

switch($sort) 
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
    case 'rating':
        usort($filtered_products, function($a, $b) 
        {
            return $b['rating'] - $a['rating'];
        });
        break;
    case 'new':
        usort($filtered_products, function($a, $b) 
        {
            return $b['id'] - $a['id'];
        });
        break;
    default:
        usort($filtered_products, function($a, $b) 
        {
            if ($a['hit'] != $b['hit']) 
            {
                return $b['hit'] - $a['hit'];
            }

            return $b['rating'] - $a['rating'];
        });
        break;
}

$items_per_page = 20;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$total_items = count($filtered_products);
$total_pages = ceil($total_items / $items_per_page);
$start_index = ($current_page - 1) * $items_per_page;
$end_index = min($start_index + $items_per_page, $total_items);

$show_pagination = $total_pages > 1;

$all_brands = array_unique(array_column($all_products, 'brand'));
sort($all_brands);

$all_categories = array_unique(array_column($all_products, 'category'));
sort($all_categories);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аксессуары - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/notifications-styles.css">
    <link rel="stylesheet" href="../css/accessories-styles.css">
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

        let sortSelect = document.getElementById('sortSelect');

        if (sortSelect) 
        {
            sortSelect.addEventListener('change', function() 
            {
                let url = new URL(window.location);
                url.searchParams.set('sort', this.value);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            });
        }

        let filterForm = document.getElementById('filterForm');

        if (filterForm) 
        {
            filterForm.addEventListener('submit', function(e) 
            {
                e.preventDefault();
                let formData = new FormData(this);
                let params = new URLSearchParams();
                
                for (let [key, value] of formData.entries()) 
                {
                    if (value) 
                    {
                        params.set(key, value);
                    }
                }
                
                params.set('page', '1');
                window.location.href = '?' + params.toString();
            });
        }

        let resetFilters = document.getElementById('resetFilters');

        if (resetFilters) 
        {
            resetFilters.addEventListener('click', function() 
            {
                window.location.href = '?';
            });
        }

        let searchForm = document.getElementById('searchForm');

        if (searchForm) 
        {
            searchForm.addEventListener('submit', function(e) 
            {
                e.preventDefault();
                let formData = new FormData(this);
                let params = new URLSearchParams();
                
                for (let [key, value] of formData.entries()) 
                {
                    if (value) 
                    {
                        params.set(key, value);
                    }
                }
                
                params.set('page', '1');
                window.location.href = '?' + params.toString();
            });
        }

        document.querySelectorAll('.filter-category').forEach(button => {
            button.addEventListener('click', function() {
                let category = this.getAttribute('data-category');
                let url = new URL(window.location);
                url.searchParams.set('category', category);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            });
        });

        document.querySelectorAll('.filter-brand').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                let params = new URLSearchParams(window.location.search);
                params.delete('brand');

                if (this.checked) 
                {
                    params.set('brand', this.value);
                }
                
                params.set('page', '1');
                window.location.href = '?' + params.toString();
            });
        });
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-4">
    <div class="hero-section text-center mb-5" style="padding-top: 105px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Аксессуары</h1>
        <p class="lead text-muted mb-4">Найдите идеальные аксессуары для вашего автомобиля</p>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="border-0 shadow-sm">
                <div class="card-body p-3">
                    <form id="searchForm" class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Поиск аксессуаров..." value="<?php echo htmlspecialchars($search_term); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">Все категории</option>
                                <?php 
                                foreach($all_categories as $category)
                                { 
                                ?>
                                    <option value="<?php echo $category; ?>" <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                                        <?php echo $category; ?>
                                    </option>
                                <?php 
                                } 
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="brand" class="form-select">
                                <option value="">Все бренды</option>
                                <?php 
                                foreach($all_brands as $brand) 
                                {
                                ?>
                                    <option value="<?php echo $brand; ?>" <?php echo $brand_filter === $brand ? 'selected' : ''; ?>>
                                        <?php echo $brand; ?>
                                    </option>
                                <?php 
                                } 
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Найти
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php 
    if ($search_term !== '' || $category_filter !== '' || $brand_filter !== '' || $min_price > 0 || $max_price > 0)
    { 
    ?>
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info py-2">
                <?php 
                if ($search_term !== '' && $category_filter !== '' && $brand_filter !== '')
                {
                    echo "Найдено $total_items товаров по запросу \"" . htmlspecialchars($search_term) . "\" в категории \"" . htmlspecialchars($category_filter) . "\" бренда \"" . htmlspecialchars($brand_filter) . "\"";
                }
                else if ($search_term !== '' && $category_filter !== '')
                {
                    echo "Найдено $total_items товаров по запросу \"" . htmlspecialchars($search_term) . "\" в категории \"" . htmlspecialchars($category_filter) . "\"";
                }
                else if ($search_term !== '' && $brand_filter !== '')
                {
                    echo "Найдено $total_items товаров по запросу \"" . htmlspecialchars($search_term) . "\" бренда \"" . htmlspecialchars($brand_filter) . "\"";
                }
                else if ($search_term !== '')
                {
                    echo "Найдено $total_items товаров по запросу \"" . htmlspecialchars($search_term) . "\"";
                }
                else if ($category_filter !== '')
                {
                    echo "Найдено $total_items товаров в категории \"" . htmlspecialchars($category_filter) . "\"";
                }
                else if ($brand_filter !== '')
                {
                    echo "Найдено $total_items товаров бренда \"" . htmlspecialchars($brand_filter) . "\"";
                }
                else
                {
                    echo "Найдено $total_items товаров";
                }
                ?>
                <a href="?" class="btn btn-sm btn-outline-secondary ms-2">Показать все</a>
            </div>
        </div>
    </div>
    <?php 
    } 
    ?>
    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="border-0 shadow-sm">
                <div class="card-body p-3">
                    <h5 class="card-title mb-3"><i class="bi bi-funnel"></i> Фильтры</h5>
                    <form id="filterForm">
                        <div class="mb-4">
                            <h6 class="mb-3">Цена, ₽</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="От" value="<?php echo $min_price ?: ''; ?>" style="width: 45%">
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="До" value="<?php echo $max_price ?: ''; ?>" style="width: 45%">
                            </div>
                        </div>
                        <div class="mb-4">
                            <h6 class="mb-3">Категории</h6>
                            <div class="list-group list-group-flush">
                                <?php 
                                foreach($all_categories as $category)
                                {
                                    $count = count(array_filter($all_products, function($product) use ($category) 
                                    {
                                        return $product['category'] === $category;
                                    }));
                                ?>
                                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center filter-category" data-category="<?php echo $category; ?>">
                                        <?php echo $category; ?>
                                        <span class="badge bg-primary rounded-pill"><?php echo $count; ?></span>
                                    </button>
                                <?php 
                                } 
                                ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h6 class="mb-3">Бренды</h6>
                            <?php 
                            foreach($all_brands as $brand)
                            { 
                                $count = count(array_filter($all_products, function($product) use ($brand) 
                                {
                                    return $product['brand'] === $brand;
                                }));
                            ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input filter-brand" type="checkbox" name="brand" value="<?php echo $brand; ?>" id="brand_<?php echo preg_replace('/[^a-zA-Z0-9]/', '_', $brand); ?>" 
                                           <?php echo $brand_filter === $brand ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="brand_<?php echo preg_replace('/[^a-zA-Z0-9]/', '_', $brand); ?>">
                                        <?php echo $brand; ?>
                                        <small class="text-muted">(<?php echo $count; ?>)</small>
                                    </label>
                                </div>
                            <?php 
                            } 
                            ?>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">Применить фильтры</button>
                        <button type="button" id="resetFilters" class="btn btn-outline-secondary w-100">Сбросить все</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-8">
            <div class="border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <span class="me-2 text-muted">Сортировка:</span>
                            <select class="form-select form-select-sm" id="sortSelect" style="width: auto;">
                                <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>По популярности</option>
                                <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>По цене (сначала дешевые)</option>
                                <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>По цене (сначала дорогие)</option>
                                <option value="new" <?php echo $sort === 'new' ? 'selected' : ''; ?>>По новизне</option>
                                <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>По рейтингу</option>
                            </select>
                        </div>
                        <div class="text-muted">
                            <?php 
                            if ($total_items > 0)
                            { 
                            ?>
                                Показано <?php echo ($end_index - $start_index); ?> из <?php echo $total_items; ?> товаров
                            <?php 
                            }
                            else
                            { 
                            ?>
                                Товары не найдены
                            <?php 
                            } 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <?php 
                if ($total_items > 0)
                { 
                ?>
                    <?php 
                    for ($i = $start_index; $i < $end_index; $i++)
                    { 
                        $product = $filtered_products[$i];
                    ?>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="card product-card h-100 border-0 shadow-sm">
                            <?php 
                            if(!empty($product['badge'])) 
                            {
                                $badge_class = $product['badge'];
                                $badge_text = $product['badge_text'];
                                
                                if ($product['hit'] && empty($badge_text)) 
                                {
                                    $badge_class = 'info';
                                    $badge_text = 'Хит';
                                }
                            ?>
                                <div class="badge bg-<?php echo $badge_class; ?> position-absolute top-0 start-0 m-2">
                                    <?php echo $badge_text; ?>
                                </div>
                            <?php 
                            } 
                            ?>
                            <div class="product-image p-3">
                                <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : '../img/no-image.png'; ?>" class="img-fluid" alt="<?php echo $product['name']; ?>" onerror="this.src='../img/no-image.png'">
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small"><?php echo $product['brand']; ?></span>
                                    <div class="small">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <span><?php echo $product['rating']; ?></span>
                                    </div>
                                </div>
                                <h6 class="card-title"><?php echo $product['name']; ?></h6>
                                <span class="text-muted small">Арт. <?php echo $product['article']; ?></span>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php 
                                        if($product['old_price'] > 0)
                                        {
                                        ?>
                                            <span class="text-danger fw-bold"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</span>
                                            <span class="text-decoration-line-through text-muted small ms-2"><?php echo number_format($product['old_price'], 0, '', ' '); ?> ₽</span>
                                        <?php 
                                        }
                                        else 
                                        {
                                        ?>
                                            <span class="fw-bold"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</span>
                                        <?php 
                                        } 
                                        ?>
                                    </div>
                                    <?php 
                                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                    {
                                    ?>
                                        <form method="POST" class="add-to-cart-form">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                                            <input type="hidden" name="product_image" value="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : '../img/no-image.png' ?>">
                                            <input type="hidden" name="price" value="<?= $product['price'] ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <input type="hidden" name="product_type" value="accessory">
                                            <button type="submit" name="add_to_cart" class="btn btn-sm btn-outline-primary add-to-cart-btn">
                                                <span class="btn-text">
                                                    <i class="bi bi-cart-plus"></i>
                                                </span>
                                            </button>
                                        </form>
                                    <?php 
                                    }
                                    else
                                    {
                                    ?>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    <?php 
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                    } 
                    ?>
                <?php 
                }
                else 
                {
                ?>
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-search display-4 text-muted mb-3"></i>
                        <h4 class="text-muted">Товары не найдены</h4>
                        <p class="text-muted mb-3">Попробуйте изменить параметры поиска или фильтры</p>
                        <a href="?" class="btn btn-primary">Показать все товары</a>
                    </div>
                <?php 
                } 
                ?>
            </div>
            <?php 
            if ($show_pagination) 
            {
            ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo buildQueryString($current_page - 1, $search_term, $category_filter, $brand_filter, $min_price, $max_price, $sort); ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
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
                            <a class="page-link" href="?<?php echo buildQueryString($page, $search_term, $category_filter, $brand_filter, $min_price, $max_price, $sort); ?>">
                                <?php echo $page; ?>
                            </a>
                        </li>
                    <?php 
                    }
                    ?>
                    <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo buildQueryString($current_page + 1, $search_term, $category_filter, $brand_filter, $min_price, $max_price, $sort); ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php 
            } 
            ?>
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
function buildQueryString($page, $search, $category, $brand, $min_price, $max_price, $sort) 
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

    if (!empty($category)) 
    {
        $params['category'] = $category;
    }

    if (!empty($brand)) 
    {
        $params['brand'] = $brand;
    }

    if ($min_price > 0) 
    {
        $params['min_price'] = $min_price;
    }

    if ($max_price > 0) 
    {
        $params['max_price'] = $max_price;
    }

    if (!empty($sort) && $sort !== 'popular') 
    {
        $params['sort'] = $sort;
    }

    return http_build_query($params);
}
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() 
{
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
            
            fetch('ajax_add_to_cart.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    showNotification(data.message, 'success');
                    updateCartCounter(data.cart_count);
                } 
                else 
                {
                    showNotification(data.message, 'error');
                }
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
    
    function updateCartCounter(newCount = null) {
        let cartCounter = document.getElementById('cartCounter');
        
        if (cartCounter) 
        {
            if (newCount !== null) 
            {
                cartCounter.textContent = newCount;
            } 
            else 
            {
                let currentCount = parseInt(cartCounter.textContent) || 0;
                cartCounter.textContent = currentCount + 1;
            }
            
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