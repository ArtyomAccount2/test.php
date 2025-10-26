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

$special_fluids = [
    ['id' => 1, 'title' => 'Liqui Moly Scheiben-Reiniger', 'art' => 'SPEC001', 'volume' => '2 л', 'price' => 450, 'stock' => true, 'hit' => true, 'brand' => 'Liqui Moly', 'type' => 'Омыватель', 'application' => 'Лобовое стекло'],
    ['id' => 2, 'title' => 'Sonax AdBlue', 'art' => 'SONAX-AB01', 'volume' => '10 л', 'price' => 890, 'stock' => true, 'hit' => false, 'brand' => 'Sonax', 'type' => 'AdBlue', 'application' => 'Система SCR'],
    ['id' => 3, 'title' => 'Wynns Injector Cleaner', 'art' => 'WYNNS-IC01', 'volume' => '0.25 л', 'price' => 680, 'stock' => true, 'hit' => true, 'brand' => 'Wynns', 'type' => 'Очиститель', 'application' => 'Инжектор'],
    ['id' => 4, 'title' => 'Motul Clean Brake', 'art' => 'MOT-SP001', 'volume' => '0.4 л', 'price' => 520, 'stock' => true, 'hit' => false, 'brand' => 'Motul', 'type' => 'Очиститель', 'application' => 'Тормоза'],
    ['id' => 5, 'title' => 'Bardahl No Frost', 'art' => 'BARD-SP01', 'volume' => '0.5 л', 'price' => 320, 'stock' => false, 'hit' => false, 'brand' => 'Bardahl', 'type' => 'Антиобледенитель', 'application' => 'Замки'],
    ['id' => 6, 'title' => 'Gunk Engine Degreaser', 'art' => 'GUNK-SP01', 'volume' => '0.5 л', 'price' => 580, 'stock' => true, 'hit' => true, 'brand' => 'Gunk', 'type' => 'Очиститель', 'application' => 'Двигатель'],
    ['id' => 7, 'title' => 'CRC Contact Cleaner', 'art' => 'CRC-SP001', 'volume' => '0.4 л', 'price' => 420, 'stock' => true, 'hit' => false, 'brand' => 'CRC', 'type' => 'Очиститель', 'application' => 'Электрика'],
    ['id' => 8, 'title' => 'Permatex Anti-Seize', 'art' => 'PERM-SP01', 'volume' => '0.1 л', 'price' => 350, 'stock' => true, 'hit' => false, 'brand' => 'Permatex', 'type' => 'Смазка', 'application' => 'Резьба'],
    ['id' => 9, 'title' => 'WD-40 Specialist', 'art' => 'WD40-SP01', 'volume' => '0.4 л', 'price' => 480, 'stock' => true, 'hit' => true, 'brand' => 'WD-40', 'type' => 'Смазка', 'application' => 'Универсальная'],
    ['id' => 10, 'title' => '3M Windshield Wash', 'art' => '3M-SP001', 'volume' => '1 л', 'price' => 290, 'stock' => true, 'hit' => false, 'brand' => '3M', 'type' => 'Омыватель', 'application' => 'Стекло'],
    ['id' => 11, 'title' => 'Liqui Moly Kühlerschutz', 'art' => 'SPEC002', 'volume' => '1.5 л', 'price' => 550, 'stock' => true, 'hit' => false, 'brand' => 'Liqui Moly', 'type' => 'Охлаждающая', 'application' => 'Радиатор'],
    ['id' => 12, 'title' => 'Sonax Glass Cleaner', 'art' => 'SONAX-GC01', 'volume' => '0.5 л', 'price' => 380, 'stock' => true, 'hit' => false, 'brand' => 'Sonax', 'type' => 'Очиститель', 'application' => 'Стекло'],
    ['id' => 13, 'title' => 'Wynns Diesel Cleaner', 'art' => 'WYNNS-DC01', 'volume' => '0.25 л', 'price' => 720, 'stock' => true, 'hit' => true, 'brand' => 'Wynns', 'type' => 'Очиститель', 'application' => 'Дизель'],
    ['id' => 14, 'title' => 'Motul Chain Clean', 'art' => 'MOT-SP002', 'volume' => '0.4 л', 'price' => 610, 'stock' => true, 'hit' => false, 'brand' => 'Motul', 'type' => 'Очиститель', 'application' => 'Цепь'],
    ['id' => 15, 'title' => 'Bardahl Injector Clean', 'art' => 'BARD-SP02', 'volume' => '0.3 л', 'price' => 490, 'stock' => false, 'hit' => false, 'brand' => 'Bardahl', 'type' => 'Очиститель', 'application' => 'Инжектор']
];

$search_query = $_GET['search'] ?? '';
$sort_type = $_GET['sort'] ?? 'default';
$brand_filter = $_GET['brand'] ?? '';
$type_filter = $_GET['type'] ?? '';
$application_filter = $_GET['application'] ?? '';

$items_per_page = 8;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$filtered_products = $special_fluids;

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

if (!empty($type_filter)) 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($type_filter) 
    {
        return $product['type'] === $type_filter;
    });
}

