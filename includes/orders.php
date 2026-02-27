<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    header("Location: ../index.php");
    exit();
}

$userData = [];
$orders = [];
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
    $orderSql = "SELECT o.id, o.order_number, o.order_date, o.total_amount, o.status, o.shipping_address, o.phone, COUNT(oi.id) as items_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC";
    
    $orderStmt = $conn->prepare($orderSql);
    
    if ($orderStmt) 
    {
        $orderStmt->bind_param("i", $userId);
        $orderStmt->execute();
        $orderResult = $orderStmt->get_result();
        
        while ($row = $orderResult->fetch_assoc()) 
        {
            $orders[] = $row;
        }

        $orderStmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) 
{
    $orderId = $_POST['order_id'] ?? 0;
    
    if ($orderId) 
    {
        $updateSql = "UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        
        if ($updateStmt) 
        {
            $updateStmt->bind_param("ii", $orderId, $userId);
            
            if ($updateStmt->execute()) 
            {
                $_SESSION['success_message'] = "Заказ успешно отменен!";
                header("Location: orders.php");
                exit();
            }
            
            $updateStmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заказы - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/orders-styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        <?php 
        if (isset($_SESSION['login_error'])) 
        { 
        ?>
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();

            <?php unset($_SESSION['login_error']); ?>
        <?php 
        } 
        ?>
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="orders-container">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h1 class="page-title mb-4">Мои заказы</h1>
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
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>История заказов</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        if (!empty($orders))
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
                                        foreach ($orders as $order)
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
                                                    switch ($order['status']) {
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
                                                    <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
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
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x display-1 text-muted"></i>
                                <h5 class="mt-3">У вас пока нет заказов</h5>
                                <p class="text-muted">Начните покупки в нашем каталоге</p>
                                <a href="assortment.php" class="btn btn-primary mt-2">
                                    <i class="bi bi-arrow-right me-1"></i>Перейти в каталог
                                </a>
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
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    function checkAndAddScrollbar() 
    {
        let tableBody = document.querySelector('.table tbody');
        let tableContainer = document.querySelector('.table-responsive');
        
        if (!tableBody || !tableContainer) 
        {
            return;
        }

        let rowCount = tableBody.querySelectorAll('tr').length;

        if (rowCount > 12)
         {
            tableContainer.classList.add('has-many-rows');
            
            let baseHeight = 400;
            let additionalHeight = Math.min((rowCount - 12) * 40, 300);
            
            tableContainer.style.maxHeight = (baseHeight + additionalHeight) + 'px';

            let table = document.querySelector('.table');

            if (table) 
            {
                table.id = 'orders-table';
            }
        } 
        else 
        {
            tableContainer.classList.remove('has-many-rows');
            tableContainer.style.maxHeight = '';
        }
    }

    checkAndAddScrollbar();
    window.addEventListener('resize', checkAndAddScrollbar);

    let observer = new MutationObserver(checkAndAddScrollbar);
    let tableBody = document.querySelector('.table tbody');

    if (tableBody) 
    {
        observer.observe(tableBody, { childList: true, subtree: true });
    }

    let tableRows = document.querySelectorAll('.table tbody tr');

    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.4s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) 
    {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    document.querySelectorAll('.alert .btn-close').forEach(button => {
        button.addEventListener('click', function() {
            let alert = this.closest('.alert');
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        });
    });

    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            if (!alert.classList.contains('alert-dismissible')) 
            {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        });
    }, 5000);
});
</script>
</body>
</html>