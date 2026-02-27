<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    header("Location: ../index.php");
    exit();
}

$userData = [];
$orderDetails = null;
$orderItems = [];
$userId = null;
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$orderId) 
{
    header("Location: orders.php");
    exit();
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
}

if ($userId) 
{
    $orderSql = "SELECT o.* FROM orders o WHERE o.id = ? AND o.user_id = ?";
    $orderStmt = $conn->prepare($orderSql);
    
    if ($orderStmt) 
    {
        $orderStmt->bind_param("ii", $orderId, $userId);
        $orderStmt->execute();
        $orderResult = $orderStmt->get_result();
        
        if ($orderResult->num_rows > 0) 
        {
            $orderDetails = $orderResult->fetch_assoc();
            $itemsSql = "SELECT oi.* FROM order_items oi WHERE oi.order_id = ?";
            $itemsStmt = $conn->prepare($itemsSql);
            $itemsStmt->bind_param("i", $orderId);
            $itemsStmt->execute();
            $itemsResult = $itemsStmt->get_result();
            
            while ($item = $itemsResult->fetch_assoc()) 
            {
                $orderItems[] = $item;
            }
            $itemsStmt->close();
        }
        $orderStmt->close();
    }
}

if (!$orderDetails) 
{
    header("Location: orders.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) 
{
    if ($orderDetails['status'] == 'pending' || $orderDetails['status'] == 'processing') 
    {
        $updateSql = "UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        
        if ($updateStmt) 
        {
            $updateStmt->bind_param("ii", $orderId, $userId);
            
            if ($updateStmt->execute()) 
            {
                $_SESSION['success_message'] = "Заказ успешно отменен!";
                header("Location: order_details.php?id=" . $orderId);
                exit();
            }
            
            $updateStmt->close();
        }
    }
}

$statusClass = '';
$statusText = '';

switch ($orderDetails['status']) 
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
        $statusText = $orderDetails['status'];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Детали заказа #<?= htmlspecialchars($orderDetails['order_number']) ?> - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/orders-styles.css">
</head>
<body>

<?php require_once("header.php"); ?>

<div class="order-details-page">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="orders.php" class="text-decoration-none">Мои заказы</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Заказ #<?= htmlspecialchars($orderDetails['order_number']) ?></li>
                    </ol>
                </nav>
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
                <div class="order-details-container mb-4 border border-2">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">
                            <i class="bi bi-box me-2"></i>Заказ #<?= htmlspecialchars($orderDetails['order_number']) ?>
                        </h4>
                        <span class="badge bg-<?= $statusClass ?> p-3 fs-6"><?= $statusText ?></span>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card info-card shadow-sm h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Информация о заказе</h6>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <p class="mb-2"><strong>Дата заказа:</strong> <?= date('d.m.Y H:i', strtotime($orderDetails['order_date'])) ?></p>
                                    <p class="mb-2"><strong>Номер заказа:</strong> #<?= htmlspecialchars($orderDetails['order_number']) ?></p>
                                    <p class="mb-2"><strong>Статус:</strong> <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span></p>
                                    <p class="mb-0"><strong>Сумма заказа:</strong> <span class="text-primary fw-bold"><?= number_format($orderDetails['total_amount'], 0, ',', ' ') ?> ₽</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card info-card shadow-sm h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="bi bi-truck me-2"></i>Информация о доставке</h6>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <p class="mb-2"><strong>Адрес доставки:</strong> <?= !empty($orderDetails['shipping_address']) ? htmlspecialchars($orderDetails['shipping_address']) : 'г. Калининград, ул. Автомобильная, 12' ?></p>
                                    <p class="mb-2"><strong>Телефон:</strong> <?= !empty($orderDetails['phone']) ? htmlspecialchars($orderDetails['phone']) : '+7 (4012) 65-65-65' ?></p>
                                    <p class="mb-2"><strong>Способ доставки:</strong> Самовывоз</p>
                                    <?php 
                                    if (!empty($orderDetails['notes']))
                                    {
                                    ?>
                                    <p class="mb-0"><strong>Примечания:</strong> <?= htmlspecialchars($orderDetails['notes']) ?></p>
                                    <?php 
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="bi bi-basket me-2"></i>Товары в заказе</h6>
                        </div>
                        <div class="card-body">
                            <div class="order-items-list">
                                <?php 
                                $subtotal = 0;

                                foreach ($orderItems as $item)
                                {
                                    $itemTotal = $item['price'] * $item['quantity'];
                                    $subtotal += $itemTotal;
                                ?>
                                <div class="order-item mb-3 p-3 border rounded bg-light">
                                    <div class="row align-items-center">
                                        <div class="col-md-1 col-3">
                                            <img src="../img/no-image.png" alt="<?= htmlspecialchars($item['product_name']) ?>" class="img-fluid rounded" style="max-width: 60px; height: auto;">
                                        </div>
                                        <div class="col-md-5 col-9">
                                            <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                            <?php 
                                            if (isset($item['product_id']) && !empty($item['product_id']))
                                            {
                                            ?>
                                                <small class="text-muted">Код товара: <?= htmlspecialchars($item['product_id']) ?></small>
                                            <?php 
                                            }

                                            else if ($item['category_product_id']) 
                                            {
                                                echo '<small class="text-muted">Код категории: ' . $item['category_product_id'] . '</small>';
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-2 col-4 text-md-center mt-2 mt-md-0">
                                            <span class="fw-bold"><?= number_format($item['price'], 0, ',', ' ') ?> ₽</span>
                                        </div>
                                        <div class="col-md-2 col-4 text-md-center mt-2 mt-md-0">
                                            <span class="badge bg-secondary">x<?= $item['quantity'] ?></span>
                                        </div>
                                        <div class="col-md-2 col-4 text-md-end mt-2 mt-md-0">
                                            <span class="fw-bold text-primary"><?= number_format($itemTotal, 0, ',', ' ') ?> ₽</span>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                }
                                ?>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Итоговая сумма</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Товары (<?= count($orderItems) ?>):</span>
                                                <span><?= number_format($subtotal, 0, ',', ' ') ?> ₽</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Доставка:</span>
                                                <span>0 ₽</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold fs-5">
                                                <span>Итого:</span>
                                                <span class="text-primary"><?= number_format($orderDetails['total_amount'], 0, ',', ' ') ?> ₽</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card shadow-sm h-100">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Дополнительная информация</h6>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-unstyled mb-0">
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Бесплатная доставка</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Гарантия на все товары</li>
                                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Оплата при получении</li>
                                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Возврат в течение 14 дней</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-4 no-print">
                        <a href="orders.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Вернуться к списку заказов
                        </a>
                        <div>
                            <?php 
                            if ($orderDetails['status'] == 'pending' || $orderDetails['status'] == 'processing')
                            {
                            ?>
                                <form method="POST" class="d-inline-block me-2">
                                    <button type="submit" name="cancel_order" class="btn btn-outline-danger" 
                                            onclick="return confirm('Вы уверены, что хотите отменить заказ?')">
                                        <i class="bi bi-x-circle me-2"></i>Отменить заказ
                                    </button>
                                </form>
                            <?php 
                            }
                            ?>
                            <button class="btn btn-outline-primary" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i>Распечатать
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once("footer.php"); ?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let cards = document.querySelectorAll('.card');

    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) 
                {
                    alert.remove();
                }
            }, 300);
        });
    }, 5000);
});
</script>
</body>
</html>