<?php
if (isset($_GET['delete_id'])) 
{
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) 
    {
        $_SESSION['message'] = 'Товар успешно удален';
    } 
    else 
    {
        $_SESSION['message'] = 'Ошибка при удалении товара';
    }
    
    echo '<script>window.location.href = "admin.php?section=products_catalog";</script>';
    exit();
}

$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? 'all';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';
$quantity_filter = $_GET['quantity_filter'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

if ($page < 1) 
{
    $page = 1;
}

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) 
{
    $where_conditions[] = "(name LIKE ? OR description LIKE ? OR article LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= 'sss';
}

if ($category_filter !== 'all') 
{
    $where_conditions[] = "category = ?";
    $params[] = $category_filter;
    $types .= 's';
}

if (!empty($price_min)) 
{
    $where_conditions[] = "price >= ?";
    $params[] = $price_min;
    $types .= 'd';
}

if (!empty($price_max)) 
{
    $where_conditions[] = "price <= ?";
    $params[] = $price_max;
    $types .= 'd';
}

if (!empty($quantity_filter)) 
{
    switch ($quantity_filter) 
    {
        case 'available':
            $where_conditions[] = "quantity > 0";
            break;
        case 'low':
            $where_conditions[] = "quantity > 0 AND quantity < 10";
            break;
        case 'out_of_stock':
            $where_conditions[] = "quantity = 0";
            break;
    }
}

