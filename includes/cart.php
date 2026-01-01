<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    header("Location: ../index.php");
    exit();
}

$userData = [];
$cartItems = [];
$cartTotal = 0;
$cartCount = 0;
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
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    if (isset($_POST['add_to_cart'])) 
    {
        $productId = $_POST['product_id'] ?? 0;
        $productName = $_POST['product_name'] ?? '';
        $productImage = $_POST['product_image'] ?? 'no-image.png';
        $price = $_POST['price'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        
        if ($userId && $productName && $price > 0) 
        {
            $checkSql = "SELECT * FROM cart WHERE user_id = ? AND product_name = ?";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bind_param("is", $userId, $productName);
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
                $insertSql = "INSERT INTO cart (user_id, product_id, product_name, product_image, price, quantity) VALUES (?, ?, ?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("iissdi", $userId, $productId, $productName, $productImage, $price, $quantity);
                $insertStmt->execute();
                $insertStmt->close();
                $_SESSION['success_message'] = "Товар добавлен в корзину!";
            }
        } 
        else 
        {
            $_SESSION['error_message'] = "Ошибка добавления товара в корзину!";
        }
        
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'cart.php'));
        exit();
    }
    
    if (isset($_POST['update_cart'])) 
    {
        $itemId = $_POST['item_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        
        if ($quantity <= 0) 
        {
            $deleteSql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("ii", $itemId, $userId);
            $deleteStmt->execute();
            $deleteStmt->close();
            $_SESSION['success_message'] = "Товар удален из корзины!";
        } 
        else 
        {
            $updateSql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("iii", $quantity, $itemId, $userId);
            $updateStmt->execute();
            $updateStmt->close();
            $_SESSION['success_message'] = "Корзина обновлена!";
        }
        
        header("Location: cart.php");
        exit();
    }
    
    if (isset($_POST['remove_from_cart'])) 
    {
        $itemId = $_POST['item_id'] ?? 0;
        
        $deleteSql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $itemId, $userId);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        $_SESSION['success_message'] = "Товар удален из корзины!";
        header("Location: cart.php");
        exit();
    }
    
    if (isset($_POST['checkout'])) 
    {
        if (empty($cartItems)) 
        {
            $_SESSION['error_message'] = "Корзина пуста!";
            header("Location: cart.php");
            exit();
        }

        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $shippingAddress = $_POST['shipping_address'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $notes = $_POST['notes'] ?? '';

        $orderSql = "INSERT INTO orders (order_number, user_id, total_amount, shipping_address, phone, notes) VALUES (?, ?, ?, ?, ?, ?)";
        $orderStmt = $conn->prepare($orderSql);
        $orderStmt->bind_param("sidis", $orderNumber, $userId, $cartTotal, $shippingAddress, $phone, $notes);
        
        if ($orderStmt->execute()) 
        {
            $orderId = $orderStmt->insert_id;
            
            foreach ($cartItems as $item) 
            {
                $itemSql = "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)";
                $itemStmt = $conn->prepare($itemSql);
                $itemStmt->bind_param("iisdi", $orderId, $item['product_id'], $item['product_name'], $item['price'], $item['quantity']);
                $itemStmt->execute();
                $itemStmt->close();
            }

            $clearSql = "DELETE FROM cart WHERE user_id = ?";
            $clearStmt = $conn->prepare($clearSql);
            $clearStmt->bind_param("i", $userId);
            $clearStmt->execute();
            $clearStmt->close();
            
            $_SESSION['success_message'] = "Заказ №{$orderNumber} успешно оформлен!";
            $_SESSION['order_number'] = $orderNumber;
            header("Location: orders.php");
            exit();
        } 
        else 
        {
            $_SESSION['error_message'] = "Ошибка при оформлении заказа!";
            header("Location: cart.php");
            exit();
        }
    }
    
    if (isset($_POST['clear_cart'])) 
    {
        $clearSql = "DELETE FROM cart WHERE user_id = ?";
        $clearStmt = $conn->prepare($clearSql);
        $clearStmt->bind_param("i", $userId);
        $clearStmt->execute();
        $clearStmt->close();
        
        $_SESSION['success_message'] = "Корзина очищена!";
        header("Location: cart.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/cart-styles.css">
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="cart-container" style="margin-top: 100px;">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="page-title mb-4">Корзина</h1>
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
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-cart3 me-2"></i>Товары в корзине</h5>
                        <?php 
                        if ($cartCount > 0) 
                        {
                        ?>
                            <span class="badge bg-light text-primary"><?= $cartCount ?> товар(ов)</span>
                        <?php 
                        }
                        ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <?php 
                        if (empty($cartItems))
                        {
                        ?>
                            <div class="text-center py-5 flex-grow-1 d-flex flex-column justify-content-center">
                                <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
                                <h5 class="text-muted mb-3">Ваша корзина пуста</h5>
                                <p class="text-muted mb-4">Начните покупки в нашем каталоге</p>
                                <a href="assortment.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-right me-2"></i>Перейти в каталог
                                </a>
                            </div>
                        <?php 
                        }
                        else
                        {
                        ?>
                            <div class="row flex-grow-1">
                                <div class="col-lg-8 d-flex flex-column">
                                    <div class="table-responsive flex-grow-1">
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
                                                        <img src="../<?= htmlspecialchars($item['product_image']) ?>" 
                                                             alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                                             class="cart-item-image">
                                                    </td>
                                                    <td>
                                                        <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                        <?php 
                                                        if ($item['product_id'])
                                                        {
                                                        ?>
                                                            <small class="text-muted">Код: <?= $item['product_id'] ?></small>
                                                        <?php 
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="cart-item-price"><?= number_format($item['price'], 0, ',', ' ') ?> ₽</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                            <div class="input-group input-group-sm" style="width: 120px;">
                                                                <button class="btn btn-outline-secondary minus-btn" type="button">-</button>
                                                                <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                                                       min="1" max="99" class="form-control text-center quantity-input">
                                                                <button class="btn btn-outline-secondary plus-btn" type="button">+</button>
                                                            </div>
                                                            <button type="submit" name="update_cart" class="btn btn-link btn-sm mt-1" style="display: none;">
                                                                Обновить
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="cart-item-total fw-bold">
                                                            <?= number_format($item['price'] * $item['quantity'], 0, ',', ' ') ?> ₽
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                                            <button type="submit" name="remove_from_cart" 
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('Удалить товар из корзины?')">
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
                                    <div class="mt-3">
                                        <form method="POST">
                                            <button type="submit" name="clear_cart" 
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Очистить всю корзину?')">
                                                <i class="bi bi-trash me-1"></i>Очистить корзину
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Оформление заказа</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="order-summary mb-4">
                                                <h6 class="mb-3">Сводка заказа</h6>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Товары (<?= $cartCount ?>):</span>
                                                    <span><?= number_format($cartTotal, 0, ',', ' ') ?> ₽</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Доставка:</span>
                                                    <span>0 ₽</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between fw-bold fs-5">
                                                    <span>Итого:</span>
                                                    <span class="text-primary"><?= number_format($cartTotal, 0, ',', ' ') ?> ₽</span>
                                                </div>
                                            </div>
                                            <form method="POST">
                                                <div class="mb-3">
                                                    <label for="shipping_address" class="form-label">Адрес доставки</label>
                                                    <textarea name="shipping_address" id="shipping_address" class="form-control" rows="2" placeholder="г. Калининград, ул. Автомобильная, 12"></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">Телефон</label>
                                                    <input type="tel" name="phone" id="phone" class="form-control" value="<?= htmlspecialchars($userData['phone_users'] ?? '') ?>" placeholder="+7 (XXX) XXX-XX-XX">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="notes" class="form-label">Примечания к заказу</label>
                                                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="Дополнительные пожелания"></textarea>
                                                </div>
                                                <button type="submit" name="checkout" class="btn btn-primary w-100 btn-lg">
                                                    <i class="bi bi-check-circle me-2"></i>Оформить заказ
                                                </button>
                                                <div class="text-center mt-3">
                                                    <small class="text-muted">
                                                        Нажимая кнопку, вы соглашаетесь с <a href="terms.php">условиями обработки данных</a>
                                                    </small>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="card shadow-sm mt-4">
                                        <div class="card-body">
                                            <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Информация</h6>
                                            <ul class="list-unstyled small">
                                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Бесплатная доставка от 5,000 ₽</li>
                                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Гарантия на все товары</li>
                                                <li class="mb-2"><i class="bi bi-check text-success me-2"></i>Возврат в течение 14 дней</li>
                                                <li><i class="bi bi-check text-success me-2"></i>Оплата при получении</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
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

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script src="../js/cart.js"></script>
</body>
</html>