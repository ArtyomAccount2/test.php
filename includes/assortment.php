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

function enhanceBrandSearch($search_term, $products) 
{
    $brands_mapping = [
        'acura' => ['acura'],
        'aixam' => ['aixam'],
        'alfa romeo' => ['alfa romeo', 'alfa'],
        'alfa' => ['alfa romeo', 'alfa'],
        'aston martin' => ['aston martin', 'aston'],
        'aston' => ['aston martin', 'aston'],
        'audi' => ['audi'],
        'bmw' => ['bmw'],
        'bentley' => ['bentley'],
        'buick' => ['buick'],
        'cadillac' => ['cadillac'],
        'chevrolet' => ['chevrolet'],
        'chrysler' => ['chrysler'],
        'dodge' => ['dodge'],
        'fiat' => ['fiat'],
        'ford' => ['ford'],
        'gaz' => ['gaz'],
        'honda' => ['honda'],
        'hummer' => ['hummer'],
        'hyundai' => ['hyundai'],
        'infiniti' => ['infiniti'],
        'jaguar' => ['jaguar'],
        'jeep' => ['jeep'],
        'kia' => ['kia'],
        'lada' => ['lada'],
        'lamborghini' => ['lamborghini'],
        'lancia' => ['lancia'],
        'land rover' => ['land rover', 'land', 'rover'],
        'land' => ['land rover', 'land'],
        'rover' => ['land rover', 'rover'],
        'lexus' => ['lexus'],
        'lotus' => ['lotus']
    ];
    
    $search_lower = strtolower(trim($search_term));
    $found_products = [];

    foreach ($brands_mapping as $brand_key => $brand_variants) 
    {
        if (in_array($search_lower, $brand_variants)) 
        {
            $found_products = array_filter($products, function($product) use ($brand_key, $brand_variants) 
            {
                $title_lower = strtolower($product['name']);
                $badge_lower = isset($product['badge']) ? strtolower($product['badge']) : '';
                $badge_match = strpos($badge_lower, 'для ') !== false && (strpos($badge_lower, $brand_key) !== false);
                $title_match = false;

                foreach ($brand_variants as $variant) 
                {
                    if (strpos($title_lower, $variant) !== false) 
                    {
                        $title_match = true;
                        break;
                    }
                }
                
                return $badge_match || $title_match;
            });
            
            if (!empty($found_products)) 
            {
                break;
            }
        }
    }

    return !empty($found_products) ? $found_products : [];
}

function searchByPartCategory($search_term, $products) 
{
    $parts_mapping = [
        'коленчатый вал' => ['коленчатый вал', 'коленвал', 'коленчатый'],
        'прокладки двигателя' => ['прокладки двигателя', 'прокладки', 'прокладка двигателя'],
        'топливный насос' => ['топливный насос', 'бензонасос', 'топливный'],
        'распределительный вал' => ['распределительный вал', 'распредвал', 'распределительный'],
        'тормозной цилиндр' => ['тормозной цилиндр', 'тормозной', 'цилиндр'],
        'тормозные колодки' => ['тормозные колодки', 'колодки тормозные', 'колодки'],
        'стабилизатор' => ['стабилизатор', 'стойка стабилизатора'],
        'тормозные суппорта' => ['тормозные суппорта', 'суппорта', 'суппорт'],
        'топливный фильтр' => ['топливный фильтр', 'фильтр топливный'],
        'тормозные диски' => ['тормозные диски', 'диски тормозные', 'тормозной диск'],
        'цапфа' => ['цапфа'],
        'сальники' => ['сальники', 'сальник']
    ];
    
    $search_lower = strtolower(trim($search_term));
    $found_products = [];
    
    foreach ($parts_mapping as $part_name => $keywords) 
    {
        if (in_array($search_lower, $keywords)) 
        {
            $found_products = array_filter($products, function($product) use ($keywords) 
            {
                $title_lower = strtolower($product['name']);
                
                foreach ($keywords as $keyword) 
                {
                    if (strpos($title_lower, $keyword) !== false) 
                    {
                        return true;
                    }
                }

                return false;
            });
            
            if (!empty($found_products)) 
            {
                break;
            }
        }
    }
    
    return !empty($found_products) ? $found_products : [];
}

$items_per_page = 12;
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

if (!empty($search_term) || (!empty($category_filter) && $category_filter !== 'все категории')) 
{
    $current_page = 1;

    if (isset($_GET['page']) && intval($_GET['page']) === 1) 
    {
        $current_page = 1;
    }
} 
else 
{
    $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
}

$offset = ($current_page - 1) * $items_per_page;

