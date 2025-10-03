<?php
error_reporting(E_ALL);
session_start();
require_once("../../config/link.php");

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

$brake_fluids = [
    ['id' => 1, 'title' => 'Liqui Moly Bremsflussigkeit DOT 4', 'art' => 'BRAKE001', 'volume' => '0.5 л', 'price' => 650, 'stock' => true, 'hit' => true, 'brand' => 'Liqui Moly', 'standard' => 'DOT 4', 'dry_boil' => '255°C', 'wet_boil' => '165°C'],
    ['id' => 2, 'title' => 'Castrol React DOT 4', 'art' => 'CAST-BF01', 'volume' => '0.5 л', 'price' => 580, 'stock' => true, 'hit' => false, 'brand' => 'Castrol', 'standard' => 'DOT 4', 'dry_boil' => '250°C', 'wet_boil' => '160°C'],
    ['id' => 3, 'title' => 'Motul DOT 5.1', 'art' => 'MOT-BF01', 'volume' => '0.5 л', 'price' => 890, 'stock' => true, 'hit' => true, 'brand' => 'Motul', 'standard' => 'DOT 5.1', 'dry_boil' => '270°C', 'wet_boil' => '180°C'],
    ['id' => 4, 'title' => 'Brembo LCF 600 Plus DOT 4', 'art' => 'BREM-BF01', 'volume' => '0.5 л', 'price' => 720, 'stock' => true, 'hit' => false, 'brand' => 'Brembo', 'standard' => 'DOT 4', 'dry_boil' => '260°C', 'wet_boil' => '170°C'],
    ['id' => 5, 'title' => 'ATE SL.6 DOT 4', 'art' => 'ATE-BF001', 'volume' => '1 л', 'price' => 950, 'stock' => true, 'hit' => true, 'brand' => 'ATE', 'standard' => 'DOT 4', 'dry_boil' => '255°C', 'wet_boil' => '165°C'],
    ['id' => 6, 'title' => 'TRW PFG550 DOT 4', 'art' => 'TRW-BF001', 'volume' => '0.5 л', 'price' => 520, 'stock' => false, 'hit' => false, 'brand' => 'TRW', 'standard' => 'DOT 4', 'dry_boil' => '250°C', 'wet_boil' => '160°C'],
    ['id' => 7, 'title' => 'Bosch ESI6-32N DOT 4', 'art' => 'BOSCH-BF01', 'volume' => '1 л', 'price' => 780, 'stock' => true, 'hit' => false, 'brand' => 'Bosch', 'standard' => 'DOT 4', 'dry_boil' => '265°C', 'wet_boil' => '175°C'],
    ['id' => 8, 'title' => 'Febi Bilstein DOT 4', 'art' => 'FEBI-BF01', 'volume' => '0.5 л', 'price' => 480, 'stock' => true, 'hit' => false, 'brand' => 'Febi', 'standard' => 'DOT 4', 'dry_boil' => '250°C', 'wet_boil' => '160°C'],
    ['id' => 9, 'title' => 'Ravenol DOT 5.1', 'art' => 'RAV-BF001', 'volume' => '0.5 л', 'price' => 820, 'stock' => true, 'hit' => true, 'brand' => 'Ravenol', 'standard' => 'DOT 5.1', 'dry_boil' => '270°C', 'wet_boil' => '180°C'],
    ['id' => 10, 'title' => 'Shell DOT 4', 'art' => 'SHELL-BF01', 'volume' => '0.5 л', 'price' => 550, 'stock' => true, 'hit' => false, 'brand' => 'Shell', 'standard' => 'DOT 4', 'dry_boil' => '255°C', 'wet_boil' => '165°C'],
    ['id' => 11, 'title' => 'Liqui Moly DOT 5.1', 'art' => 'BRAKE002', 'volume' => '0.5 л', 'price' => 920, 'stock' => true, 'hit' => false, 'brand' => 'Liqui Moly', 'standard' => 'DOT 5.1', 'dry_boil' => '275°C', 'wet_boil' => '185°C'],
    ['id' => 12, 'title' => 'Castrol React DOT 5.1', 'art' => 'CAST-BF02', 'volume' => '0.5 л', 'price' => 850, 'stock' => true, 'hit' => false, 'brand' => 'Castrol', 'standard' => 'DOT 5.1', 'dry_boil' => '270°C', 'wet_boil' => '180°C'],
    ['id' => 13, 'title' => 'Motul DOT 4', 'art' => 'MOT-BF02', 'volume' => '1 л', 'price' => 1100, 'stock' => true, 'hit' => true, 'brand' => 'Motul', 'standard' => 'DOT 4', 'dry_boil' => '265°C', 'wet_boil' => '175°C'],
    ['id' => 14, 'title' => 'Brembo DOT 5.1', 'art' => 'BREM-BF02', 'volume' => '0.5 л', 'price' => 950, 'stock' => false, 'hit' => false, 'brand' => 'Brembo', 'standard' => 'DOT 5.1', 'dry_boil' => '275°C', 'wet_boil' => '185°C'],
    ['id' => 15, 'title' => 'ATE TYP 200 DOT 4', 'art' => 'ATE-BF002', 'volume' => '1 л', 'price' => 1200, 'stock' => true, 'hit' => true, 'brand' => 'ATE', 'standard' => 'DOT 4', 'dry_boil' => '260°C', 'wet_boil' => '170°C']
];

