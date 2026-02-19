<?php
if (!isset($_GET['category_type']) || empty($_GET['category_type'])) 
{
    $_SESSION['error_message'] = 'Не указан тип категории';
    echo '<script>window.location.href = "admin.php?section=categories";</script>';
    exit();
}

$category_type = $_GET['category_type'];

$display_names = [
    'antifreeze' => 'Антифризы',
    'brake-fluid' => 'Тормозные жидкости',
    'cooling-fluid' => 'Охлаждающие жидкости',
    'power-steering' => 'Жидкости ГУР',
    'special-fluid' => 'Специальные жидкости',
    'kit' => 'Комплекты',
    'transmission-oil' => 'Трансмиссионные масла',
    'motor-oil' => 'Моторные масла'
];

$category_name = $display_names[$category_type] ?? ucfirst(str_replace('-', ' ', $category_type));

if (isset($_GET['delete_product_id'])) 
{
    $delete_id = (int)$_GET['delete_product_id'];
    $check_stmt = $conn->prepare("SELECT id FROM category_products WHERE id = ? AND category_type = ?");
    $check_stmt->bind_param("is", $delete_id, $category_type);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) 
    {
        $delete_stmt = $conn->prepare("DELETE FROM category_products WHERE id = ?");
        $delete_stmt->bind_param("i", $delete_id);
        
        if ($delete_stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Товар успешно удален из категории';
        } 
        else 
        {
            $_SESSION['error_message'] = 'Ошибка при удалении товара: ' . $conn->error;
        }
    } 
    else 
    {
        $_SESSION['error_message'] = 'Товар не найден или не принадлежит этой категории';
    }
    
    echo '<script>window.location.href = "admin.php?section=category_products&category_type=' . urlencode($category_type) . '";</script>';
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

if ($page < 1) 
{
    $page = 1;
}

$count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM category_products WHERE category_type = ?");
$count_stmt->bind_param("s", $category_type);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $per_page);

if ($page > $total_pages && $total_pages > 0) 
{
    $page = $total_pages;
    $offset = ($page - 1) * $per_page;
}

$products_stmt = $conn->prepare("SELECT * FROM category_products WHERE category_type = ? ORDER BY id DESC LIMIT ? OFFSET ?");
$products_stmt->bind_param("sii", $category_type, $per_page, $offset);
$products_stmt->execute();
$products_result = $products_stmt->get_result();
$products = [];

while ($row = $products_result->fetch_assoc()) 
{
    $products[] = $row;
}

$stats_stmt = $conn->prepare("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN stock = 1 THEN 1 ELSE 0 END) as in_stock,
    SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
    COALESCE(AVG(price), 0) as avg_price,
    SUM(price) as total_value,
    COUNT(DISTINCT brand) as brands_count
    FROM category_products WHERE category_type = ?");
$stats_stmt->bind_param("s", $category_type);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-box me-2"></i>Товары категории: <?= htmlspecialchars($category_name) ?>
        <small class="text-muted fs-6">(таблица category_products)</small>
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=categories" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            <span class="d-none d-sm-inline">Назад к категориям</span>
        </a>
    </div>
</div>
<?php 
if (isset($_SESSION['success_message']))
{
?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success_message'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message']))
{
?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $_SESSION['error_message'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
unset($_SESSION['error_message']);
}
?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Всего товаров</h5>
                <h2 class="text-primary"><?= $stats['total'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">В наличии</h5>
                <h2 class="text-success"><?= $stats['in_stock'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Средняя цена</h5>
                <h2 class="text-warning"><?= number_format($stats['avg_price'] ?? 0, 2, '.', ' ') ?> ₽</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Брендов</h5>
                <h2 class="text-info"><?= $stats['brands_count'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Список товаров</h5>
            </div>
            <div class="col-auto">
                <span class="text-muted">Тип категории: <code><?= htmlspecialchars($category_type) ?></code></span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php 
        if (count($products) > 0)
        {
        ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Название</th>
                        <th>Артикул</th>
                        <th>Объем</th>
                        <th>Цена</th>
                        <th>В наличии</th>
                        <th>Хит</th>
                        <th>Бренд</th>
                        <th width="100">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($products as $product)
                    {
                    ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($product['title']) ?></strong>
                                <?php 
                                if (!empty($product['type']))
                                {
                                ?>
                                    <br><small class="text-muted">Тип: <?= htmlspecialchars($product['type']) ?></small>
                                <?php 
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($product['art'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($product['volume'] ?? '—') ?></td>
                            <td>
                                <strong><?= number_format($product['price'], 2, '.', ' ') ?> ₽</strong>
                            </td>
                            <td>
                                <?php 
                                if ($product['stock'] == 1)
                                {
                                ?>
                                    <span class="badge bg-success">Да</span>
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <span class="badge bg-danger">Нет</span>
                                <?php 
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if ($product['hit'] == 1)
                                {
                                ?>
                                    <span class="badge bg-warning text-dark">Хит</span>
                                <?php 
                                }
                                else
                                { 
                                ?>
                                    <span class="badge bg-secondary">—</span>
                                <?php 
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($product['brand'] ?? '—') ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="admin.php?section=edit_category_product&id=<?= $product['id'] ?>&category_type=<?= urlencode($category_type) ?>&page=<?= $page ?>" 
                                        class="btn btn-outline-primary"
                                        title="Редактировать товар">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="admin.php?section=category_products&delete_product_id=<?= $product['id'] ?>&category_type=<?= urlencode($category_type) ?>" 
                                        class="btn btn-outline-danger"
                                        onclick="return confirm('Удалить этот товар?\n\nТовар: <?= htmlspecialchars(addslashes($product['title'])) ?>')"
                                        title="Удалить товар">
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
        <?php 
        }
        
        if ($total_pages > 1)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=category_products&category_type=<?= urlencode($category_type) ?>&page=1">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=category_products&category_type=<?= urlencode($category_type) ?>&page=<?= $page - 1 ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++)
                {
                ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?section=category_products&category_type=<?= urlencode($category_type) ?>&page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=category_products&category_type=<?= urlencode($category_type) ?>&page=<?= $page + 1 ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=category_products&category_type=<?= urlencode($category_type) ?>&page=<?= $total_pages ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $page ?> из <?= $total_pages ?> | Показано <?= count($products) ?> из <?= $total_products ?> товаров
            </div>
        </nav>
        <?php 
        }
        else
        {
        ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-box fs-1 d-block mb-3"></i>
            <h5>В этой категории нет товаров</h5>
            <p class="mb-0">Категория: <code><?= htmlspecialchars($category_type) ?></code></p>
        </div>
        <?php 
        }
        ?>
    </div>
</div>
<?php 
if (count($products) > 0)
{
?>
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Детальная информация</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Тип категории</label>
                            <div><code><?= htmlspecialchars($category_type) ?></code></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Отображаемое название</label>
                            <div><?= htmlspecialchars($category_name) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Общая стоимость товаров</label>
                            <div><strong><?= number_format($stats['total_value'] ?? 0, 2, '.', ' ') ?> ₽</strong></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Товаров не в наличии</label>
                            <div><?= $stats['out_of_stock'] ?? 0 ?> шт.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
}
?>