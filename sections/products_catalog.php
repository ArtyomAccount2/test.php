<?php
if (isset($_GET['delete_id'])) 
{
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $_SESSION['message'] = 'Товар успешно удален';
    echo '<script>window.location.href = "admin.php?section=products_catalog";</script>';
    exit();
}

$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? 'all';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';
$quantity_filter = $_GET['quantity_filter'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';

$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($current_page < 1) 
{
    $current_page = 1;
}

$offset = ($current_page - 1) * $items_per_page;

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
    switch ($quantity_filter) {
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
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);

$products_sql = "SELECT * FROM products $where_sql ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($products_sql);

$limit_param = $items_per_page;
$offset_param = $offset;
$limit_types = $types . 'ii';

if (!empty($params)) 
{
    $all_params = array_merge($params, [$limit_param, $offset_param]);
    $stmt->bind_param($limit_types, ...$all_params);
} 
else 
{
    $stmt->bind_param("ii", $limit_param, $offset_param);
}

$stmt->execute();
$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) 
{
    $products[] = $row;
}

function buildPaginationUrl($page, $current_params = []) 
{
    $params = array_merge($current_params, ['page' => $page]);
    return 'admin.php?' . http_build_query($params);
}

$pagination_params = [
    'section' => 'products_catalog',
    'search' => $search,
    'category' => $category_filter,
    'price_min' => $price_min,
    'price_max' => $price_max,
    'quantity_filter' => $quantity_filter,
    'status_filter' => $status_filter
];

$pagination_params = array_filter($pagination_params, function($value) 
{
    return $value !== '' && $value !== null;
});
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-card-checklist me-2"></i>Каталог товаров
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
                            <option value="Запчасти" <?= $category_filter == 'Запчасти' ? 'selected' : '' ?>>Запчасти</option>
                            <option value="Масла" <?= $category_filter == 'Масла' ? 'selected' : '' ?>>Масла</option>
                            <option value="Аксессуары" <?= $category_filter == 'Аксессуары' ? 'selected' : '' ?>>Аксессуары</option>
                        </select>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-filter me-1"></i>Фильтр
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php 
        if ($total_items > 0)
        {
        ?>
        <div class="mb-3 text-muted">
            Показано <?= min($items_per_page, $total_items - $offset) ?> из <?= $total_items ?> товаров
        </div>
        <?php 
        }
        ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Цена</th>
                        <th>Остаток</th>
                        <th>Статус</th>
                        <th>Действия</th>
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
                            <td><?= htmlspecialchars($product['category']) ?></td>
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
                                    <a href="admin.php?section=edit_products&id=<?= $product['id'] ?>" class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" onclick="if(confirm('Удалить товар?')) { window.location.href='admin.php?section=products_catalog&delete_id=<?= $product['id'] ?>&page=<?= $current_page ?>'; }">
                                        <i class="bi bi-trash"></i>
                                    </button>
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
        <nav aria-label="Page navigation" class="mt-3">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $current_page == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $current_page > 1 ? buildPaginationUrl($current_page - 1, $pagination_params) : '#' ?>" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php 
                if ($current_page > 3)
                {
                ?>
                <li class="page-item">
                    <a class="page-link" href="<?= buildPaginationUrl(1, $pagination_params) ?>">1</a>
                </li>
                    <?php 
                    if ($current_page > 4)
                    {
                    ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <?php 
                    }
                } 

                for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++)
                {
                ?>
                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= buildPaginationUrl($i, $pagination_params) ?>"><?= $i ?></a>
                </li>
                <?php
                }
                
                if ($current_page < $total_pages - 2)
                {
                    if ($current_page < $total_pages - 3) 
                    {
                    ?>
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                    <?php 
                    }
                    ?>
                <li class="page-item">
                    <a class="page-link" href="<?= buildPaginationUrl($total_pages, $pagination_params) ?>"><?= $total_pages ?></a>
                </li>
                <?php
                }
                ?>
                <li class="page-item <?= $current_page == $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="<?= $current_page < $total_pages ? buildPaginationUrl($current_page + 1, $pagination_params) : '#' ?>" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
            <div class="d-flex justify-content-center mt-2">
                <form method="GET" action="admin.php" class="d-flex align-items-center">
                    <?php 
                    foreach ($pagination_params as $key => $value)
                    {
                    ?>
                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                    <?php
                    }
                    ?>
                    <span class="me-2">Страница:</span>
                    <input type="number" class="form-control form-control-sm" name="page" 
                           min="1" max="<?= $total_pages ?>" value="<?= $current_page ?>" 
                           style="width: 70px;" onchange="if(this.value >= 1 && this.value <= <?= $total_pages ?>) this.form.submit()">
                    <span class="ms-2">из <?= $total_pages ?></span>
                </form>
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