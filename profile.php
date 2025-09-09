<?php
session_start();
require_once("config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    header("Location: ../index.php");
    exit();
}

$userData = [];
$orderStats = [
    'total_orders' => 0,
    'total_amount' => 0,
    'pending_orders' => 0,
    'completed_orders' => 0
];
$userId = null;

if (isset($_SESSION['user'])) 
{
    $username = $_SESSION['user'];

    $sql = "SELECT * FROM users WHERE CONCAT(surname_users, ' ', name_users, ' ', patronymic_users) = ? OR person_users = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) 
    {
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) 
        {
            $userData = $result->fetch_assoc();
            $userId = $userData['id_users'] ?? 0;
        }

        $stmt->close();
    } 
    else 
    {
        error_log("Ошибка подготовки запроса: " . $conn->error);
    }

    if ($userId) 
    {
        $orderSql = "SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(total_amount), 0) as total_amount,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders
        FROM orders WHERE user_id = ?";
        
        $orderStmt = $conn->prepare($orderSql);
        
        if ($orderStmt) 
        {
            $orderStmt->bind_param("i", $userId);
            $orderStmt->execute();
            $orderResult = $orderStmt->get_result();
            
            if ($orderResult->num_rows > 0) 
            {
                $orderStats = $orderResult->fetch_assoc();
            }

            $orderStmt->close();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) 
{
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && $userId) 
    {
        $updateSql = "UPDATE users SET email_users = ?, phone_users = ? WHERE id_users = ?";
        $updateStmt = $conn->prepare($updateSql);
        
        if ($updateStmt) 
        {
            $updateStmt->bind_param("ssi", $email, $phone, $userId);

            if ($updateStmt->execute()) 
            {
                $_SESSION['success_message'] = "Данные успешно обновлены!";
                header("Location: profile.php");
                exit();
            }

            $updateStmt->close();
        }
    } 
    else 
    {
        $_SESSION['error_message'] = "Пожалуйста, заполните все поля корректно!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile-styles.css">
</head>
<body>

<nav class="navbar navbar-expand-xl navbar-light bg-light shadow-sm fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php"><img src="img/Auto.png" alt="Лал-Авто" height="75"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../index.php">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="includes/assortment.php">Каталог</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="includes/orders.php">Мои заказы</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="includes/support.php">Поддержка</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Профиль</a></li>
                        <li><a class="dropdown-item" href="includes/orders.php"><i class="bi bi-cart3 me-2"></i>Заказы</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="files/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выйти</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="profile-container">
    <div class="container py-5">
        <?php 
        if (isset($_SESSION['success_message']))
        { 
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php 
        } 
        ?>
        <?php 
        if (isset($_SESSION['error_message'])) 
        {
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php 
        } 
        ?>
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="profile-sidebar card shadow-sm">
                    <div class="card-body text-center">
                        <div class="profile-avatar mb-3">
                            <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto">
                                <i class="bi bi-person-fill" style="font-size: 2.5rem;"></i>
                            </div>
                            <button class="btn-avatar-edit" data-bs-toggle="tooltip" title="Сменить фото">
                                <i class="bi bi-camera-fill"></i>
                            </button>
                        </div>
                        <h5 class="card-title"><?= htmlspecialchars($_SESSION['user']); ?></h5>
                        <p class="text-muted small">Премиум статус</p>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="badge bg-success rounded-pill px-3 py-2 mb-3">
                            <i class="bi bi-star-fill me-1"></i>
                            <?= $orderStats['total_amount'] > 0 ? number_format($orderStats['total_amount'] * 0.05) : '256' ?> баллов
                        </div>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                            <i class="bi bi-person me-2"></i>Профиль
                        </a>
                        <a href="#orders" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="bi bi-cart3 me-2"></i>Мои заказы
                            <?php 
                            if ($orderStats['pending_orders'] > 0) 
                            {
                            ?>
                                <span class="badge bg-danger float-end"><?= $orderStats['pending_orders'] ?></span>
                            <?php 
                            } 
                            ?>
                        </a>
                        <a href="#wishlist" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="bi bi-heart me-2"></i>Избранное
                            <span class="badge bg-primary float-end">12</span>
                        </a>
                        <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                            <i class="bi bi-bell me-2"></i>Уведомления
                            <span class="badge bg-warning float-end">3</span>
                        </a>
                    </div>
                </div>
                <div class="stats-widget card shadow-sm mt-5">
                    <div class="card-header bg-transparent">
                        <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Статистика</h6>
                    </div>
                    <div class="card-body">
                        <div class="stat-item d-flex justify-content-between mb-2">
                            <span>Заказов:</span>
                            <strong><?= $orderStats['total_orders'] ?></strong>
                        </div>
                        <div class="stat-item d-flex justify-content-between mb-2">
                            <span>На сумму:</span>
                            <strong><?= number_format($orderStats['total_amount'], 0, ',', ' ') ?> ₽</strong>
                        </div>
                        <div class="stat-item d-flex justify-content-between">
                            <span>Активность:</span>
                            <span class="badge bg-success">Высокая</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="profile">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-dark d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-person me-2"></i>Личная информация</h5>
                                <?php 
                                if ($userId)
                                {
                                ?>
                                    <span class="badge bg-light text-primary">ID: <?= $userId ?></span>
                                <?php 
                                } 
                                ?>
                            </div>
                            <div class="card-body">
                                <form id="profileForm" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Фамилия</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($userData['surname_users'] ?? 'Не указано') ?>" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Имя</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($userData['name_users'] ?? 'Не указано') ?>" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Отчество</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($userData['patronymic_users'] ?? 'Не указано') ?>" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email<span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($userData['email_users'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Телефон<span class="text-danger">*</span></label>
                                            <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($userData['phone_users'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Дата регистрации</label>
                                            <input type="text" class="form-control" value="<?= date('d.m.Y', strtotime($userData['registration_date'] ?? '2024-01-01')) ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" name="update_profile" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>Сохранить изменения
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                                            <i class="bi bi-arrow-clockwise me-1"></i>Отменить
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="stat-card card text-center">
                                    <div class="card-body">
                                        <i class="bi bi-cart3 stat-icon text-primary"></i>
                                        <h3 class="stat-number"><?= $orderStats['total_orders'] ?></h3>
                                        <p class="stat-label">Всего<br>заказов</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="stat-card card text-center">
                                    <div class="card-body">
                                        <i class="bi bi-cash stat-icon text-success"></i>
                                        <h3 class="stat-number"><?= number_format($orderStats['total_amount'], 0, ',', ' ') ?> ₽</h3>
                                        <p class="stat-label">Общая<br>сумма</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="stat-card card text-center">
                                    <div class="card-body">
                                        <i class="bi bi-truck stat-icon text-info"></i>
                                        <h3 class="stat-number"><?= $orderStats['pending_orders'] ?></h3>
                                        <p class="stat-label">Активные<br>заказы</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="stat-card card text-center">
                                    <div class="card-body">
                                        <i class="bi bi-star-fill stat-icon text-warning"></i>
                                        <h3 class="stat-number">4.8</h3>
                                        <p class="stat-label">Рейтинг</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card shadow-sm mt-4">
                            <div class="card-header bg-transparent">
                                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Недавняя активность</h6>
                            </div>
                            <div class="card-body">
                                <div class="activity-timeline">
                                    <div class="activity-item">
                                        <div class="activity-icon bg-success">
                                            <i class="bi bi-cart-check"></i>
                                        </div>
                                        <div class="activity-content">
                                            <span>Заказ #12345 завершен</span>
                                            <small class="text-muted">2 часа назад</small>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-icon bg-info">
                                            <i class="bi bi-chat-dots"></i>
                                        </div>
                                        <div class="activity-content">
                                            <span>Оставлен отзыв к товару</span>
                                            <small class="text-muted">Вчера, 15:30</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="orders">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-dark">
                                <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>История заказов</h5>
                            </div>
                            <div class="card-body">
                                <?php 
                                if ($orderStats['total_orders'] > 0) 
                                {
                                ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>№ Заказа</th>
                                                    <th>Дата</th>
                                                    <th>Сумма</th>
                                                    <th>Статус</th>
                                                    <th>Действия</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>#12345</td>
                                                    <td>12.01.2024</td>
                                                    <td>12,450 ₽</td>
                                                    <td><span class="badge bg-success">Выполнен</span></td>
                                                    <td><button class="btn btn-sm btn-outline-primary">Подробнее</button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php 
                                }
                                else
                                { 
                                ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-cart-x display-1 text-muted"></i>
                                        <p class="text-muted mt-3">У вас пока нет заказов</p>
                                        <a href="includes/assortment.php" class="btn btn-primary mt-2">Перейти в каталог</a>
                                    </div>
                                <?php 
                                } 
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="wishlist">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-dark">
                                <h5 class="mb-0"><i class="bi bi-heart me-2"></i>Избранное</h5>
                            </div>
                            <div class="card-body">
                                <div class="wishlist-items">
                                    <div class="wishlist-item d-flex align-items-center">
                                        <img src="img/no-image.png" alt="Моторное масло" class="wishlist-item-img me-3">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Моторное масло Castrol 5W-40</h6>
                                            <p class="text-muted mb-1">Артикул: CAST-5W40-4L</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="wishlist-item-price">3,450 ₽</span>
                                                <span class="badge bg-success">В наличии</span>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <a href="includes/assortment.php" class="btn btn-primary btn-sm me-2">
                                                <i class="bi bi-cart-plus"></i>
                                            </a>
                                            <button class="btn btn-outline-danger btn-sm btn-remove-wishlist">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="wishlist-item d-flex align-items-center">
                                        <img src="img/no-image.png" alt="Воздушный фильтр" class="wishlist-item-img me-3">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">Воздушный фильтр Mann</h6>
                                            <p class="text-muted mb-1">Артикул: MANN-FILTER</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="wishlist-item-price">1,890 ₽</span>
                                                <span class="badge bg-warning">Под заказ</span>
                                            </div>
                                        </div>
                                        <div class="ms-3">
                                            <a href="includes/assortment.php" class="btn btn-primary btn-sm me-2">
                                                <i class="bi bi-cart-plus"></i>
                                            </a>
                                            <button class="btn btn-outline-danger btn-sm btn-remove-wishlist">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="notifications">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-dark">
                                <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Уведомления</h5>
                            </div>
                            <div class="card-body">
                                <div class="notification-list">
                                    <div class="notification-item alert alert-info">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">Новое поступление</h6>
                                                <p class="mb-1">Появились в наличии запчасти для Toyota Camry</p>
                                                <small class="text-muted">2 часа назад</small>
                                            </div>
                                            <button class="btn btn-sm btn-outline-secondary">Прочитано</button>
                                        </div>
                                    </div>
                                    <div class="notification-item alert alert-warning">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">Заказ готов к выдаче</h6>
                                                <p class="mb-1">Ваш заказ #12345 готов к получению</p>
                                                <small class="text-muted">Вчера, 15:30</small>
                                            </div>
                                            <button class="btn btn-sm btn-outline-secondary">Прочитано</button>
                                        </div>
                                    </div>
                                    <div class="notification-item alert alert-success">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">Скидка 15%</h6>
                                                <p class="mb-1">Специальное предложение для вас действует до конца недели</p>
                                                <small class="text-muted">3 дня назад</small>
                                            </div>
                                            <button class="btn btn-sm btn-outline-secondary">Прочитано</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer-section bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <p class="mb-3">Лал-Авто - ваш надежный партнер в мире автозапчастей с 2010 года. Мы предлагаем оригинальные и качественные автокомплектующие от ведущих мировых производителей с гарантией и профессиональной поддержкой.</p>
                <div class="contact-info">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-telephone me-2"></i>
                        <a href="tel:+74012656565" class="text-white">+7 (4012) 65-65-65</a>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-envelope me-2"></i>
                        <a href="mailto:info@lal-auto.ru" class="text-white">info@lal-auto.ru</a>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-geo-alt me-2"></i>
                        <span>г. Калининград, ул. Автомобильная, 12</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase mb-4 text-center">Быстрые ссылки</h5>
                <ul class="list-unstyled text-center">
                    <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">Главная</a></li>
                    <li class="mb-2"><a href="includes/shops.php" class="text-white text-decoration-none">Магазины</a></li>
                    <li class="mb-2"><a href="includes/service.php" class="text-white text-decoration-none">Автосервис</a></li>
                    <li class="mb-2"><a href="includes/assortment.php" class="text-white text-decoration-none">Ассортимент</a></li>
                    <li class="mb-2"><a href="includes/news.php" class="text-white text-decoration-none">Новости</a></li>
                    <li class="mb-2"><a href="includes/contacts.php" class="text-white text-decoration-none">Контакты</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <h5 class="text-uppercase mb-4 text-center">Информация</h5>
                <ul class="list-unstyled text-center">
                    <li class="mb-2"><a href="includes/privacy.php" class="text-white text-decoration-none">Политика конфиденциальности</a></li>
                    <li class="mb-2"><a href="includes/terms.php" class="text-white text-decoration-none">Условия использования</a></li>
                    <li class="mb-2"><a href="includes/delivery.php" class="text-white text-decoration-none">Оплата и доставка</a></li>
                    <li class="mb-2"><a href="includes/return.php" class="text-white text-decoration-none">Возврат и обмен</a></li>
                    <li class="mb-2"><a href="includes/vacancies.php" class="text-white text-decoration-none">Вакансии</a></li>
                    <li class="mb-2"><a href="includes/suppliers.php" class="text-white text-decoration-none">Поставщикам</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h5 class="text-uppercase mb-4 text-center">Мы в соцсетях</h5>
                <div class="social-links mb-4 text-center">
                    <a href="https://vk.com/lalauto" class="text-white me-3" target="_blank">
                        <img  src="img/image 33.png" alt="VK" width="32" height="32">
                    </a>
                    <a href="https://t.me/s/lalauto" class="text-white" target="_blank">
                        <img src="img/image 32.png" alt="Telegram" width="32" height="32">
                    </a>
                </div>
                <h5 class="text-uppercase mb-3 text-center">Подписаться на новости</h5>
                <form class="subscribe-form px-3 px-md-0">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Ваш email" aria-label="Ваш email">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-envelope-arrow-up"></i>
                        </button>
                    </div>
                </form>
                <div class="payment-methods mt-3 text-center">
                    <h6 class="mb-2">Способы оплаты:</h6>
                    <div class="d-flex justify-content-center">
                        <img src="img/1.png" alt="Visa" class="me-2" width="40">
                        <img src="img/2.png" alt="Mastercard" class="me-2" width="40">
                        <img src="img/3.png" alt="МИР" width="40">
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-4 bg-light">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0">© 2025 Лал-Авто. Все права защищены.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="includes/sitemap.php" class="text-white text-decoration-none me-3">Карта сайта</a>
                <a href="includes/api.php" class="text-white text-decoration-none">Разработчикам API</a>
            </div>
        </div>
    </div>
</footer>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="js/profile.js"></script>
</body>
</html>