$search_query = $_GET['search'] ?? '';
$sort_type = $_GET['sort'] ?? 'default';
$brand_filter = $_GET['brand'] ?? '';
$standard_filter = $_GET['standard'] ?? '';

$items_per_page = 8;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$filtered_products = $brake_fluids;

if (!empty($search_query)) 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($search_query) 
    {
        return stripos($product['title'], $search_query) !== false || stripos($product['art'], $search_query) !== false || stripos($product['brand'], $search_query) !== false;
    });
}

if (!empty($brand_filter)) 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($brand_filter) 
    {
        return $product['brand'] === $brand_filter;
    });
}

if (!empty($standard_filter)) 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($standard_filter) 
    {
        return $product['standard'] === $standard_filter;
    });
}

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
$current_page = min($current_page, $total_pages);
$offset = ($current_page - 1) * $items_per_page;

$paginated_products = array_slice($filtered_products, $offset, $items_per_page);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тормозные жидкости - Лал-Авто</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/style.css">
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
                        <a class="nav-link text-dark" href="../../includes/oils.php">Масла и тех. жидкости</a>
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
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0" style="padding-top: 50px;">Тормозные жидкости</h1>
                <p class="text-muted mt-2">Качественные тормозные жидкости для надежной работы тормозной системы</p>
            </div>
        </div>
        <div class="filter-section mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="bi bi-funnel"></i> Фильтры</h2>
                <div>
                    <a href="?" class="btn btn-sm btn-outline-secondary me-2">Сбросить</a>
                    <button class="btn btn-sm btn-primary" onclick="applyFilters()">Применить</button>
                </div>
            </div>
            <form method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Бренд</label>
                            <select class="form-select" name="brand">
                                <option value="">Все бренды</option>
                                <option value="Liqui Moly" <?php echo $brand_filter === 'Liqui Moly' ? 'selected' : ''; ?>>Liqui Moly</option>
                                <option value="Castrol" <?php echo $brand_filter === 'Castrol' ? 'selected' : ''; ?>>Castrol</option>
                                <option value="Motul" <?php echo $brand_filter === 'Motul' ? 'selected' : ''; ?>>Motul</option>
                                <option value="Brembo" <?php echo $brand_filter === 'Brembo' ? 'selected' : ''; ?>>Brembo</option>
                                <option value="ATE" <?php echo $brand_filter === 'ATE' ? 'selected' : ''; ?>>ATE</option>
                                <option value="TRW" <?php echo $brand_filter === 'TRW' ? 'selected' : ''; ?>>TRW</option>
                                <option value="Bosch" <?php echo $brand_filter === 'Bosch' ? 'selected' : ''; ?>>Bosch</option>
                                <option value="Febi" <?php echo $brand_filter === 'Febi' ? 'selected' : ''; ?>>Febi</option>
                                <option value="Ravenol" <?php echo $brand_filter === 'Ravenol' ? 'selected' : ''; ?>>Ravenol</option>
                                <option value="Shell" <?php echo $brand_filter === 'Shell' ? 'selected' : ''; ?>>Shell</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Стандарт</label>
                            <select class="form-select" name="standard">
                                <option value="">Все</option>
                                <option value="DOT 4" <?php echo $standard_filter === 'DOT 4' ? 'selected' : ''; ?>>DOT 4</option>
                                <option value="DOT 5.1" <?php echo $standard_filter === 'DOT 5.1' ? 'selected' : ''; ?>>DOT 5.1</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Объем</label>
                            <select class="form-select" name="volume">
                                <option value="">Все</option>
                                <option value="0.5">0.5 л</option>
                                <option value="1">1 л</option>
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
                            <li><a class="dropdown-item" href="?<?php echo buildQueryString(['sort' => 'default', 'page' => 1]); ?>">По умолчанию</a></li>
                            <li><a class="dropdown-item" href="?<?php echo buildQueryString(['sort' => 'popular', 'page' => 1]); ?>">По популярности</a></li>
                            <li><a class="dropdown-item" href="?<?php echo buildQueryString(['sort' => 'price_asc', 'page' => 1]); ?>">По цене (возрастание)</a></li>
                            <li><a class="dropdown-item" href="?<?php echo buildQueryString(['sort' => 'price_desc', 'page' => 1]); ?>">По цене (убывание)</a></li>
                            <li><a class="dropdown-item" href="?<?php echo buildQueryString(['sort' => 'name', 'page' => 1]); ?>">По названию</a></li>
                        </ul>
                    </div>
                    <form method="GET" class="d-flex">
                        <div class="input-group" style="width: 200px;">
                            <input type="text" class="form-control" name="search" placeholder="Поиск..." value="<?php echo htmlspecialchars($search_query); ?>">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                            <?php 
                            if (!empty($search_query) || !empty($brand_filter) || !empty($standard_filter))
                            {
                            ?>
                                <a href="?" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                            <?php
                            }
                            ?>
                        </div>
                        <input type="hidden" name="brand" value="<?php echo $brand_filter; ?>">
                        <input type="hidden" name="standard" value="<?php echo $standard_filter; ?>">
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
                        echo '
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="product-card card h-100">
                                '.($product['hit'] ? '<span class="badge bg-danger position-absolute top-0 start-0 m-2">Хит</span>' : '').'
                                <img src="../../img/no-image.png" class="product-img card-img-top p-3" alt="'.$product['title'].'">
                                <div class="card-body">
                                    <h5 class="product-title card-title">'.$product['title'].'</h5>
                                    <p class="product-meta text-muted small mb-2">Арт. '.$product['art'].', '.$product['volume'].'</p>
                                    <div class="product-specs mb-2">
                                        <small class="text-muted">Стандарт: '.$product['standard'].'</small><br>
                                        <small class="text-muted">Темп. кипения: '.$product['dry_boil'].' (сух.) / '.$product['wet_boil'].' (влаж.)</small>
                                    </div>
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
                <?php if ($total_pages > 1) 
                { 
                ?>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php 
                        if ($current_page <= 1) 
                        { 
                            echo 'disabled'; 
                        } 
                        ?>">
                            <a class="page-link" href="?<?php echo buildQueryString(['page' => $current_page - 1]); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php 
                        if ($current_page > 3) 
                        { 
                        ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo buildQueryString(['page' => 1]); ?>">1</a>
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
                            ?>
                        <?php 
                        } 
                        ?>
                        <?php 
                        for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) 
                        { 
                        ?>
                        <li class="page-item 
                        <?php 
                        if ($i == $current_page) 
                        { 
                            echo 'active'; 
                        } 
                        ?>">
                            <a class="page-link" href="?<?php echo buildQueryString(['page' => $i]); ?>"><?php echo $i; ?></a>
                        </li>
                        <?php 
                        } 
                        ?>
                        <?php 
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
                            <a class="page-link" href="?<?php echo buildQueryString(['page' => $total_pages]); ?>"><?php echo $total_pages; ?></a>
                        </li>
                        <?php 
                        } 
                        ?>
                        <li class="page-item 
                        <?php 
                        if ($current_page >= $total_pages) 
                        { 
                            echo 'disabled'; 
                        } 
                        ?>">
                            <a class="page-link" href="?<?php echo buildQueryString(['page' => $current_page + 1]); ?>" aria-label="Next">
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
                ?>
            <?php 
            } 
            ?>
        </div>
    </div>

<?php 
    require_once("../../includes/footer.php"); 
?>

<script src="../../js/bootstrap.bundle.min.js"></script>
<script>
function applyFilters() 
{
    document.getElementById('filterForm').submit();
}

function buildQueryString(params) 
{
    let currentParams = new URLSearchParams(window.location.search);
    let keys = Object.keys(params);
    
    for (let i = 0; i < keys.length; i++) {
        let key = keys[i];
        if (params[key]) {
            currentParams.set(key, params[key]);
        } else {
            currentParams.delete(key);
        }
    }
    
    return currentParams.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    let paginationLinks = document.querySelectorAll('.pagination a');
    
    for (let i = 0; i < paginationLinks.length; i++) 
    {
        paginationLinks[i].addEventListener('click', function(e) 
        {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

            setTimeout(function() {
                window.location.href = this.href;
            }.bind(this), 300);
        });
    }
});
</script>
</body>
</html>

<?php
function buildQueryString($newParams = []) 
{
    $params = array_merge($_GET, $newParams);
    return http_build_query($params);
}
?>