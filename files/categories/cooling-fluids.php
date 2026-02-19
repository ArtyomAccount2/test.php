<?php
error_reporting(E_ALL);
session_start();
require_once("../../config/link.php");
require_once("../../includes/category_functions.php");

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
        header("Location: ../../admin.php");
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

$search_query = $_GET['search'] ?? '';
$sort_type = $_GET['sort'] ?? 'default';
$brand_filter = $_GET['brand'] ?? '';
$type_filter = $_GET['type'] ?? '';
$color_filter = $_GET['color'] ?? '';
$freezing_filter = $_GET['freezing'] ?? '';
$volume_filter = $_GET['volume'] ?? '';

$items_per_page = 8;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$filtered_products = getCategoryProducts($conn, 'cooling-fluid', $search_query, $brand_filter, $sort_type, [
    'type' => $type_filter,
    'color' => $color_filter,
    'freezing' => $freezing_filter,
    'volume' => $volume_filter
]);

$total_items = count($filtered_products);
$total_pages = ceil($total_items / $items_per_page);
$current_page = min($current_page, max(1, $total_pages));
$offset = ($current_page - 1) * $items_per_page;

$paginated_products = array_slice($filtered_products, $offset, $items_per_page);

$brands = getFilterOptions($conn, 'cooling-fluid', 'brand');
$types = getFilterOptions($conn, 'cooling-fluid', 'type');
$colors = getFilterOptions($conn, 'cooling-fluid', 'color');
$freezings = getFilterOptions($conn, 'cooling-fluid', 'freezing');
$volumes = getFilterOptions($conn, 'cooling-fluids', 'volume');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Охлаждающие жидкости - Лал-Авто</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/notifications-styles.css">
    <link rel="stylesheet" href="../../css/oils-styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        <?php 
        if (isset($_SESSION['login_error'])) 
        { 
        ?>
            let loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            <?php unset($_SESSION['login_error']); ?>
        <?php 
        } 
        ?>
    });
    </script>
