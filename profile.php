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
    $sql = "SELECT id_users FROM users WHERE CONCAT(surname_users, ' ', name_users, ' ', patronymic_users) = ? OR person_users = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) 
    {
        $userData = $result->fetch_assoc();
        $userId = $userData['id_users'];
    }

    $stmt->close();
}

$wishlistItems = [];
$wishlistCount = 0;

if ($userId) 
{
    $sql = "SELECT * FROM wishlist WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) 
    {
        $wishlistItems[] = $row;
    }

    $wishlistCount = count($wishlistItems);
    $stmt->close();
}

$notifications = [];
$unreadCount = 0;

if ($userId) 
{
    $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) 
    {
        $notifications[] = $row;

        if (!$row['is_read']) 
        {
            $unreadCount++;
        }
    }

    $stmt->close();
}

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
        $orderSql = "SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_amount, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders, SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders FROM orders WHERE user_id = ?";
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if (isset($_POST['update_avatar']) && $userId) 
    {
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) 
        {
            $avatar = $_FILES['avatar'];
            
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $fileType = mime_content_type($avatar['tmp_name']);
            
            if (in_array($fileType, $allowedTypes)) {
                
                if ($avatar['size'] <= 2 * 1024 * 1024) 
                {
                    
                    $uploadDir = 'uploads/avatars/';
                    
                    if (!file_exists($uploadDir)) 
                    {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileExtension = pathinfo($avatar['name'], PATHINFO_EXTENSION);
                    $fileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExtension;
                    $filePath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($avatar['tmp_name'], $filePath)) 
                    {   
                        if (!empty($userData['avatar_users']) && $userData['avatar_users'] !== 'img/no-avatar.png') 
                        {
                            if (file_exists($userData['avatar_users'])) 
                            {
                                unlink($userData['avatar_users']);
                            }
                        }
                        
                        $avatarUpdateSql = "UPDATE users SET avatar_users = ? WHERE id_users = ?";
                        $avatarStmt = $conn->prepare($avatarUpdateSql);

                        if ($avatarStmt) 
                        {
                            $avatarStmt->bind_param("si", $filePath, $userId);

                            if ($avatarStmt->execute()) 
                            {
                                $_SESSION['success_message'] = "Аватар успешно обновлен!";
                            }

                            $avatarStmt->close();
                        }
                    } 
                    else 
                    {
                        $_SESSION['error_message'] = "Ошибка загрузки файла.";
                    }
                } 
                else 
                {
                    $_SESSION['error_message'] = "Размер файла не должен превышать 2MB.";
                }
            } 
            else 
            {
                $_SESSION['error_message'] = "Разрешены только файлы JPG, PNG и GIF.";
            }
        } 
        else 
        {
            $_SESSION['error_message'] = "Пожалуйста, выберите файл для загрузки.";
        }
        
        header("Location: profile.php");
        exit();
    }
    else if (isset($_POST['update_profile'])) 
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
                }

                $updateStmt->close();
            }
        } 
        else 
        {
            $_SESSION['error_message'] = "Пожалуйста, заполните все поля корректно!";
        }
        
        header("Location: profile.php");
        exit();
    }

    if (isset($_POST['wishlist_action'])) 
    {
        $productName = $_POST['product_name'] ?? '';
        $productImage = $_POST['product_image'] ?? 'img/no-image.png';
        $price = $_POST['price'] ?? 0;
        
        if ($productName && $userId) 
        {
            $checkSql = "SELECT id FROM wishlist WHERE user_id = ? AND product_name = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("is", $userId, $productName);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            
            if ($result->num_rows > 0) 
            {
                $row = $result->fetch_assoc();
                $deleteSql = "DELETE FROM wishlist WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $row['id']);
                $deleteStmt->execute();
                $_SESSION['success_message'] = "Удалено из избранного";
            } 
            else 
            {
                $insertSql = "INSERT INTO wishlist (user_id, product_name, product_image, price) VALUES (?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("issd", $userId, $productName, $productImage, $price);
                $insertStmt->execute();
                $_SESSION['success_message'] = "Добавлено в избранное";
            }
        }

        header("Location: profile.php");
        exit();
    }

    if (isset($_POST['remove_from_wishlist'])) 
    {
        $wishlistId = $_POST['wishlist_id'] ?? 0;

        if ($wishlistId && $userId) 
        {
            $sql = "DELETE FROM wishlist WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $wishlistId, $userId);
            $stmt->execute();
            $_SESSION['success_message'] = "Удалено из избранного";
        }

        header("Location: profile.php");
        exit();
    }

    if (isset($_POST['mark_notification_read'])) 
    {
        $notificationId = $_POST['notification_id'] ?? 0;

        if ($notificationId && $userId) 
        {
            $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $notificationId, $userId);
            $stmt->execute();
        }

        header("Location: profile.php");
        exit();
    }
}

