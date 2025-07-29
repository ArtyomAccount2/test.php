<?php
session_start();
require_once("config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM `users` WHERE `login_users` != 'admin'");
$stmt->execute();
$result = $stmt->get_result();
$users = [];

while ($row = $result->fetch_assoc()) 
{
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Административная панель</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin-styles.css">
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
            <li class="active">
                <a href="#usersSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="bi bi-people"></i>Пользователи
                </a>
                <ul class="collapse list-unstyled" id="usersSubmenu">
                    <li>
                        <a href="#"><i class="bi bi-list-check"></i>Список пользователей</a>
                    </li>
                    <li>
                        <a href="#"><i class="bi bi-person-plus"></i>Добавить пользователя</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-shop"></i>Магазины
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-tools"></i>Автосервис
                </a>
            </li>
            <li>
                <a href="#productsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="bi bi-box-seam"></i>Товары
                </a>
                <ul class="collapse list-unstyled" id="productsSubmenu">
                    <li>
                        <a href="#"><i class="bi bi-card-checklist"></i>Каталог</a>
                    </li>
                    <li>
                        <a href="#"><i class="bi bi-plus-circle"></i>Добавить товар</a>
                    </li>
                    <li>
                        <a href="#"><i class="bi bi-tags"></i>Категории</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-newspaper"></i>Новости
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="bi bi-chat-square-text"></i>Отзывы
                </a>
            </li>
            <li>
                <a href="#">
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
                <a class="navbar-brand me-auto" href="#">
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
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Профиль</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Настройки</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../files/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выйти</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <h2 class="mb-3 mb-md-0">
                    <i class="bi bi-people me-2"></i>Управление пользователями
                </h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        <span class="d-none d-sm-inline">Добавить пользователя</span>
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-download me-1"></i>
                        <span class="d-none d-sm-inline">Экспорт</span>
                    </button>
                </div>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control" placeholder="Поиск пользователей...">
                                <button class="btn btn-outline-secondary" type="button">Найти</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-md-end gap-2">
                                <button type="button" class="btn btn-outline-secondary">
                                    <i class="bi bi-filter me-1"></i>
                                    <span class="d-none d-sm-inline">Фильтр</span>
                                </button>
                                <button type="button" class="btn btn-outline-secondary">
                                    <i class="bi bi-sliders me-1"></i>
                                    <span class="d-none d-sm-inline">Колонки</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">ID</th>
                                    <th class="d-none d-sm-table-cell">ФИО</th>
                                    <th>Логин</th>
                                    <th class="d-none d-md-table-cell">Email</th>
                                    <th class="d-none d-lg-table-cell">Телефон</th>
                                    <th class="d-none d-xl-table-cell">Регион</th>
                                    <th>Тип</th>
                                    <th width="100">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ($users as $user)
                                { 
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id_users'] ?? '') ?></td>
                                    <td class="d-none d-sm-table-cell">
                                        <?php
                                        $surname = htmlspecialchars($user['surname_users'] ?? '');
                                        $name = htmlspecialchars($user['name_users'] ?? '');
                                        $patronymic = htmlspecialchars($user['patronymic_users'] ?? '');
                                        
                                        if (empty($surname) || empty($name) || empty($patronymic)) 
                                        {
                                            echo 'Не указано';
                                        } 
                                        else 
                                        {
                                            echo trim(implode(' ', [$surname, $name, $patronymic]));
                                        }
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['login_users'] ?? '') ?></td>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($user['email_users'] ?? '') ?></td>
                                    <td class="d-none d-lg-table-cell"><?= htmlspecialchars($user['phone_users'] ?? '') ?></td>
                                    <td class="d-none d-xl-table-cell"><?= htmlspecialchars($user['region_users'] ?? '') ?></td>
                                    <td>
                                        <span class="badge bg-<?= empty($user['organization_users']) ? 'info' : 'warning' ?>">
                                            <?= empty($user['organization_users']) ? 'Физ.' : 'Юр.' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="files/edit_user.php?id=<?= $user['id_users'] ?>" class="btn btn-outline-primary" title="Редактировать">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="files/delete_user.php?id=<?= $user['id_users'] ?>" class="btn btn-outline-danger" title="Удалить">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php 
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let sidebar = document.getElementById('sidebar');
    let sidebarToggle = document.getElementById('sidebarToggle');
    let sidebarClose = document.getElementById('sidebarClose');
    
    if (sidebarToggle) 
    {
        sidebarToggle.addEventListener('click', function() 
        {
            sidebar.classList.toggle('active');
        });
    }

    if (sidebarClose) 
    {
        sidebarClose.addEventListener('click', function() 
        {
            sidebar.classList.add('active');
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
    
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

    tooltipTriggerList.map(function (tooltipTriggerEl) 
    {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    let currentPage = window.location.pathname.split('/').pop();
    let navLinks = document.querySelectorAll('#sidebar a');
    
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) 
        {
            link.classList.add('active');
            let parent = link.closest('.collapse');
            
            if (parent) 
            {
                parent.classList.add('show');
                let parentLink = parent.previousElementSibling;
                
                if (parentLink) 
                {
                    parentLink.classList.add('active');
                    parentLink.setAttribute('aria-expanded', 'true');
                }
            }
        }
    });
});
</script>
</body>
</html>