if (!empty($status_filter)) 
{
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

$where_sql = '';

if (!empty($where_conditions)) 
{
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

$count_sql = "SELECT COUNT(*) as total FROM products $where_sql";
$count_stmt = $conn->prepare($count_sql);

if (!empty($params)) 
{
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $per_page);

$products_sql = "SELECT * FROM products $where_sql ORDER BY id DESC LIMIT ? OFFSET ?";
$params_for_data = $params;
$types_for_data = $types . 'ii';
$params_for_data[] = $per_page;
$params_for_data[] = $offset;

$stmt = $conn->prepare($products_sql);

if (!empty($params_for_data)) 
{
    $stmt->bind_param($types_for_data, ...$params_for_data);
}

$stmt->execute();
$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) 
{
    $products[] = $row;
}

$stats_stmt = $conn->query("SELECT 
    COUNT(*) as total, 
    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available, 
    SUM(CASE WHEN status = 'low' THEN 1 ELSE 0 END) as low, 
    SUM(CASE WHEN status = 'out_of_stock' THEN 1 ELSE 0 END) as out_of_stock, 
    COALESCE(AVG(price), 0) as avg_price, 
    SUM(quantity) as total_quantity 
    FROM products");
$stats = $stats_stmt->fetch_assoc();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-card-checklist me-2"></i>Каталог товаров (таблица products)
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=products_add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить товар</span>
        </a>
        <a href="admin.php?section=products_catalog&export=1" class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i>
            <span class="d-none d-sm-inline">Экспорт</span>
        </a>
    </div>
</div>
<?php 
if (isset($_SESSION['message']))
{
?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['message'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
unset($_SESSION['message']); 
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
                <h2 class="text-success"><?= $stats['available'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Средняя цена</h5>
                <h2 class="text-warning"><?= number_format($stats['avg_price'], 2, '.', ' ') ?> ₽</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Общий остаток</h5>
                <h2 class="text-info"><?= $stats['total_quantity'] ?> шт.</h2>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="row g-3">
            <div class="col-md-6">
                <form method="GET" action="admin.php" class="d-flex">
                    <input type="hidden" name="section" value="products_catalog">
                    <input type="hidden" name="page" value="1">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Поиск товаров..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit">Найти</button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-md-end gap-2">
                    <form method="GET" action="admin.php" class="d-flex">
                        <input type="hidden" name="section" value="products_catalog">
                        <input type="hidden" name="page" value="1">
                        <?php 
                        if (!empty($search))
                        {
                        ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php 
                        }
                        ?>
                        <select class="form-select" name="category" onchange="this.form.submit()" style="width: auto;">
                            <option value="all" <?= $category_filter == 'all' ? 'selected' : '' ?>>Все категории</option>
                            <?php
                            $cat_result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category");

                            while ($cat_row = $cat_result->fetch_assoc()) 
                            {
                                $selected = ($category_filter == $cat_row['category']) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($cat_row['category']) . '" ' . $selected . '>' . htmlspecialchars($cat_row['category']) . '</option>';
                            }
                            ?>
                        </select>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-filter me-1"></i>Фильтр
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Цена</th>
                        <th>Остаток</th>
                        <th>Статус</th>
                        <th width="120">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($products) > 0)
                    {
                        foreach ($products as $product)
                        {
                    ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($product['name']) ?></strong>
                                <?php 
                                if (!empty($product['article']))
                                {
                                ?>
                                <br><small class="text-muted">Арт: <?= htmlspecialchars($product['article']) ?></small>
                                <?php 
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($product['category'] ?? '—') ?></td>
                            <td><?= number_format($product['price'], 2, '.', ' ') ?> ₽</td>
                            <td><?= $product['quantity'] ?> шт.</td>
                            <td>
                                <?php 
                                $status_badge = [
                                    'available' => ['bg-success', 'В наличии'],
                                    'low' => ['bg-warning', 'Мало'],
                                    'out_of_stock' => ['bg-danger', 'Нет в наличии']
                                ];
                                $status = $product['status'] ?? 'available';
                                ?>
                                <span class="badge <?= $status_badge[$status][0] ?>"><?= $status_badge[$status][1] ?></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="admin.php?section=edit_products&id=<?= $product['id'] ?>&page=<?= $page ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $category_filter !== 'all' ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= !empty($quantity_filter) ? '&quantity_filter=' . urlencode($quantity_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="admin.php?section=products_catalog&delete_id=<?= $product['id'] ?>&page=<?= $page ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $category_filter !== 'all' ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= !empty($quantity_filter) ? '&quantity_filter=' . urlencode($quantity_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Удалить товар?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        }
                    }
                    else
                    {
                    ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="bi bi-box fs-1 d-block mb-2"></i>
                            Товары не найдены
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
        if ($total_pages > 1)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=products_catalog&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $category_filter !== 'all' ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= !empty($quantity_filter) ? '&quantity_filter=' . urlencode($quantity_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=products_catalog&page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $category_filter !== 'all' ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= !empty($quantity_filter) ? '&quantity_filter=' . urlencode($quantity_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
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
                    <a class="page-link" href="admin.php?section=products_catalog&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $category_filter !== 'all' ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= !empty($quantity_filter) ? '&quantity_filter=' . urlencode($quantity_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=products_catalog&page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $category_filter !== 'all' ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= !empty($quantity_filter) ? '&quantity_filter=' . urlencode($quantity_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=products_catalog&page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $category_filter !== 'all' ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= !empty($quantity_filter) ? '&quantity_filter=' . urlencode($quantity_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
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
        ?>
    </div>
</div>

<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Расширенный фильтр</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="admin.php" id="filterForm">
                    <input type="hidden" name="section" value="products_catalog">
                    <input type="hidden" name="page" value="1">
                    <?php 
                    if (!empty($search))
                    {
                    ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php 
                    } 

                    if (!empty($category_filter) && $category_filter !== 'all')
                    {
                    ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category_filter) ?>">
                    <?php
                    }
                    ?>
                    <div class="mb-3">
                        <label class="form-label">Цена</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="number" class="form-control" name="price_min" placeholder="От" value="<?= htmlspecialchars($price_min) ?>">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" name="price_max" placeholder="До" value="<?= htmlspecialchars($price_max) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Количество</label>
                        <select class="form-select" name="quantity_filter">
                            <option value="">Все</option>
                            <option value="available" <?= $quantity_filter == 'available' ? 'selected' : '' ?>>В наличии (>0)</option>
                            <option value="low" <?= $quantity_filter == 'low' ? 'selected' : '' ?>>Мало (<10)</option>
                            <option value="out_of_stock" <?= $quantity_filter == 'out_of_stock' ? 'selected' : '' ?>>Нет в наличии</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Статус</label>
                        <select class="form-select" name="status_filter">
                            <option value="">Все</option>
                            <option value="available" <?= $status_filter == 'available' ? 'selected' : '' ?>>В наличии</option>
                            <option value="low" <?= $status_filter == 'low' ? 'selected' : '' ?>>Мало</option>
                            <option value="out_of_stock" <?= $status_filter == 'out_of_stock' ? 'selected' : '' ?>>Нет в наличии</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="admin.php?section=products_catalog" class="btn btn-outline-secondary me-auto">Сбросить все фильтры</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('filterForm').submit()">Применить</button>
            </div>
        </div>
    </div>
</div>

<script>
function exportToCSV() 
{
    let params = new URLSearchParams(window.location.search);
    params.set('export', '1');
    
    window.location.href = `admin.php?${params.toString()}`;
}
</script>