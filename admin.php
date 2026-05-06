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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Административная панель - <?= ucfirst(str_replace('_', ' ', $current_section)) ?></title>
    <link rel="icon" href="img/iconAuto.png" type="image/png" height="32">
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

<div id="sidebarOverlay" class="sidebar-overlay"></div>
<div class="wrapper d-flex">
    <nav id="sidebar" class="active">
        <div class="sidebar-header d-flex justify-content-between align-items-center px-3 py-2">
            <h3 class="mb-0 d-none d-lg-block">Админ-панель</h3>
            <strong class="d-lg-none">Aдминка</strong>
            <button type="button" class="btn-close btn-close-white" id="sidebarCloseBtn" aria-label="Закрыть меню"></button>
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
            <li class="<?= isActiveSection('requests') ? 'active' : '' ?>">
                <a href="admin.php?section=requests">
                    <i class="bi bi-envelope-paper"></i>Заявки
                </a>
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
                <button type="button" id="sidebarToggle" class="btn btn-outline-secondary me-2" aria-label="Открыть меню">
                    <i class="bi bi-list"></i>
                </button>
                <a class="navbar-brand me-auto" href="admin.php?section=users_list">
                    <img src="img/Auto.png" alt="Лал-Авто" height="30" class="d-inline-block align-top">
                </a>
                <div class="d-flex align-items-center">
                    <div class="dropdown me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle notification-btn" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="notification-badge badge bg-danger" id="notificationBadge" style="display: none;">0</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown" style="min-width: 320px; max-width: 400px;">
                            <li class="notification-header">
                                <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>Уведомления</span>
                                    <button class="btn btn-sm btn-link mark-all-read" style="font-size: 12px;">Все прочитаны</button>
                                </h6>
                            </li>
                            <li class="notification-list" style="max-height: 400px; overflow-y: auto;">
                                <div class="text-center py-3" id="notificationsLoading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Загрузка...</span>
                                    </div>
                                </div>
                                <div id="notificationsList"></div>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-primary" href="#" id="viewAllNotifications">Показать все</a></li>
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
                case 'requests':
                    include 'sections/requests.php';
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
class NotificationManager 
{
    constructor() 
    {
        this.updateInterval = null;
        this.init();
    }
    
    init() 
    {
        this.loadNotifications();
        this.setupEventListeners();
        this.startAutoUpdate();
    }
    
    loadNotifications() 
    {
        fetch('sections/admin-notifications.php?action=get_notifications')
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    this.renderNotifications(data.notifications);
                    this.updateBadge(data.unread_count);
                }
            })
            .catch(error => console.error('Error loading notifications:', error));
    }
    
    renderNotifications(notifications) 
    {
        let container = document.getElementById('notificationsList');
        let loading = document.getElementById('notificationsLoading');
        
        if (!container) 
        {
            return;
        }
        
        if (loading) 
        {
            loading.style.display = 'none';
        }
        
        if (!notifications || notifications.length === 0) 
        {
            container.innerHTML = '<div class="text-center py-3 text-muted">Нет уведомлений</div>';
            return;
        }
        
        container.innerHTML = notifications.map(notif => `
            <div class="notification-item ${notif.is_read ? '' : 'unread'}" data-id="${notif.id}">
                <div class="d-flex">
                    <div class="notification-icon flex-shrink-0">
                        <i class="bi bi-${this.getIconByType(notif.type)}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <strong class="notification-title">${this.escapeHtml(notif.title)}</strong>
                            <small class="text-muted notification-time">${this.formatDate(notif.created_at)}</small>
                        </div>
                        <div class="notification-message small">${this.escapeHtml(notif.message)}</div>
                    </div>
                    ${!notif.is_read ? '<button class="btn btn-sm btn-link mark-read-btn" data-id="' + notif.id + '">✓</button>' : ''}
                </div>
            </div>
        `).join('');

        document.querySelectorAll('.mark-read-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                let id = btn.getAttribute('data-id');
                this.markAsRead(id);
            });
        });

        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', () => {
                let id = item.getAttribute('data-id');

                if (!item.classList.contains('unread')) 
                {
                    return;
                }

                this.markAsRead(id);
            });
        });
    }
    
    markAsRead(id) 
    {
        fetch(`sections/admin-notifications.php?action=mark_read&id=${id}`)
            .then(response => response.json())
            .then(() => {
                this.loadNotifications();
            });
    }
    
    markAllRead() 
    {
        fetch('sections/admin-notifications.php?action=mark_all_read')
            .then(response => response.json())
            .then(() => {
                this.loadNotifications();
            });
    }
    
    updateBadge(count) 
    {
        let badge = document.getElementById('notificationBadge');

        if (badge) 
        {
            if (count > 0) 
            {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-block';
            } 
            else 
            {
                badge.style.display = 'none';
            }
        }
    }
    
    getIconByType(type) 
    {
        let icons = {
            'info': 'info-circle',
            'success': 'check-circle',
            'warning': 'exclamation-triangle',
            'danger': 'x-circle'
        };

        return icons[type] || 'bell';
    }
    
    formatDate(dateString) 
    {
        let date = new Date(dateString);
        let now = new Date();
        let diff = now - date;
        let minutes = Math.floor(diff / 60000);
        let hours = Math.floor(diff / 3600000);
        let days = Math.floor(diff / 86400000);
        
        if (minutes < 1) 
        {
            return 'Только что';
        }

        if (minutes < 60) 
        {
            return `${minutes} мин назад`;
        }

        if (hours < 24) 
        {
            return `${hours} ч назад`;
        }

        if (days < 7) 
        {
            return `${days} дн назад`;
        }
        
        return date.toLocaleDateString('ru-RU');
    }
    
    escapeHtml(text) 
    {
        let div = document.createElement('div');
        div.textContent = text;

        return div.innerHTML;
    }
    
    setupEventListeners() 
    {
        let markAllBtn = document.querySelector('.mark-all-read');

        if (markAllBtn) 
        {
            markAllBtn.addEventListener('click', () => this.markAllRead());
        }
        
        let viewAllBtn = document.getElementById('viewAllNotifications');

        if (viewAllBtn) 
        {
            viewAllBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.showAllNotifications();
            });
        }

        let dropdown = document.getElementById('notificationDropdown');

        if (dropdown) 
        {
            dropdown.addEventListener('show.bs.dropdown', () => {
                this.loadNotifications();
            });
        }
    }
    
    showAllNotifications() 
    {
        fetch('sections/admin-notifications.php?action=get_notifications')
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    let modalHtml = `
                        <div class="modal fade" id="notificationsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                            <div class="modal-dialog modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Все уведомления</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        ${data.notifications.length === 0 ? 
                                            '<p class="text-center text-muted">Нет уведомлений</p>' :
                                            data.notifications.map(notif => `
                                                <div class="notification-item ${notif.is_read ? '' : 'unread'} mb-2 p-2 border rounded">
                                                    <div class="d-flex">
                                                        <div class="notification-icon flex-shrink-0 me-2">
                                                            <i class="bi bi-${this.getIconByType(notif.type)}"></i>
                                                        </div>
                                                        <div>
                                                            <strong>${this.escapeHtml(notif.title)}</strong>
                                                            <div class="small text-muted">${this.formatDate(notif.created_at)}</div>
                                                            <div class="mt-1">${this.escapeHtml(notif.message)}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('')
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    let oldModal = document.getElementById('notificationsModal');

                    if (oldModal) 
                    {
                        oldModal.remove();
                    }
                    
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                    let modal = new bootstrap.Modal(document.getElementById('notificationsModal'));
                    modal.show();
                }
            });
    }
    
    startAutoUpdate() 
    {
        this.updateInterval = setInterval(() => {
            this.loadNotifications();
        }, 30000);
    }
}

