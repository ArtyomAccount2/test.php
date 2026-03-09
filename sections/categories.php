<?php
if (isset($_GET['delete_id'])) 
{
    $delete_id = (int)$_GET['delete_id'];
    $get_type_stmt = $conn->prepare("SELECT category_type FROM category_products WHERE id = ?");
    $get_type_stmt->bind_param("i", $delete_id);
    $get_type_stmt->execute();
    $type_result = $get_type_stmt->get_result();
    
    if ($type_row = $type_result->fetch_assoc()) 
    {
        $category_type = $type_row['category_type'];
        $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM category_products WHERE category_type = ?");
        $check_stmt->bind_param("s", $category_type);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $product_count = $check_result->fetch_assoc()['count'];
        
        if ($product_count > 0) 
        {
            $_SESSION['error_message'] = 'Невозможно удалить категорию, в ней есть товары (' . $product_count . ' шт.)';
        } 
        else 
        {
            $delete_products_stmt = $conn->prepare("DELETE FROM category_products WHERE category_type = ?");
            $delete_products_stmt->bind_param("s", $category_type);
            $delete_products_stmt->execute();
            
            $_SESSION['success_message'] = 'Категория успешно удалена';
        }
    } 
    else 
    {
        $_SESSION['error_message'] = 'Категория не найдена';
    }
    
    echo '<script>window.location.href = "admin.php?section=categories";</script>';
    exit();
}

$types_query = "SELECT DISTINCT category_type FROM category_products ORDER BY category_type";
$types_result = $conn->query($types_query);
$existing_types = [];

if ($types_result) 
{
    while ($row = $types_result->fetch_assoc()) 
    {
        $existing_types[] = $row['category_type'];
    }
}

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

$total_categories = count($existing_types);
$total_products = 0;
$in_stock = 0;

$products_count_query = "SELECT COUNT(*) as count FROM category_products";
$products_result = $conn->query($products_count_query);

if ($products_result) 
{
    $total_products = $products_result->fetch_assoc()['count'];
}

$in_stock_query = "SELECT COUNT(*) as count FROM category_products WHERE stock = 1";
$in_stock_result = $conn->query($in_stock_query);

if ($in_stock_result) 
{
    $in_stock = $in_stock_result->fetch_assoc()['count'];
}

$search = $_GET['search'] ?? '';
$type_filter = $_GET['type_filter'] ?? 'all';
$brand_filter = $_GET['brand_filter'] ?? 'all';
$in_stock_filter = $_GET['in_stock_filter'] ?? 'all';
$price_min = $_GET['price_min'] ?? '';
$price_max = $_GET['price_max'] ?? '';
$sort = $_GET['sort'] ?? 'name_asc';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

if ($page < 1) 
{
    $page = 1;
}

$brands_query = "SELECT DISTINCT brand FROM category_products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand";
$brands_result = $conn->query($brands_query);
$brands = [];

if ($brands_result) 
{
    while ($row = $brands_result->fetch_assoc()) 
    {
        $brands[] = $row['brand'];
    }
}

$categories = [];

foreach ($existing_types as $index => $type) 
{
    if ($type_filter !== 'all' && $type !== $type_filter) 
    {
        continue;
    }

    $stats_sql = "SELECT COUNT(*) as products_count, COALESCE(AVG(price), 0) as avg_price, SUM(CASE WHEN stock = 1 THEN 1 ELSE 0 END) as available_products FROM category_products WHERE category_type = ?";
    $stats_params = [$type];
    $stats_types = "s";

    if ($brand_filter !== 'all') 
    {
        $stats_sql .= " AND brand = ?";
        $stats_params[] = $brand_filter;
        $stats_types .= "s";
    }

    if ($in_stock_filter === 'in_stock') 
    {
        $stats_sql .= " AND stock = 1";
    } 
    elseif ($in_stock_filter === 'out_of_stock') 
    {
        $stats_sql .= " AND stock = 0";
    }

    if (!empty($price_min)) 
    {
        $stats_sql .= " AND price >= ?";
        $stats_params[] = $price_min;
        $stats_types .= "d";
    }
    
    if (!empty($price_max)) 
    {
        $stats_sql .= " AND price <= ?";
        $stats_params[] = $price_max;
        $stats_types .= "d";
    }
    
    $stats_stmt = $conn->prepare($stats_sql);
    
    if (!empty($stats_params)) 
    {
        $stats_stmt->bind_param($stats_types, ...$stats_params);
    }
    
    $stats_stmt->execute();
    $type_stats = $stats_stmt->get_result()->fetch_assoc();

    $id_stmt = $conn->prepare("SELECT id FROM category_products WHERE category_type = ? LIMIT 1");
    $id_stmt->bind_param("s", $type);
    $id_stmt->execute();
    $id_result = $id_stmt->get_result();
    $first_product = $id_result->fetch_assoc();
    $category_id = $first_product ? $first_product['id'] : 0;
    $display_name = $display_names[$type] ?? ucfirst(str_replace('-', ' ', $type));

    if (!empty($search) && stripos($display_name, $search) === false && stripos($type, $search) === false) 
    {
        continue;
    }

    if ($type_stats['products_count'] > 0 || $type_filter === $type) 
    {
        $categories[] = [
            'id' => $category_id,
            'category_type' => $type,
            'display_name' => $display_name,
            'products_count' => $type_stats['products_count'] ?? 0,
            'avg_price' => $type_stats['avg_price'] ?? 0,
            'available_products' => $type_stats['available_products'] ?? 0
        ];
    }
}