$_SESSION['last_assortment_page'] = [
    'page' => $current_page,
    'search' => $search_term,
    'category' => $category_filter
];

$categories_sql = "SELECT DISTINCT category FROM products WHERE status = 'available' AND category IS NOT NULL ORDER BY category";
$categories_result = $conn->query($categories_sql);
$available_categories = [];

while ($cat_row = $categories_result->fetch_assoc()) 
{
    $available_categories[] = $cat_row['category'];
}

$sql = "SELECT * FROM products WHERE status = 'available' AND product_type = 'part'";
$params = [];
$types = "";

if (!empty($search_term)) 
{
    $sql .= " AND (name LIKE ? OR description LIKE ? OR category LIKE ? OR article LIKE ?)";
    $search_like = "%$search_term%";
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
    $params[] = $search_like;
    $types .= "ssss";
}

if (!empty($category_filter) && $category_filter !== 'все категории') 
{
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

$count_sql = "SELECT COUNT(*) as total FROM products WHERE status = 'available' AND product_type = 'part'";
$count_params = [];
$count_types = "";

if (!empty($search_term)) 
{
    $count_sql .= " AND (name LIKE ? OR description LIKE ? OR category LIKE ? OR article LIKE ?)";
    $search_like = "%$search_term%";
    $count_params[] = $search_like;
    $count_params[] = $search_like;
    $count_params[] = $search_like;
    $count_params[] = $search_like;
    $count_types .= "ssss";
}

if (!empty($category_filter) && $category_filter !== 'все категории') 
{
    $count_sql .= " AND category = ?";
    $count_params[] = $category_filter;
    $count_types .= "s";
}

$count_stmt = $conn->prepare($count_sql);

if (!empty($count_types)) 
{
    $count_stmt->bind_param($count_types, ...$count_params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_row = $count_result->fetch_assoc();
$total_items = $total_row['total'] ?? 0;
$count_stmt->close();
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $items_per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $conn->prepare($sql);

if (!empty($types)) 
{
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$filtered_products = [];

while ($row = $result->fetch_assoc()) 
{
    $filtered_products[] = $row;
}
$stmt->close();

if ($total_items == 0 && !empty($search_term)) 
{
    $all_stmt = $conn->prepare("SELECT * FROM products WHERE status = 'available'");
    $all_stmt->execute();
    $all_result = $all_stmt->get_result();
    $all_products_db = [];

    while ($row = $all_result->fetch_assoc()) {
        $all_products_db[] = $row;
    }

    $all_stmt->close();
    $brand_results = enhanceBrandSearch($search_term, $all_products_db);
    
    if (!empty($brand_results)) 
    {
        $filtered_products = $brand_results;
    } 
    else 
    {
        $part_results = searchByPartCategory($search_term, $all_products_db);
        
        if (!empty($part_results)) 
        {
            $filtered_products = $part_results;
        }
    }

    $total_items = count($filtered_products);
    $filtered_products = array_slice($filtered_products, $offset, $items_per_page);
}

$total_pages = ceil($total_items / $items_per_page);

if ($current_page > $total_pages && $total_pages > 0) 
{
    $current_page = 1;
    $offset = 0;

    if (!empty($types)) 
    {
        array_pop($params);
        array_pop($params);
        $types = substr($types, 0, -2);
        
        $params[] = $items_per_page;
        $params[] = $offset;
        $types .= "ii";
        
        $stmt = $conn->prepare(str_replace("LIMIT ? OFFSET ?", "LIMIT ? OFFSET ?", $sql));
        if (!empty($types)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $filtered_products = [];
        
        while ($row = $result->fetch_assoc()) {
            $filtered_products[] = $row;
        }
        $stmt->close();
    }
}

function buildQueryString($page, $search, $category) 
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

    if (!empty($category) && $category !== 'все категории') 
    {
        $params['category'] = $category;
    }

    return http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ассортимент - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/notifications-styles.css">
    <link rel="stylesheet" href="../css/assortment-styles.css">
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

<div class="container my-4">
    <div class="hero-section text-center mb-5" style="padding-top: 105px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Каталог автозапчастей</h1>
        <p class="lead text-muted mb-4">Более <?php echo $total_items; ?> наименований оригинальных и качественных аналогов</p>
        <?php 
        if ($search_term !== '') 
        {
        ?>
        <div class="alert alert-info d-inline-block">
            <i class="bi bi-search me-2"></i>
            <?php 
            if ($search_term !== '' && $category_filter !== '' && $category_filter !== 'все категории') 
            {
                echo 'Поиск: "' . htmlspecialchars($search_term) . '" в категории "' . htmlspecialchars($category_filter) . '"';
            } 
            else if ($search_term !== '') 
            {
                echo 'Результаты поиска: "' . htmlspecialchars($search_term) . '"';
            }
            ?>
        </div>
        <?php 
        } 
        ?>
    </div>     
    <div class="row mb-4">
        <div class="col-md-5 col-lg-6">
            <div class="search-container position-relative">
                <input type="text" id="partsSearch" placeholder="Поиск по каталогу..." class="form-control" 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button class="btn btn-link search-clear" type="button" style="display: none;">
                    <i class="bi bi-x"></i>
                </button>
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <select id="categoryFilter" class="form-select">
                <option value="все категории" <?php echo $category_filter === '' || $category_filter === 'все категории' ? 'selected' : ''; ?>>Все категории</option>
                <?php 
                foreach ($available_categories as $category)
                {
                ?>
                    <option value="<?php echo htmlspecialchars($category); ?>" <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                        <?php echo ucfirst(htmlspecialchars($category)); ?>
                    </option>
                <?php 
                }
                ?>
            </select>
        </div>
        <div class="col-md-3 col-lg-2">
            <button id="searchButton" class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Найти
            </button>
        </div>
    </div>
    <?php 
    if ($search_term !== '' || $category_filter !== '') 
    {
    ?>
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info py-2">
                <?php 
                if ($search_term !== '' && $category_filter !== '' && $category_filter !== 'все категории') 
                {
                    echo 'Найдено ' . $total_items . ' товаров по запросу "' . htmlspecialchars($search_term) . '" в категории "' . htmlspecialchars($category_filter) . '"';
                } 
                else if ($search_term !== '') 
                {
                    echo 'Найдено ' . $total_items . ' товаров по запросу "' . htmlspecialchars($search_term) . '"';
                } 
                else if ($category_filter !== '' && $category_filter !== 'все категории') 
                {
                    echo 'Найдено ' . $total_items . ' товаров в категории "' . htmlspecialchars($category_filter) . '"';
                }
                ?>
                <?php 
                if ($search_term !== '' || ($category_filter !== '' && $category_filter !== 'все категории'))
                {
                ?>
                    <a href="assortment.php?page=<?= $current_page > 1 ? $current_page : '' ?>" class="btn btn-sm btn-outline-secondary ms-2">Показать все</a>
                <?php 
                }
                ?>
            </div>
        </div>
    </div>
    <?php 
    } 
    ?>
    <div class="row g-3" id="productsContainer">
        <?php 
        if ($total_items > 0 && !empty($filtered_products))
        { 
            foreach ($filtered_products as $product)
            {
                $product_detail_url = "product_detail.php?id=" . $product['id'] . "&back=" . urlencode("assortment.php?" . buildQueryString($current_page, $search_term, $category_filter));
        ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="product-card">
                        <?php 
                        if (!empty($product['badge'])) 
                        {
                        ?>
                            <div class="product-badge"><?php echo htmlspecialchars($product['badge']); ?></div>
                        <?php 
                        }
                        ?>
                        <div class="product-image">
                            <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : '../img/no-image.png'; ?>" 
                                 class="product-img" alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='../img/no-image.png'">
                        </div>
                        <div class="product-body">
                            <h6 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                            <div class="product-price">
                                <?php 
                                if (!empty($product['old_price']) && $product['old_price'] > $product['price'])
                                {
                                ?>
                                    <span class="current-price"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</span>
                                    <span class="old-price"><?php echo number_format($product['old_price'], 0, '', ' '); ?> ₽</span>
                                <?php 
                                }
                                else
                                { 
                                ?>
                                    <span class="current-price"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</span>
                                <?php 
                                }
                                ?>
                            </div>
                            <div class="product-actions">
                                <?php 
                                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                {
                                ?>
                                    <form method="POST" action="../profile.php" class="d-inline me-1">
                                        <input type="hidden" name="wishlist_action" value="1">
                                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                                        <input type="hidden" name="product_image" value="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : '../img/no-image.png' ?>">
                                        <input type="hidden" name="price" value="<?= $product['price'] ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="cart.php" class="d-inline add-to-cart-form">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                                        <input type="hidden" name="product_image" value="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : '../img/no-image.png' ?>">
                                        <input type="hidden" name="price" value="<?= $product['price'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm add-to-cart-btn">
                                            <span class="btn-text">
                                                <i class="bi bi-cart-plus"></i> В корзину
                                            </span>
                                        </button>
                                    </form>
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <i class="bi bi-cart-plus"></i> В корзину
                                    </button>
                                <?php 
                                }
                                ?>
                                <a href="<?= $product_detail_url ?>" class="btn btn-outline-secondary btn-sm">Подробнее</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
            }
        }
        else
        { 
        ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search display-4 text-muted mb-3"></i>
                <h4 class="text-muted">Товары не найдены</h4>
                <p class="text-muted mb-3">Попробуйте изменить параметры поиска</p>
                <a href="assortment.php" class="btn btn-primary">Показать все товары</a>
            </div>
        <?php 
        }
        ?>
    </div>
    <?php 
    if ($total_pages > 1) 
    {
    ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo buildQueryString($current_page - 1, $search_term, $category_filter); ?>">Назад</a>
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
                    <a class="page-link" href="?<?php echo buildQueryString($page, $search_term, $category_filter); ?>"><?php echo $page; ?></a>
                </li>
            <?php 
            }
            ?>
            <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo buildQueryString($current_page + 1, $search_term, $category_filter); ?>">Вперед</a>
            </li>
        </ul>
    </nav>
    <div class="text-center text-muted mt-2">
        Страница <?php echo $current_page; ?> из <?php echo $total_pages; ?> | Показано <?php echo count($filtered_products); ?> из <?php echo $total_items; ?> товаров
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
            Найдено <?php echo $total_items; ?> товаров
        </div>
        <?php 
        }
        ?>
    <?php 
    } 
    ?>
</div>
<div id="cartNotification" class="notification">
    <i class="bi bi-check-circle-fill"></i>
    <span>Товар добавлен в корзину!</span>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let urlParams = new URLSearchParams(window.location.search);
    let page = urlParams.get('page') || 1;
    let search = urlParams.get('search') || '';
    let category = urlParams.get('category') || '';
    
    sessionStorage.setItem('last_assortment_params', JSON.stringify({
        page: page,
        search: search,
        category: category
    }));
    
    function performSearch() 
    {
        let searchTerm = document.getElementById('partsSearch').value.trim();
        let categoryFilter = document.getElementById('categoryFilter').value;
        let params = new URLSearchParams();

        if (searchTerm) 
        {
            params.set('search', searchTerm);
        }

        if (categoryFilter && categoryFilter !== 'все категории') 
        {
            params.set('category', categoryFilter);
        }
        
        window.location.href = '?' + params.toString();
    }

    function resetSearch() 
    {
        document.getElementById('partsSearch').value = '';
        document.getElementById('categoryFilter').value = 'все категории';
        document.querySelector('.search-clear').style.display = 'none';
        sessionStorage.removeItem('last_assortment_params');
        
        window.location.href = 'assortment.php';
    }

    document.getElementById('searchButton').addEventListener('click', performSearch);
        
    document.getElementById('partsSearch').addEventListener('keypress', function(e) 
    {
        if (e.key === 'Enter') 
        {
            performSearch();
        }
    });

    document.getElementById('categoryFilter').addEventListener('change', function() 
    {
        performSearch();
    });

    document.querySelector('.search-clear').addEventListener('click', resetSearch);

    document.getElementById('partsSearch').addEventListener('input', function() 
    {
        document.querySelector('.search-clear').style.display = this.value ? 'block' : 'none';
    });

    if (document.getElementById('partsSearch').value) 
    {
        document.querySelector('.search-clear').style.display = 'block';
    }

    document.querySelectorAll('a[href*="product_detail.php"]').forEach(link => {
        let href = link.getAttribute('href');

        if (!href.includes('back=')) 
        {
            let currentUrl = window.location.pathname + window.location.search;
            let separator = href.includes('?') ? '&' : '?';

            link.setAttribute('href', href + separator + 'back=' + encodeURIComponent(currentUrl));
        }
    });

    let addToCartForms = document.querySelectorAll('.add-to-cart-form');

    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) 
        {
            e.preventDefault();
            
            let submitButton = this.querySelector('.add-to-cart-btn');

            if (!submitButton) 
            {
                return;
            }

            let originalWidth = submitButton.offsetWidth + 'px';
            let originalHeight = submitButton.offsetHeight + 'px';
            let originalHtml = submitButton.innerHTML;
            let originalDisabled = submitButton.disabled;

            submitButton.style.minWidth = originalWidth;
            submitButton.style.minHeight = originalHeight;
            submitButton.style.width = originalWidth;
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
                    submitButton.style.minWidth = '';
                    submitButton.style.minHeight = '';
                    submitButton.style.width = '';
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
    
    function updateCartCounter(newCount = null) 
    {
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
});
</script>
</body>
</html>