</head>
<body>

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-xl navbar-light bg-light shadow-sm fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../../index.php"><img src="../../img/Auto.png" alt="Лал-Авто" height="75"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="../../index.php">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="../../includes/oils.php?sort=default&page=1">Масла и тех. жидкости</a>
                    </li>
                </ul>
                <div class="ms-xl-3 ms-lg-2 ms-md-1">
                    <?php 
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
                    {
                    ?>
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <p class="mb-0 text-center text-md-end me-md-2" style="font-size: 0.9em; white-space: nowrap;">
                                <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>
                            </p>
                            <a href="../../profile.php" class="profile-button w-md-auto text-decoration-none">
                                <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                            </a>
                        </div>
                    <?php 
                    } 
                    else 
                    {
                    ?>
                        <div class="d-flex flex-wrap flex-md-nowrap">
                            <a href="#" class="btn btn-primary button-link w-md-auto mx-1" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Войти
                            </a>
                            <a href="#" class="btn btn-primary button-link w-md-auto" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="bi bi-r-circle"></i>
                                Зарегистрироваться
                            </a>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="loginModalLabel">Авторизация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" id="username" placeholder="Введите логин" required value="<?= htmlspecialchars($form_data['login'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Введите пароль" required value="<?= htmlspecialchars($form_data['password'] ?? '') ?>">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="rememberMe" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                        </div>
                        <?php 
                        if (isset($_SESSION['error_message'])) 
                        {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($_SESSION['error_message']); ?>
                            </div>
                        <?php 
                            unset($_SESSION['error_message']);
                        }
                        ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Войти
                        </button>
                        <a href="#" class="btn btn-link">Забыли пароль?</a>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="registerModalLabel">Регистрация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <a href="../../individuel.php" type="button" class="btn btn-primary mb-2" id="individualsBtn">
                        <i class="bi bi-person-add"></i> Физические лица
                    </a>
                    <div id="individualsInfo" class="registration-info">
                        <p>- если Вы - физическое лицо, пройдите регистрацию. Регистрация возможна как при наличии карты скидок, так и при её отсутствии.</p>
                    </div>
                    <a href="../../legalEntity.php" type="button" class="btn btn-primary mb-2" id="legalEntitiesBtn">
                        <i class="bi bi-person-add"></i> Юридические лица и ИП
                    </a>
                    <div id="legalEntitiesInfo" class="registration-info">
                        <p>- если Вы - представитель организации, учреждения, предприятия или фирмы, заполните данную форму регистрации.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5 pt-4">
        <div class="row mb-4 align-items-center" style="padding-top: 75px;">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold text-primary mb-3">Охлаждающие жидкости</h1>
                <p class="lead text-muted mb-4">Антифризы и тосолы для эффективного охлаждения двигателя</p>
                <?php 
                if (!empty($search_query) || !empty($brand_filter) || !empty($type_filter) || !empty($color_filter) || !empty($freezing_filter))
                {
                    $filters_applied = [];

                    if (!empty($search_query)) 
                    {
                        $filters_applied[] = 'поиск: "' . htmlspecialchars($search_query) . '"';
                    }

                    if (!empty($brand_filter)) 
                    {
                        $filters_applied[] = 'бренд: ' . htmlspecialchars($brand_filter);
                    }

                    if (!empty($type_filter)) 
                    {
                        $filters_applied[] = 'тип: ' . htmlspecialchars($type_filter);
                    }

                    if (!empty($color_filter)) 
                    {
                        $filters_applied[] = 'цвет: ' . htmlspecialchars($color_filter);
                    }

                    if (!empty($freezing_filter)) 
                    {
                        $filters_applied[] = 'замерзание: ' . htmlspecialchars($freezing_filter);
                    }
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
                        <a href="cooling-fluids.php?sort=default&page=1" class="btn btn-sm btn-outline-secondary ms-2">Показать все</a>
                    </p>
                <?php 
                } 
                ?>
            </div>
        </div>
        <div class="filter-section mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="bi bi-funnel"></i> Фильтры</h2>
                <div>
                    <a href="cooling-fluids.php?sort=default&page=1" class="btn btn-sm btn-outline-secondary me-2">Сбросить</a>
                    <button class="btn btn-sm btn-primary" onclick="applyFilters()">Применить</button>
                </div>
            </div>
            <form method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-6 col-lg-2">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Бренд</label>
                            <select class="form-select" name="brand">
                                <option value="">Все бренды</option>
                                <?php 
                                foreach($brands as $brand)
                                {
                                ?>
                                    <option value="<?php echo htmlspecialchars($brand); ?>" <?php echo $brand_filter === $brand ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand); ?>
                                    </option>
                                <?php 
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-2">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Тип</label>
                            <select class="form-select" name="type">
                                <option value="">Все</option>
                                <?php 
                                foreach($types as $type)
                                {
                                ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $type_filter === $type ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type); ?>
                                    </option>
                                <?php 
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Цвет</label>
                            <select class="form-select" name="color">
                                <option value="">Все</option>
                                <?php 
                                foreach($colors as $color)
                                {
                                ?>
                                    <option value="<?php echo htmlspecialchars($color); ?>" <?php echo $color_filter === $color ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($color); ?>
                                    </option>
                                <?php 
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Температура замерзания</label>
                            <select class="form-select" name="freezing">
                                <option value="">Все</option>
                                <?php 
                                foreach($freezings as $freezing)
                                {
                                ?>
                                    <option value="<?php echo htmlspecialchars($freezing); ?>" <?php echo $freezing_filter === $freezing ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($freezing); ?>
                                    </option>
                                <?php 
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-2">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Объем</label>
                            <select class="form-select" name="volume">
                                <option value="">Все</option>
                                <?php 
                                foreach($volumes as $volume)
                                {
                                ?>
                                    <option value="<?php echo htmlspecialchars($volume); ?>" <?php echo $volume_filter === $volume ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($volume); ?>
                                    </option>
                                <?php 
                                }
                                ?>
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
                            <li><a class="dropdown-item" href="?<?php echo buildCategoryQueryString(['sort' => 'default', 'page' => 1]); ?>">По умолчанию</a></li>
                            <li><a class="dropdown-item" href="?<?php echo buildCategoryQueryString(['sort' => 'popular', 'page' => 1]); ?>">По популярности</a></li>
                            <li><a class="dropdown-item" href="?<?php echo buildCategoryQueryString(['sort' => 'price_asc', 'page' => 1]); ?>">По цене (возрастание)</a></li>
                            <li><a class="dropdown-item" href="?<?php echo buildCategoryQueryString(['sort' => 'price_desc', 'page' => 1]); ?>">По цене (убывание)</a></li>
                            <li><a class="dropdown-item" href="?<?php echo buildCategoryQueryString(['sort' => 'name', 'page' => 1]); ?>">По названию</a></li>
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
                                <a href="?<?php echo buildCategoryQueryString(['search' => '', 'page' => 1]); ?>" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                            <?php
                            }
                            ?>
                        </div>
                        <input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand_filter); ?>">
                        <input type="hidden" name="type" value="<?php echo htmlspecialchars($type_filter); ?>">
                        <input type="hidden" name="color" value="<?php echo htmlspecialchars($color_filter); ?>">
                        <input type="hidden" name="freezing" value="<?php echo htmlspecialchars($freezing_filter); ?>">
                        <input type="hidden" name="volume" value="<?php echo htmlspecialchars($volume_filter); ?>">
                        <input type="hidden" name="sort" value="<?php echo $sort_type; ?>">
                        <input type="hidden" name="page" value="1">
                    </form>
                </div>
            </div>
            <?php 
            if (empty($paginated_products))
            {
            ?>
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i> По вашему запросу ничего не найдено.
                </div>
            <?php 
            }
            else
            {
            ?>
                <div class="row g-4">
                    <?php
                    foreach ($paginated_products as $product) 
                    {
                        $product_url = 'product_detail.php?id=' . $product['id'] . '&back=' . urlencode("cooling-fluids.php?" . buildCategoryQueryString(['page' => $current_page]));
                        ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="product-card card h-100">
                                <?php 
                                if($product['hit'])
                                {
                                ?>
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">Хит</span>
                                <?php 
                                }
                                ?>
                                <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : '../../img/no-image.png'; ?>" class="product-img card-img-top p-3" alt="<?php echo htmlspecialchars($product['title']); ?>" onerror="this.src='../../img/no-image.png'">
                                <div class="card-body">
                                    <h5 class="product-title card-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                                    <p class="product-meta text-muted small mb-2">
                                        Арт. <?php echo htmlspecialchars($product['art']); ?>, <?php echo htmlspecialchars($product['volume']); ?>
                                    </p>
                                    <h4 class="product-price mb-3"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</h4>
                                    <p class="product-stock <?php echo $product['stock'] ? 'text-success' : 'text-danger'; ?> mb-3">
                                        <i class="bi <?php echo $product['stock'] ? 'bi-check-circle' : 'bi-x-circle'; ?>"></i> 
                                        <?php echo $product['stock'] ? 'В наличии' : 'Нет в наличии'; ?>
                                    </p>
                                    <div class="product-actions d-grid gap-2">
                                        <?php 
                                        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                        {
                                        ?>
                                            <form method="POST" action="../../includes/add_to_cart.php" class="add-to-cart-form">
                                                <input type="hidden" name="category_product_id" value="<?php echo $product['id']; ?>">
                                                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['title']); ?>">
                                                <input type="hidden" name="product_image" value="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : '../../img/no-image.png'; ?>">
                                                <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="product_type" value="<?php echo $product['category_type']; ?>">
                                                <button type="submit" class="btn btn-sm w-100 <?php echo $product['stock'] ? 'btn-primary' : 'btn-outline-secondary disabled'; ?> add-to-cart-btn">
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
                                            <button class="btn btn-sm <?php echo $product['stock'] ? 'btn-primary' : 'btn-outline-secondary disabled'; ?>" data-bs-toggle="modal" data-bs-target="#loginModal">
                                                <i class="bi bi-cart-plus"></i> В корзину
                                            </button>
                                        <?php 
                                        }
                                        ?>
                                        <a href="<?php echo $product_url; ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-info-circle"></i> Подробнее
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <?php 
                if ($total_pages > 1) 
                { 
                ?>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php if ($current_page <= 1) echo 'disabled'; ?>">
                            <a class="page-link" href="?<?php echo buildCategoryQueryString(['page' => $current_page - 1]); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php 
                        if ($current_page > 3)
                        {
                        ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo buildCategoryQueryString(['page' => 1]); ?>">1</a>
                        </li>
                            <?php 
                            if ($current_page > 4)
                            {
                            ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php 
                            }
                        }
                        
                        for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++)
                        {
                        ?>
                        <li class="page-item <?php if ($i == $current_page) echo 'active'; ?>">
                            <a class="page-link" href="?<?php echo buildCategoryQueryString(['page' => $i]); ?>"><?php echo $i; ?></a>
                        </li>
                        <?php 
                        }
                        
                        if ($current_page < $total_pages - 2)
                        {
                        ?>
                            <?php 
                            if ($current_page < $total_pages - 3)
                            {
                            ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                            <?php 
                            }
                            ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo buildCategoryQueryString(['page' => $total_pages]); ?>"><?php echo $total_pages; ?></a>
                        </li>
                        <?php 
                        }
                        ?>
                        <li class="page-item <?php if ($current_page >= $total_pages) echo 'disabled'; ?>">
                            <a class="page-link" href="?<?php echo buildCategoryQueryString(['page' => $current_page + 1]); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                    <div class="text-center text-muted mt-2">
                        Показано <?php echo ($offset + 1); ?>-<?php echo min($offset + $items_per_page, $total_items); ?> из <?php echo $total_items; ?> товаров
                    </div>
                </nav>
                <?php 
                } 
            } 
            ?>
        </div>
    </div>
    <div id="cartNotification" class="notification">
        <i class="bi bi-check-circle-fill"></i>
        <span>Товар добавлен в корзину!</span>
    </div>

<?php 
    require_once("../../includes/footer.php"); 
?>

<script src="../../js/bootstrap.bundle.min.js"></script>
<script src="../../js/script.js"></script>
<script>
function applyFilters() 
{
    document.getElementById('filterForm').submit();
}

function buildCategoryQueryString(params) 
{
    let currentParams = new URLSearchParams(window.location.search);
    let keys = Object.keys(params);
    
    for (let i = 0; i < keys.length; i++) 
    {
        let key = keys[i];

        if (params[key]) 
        {
            currentParams.set(key, params[key]);
        } 
        else 
        {
            currentParams.delete(key);
        }
    }
    
    return currentParams.toString();
}

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

            let formData = new FormData(this);

            fetch('../../includes/add_to_cart.php', {
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
                showNotification('Ошибка соединения', 'error');
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
    
    function updateCartCounter(newCount) 
    {
        let cartCounter = document.getElementById('cartCounter');
        
        if (!cartCounter) 
        {
            cartCounter = document.querySelector('.cart-counter');
        }
        
        if (cartCounter) 
        {
            cartCounter.textContent = newCount;
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