$cartItems = [];
$cartTotal = 0;
$cartCount = 0;

if ($userId) 
{
    $cartSql = "SELECT * FROM cart WHERE user_id = ? ORDER BY created_at DESC";
    $cartStmt = $conn->prepare($cartSql);
    
    if ($cartStmt) 
    {
        $cartStmt->bind_param("i", $userId);
        $cartStmt->execute();
        $cartResult = $cartStmt->get_result();
        
        while ($item = $cartResult->fetch_assoc()) 
        {
            $cartItems[] = $item;
            $cartTotal += $item['price'] * $item['quantity'];
            $cartCount += $item['quantity'];
        }
        $cartStmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId) 
{
    if (isset($_POST['update_cart_item'])) 
    {
        $itemId = $_POST['item_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;

        $quantity = max(1, min(99, intval($quantity)));
        
        $updateSql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("iii", $quantity, $itemId, $userId);
        $updateStmt->execute();
        $updateStmt->close();
        $_SESSION['success_message'] = "Корзина обновлена!";
        
        header("Location: profile.php");
        exit();
    }

    if (isset($_POST['remove_cart_item'])) 
    {
        $itemId = $_POST['item_id'] ?? 0;
        
        $deleteSql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $itemId, $userId);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        $_SESSION['success_message'] = "Товар удален из корзины!";
        header("Location: profile.php");
        exit();
    }

    if (isset($_POST['clear_cart_profile'])) 
    {
        $clearSql = "DELETE FROM cart WHERE user_id = ?";
        $clearStmt = $conn->prepare($clearSql);
        $clearStmt->bind_param("i", $userId);
        $clearStmt->execute();
        $clearStmt->close();
        
        $_SESSION['success_message'] = "Корзина очищена!";
        header("Location: profile.php");
        exit();
    }
}

$ordersList = [];

if ($userId) 
{
    $ordersSql = "SELECT o.id, o.order_number, o.order_date, o.total_amount, o.status, o.shipping_address, o.phone, COUNT(oi.id) as items_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC";
    
    $ordersStmt = $conn->prepare($ordersSql);
    
    if ($ordersStmt) 
    {
        $ordersStmt->bind_param("i", $userId);
        $ordersStmt->execute();
        $ordersResult = $ordersStmt->get_result();
        
        while ($row = $ordersResult->fetch_assoc()) 
        {
            $ordersList[] = $row;
        }

        $ordersStmt->close();
    }
}

if (isset($_POST['cancel_order'])) 
{
    $orderId = $_POST['order_id'] ?? 0;
    
    if ($orderId && $userId) 
    {
        $updateSql = "UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        
        if ($updateStmt) 
        {
            $updateStmt->bind_param("ii", $orderId, $userId);
            
            if ($updateStmt->execute()) 
            {
                $_SESSION['success_message'] = "Заказ успешно отменен!";
            }

            $updateStmt->close();
        }
        
        header("Location: profile.php");
        exit();
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
                <li class="nav-item">
                    <a class="nav-link text-dark" href="includes/developer.php">Настройки разработчика</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Профиль</a></li>
                        <li><a class="dropdown-item" href="includes/orders.php"><i class="bi bi-list-check me-2"></i>Заказы</a></li>
                        <li><a class="dropdown-item" href="includes/cart.php"><i class="bi bi-cart3 me-2"></i>Корзина</a></li>
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
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-3">
                <div class="d-flex flex-column h-100">
                    <div class="profile-sidebar card shadow-sm h-100 mb-0">
                        <div class="card-body text-center">
                            <div class="profile-avatar mb-3">
                                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto">
                                    <?php 
                                    if (!empty($userData['avatar_users'])) 
                                    {
                                    ?>
                                        <img src="<?= htmlspecialchars($userData['avatar_users']) ?>" alt="Аватар" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                    <?php 
                                    } 
                                    else 
                                    {
                                    ?>
                                        <i class="bi bi-person-fill" style="font-size: 2.5rem;"></i>
                                    <?php 
                                    } 
                                    ?>
                                </div>
                                <form id="avatarForm" method="POST" enctype="multipart/form-data" class="d-inline">
                                    <input type="file" name="avatar" id="avatarInput" accept="image/*" class="d-none" onchange="document.getElementById('avatarForm').submit()">
                                    <button type="button" class="btn-avatar-edit" onclick="document.getElementById('avatarInput').click()" data-bs-toggle="tooltip" title="Сменить фото">
                                        <i class="bi bi-camera-fill"></i>
                                    </button>
                                    <input type="hidden" name="update_avatar" value="1">
                                </form>
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
                                <i class="bi bi-list-check me-2"></i>Мои заказы
                                <?php 
                                if ($orderStats['pending_orders'] > 0)
                                {
                                ?>
                                    <span class="badge bg-warning float-end"><?= $orderStats['pending_orders'] ?></span>
                                <?php 
                                }
                                ?>
                            </a>
                            <a href="#cart" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                                <i class="bi bi-cart3 me-2"></i>Корзина
                                <?php 
                                if ($cartCount > 0)
                                {
                                ?>
                                    <span class="badge bg-danger float-end"><?= $cartCount ?></span>
                                <?php 
                                }
                                ?>
                            </a>
                            <a href="#wishlist" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                                <i class="bi bi-heart me-2"></i>Избранное
                                <?php 
                                if ($wishlistCount > 0)
                                {
                                ?>
                                    <span class="badge bg-primary float-end"><?= $wishlistCount ?></span>
                                <?php 
                                }
                                ?>
                            </a>
                            <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                                <i class="bi bi-bell me-2"></i>Уведомления
                                <?php 
                                if ($unreadCount > 0)
                                {
                                ?>
                                    <span class="badge bg-warning float-end"><?= $unreadCount ?></span>
                                <?php
                                }
                                ?>
                            </a>
                        </div>
                    </div>
                    <div class="stats-widget card shadow-sm h-100 mt-4">
                        <div class="card-header bg-transparent">
                            <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Статистика</h6>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="stat-item d-flex justify-content-between mb-3">
                                <span>Заказов:</span>
                                <strong><?= $orderStats['total_orders'] ?></strong>
                            </div>
                            <div class="stat-item d-flex justify-content-between mb-3">
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
            </div>
            <div class="col-lg-9">
                <div class="tab-content">
                    <div class="tab-pane fade show active h-100" id="profile">
                        <div class="d-flex flex-column h-100">
                            <div class="card shadow-sm mb-4">
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
                            <div class="row mb-4 g-3">
                                <div class="col-sm-6 col-md-3">
                                    <div class="stat-card card text-center h-100">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="bi bi-cart3 stat-icon text-primary mb-2"></i>
                                            <h3 class="stat-number"><?= $orderStats['total_orders'] ?></h3>
                                            <p class="stat-label">Всего<br>заказов</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="stat-card card text-center h-100">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="bi bi-cash stat-icon text-success mb-2"></i>
                                            <h3 class="stat-number"><?= number_format($orderStats['total_amount'], 0, ',', ' ') ?> ₽</h3>
                                            <p class="stat-label">Общая<br>сумма</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="stat-card card text-center h-100">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="bi bi-truck stat-icon text-info mb-2"></i>
                                            <h3 class="stat-number"><?= isset($orderStats['pending_orders']) ? $orderStats['pending_orders'] : 0 ?></h3>
                                            <p class="stat-label">Активные<br>заказы</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="stat-card card text-center h-100">
                                        <div class="card-body d-flex flex-column justify-content-center">
                                            <i class="bi bi-star-fill stat-icon text-warning mb-2"></i>
                                            <h3 class="stat-number">4.8</h3>
                                            <p class="stat-label">Рейтинг</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow-sm h-100">
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
                    </div>
                    <div class="tab-pane fade h-100" id="orders">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-dark">
                                <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>История заказов</h5>
                            </div>
                            <div class="card-body">
                                <?php 
                                if (!empty($ordersList)) 
                                {
                                ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>№ Заказа</th>
                                                    <th>Дата</th>
                                                    <th>Кол-во товаров</th>
                                                    <th>Сумма</th>
                                                    <th>Статус</th>
                                                    <th>Действия</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                foreach ($ordersList as $order)
                                                {
                                                ?>
                                                    <tr>
                                                        <td>#<?= htmlspecialchars($order['order_number']) ?></td>
                                                        <td><?= date('d.m.Y', strtotime($order['order_date'])) ?></td>
                                                        <td><?= $order['items_count'] ?></td>
                                                        <td><?= number_format($order['total_amount'], 0, ',', ' ') ?> ₽</td>
                                                        <td>
                                                            <?php
                                                            $statusClass = '';
                                                            switch ($order['status']) 
                                                            {
                                                                case 'pending':
                                                                    $statusClass = 'warning';
                                                                    $statusText = 'В обработке';
                                                                    break;
                                                                case 'processing':
                                                                    $statusClass = 'info';
                                                                    $statusText = 'В процессе';
                                                                    break;
                                                                case 'completed':
                                                                    $statusClass = 'success';
                                                                    $statusText = 'Завершен';
                                                                    break;
                                                                case 'cancelled':
                                                                    $statusClass = 'danger';
                                                                    $statusText = 'Отменен';
                                                                    break;
                                                                default:
                                                                    $statusClass = 'secondary';
                                                                    $statusText = $order['status'];
                                                            }
                                                            ?>
                                                            <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                                        </td>
                                                        <td>
                                                            <a href="includes/order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-eye me-1"></i>Подробнее
                                                            </a>
                                                            <?php 
                                                            if ($order['status'] == 'pending' || $order['status'] == 'processing')
                                                            {
                                                            ?>
                                                                <form method="POST" class="d-inline-block ms-1">
                                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                                    <button type="submit" name="cancel_order" class="btn btn-sm btn-outline-danger" 
                                                                            onclick="return confirm('Вы уверены, что хотите отменить заказ?')">
                                                                        <i class="bi bi-x-circle me-1"></i>Отменить
                                                                    </button>
                                                                </form>
                                                            <?php
                                                            }
                                                            ?>
                                                        </td>
                                                    </tr>
                                                <?php 
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <div class="text-center py-5 flex-grow-1 d-flex flex-column justify-content-center">
                                        <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
                                        <h5 class="text-muted mb-3">Нет ваших заказов</h5>
                                        <p class="text-muted mb-4">У вас пока нет заказов</p>
                                        <a href="includes/assortment.php" class="btn btn-primary">
                                            <i class="bi bi-arrow-right me-2"></i>Перейти в каталог
                                        </a>
                                    </div>
                                <?php 
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade h-100" id="cart">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-dark d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Корзина</h5>
                                <?php 
                                if ($cartCount > 0)
                                {
                                ?>
                                    <span class="badge bg-danger"><?= $cartCount ?> товар(ов)</span>
                                <?php 
                                }
                                ?>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <?php 
                                if (!empty($cartItems))
                                {
                                ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th style="width: 100px;">Фото</th>
                                                    <th>Товар</th>
                                                    <th class="text-center">Цена</th>
                                                    <th class="text-center">Кол-во</th>
                                                    <th class="text-center">Сумма</th>
                                                    <th class="text-center">Действия</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                foreach ($cartItems as $item) 
                                                {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <img src="<?= htmlspecialchars($item['product_image']) ?>" 
                                                            alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                            class="cart-item-image">
                                                    </td>
                                                    <td>
                                                        <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                        <?php 
                                                        if ($item['product_id']) 
                                                        {
                                                            echo '<small class="text-muted">Код товара: ' . $item['product_id'] . '</small>';
                                                        } 
                                                        else if ($item['category_product_id']) 
                                                        {
                                                            echo '<small class="text-muted">Код категории: ' . $item['category_product_id'] . '</small>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="cart-item-price"><?= number_format($item['price'], 0, ',', ' ') ?> ₽</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <form method="POST" class="d-inline update-cart-form" data-item-id="<?= $item['id'] ?>">
                                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                                <button class="btn btn-outline-secondary minus-btn" type="button">-</button>
                                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                                                    min="1" max="99" class="form-control text-center quantity-input">
                                                                <button class="btn btn-outline-secondary plus-btn" type="button">+</button>
                                                            </div>
                                                            <button type="submit" name="update_cart_item" class="d-none submit-update"></button>
                                                        </form>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="cart-item-total fw-bold">
                                                            <?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> ₽
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <form method="POST" class="d-inline remove-cart-form ms-2">
                                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                            <button type="submit" name="remove_cart_item" 
                                                                    class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php 
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3 pt-3 border-top">
                                        <div class="row align-items-center">
                                            <div class="col-md-6 mb-3 mb-md-0 button-active">
                                                <div class="fw-bold fs-5">Итого: <?= number_format($cartTotal, 0, ',', ' ') ?> ₽</div>
                                                <small class="text-muted">Товаров: <?= $cartCount ?> шт.</small>
                                            </div>
                                            <div class="col-md-6 text-md-end button-active">
                                                <form method="POST" class="d-inline-block me-2">
                                                    <button type="submit" name="clear_cart_profile" class="btn btn-outline-danger btn-sm" onclick="return confirm('Очистить всю корзину?')">
                                                        <i class="bi bi-trash me-1"></i>Очистить корзину
                                                    </button>
                                                </form>
                                                <a href="includes/cart.php" class="btn btn-primary">
                                                    <i class="bi bi-arrow-right me-1"></i>Оформить заказ
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <div class="text-center py-5 flex-grow-1 d-flex flex-column justify-content-center">
                                        <i class="bi bi-cart display-1 text-muted mb-4"></i>
                                        <h5 class="text-muted mb-3">Ваша корзина пуста</h5>
                                        <p class="text-muted mb-4">Добавьте товары из каталога</p>
                                        <a href="includes/assortment.php" class="btn btn-primary">
                                            <i class="bi bi-arrow-right me-2"></i>Перейти в каталог
                                        </a>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade h-100" id="wishlist">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-dark">
                                <h5 class="mb-0"><i class="bi bi-heart me-2"></i>Избранное</h5>
                            </div>
                            <div class="card-body">
                                <?php 
                                if (!empty($wishlistItems))
                                {
                                ?>
                                    <div class="wishlist-items">
                                        <?php 
                                        foreach ($wishlistItems as $item)
                                        {
                                        ?>
                                            <div class="wishlist-item d-flex align-items-center mb-3 p-3 border rounded">
                                                <img src="<?= $item['product_image'] ?: 'img/no-image.png' ?>" 
                                                    alt="Товар" class="wishlist-item-img me-3" style="width: 80px; height: 80px;">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="text-success fw-bold"><?= number_format($item['price'], 0, ',', ' ') ?> ₽</span>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="wishlist_id" value="<?= $item['id'] ?>">
                                                            <button type="submit" name="remove_from_wishlist" 
                                                                    class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-trash"></i> Удалить
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php 
                                        }
                                        ?>
                                    </div>
                                <?php 
                                }
                                else
                                { 
                                ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-heart display-1 text-muted mb-3"></i>
                                        <h5>Список избранного пуст</h5>
                                        <p class="text-muted">Добавляйте товары кнопкой ❤️ в каталоге</p>
                                        <a href="includes/assortment.php" class="btn btn-primary mt-2">
                                            Перейти в каталог
                                        </a>
                                    </div>
                                <?php 
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade h-100" id="notifications">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-dark">
                                <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Уведомления</h5>
                            </div>
                            <div class="card-body" id="notificationsContainer">
                                <?php 
                                if (!empty($notifications))
                                {
                                ?>
                                    <div class="notification-list">
                                        <?php 
                                        foreach ($notifications as $notification)
                                        {
                                            if (!$notification['is_read']) 
                                            {
                                                $alertClass = 'alert-info';
                                                $bgClass = '';
                                            } 
                                            else 
                                            {
                                                $alertClass = '';
                                                $bgClass = 'bg-light';
                                            }
                                            ?>
                                            <div class="notification-item alert <?= $alertClass ?> <?= $bgClass ?> mb-3 p-3 rounded" 
                                                data-id="<?= $notification['id'] ?>"
                                                data-read="<?= $notification['is_read'] ? '1' : '0' ?>">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="mb-1"><?= htmlspecialchars($notification['title']) ?></h6>
                                                        <p class="mb-1"><?= htmlspecialchars($notification['message']) ?></p>
                                                        <small class="text-muted">
                                                            <?= date('d.m.Y H:i', strtotime($notification['created_at'])) ?>
                                                        </small>
                                                    </div>
                                                    <div class="notification-actions">
                                                        <?php 
                                                        if (!$notification['is_read'])
                                                        {
                                                        ?>
                                                            <button class="btn btn-sm btn-outline-success mark-as-read-btn" data-id="<?= $notification['id'] ?>">
                                                                Прочитано
                                                            </button>
                                                        <?php 
                                                        }
                                                        ?>
                                                        <button class="btn btn-sm btn-outline-danger delete-notification-btn" data-id="<?= $notification['id'] ?>">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php 
                                        }
                                        ?>
                                    </div>
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <div class="text-center py-5" id="noNotifications">
                                        <i class="bi bi-bell-slash display-1 text-muted mb-3"></i>
                                        <h5>Уведомлений пока нет</h5>
                                        <p class="text-muted">Здесь будут появляться ваши уведомления</p>
                                    </div>
                                <?php 
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="socialFloat" class="social-float-container">
        <button id="socialToggle" class="social-toggle-btn" title="Социальные сети">
            <i class="bi bi-chevron-up"></i>
        </button>
        <div class="social-icons-container">
            <a href="https://vk.com/lalauto" class="social-icon-float" target="_blank" title="ВКонтакте">
                <img src="img/image 33.png" alt="VK" width="32" height="32">
            </a>
            <a href="https://t.me/s/lalauto" class="social-icon-float" target="_blank" title="Telegram">
                <img src="img/image 32.png" alt="Telegram" width="32" height="32">
            </a>
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
                <p class="mb-0">© 2026 Лал-Авто. Все права защищены.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="includes/sitemap.php" class="text-white text-decoration-none me-3">Карта сайта</a>
                <a href="includes/api.php" class="text-white text-decoration-none">Разработчикам API</a>
            </div>
        </div>
    </div>
</footer>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/profile.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let formSubmissionInProgress = false;

    document.addEventListener('click', function(e) 
    {
        let target = e.target;

        if (target.classList.contains('plus-btn')) 
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            if (formSubmissionInProgress)
            {
                return;
            }
            
            let input = target.closest('.input-group').querySelector('.quantity-input');
            let form = target.closest('.update-cart-form');
            
            if (!input || !form) 
            {
                return;
            }
            
            let currentValue = parseInt(input.value) || 1;
            
            if (currentValue < 99) 
            {
                input.value = currentValue + 1;
                submitCartForm(form);
            }
        }

        if (target.classList.contains('minus-btn')) 
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            if (formSubmissionInProgress)
            {
                return;
            }
            
            let input = target.closest('.input-group').querySelector('.quantity-input');
            let form = target.closest('.update-cart-form');
            
            if (!input || !form) 
            {
                return;
            }
            
            let currentValue = parseInt(input.value) || 1;

            if (currentValue > 1) 
            {
                input.value = currentValue - 1;
                submitCartForm(form);
            }
        }
    });

    document.addEventListener('change', function(e) 
    {
        if (e.target.classList.contains('quantity-input'))
        {
            e.preventDefault();
            e.stopImmediatePropagation();
            
            if (formSubmissionInProgress) 
            {
                return;
            }
            
            let input = e.target;
            let form = input.closest('.update-cart-form');
            
            if (!form) 
            {
                return;
            }
            
            let value = parseInt(input.value) || 1;
            
            if (value < 1) 
            {
                input.value = 1;
                return;
            }

            if (value > 99) 
            {
                input.value = 99;
            }
            
            submitCartForm(form);
        }
    });

    function submitCartForm(form) 
    {
        if (formSubmissionInProgress) 
        {
            return;
        }
        
        formSubmissionInProgress = true;
        
        let submitBtn = form.querySelector('.submit-update');

        if (submitBtn) 
        {
            submitBtn.click();
        }

        setTimeout(() => {
            formSubmissionInProgress = false;
        }, 300);
    }

    document.addEventListener('submit', function(e) 
    {
        let form = e.target;
        
        if (form.classList.contains('remove-cart-form')) 
        {
            if (!confirm('Удалить товар из корзины?')) 
            {
                e.preventDefault();
                return false;
            }
        }
    });

    document.querySelectorAll('.plus-btn, .minus-btn').forEach(btn => {
        btn.replaceWith(btn.cloneNode(true));
    });
});
</script>
</body>
</html>