if (!empty($application_filter)) 
{
    $filtered_products = array_filter($filtered_products, function($product) use ($application_filter) 
    {
        return $product['application'] === $application_filter;
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
    <title>Специальные жидкости - Лал-Авто</title>
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
        <div class="row mb-4 align-items-center" style="padding-top: 75px;">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold text-primary mb-3">Специальные жидкости</h1>
                <p class="lead text-muted mb-4">Очистители, смазки и другие специальные средства</p>
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
                    <div class="col-md-3">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Бренд</label>
                            <select class="form-select" name="brand">
                                <option value="">Все бренды</option>
                                <option value="Liqui Moly" <?php echo $brand_filter === 'Liqui Moly' ? 'selected' : ''; ?>>Liqui Moly</option>
                                <option value="Sonax" <?php echo $brand_filter === 'Sonax' ? 'selected' : ''; ?>>Sonax</option>
                                <option value="Wynns" <?php echo $brand_filter === 'Wynns' ? 'selected' : ''; ?>>Wynns</option>
                                <option value="Motul" <?php echo $brand_filter === 'Motul' ? 'selected' : ''; ?>>Motul</option>
                                <option value="Bardahl" <?php echo $brand_filter === 'Bardahl' ? 'selected' : ''; ?>>Bardahl</option>
                                <option value="Gunk" <?php echo $brand_filter === 'Gunk' ? 'selected' : ''; ?>>Gunk</option>
                                <option value="CRC" <?php echo $brand_filter === 'CRC' ? 'selected' : ''; ?>>CRC</option>
                                <option value="Permatex" <?php echo $brand_filter === 'Permatex' ? 'selected' : ''; ?>>Permatex</option>
                                <option value="WD-40" <?php echo $brand_filter === 'WD-40' ? 'selected' : ''; ?>>WD-40</option>
                                <option value="3M" <?php echo $brand_filter === '3M' ? 'selected' : ''; ?>>3M</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Тип</label>
                            <select class="form-select" name="type">
                                <option value="">Все</option>
                                <option value="Омыватель" <?php echo $type_filter === 'Омыватель' ? 'selected' : ''; ?>>Омыватель</option>
                                <option value="AdBlue" <?php echo $type_filter === 'AdBlue' ? 'selected' : ''; ?>>AdBlue</option>
                                <option value="Очиститель" <?php echo $type_filter === 'Очиститель' ? 'selected' : ''; ?>>Очиститель</option>
                                <option value="Антиобледенитель" <?php echo $type_filter === 'Антиобледенитель' ? 'selected' : ''; ?>>Антиобледенитель</option>
                                <option value="Смазка" <?php echo $type_filter === 'Смазка' ? 'selected' : ''; ?>>Смазка</option>
                                <option value="Охлаждающая" <?php echo $type_filter === 'Охлаждающая' ? 'selected' : ''; ?>>Охлаждающая</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Применение</label>
                            <select class="form-select" name="application">
                                <option value="">Все</option>
                                <option value="Лобовое стекло" <?php echo $application_filter === 'Лобовое стекло' ? 'selected' : ''; ?>>Лобовое стекло</option>
                                <option value="Система SCR" <?php echo $application_filter === 'Система SCR' ? 'selected' : ''; ?>>Система SCR</option>
                                <option value="Инжектор" <?php echo $application_filter === 'Инжектор' ? 'selected' : ''; ?>>Инжектор</option>
                                <option value="Тормоза" <?php echo $application_filter === 'Тормоза' ? 'selected' : ''; ?>>Тормоза</option>
                                <option value="Замки" <?php echo $application_filter === 'Замки' ? 'selected' : ''; ?>>Замки</option>
                                <option value="Двигатель" <?php echo $application_filter === 'Двигатель' ? 'selected' : ''; ?>>Двигатель</option>
                                <option value="Электрика" <?php echo $application_filter === 'Электрика' ? 'selected' : ''; ?>>Электрика</option>
                                <option value="Резьба" <?php echo $application_filter === 'Резьба' ? 'selected' : ''; ?>>Резьба</option>
                                <option value="Универсальная" <?php echo $application_filter === 'Универсальная' ? 'selected' : ''; ?>>Универсальная</option>
                                <option value="Стекло" <?php echo $application_filter === 'Стекло' ? 'selected' : ''; ?>>Стекло</option>
                                <option value="Радиатор" <?php echo $application_filter === 'Радиатор' ? 'selected' : ''; ?>>Радиатор</option>
                                <option value="Дизель" <?php echo $application_filter === 'Дизель' ? 'selected' : ''; ?>>Дизель</option>
                                <option value="Цепь" <?php echo $application_filter === 'Цепь' ? 'selected' : ''; ?>>Цепь</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="filter-group mb-3">
                            <label class="form-label filter-title">Объем</label>
                            <select class="form-select" name="volume">
                                <option value="">Все</option>
                                <option value="0.1">0.1 л</option>
                                <option value="0.25">0.25 л</option>
                                <option value="0.3">0.3 л</option>
                                <option value="0.4">0.4 л</option>
                                <option value="0.5">0.5 л</option>
                                <option value="1">1 л</option>
                                <option value="1.5">1.5 л</option>
                                <option value="2">2 л</option>
                                <option value="10">10 л</option>
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
                            if (!empty($search_query) || !empty($brand_filter) || !empty($type_filter) || !empty($application_filter))
                            {
                            ?>
                                <a href="?" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                            <?php
                            }
                            ?>
                        </div>
                        <input type="hidden" name="brand" value="<?php echo $brand_filter; ?>">
                        <input type="hidden" name="type" value="<?php echo $type_filter; ?>">
                        <input type="hidden" name="application" value="<?php echo $application_filter; ?>">
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
                                        <small class="text-muted">Тип: '.$product['type'].'</small><br>
                                        <small class="text-muted">Применение: '.$product['application'].'</small>
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
                <?php 
                if ($total_pages > 1) 
                { 
                ?>
                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item 
                        <?php 
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