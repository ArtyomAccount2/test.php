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

$where_sql = '';

if (!empty($where_conditions)) 
{
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

$stmt = $conn->prepare("SELECT * FROM products $where_sql ORDER BY id DESC");

if (!empty($params)) 
{
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) 
{
    $products[] = $row;
}
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
                    ?>
                        <?php 
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
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="if(confirm('Удалить товар?')) { window.location.href='admin.php?section=products_catalog&delete_id=<?= $product['id'] ?>'; }">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php 
                        }
                        ?>
                    <?php 
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
        if (count($products) > 0)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3">
            <ul class="pagination justify-content-center">
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Previous">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            </ul>
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
                    <div class="mb-3">
                        <label class="form-label">Цена</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="number" class="form-control" name="price_min" placeholder="От">
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" name="price_max" placeholder="До">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Количество</label>
                        <select class="form-select" name="quantity_filter">
                            <option value="">Все</option>
                            <option value="available">В наличии (>0)</option>
                            <option value="low">Мало (<10)</option>
                            <option value="out_of_stock">Нет в наличии</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
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