usort($categories, function($a, $b) use ($sort) 
{
    switch ($sort) 
    {
        case 'name_asc':
            return strcmp($a['display_name'], $b['display_name']);
        case 'name_desc':
            return strcmp($b['display_name'], $a['display_name']);
        case 'products_desc':
            return $b['products_count'] <=> $a['products_count'];
        case 'products_asc':
            return $a['products_count'] <=> $b['products_count'];
        case 'price_desc':
            return $b['avg_price'] <=> $a['avg_price'];
        case 'price_asc':
            return $a['avg_price'] <=> $b['avg_price'];
        case 'id_asc':
            return $a['id'] <=> $b['id'];
        case 'id_desc':
            return $b['id'] <=> $a['id'];
        default:
            return strcmp($a['display_name'], $b['display_name']);
    }
});

$total_categories_filtered = count($categories);
$total_pages = ceil($total_categories_filtered / $per_page);
$page = min($page, max(1, $total_pages));
$offset = ($page - 1) * $per_page;
$paginated_categories = array_slice($categories, $offset, $per_page);
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-tags me-2"></i>Категории товаров (из таблицы category_products)
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=categories_add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить категорию</span>
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
                <h5 class="card-title">Всего категорий</h5>
                <h2 class="text-primary"><?= $total_categories ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">С товарами</h5>
                <h2 class="text-success"><?= count($existing_types) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Всего товаров</h5>
                <h2 class="text-info"><?= $total_products ?> шт.</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">В наличии</h5>
                <h2 class="text-success"><?= $in_stock ?> шт.</h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <div class="row g-3">
            <div class="col-md-4">
                <form method="GET" action="admin.php" class="d-flex">
                    <input type="hidden" name="section" value="categories">
                    <input type="hidden" name="page" value="1">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Поиск категорий..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit">Найти</button>
                    </div>
                </form>
            </div>
            <div class="col-md-8">
                <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                    <form method="GET" action="admin.php" class="d-flex">
                        <input type="hidden" name="section" value="categories">
                        <input type="hidden" name="page" value="1">
                        <?php 
                        if (!empty($search))
                        {
                        ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php 
                        }
                        
                        if ($brand_filter !== 'all')
                        {
                        ?>
                            <input type="hidden" name="brand_filter" value="<?= htmlspecialchars($brand_filter) ?>">
                        <?php 
                        }
                        
                        if ($in_stock_filter !== 'all')
                        {
                        ?>
                            <input type="hidden" name="in_stock_filter" value="<?= htmlspecialchars($in_stock_filter) ?>">
                        <?php 
                        }
                        
                        if (!empty($price_min))
                        {
                        ?>
                            <input type="hidden" name="price_min" value="<?= htmlspecialchars($price_min) ?>">
                        <?php 
                        }
                        
                        if (!empty($price_max))
                        {
                        ?>
                            <input type="hidden" name="price_max" value="<?= htmlspecialchars($price_max) ?>">
                        <?php 
                        }
                        ?>
                        <select class="form-select" name="type_filter" onchange="this.form.submit()" style="width: auto;">
                            <option value="all" <?= $type_filter == 'all' ? 'selected' : '' ?>>Все типы</option>
                            <?php 
                            foreach ($existing_types as $type)
                            {
                            ?>
                                <option value="<?= htmlspecialchars($type) ?>" <?= $type_filter == $type ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($display_names[$type] ?? ucfirst(str_replace('-', ' ', $type))) ?>
                                </option>
                            <?php 
                            }
                            ?>
                        </select>
                    </form>
                    <form method="GET" action="admin.php" class="d-flex">
                        <input type="hidden" name="section" value="categories">
                        <input type="hidden" name="page" value="1">
                        <?php 
                        if (!empty($search))
                        {
                        ?>
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php 
                        }
                        
                        if ($type_filter !== 'all')
                        {
                        ?>
                            <input type="hidden" name="type_filter" value="<?= htmlspecialchars($type_filter) ?>">
                        <?php 
                        }
                        
                        if ($brand_filter !== 'all') 
                        {
                        ?>
                            <input type="hidden" name="brand_filter" value="<?= htmlspecialchars($brand_filter) ?>">
                        <?php 
                        }
                        
                        if ($in_stock_filter !== 'all')
                        {
                        ?>
                            <input type="hidden" name="in_stock_filter" value="<?= htmlspecialchars($in_stock_filter) ?>">
                        <?php 
                        }
                        
                        if (!empty($price_min))
                        {
                        ?>
                            <input type="hidden" name="price_min" value="<?= htmlspecialchars($price_min) ?>">
                        <?php 
                        }
                        
                        if (!empty($price_max))
                        {
                        ?>
                            <input type="hidden" name="price_max" value="<?= htmlspecialchars($price_max) ?>">
                        <?php 
                        }
                        ?>
                        <select class="form-select" name="sort" onchange="this.form.submit()" style="width: auto;">
                            <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>По названию (А-Я)</option>
                            <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>По названию (Я-А)</option>
                            <option value="id_asc" <?= $sort == 'id_asc' ? 'selected' : '' ?>>По ID (возрастание)</option>
                            <option value="id_desc" <?= $sort == 'id_desc' ? 'selected' : '' ?>>По ID (убывание)</option>
                            <option value="products_desc" <?= $sort == 'products_desc' ? 'selected' : '' ?>>По кол-ву товаров (убыв.)</option>
                            <option value="products_asc" <?= $sort == 'products_asc' ? 'selected' : '' ?>>По кол-ву товаров (возр.)</option>
                            <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>По цене (убыв.)</option>
                            <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>По цене (возр.)</option>
                        </select>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="bi bi-filter me-1"></i>Фильтр
                    </button>
                </div>
            </div>
        </div>
        <?php 
        if ($type_filter !== 'all' || $brand_filter !== 'all' || $in_stock_filter !== 'all' || !empty($price_min) || !empty($price_max))
        {
        ?>
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted small">Активные фильтры:</span>
                    <?php 
                    if ($type_filter !== 'all')
                    {
                    ?>
                        <span class="badge bg-info d-inline-flex align-items-center">
                            Тип: <?= htmlspecialchars($display_names[$type_filter] ?? ucfirst(str_replace('-', ' ', $type_filter))) ?>
                            <a href="admin.php?section=categories&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?>" class="text-white ms-1" style="text-decoration: none;">×</a>
                        </span>
                    <?php 
                    }
                    
                    if ($brand_filter !== 'all')
                    {
                    ?>
                        <span class="badge bg-info d-inline-flex align-items-center">
                            Бренд: <?= htmlspecialchars($brand_filter) ?>
                            <a href="admin.php?section=categories&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?>" class="text-white ms-1" style="text-decoration: none;">×</a>
                        </span>
                    <?php 
                    }
                    
                    if ($in_stock_filter !== 'all')
                    {
                    ?>
                        <span class="badge bg-info d-inline-flex align-items-center">
                            Наличие: <?= $in_stock_filter === 'in_stock' ? 'В наличии' : 'Нет в наличии' ?>
                            <a href="admin.php?section=categories&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?>" class="text-white ms-1" style="text-decoration: none;">×</a>
                        </span>
                    <?php 
                    }
                    ?>
                    <?php 
                    if (!empty($price_min) || !empty($price_max))
                    {
                    ?>
                        <span class="badge bg-info d-inline-flex align-items-center">
                            Цена: 
                            <?php 
                            if (!empty($price_min) && !empty($price_max))
                            {
                            ?>
                                от <?= number_format($price_min, 0, '', ' ') ?> до <?= number_format($price_max, 0, '', ' ') ?> ₽
                            <?php 
                            }
                            else if (!empty($price_min))
                            {
                            ?>
                                от <?= number_format($price_min, 0, '', ' ') ?> ₽
                            <?php 
                            }
                            else if (!empty($price_max))
                            {
                            ?>
                                до <?= number_format($price_max, 0, '', ' ') ?> ₽
                            <?php 
                            }
                            ?>
                            <a href="admin.php?section=categories&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?>" class="text-white ms-1" style="text-decoration: none;">×</a>
                        </span>
                    <?php 
                    }
                    ?>
                    <a href="admin.php?section=categories" class="btn btn-sm btn-outline-secondary">Сбросить все</a>
                </div>
            </div>
        </div>
        <?php 
        }
        ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Название категории</th>
                        <th>Тип (системный)</th>
                        <th>Товаров</th>
                        <th>В наличии</th>
                        <th>Средняя цена</th>
                        <th width="120">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($paginated_categories) > 0)
                    {
                        foreach ($paginated_categories as $category)
                        {
                    ?>
                        <tr>
                            <td><?= $category['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($category['display_name']) ?></strong>
                            </td>
                            <td>
                                <code><?= htmlspecialchars($category['category_type']) ?></code>
                            </td>
                            <td>
                                <span class="badge <?= $category['products_count'] > 0 ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $category['products_count'] ?> шт.
                                </span>
                            </td>
                            <td>
                                <?php 
                                if ($category['available_products'] > 0)
                                {
                                ?>
                                    <span class="badge bg-success"><?= $category['available_products'] ?> шт.</span>
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <span class="badge bg-secondary">0</span>
                                <?php 
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if ($category['products_count'] > 0)
                                {
                                ?>
                                    <?= number_format($category['avg_price'], 2, '.', ' ') ?> ₽
                                <?php 
                                }
                                else
                                {
                                ?>
                                    <span class="text-muted">—</span>
                                <?php 
                                }
                                ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="admin.php?section=categories_add&type=<?= urlencode($category['category_type']) ?>&page=<?= $page ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= $sort != 'name_asc' ? '&sort=' . urlencode($sort) : '' ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="admin.php?section=category_products&category_type=<?= urlencode($category['category_type']) ?>" 
                                       class="btn btn-outline-info"
                                       title="Просмотр товаров категории">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="admin.php?section=categories&delete_id=<?= $category['id'] ?>&page=<?= $page ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= $sort != 'name_asc' ? '&sort=' . urlencode($sort) : '' ?>" 
                                       class="btn btn-outline-danger <?= $category['products_count'] > 0 ? 'disabled' : '' ?>"
                                       onclick="<?= $category['products_count'] > 0 ? 'return false' : 'return confirm(\'Удалить категорию?\')' ?>">
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
                            <i class="bi bi-tags fs-1 d-block mb-2"></i>Категории не найдены
                            <?php 
                            if (empty($existing_types))
                            {
                            ?>
                            <div class="mt-3">
                                <p class="text-muted">В таблице category_products нет ни одной категории</p>
                                <a href="admin.php?section=categories_add" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle"></i> Создать категорию
                                </a>
                            </div>
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
        if ($total_pages > 1)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=categories&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= $sort != 'name_asc' ? '&sort=' . urlencode($sort) : '' ?>">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=categories&page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= $sort != 'name_asc' ? '&sort=' . urlencode($sort) : '' ?>">
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
                    <a class="page-link" href="admin.php?section=categories&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= $sort != 'name_asc' ? '&sort=' . urlencode($sort) : '' ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=categories&page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= $sort != 'name_asc' ? '&sort=' . urlencode($sort) : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=categories&page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $type_filter !== 'all' ? '&type_filter=' . urlencode($type_filter) : '' ?><?= $brand_filter !== 'all' ? '&brand_filter=' . urlencode($brand_filter) : '' ?><?= $in_stock_filter !== 'all' ? '&in_stock_filter=' . urlencode($in_stock_filter) : '' ?><?= !empty($price_min) ? '&price_min=' . urlencode($price_min) : '' ?><?= !empty($price_max) ? '&price_max=' . urlencode($price_max) : '' ?><?= $sort != 'name_asc' ? '&sort=' . urlencode($sort) : '' ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $page ?> из <?= $total_pages ?> | Показано <?= count($paginated_categories) ?> из <?= $total_categories_filtered ?> категорий
            </div>
        </nav>
        <?php
        }
        ?>
    </div>
</div>

<div class="modal fade" id="filterModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Расширенный фильтр категорий</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET" action="admin.php" id="filterForm">
                    <input type="hidden" name="section" value="categories">
                    <input type="hidden" name="page" value="1">
                    <?php 
                    if (!empty($search))
                    {
                    ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php 
                    }
                    ?>
                    <div class="mb-3">
                        <label class="form-label">Бренд</label>
                        <select class="form-select" name="brand_filter">
                            <option value="all">Все бренды</option>
                            <?php 
                            foreach ($brands as $brand)
                            {
                            ?>
                                <option value="<?= htmlspecialchars($brand) ?>" <?= $brand_filter == $brand ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($brand) ?>
                                </option>
                            <?php 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Наличие</label>
                        <select class="form-select" name="in_stock_filter">
                            <option value="all">Все</option>
                            <option value="in_stock" <?= $in_stock_filter == 'in_stock' ? 'selected' : '' ?>>В наличии</option>
                            <option value="out_of_stock" <?= $in_stock_filter == 'out_of_stock' ? 'selected' : '' ?>>Нет в наличии</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Цена</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="number" class="form-control" name="price_min" placeholder="От" value="<?= htmlspecialchars($price_min) ?>" step="0.01" min="0">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" name="price_max" placeholder="До" value="<?= htmlspecialchars($price_max) ?>" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="admin.php?section=categories" class="btn btn-outline-secondary me-auto">Сбросить все фильтры</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('filterForm').submit()">Применить</button>
            </div>
        </div>
    </div>
</div>