<?php
session_start();
require_once("config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: index.php");
    exit();
}

$current_section = isset($_GET['section']) ? $_GET['section'] : 'users_list';
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

$users = [];

if ($current_section === 'users_list') 
{
    $stmt = $conn->prepare("SELECT * FROM `users` WHERE `login_users` != 'admin'");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) 
    {
        $users[] = $row;
    }
}

$products = [];

if ($current_section === 'products_catalog') 
{
    $stmt = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) 
    {
        $products[] = $row;
    }
}

$categories = [];

if ($current_section === 'categories') 
{
    $stmt = $conn->prepare("SELECT * FROM `category_products` ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) 
    {
        $categories[] = $row;
    }
}

$shops = [];

if ($current_section === 'shops') 
{
    $stmt = $conn->prepare("SELECT * FROM `shops` ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) 
    {
        $shops[] = $row;
    }
}

$news = [];

if ($current_section === 'news') 
{
    $stmt = $conn->prepare("SELECT * FROM `news` ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) 
    {
        $news[] = $row;
    }
}

$services = [];

if ($current_section === 'service') 
{
    $stmt = $conn->prepare("SELECT * FROM `services` ORDER BY id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) 
    {
        $services[] = $row;
    }
}

function isActiveSection($section) 
{
    global $current_section;
    return $current_section === $section ? 'active' : '';
}

function isActiveSubmenu($parent, $item = null) 
{
    global $current_section;

    if ($item) 
    {
        return $current_section === $item ? 'active' : '';
    }

    return strpos($current_section, $parent) === 0 ? 'active' : '';
}

if (isset($_GET['export']) && $current_section === 'users_list') 
{
    include 'files/export_users.php';
    exit();
}

if (isset($_GET['export']) && $current_section === 'products_catalog') 
{
    include 'files/export_products_catalog.php';
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Административная панель - <?= ucfirst(str_replace('_', ' ', $current_section)) ?></title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin-styles.css">
    <script>
        var currentSection = '<?= $current_section ?>';
        var userId = null;
    </script>
</head>
<body class="admin-body">

<div class="wrapper d-flex">
    <nav id="sidebar" class="active">
        <div class="sidebar-header d-flex justify-content-between align-items-center px-3 py-2">
            <h3 class="mb-0 d-none d-lg-block">Админ-панель</h3>
            <strong class="d-lg-none">AP</strong>
            <button type="button" class="btn-close btn-close-white d-lg-none" id="sidebarToggle"></button>
        </div>
        <ul class="list-unstyled components">
            <li class="<?= isActiveSubmenu('users') ? 'active' : '' ?>">
                <a href="#usersSubmenu" data-bs-toggle="collapse" aria-expanded="<?= isActiveSubmenu('users') ? 'true' : 'false' ?>" class="dropdown-toggle">
                    <i class="bi bi-people"></i>Пользователи
                </a>
                <ul class="collapse list-unstyled <?= isActiveSubmenu('users') ? 'show' : '' ?>" id="usersSubmenu">
                    <li class="<?= isActiveSection('users_list') ? 'active' : '' ?>">
                        <a href="admin.php?section=users_list"><i class="bi bi-list-check"></i>Список пользователей</a>
                    </li>
                    <li class="<?= isActiveSection('users_add') ? 'active' : '' ?>">
                        <a href="admin.php?section=users_add"><i class="bi bi-person-plus"></i>Добавить пользователя</a>
                    </li>
                </ul>
            </li>
            <li class="<?= isActiveSection('shops') ? 'active' : '' ?>">
                <a href="admin.php?section=shops">
                    <i class="bi bi-shop"></i>Магазины
                </a>
            </li>
            <li class="<?= isActiveSection('service') ? 'active' : '' ?>">
                <a href="admin.php?section=service">
                    <i class="bi bi-tools"></i>Автосервис
                </a>
            </li>
            <li class="<?= isActiveSubmenu('products') ? 'active' : '' ?>">
                <a href="#productsSubmenu" data-bs-toggle="collapse" aria-expanded="<?= isActiveSubmenu('products') ? 'true' : 'false' ?>" class="dropdown-toggle">
                    <i class="bi bi-box-seam"></i>Товары
                </a>
                <ul class="collapse list-unstyled <?= isActiveSubmenu('products') ? 'show' : '' ?>" id="productsSubmenu">
                    <li class="<?= isActiveSection('products_catalog') ? 'active' : '' ?>">
                        <a href="admin.php?section=products_catalog"><i class="bi bi-card-checklist"></i>Каталог</a>
                    </li>
                    <li class="<?= isActiveSection('products_add') ? 'active' : '' ?>">
                        <a href="admin.php?section=products_add"><i class="bi bi-plus-circle"></i>Добавить товар</a>
                    </li>
                </ul>
            </li>
            <li class="<?= isActiveSection('categories') ? 'active' : '' ?>">
                <a href="#categoriesSubmenu" data-bs-toggle="collapse" aria-expanded="<?= isActiveSubmenu('categories') ? 'true' : 'false' ?>" class="dropdown-toggle">
                    <i class="bi bi-tags"></i>Категории
                </a>
                <ul class="collapse list-unstyled <?= isActiveSubmenu('categories') ? 'show' : '' ?>" id="categoriesSubmenu">
                    <li class="<?= isActiveSection('categories') ? 'active' : '' ?>">
                        <a href="admin.php?section=categories"><i class="bi bi-list-check"></i>Список категорий</a>
                    </li>
                    <li class="<?= isActiveSection('categories_add') ? 'active' : '' ?>">
                        <a href="admin.php?section=categories_add"><i class="bi bi-plus-circle"></i>Добавить категорию</a>
                    </li>
                </ul>
            </li>
            <li class="<?= isActiveSection('news') ? 'active' : '' ?>">
                <a href="admin.php?section=news">
                    <i class="bi bi-newspaper"></i>Новости
                </a>
            </li>
            <li class="<?= isActiveSection('reviews') ? 'active' : '' ?>">
                <a href="admin.php?section=reviews">
                    <i class="bi bi-chat-square-text"></i>Отзывы
                </a>
            </li>
            <li class="<?= isActiveSection('settings') ? 'active' : '' ?>">
                <a href="admin.php?section=settings">
                    <i class="bi bi-gear"></i>Настройки
                </a>
            </li>
        </ul>
    </nav>

    <div id="content" class="w-100">
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container-fluid px-3">
                <button type="button" id="sidebarToggle" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-list"></i>
                </button>
                <a class="navbar-brand me-auto" href="admin.php?section=users_list">
                    <img src="img/Auto.png" alt="Лал-Авто" height="30" class="d-inline-block align-top">
                </a>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="badge bg-danger">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownNotifications">
                            <li><h6 class="dropdown-header">Уведомления</h6></li>
                            <li><a class="dropdown-item" href="#">Новый заказ #1234</a></li>
                            <li><a class="dropdown-item" href="#">Новый отзыв</a></li>
                            <li><a class="dropdown-item" href="#">Системное обновление</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-primary" href="#">Показать все</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span class="d-none d-md-inline">Администратор</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="admin.php"><i class="bi bi-person me-2"></i>Профиль</a></li>
                            <li><a class="dropdown-item" href="admin.php?section=settings"><i class="bi bi-gear me-2"></i>Настройки</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../files/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выйти</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid py-3">
            <?php
            switch ($current_section) 
            {
                case 'users_list':
                    include 'sections/users_list.php';
                    break;
                case 'users_add':
                    include 'sections/users_add.php';
                    break;
                case 'shops':
                    include 'sections/shops.php';
                    break;
                case 'service':
                    include 'sections/service.php';
                    break;
                case 'products_catalog':
                    include 'sections/products_catalog.php';
                    break;
                case 'products_add':
                    include 'sections/products_add.php';
                    break;
                case 'categories':
                    include 'sections/categories.php';
                    break;
                case 'category_products':
                    include 'sections/category_products.php';
                    break;
                case 'categories_add':
                    include 'sections/categories_add.php';
                    break;
                case 'edit_category_product':
                    include 'sections/edit_category_product.php';
                    break;
                case 'news':
                    include 'sections/news.php';
                    break;
                case 'reviews':
                    include 'sections/reviews.php';
                    break;
                case 'settings':
                    include 'sections/settings.php';
                    break;
                case 'edit_products':
                    include 'files/edit_products.php';
                    break;
                default:
                    include 'sections/users_list.php';
                    break;
            }
            ?>
        </div>
    </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let sidebar = document.getElementById('sidebar');
    let sidebarToggle = document.getElementById('sidebarToggle');
    
    if (sidebarToggle) 
    {
        sidebarToggle.addEventListener('click', function() 
        {
            sidebar.classList.toggle('active');
        });
    }

    document.addEventListener('click', function(e) 
    {
        if (window.innerWidth < 992 && !sidebar.contains(e.target) && e.target !== sidebarToggle) 
        {
            sidebar.classList.add('active');
        }
    });

    function handleResize() 
    {
        if (window.innerWidth >= 992) 
        {
            sidebar.classList.remove('active');
        }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize();

    document.querySelectorAll('.collapse').forEach(collapse => {
        if (collapse.classList.contains('show')) 
        {
            let parentLink = collapse.previousElementSibling;

            if (parentLink) 
            {
                parentLink.setAttribute('aria-expanded', 'true');
            }
        }
    });
});
</script>
</body>
</html>