document.addEventListener('DOMContentLoaded', function() 
{
    let sidebar = document.getElementById('sidebar');
    let sidebarToggle = document.getElementById('sidebarToggle');
    let sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
    let overlay = document.getElementById('sidebarOverlay');

    let body = document.body;
    window.notificationManager = new NotificationManager();

    function openSidebar() 
    {
        if (!sidebar) 
        {
            return;
        }

        sidebar.classList.remove('active');

        if (overlay) 
        {
            overlay.classList.add('show');
        }

        body.style.overflow = 'hidden';
    }

    function closeSidebar() 
    {
        if (!sidebar) 
        {
            return;
        }

        sidebar.classList.add('active');

        if (overlay) 
        {
            overlay.classList.remove('show');
        }

        body.style.overflow = '';
    }

    function handleResize() 
    {
        let isDesktop = window.innerWidth >= 992.02;
        
        if (isDesktop) 
        {
            sidebar.classList.remove('active');

            if (overlay) 
            {
                overlay.classList.remove('show');
            }

            body.style.overflow = '';
        } 
        else 
        {
            sidebar.classList.add('active');

            if (overlay) 
            {
                overlay.classList.remove('show');
            }

            body.style.overflow = '';
        }
    }

    if (sidebarToggle) 
    {
        sidebarToggle.addEventListener('click', function(e) 
        {
            e.stopPropagation();

            if (window.innerWidth < 992.02) 
            {
                openSidebar();
            }
        });
    }

    if (sidebarCloseBtn) 
    {
        sidebarCloseBtn.addEventListener('click', function() 
        {
            closeSidebar();
        });
    }

    if (overlay) 
    {
        overlay.addEventListener('click', function() 
        {
            closeSidebar();
        });
    }

    document.addEventListener('keydown', function(e) 
    {
        if (e.key === 'Escape' && window.innerWidth < 992.02 && sidebar && !sidebar.classList.contains('active')) 
        {
            closeSidebar();
        }
    });

    if (sidebar) 
    {
        sidebar.querySelectorAll('a[href^="admin.php"]').forEach(link => {
            link.addEventListener('click', function() 
            {
                if (window.innerWidth < 992.02) 
                {
                    setTimeout(() => {
                        closeSidebar();
                    }, 150);
                }
            });
        });
    }

    window.addEventListener('resize', function() 
    {
        handleResize();
    });

    handleResize();

    document.querySelectorAll('#sidebar .dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) 
        {
            let targetId = this.getAttribute('href');

            if (targetId && targetId.startsWith('#')) 
            {
                e.preventDefault();
                let targetCollapse = document.querySelector(targetId);

                if (targetCollapse) 
                {
                    let bsCollapse = bootstrap.Collapse.getOrCreateInstance(targetCollapse, {
                        toggle: false
                    });

                    bsCollapse.toggle();
                    let isExpanded = targetCollapse.classList.contains('show');
                    this.setAttribute('aria-expanded', isExpanded);
                }
            }
        });
    });
});
</script>
</body>
</html>