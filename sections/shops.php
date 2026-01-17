<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_shop'])) 
{
    $name = trim($_POST['name']);
    $type = $_POST['type'] ?? 'branch';
    $region = $_POST['region'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $area = floatval($_POST['area'] ?? 0);
    $employees = intval($_POST['employees'] ?? 0);
    $status = $_POST['status'] ?? 'active';
    $description = trim($_POST['description'] ?? '');
    $schedule_start = $_POST['schedule_start'] ?? '';
    $schedule_end = $_POST['schedule_end'] ?? '';
    $email = trim($_POST['email'] ?? '');
    
    $schedule = (!empty($schedule_start) && !empty($schedule_end)) ? "$schedule_start - $schedule_end" : '';
    
    if (!empty($name) && !empty($region) && !empty($phone)) 
    {
        $stmt = $conn->prepare("INSERT INTO shops (name, type, region, phone, address, area, employees, status, description, schedule, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssdissss", $name, $type, $region, $phone, $address, $area, $employees, $status, $description, $schedule, $email);
        
        if ($stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Магазин успешно добавлен';
            echo '<script>window.location.href = "admin.php?section=shops";</script>';
            exit();
        } 
        else 
        {
            $_SESSION['error_message'] = 'Ошибка при добавлении магазина: ' . $conn->error;
        }
    }
}

if (isset($_GET['delete_id'])) 
{
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM shops WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) 
    {
        $_SESSION['success_message'] = 'Магазин удален';
    } 
    else 
    {
        $_SESSION['error_message'] = 'Ошибка при удалении магазина';
    }

    echo '<script>window.location.href = "admin.php?section=shops";</script>';
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 1;
$offset = ($page - 1) * $per_page;

$region_filter = $_GET['region'] ?? '';
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) 
{
    $where_conditions[] = "(name LIKE ? OR address LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    $types .= 'ssss';
}

if (!empty($region_filter)) 
{
    $where_conditions[] = "region = ?";
    $params[] = $region_filter;
    $types .= 's';
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

$count_query = "SELECT COUNT(*) as total FROM shops $where_sql";
$count_stmt = $conn->prepare($count_query);

if (!empty($params)) 
{
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_shops = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_shops / $per_page);

$query = "SELECT * FROM shops $where_sql ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($query);

if (!empty($params)) 
{
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$shops = [];

while ($row = $result->fetch_assoc()) 
{
    $shops[] = $row;
}

$stats_stmt = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active, SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance, SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive, COALESCE(SUM(area), 0) as total_area FROM shops");
$stats = $stats_stmt->fetch_assoc();

$regions_stmt = $conn->query("SELECT DISTINCT region FROM shops WHERE region IS NOT NULL AND region != '' ORDER BY region");
$regions = [];

while ($row = $regions_stmt->fetch_assoc()) 
{
    $regions[] = $row['region'];
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-shop me-2"></i>Управление магазинами
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShopModal">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить магазин</span>
        </button>
        <a href="files/export_shops.php" class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i>
            <span class="d-none d-sm-inline">Экспорт</span>
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
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <form method="GET" action="admin.php" class="row g-3 align-items-center">
                    <input type="hidden" name="section" value="shops">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Поиск магазинов..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">Найти</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="region" onchange="this.form.submit()">
                            <option value="">Все регионы</option>
                            <?php 
                            foreach ($regions as $region)
                            {
                            ?>
                            <option value="<?= htmlspecialchars($region) ?>" <?= $region_filter == $region ? 'selected' : '' ?>>
                                <?= htmlspecialchars($region) ?>
                            </option>
                            <?php 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">Все статусы</option>
                            <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>Активные</option>
                            <option value="inactive" <?= $status_filter == 'inactive' ? 'selected' : '' ?>>Неактивные</option>
                            <option value="maintenance" <?= $status_filter == 'maintenance' ? 'selected' : '' ?>>На обслуживании</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 d-flex justify-content-between mb-4">
        <div class="card shadow-sm w-49-5">
            <div class="card-header bg-white">
                <h5 class="mb-0">Статистика магазинов</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Всего магазинов:</span>
                    <strong class="text-primary"><?= $stats['total'] ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Активные:</span>
                    <strong class="text-success"><?= $stats['active'] ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>На обслуживании:</span>
                    <strong class="text-warning"><?= $stats['maintenance'] ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Неактивные:</span>
                    <strong class="text-secondary"><?= $stats['inactive'] ?></strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Общая площадь:</span>
                    <strong><?= number_format($stats['total_area'], 1) ?> м²</strong>
                </div>
            </div>
        </div>
        <div class="card shadow-sm w-49-5">
            <div class="card-header bg-white">
                <h5 class="mb-0">Быстрые действия</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2 h-100">
                    <button class="btn btn-outline-primary btn-sm" onclick="showOnMap()">
                        <i class="bi bi-geo-alt me-1"></i>Посмотреть на карте
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="showSalesStats()">
                        <i class="bi bi-graph-up me-1"></i>Статистика продаж
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="showEmployees()">
                        <i class="bi bi-people me-1"></i>Сотрудники магазинов
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="showSchedule()">
                        <i class="bi bi-clock-history me-1"></i>Графики работы
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Список магазинов</h5>
                <span class="badge bg-primary"><?= $total_shops ?> магазинов</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="position: sticky; left: 0; background: #f8f9fa; z-index: 10;">ID</th>
                                <th style="min-width: 200px;">Название</th>
                                <th style="min-width: 200px;">Адрес</th>
                                <th style="min-width: 120px;">Регион</th>
                                <th style="min-width: 150px;">Телефон</th>
                                <th style="min-width: 100px;">Статус</th>
                                <th style="min-width: 120px; position: sticky; right: 0; background: #f8f9fa; z-index: 10;">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (count($shops) > 0)
                            {
                                foreach ($shops as $shop)
                                {
                                ?>
                                <tr>
                                    <td style="position: sticky; left: 0; background: white; z-index: 1;"><?= $shop['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($shop['name']) ?></strong>
                                        <br><small class="text-muted">
                                            <?php 
                                            $type_labels = [
                                                'main' => 'Основной',
                                                'branch' => 'Филиал', 
                                                'partner' => 'Партнёрский'
                                            ];
                                            echo $type_labels[$shop['type'] ?? 'branch'];
                                            ?>
                                        </small>
                                    </td>
                                    <td><?= htmlspecialchars($shop['address'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($shop['region']) ?></td>
                                    <td><?= htmlspecialchars($shop['phone']) ?></td>
                                    <td>
                                        <?php
                                        $status_badges = [
                                            'active' => ['bg-success', 'Активен'],
                                            'inactive' => ['bg-secondary', 'Неактивен'],
                                            'maintenance' => ['bg-warning', 'На обслуживании']
                                        ];
                                        $status = $shop['status'] ?? 'active';
                                        ?>
                                        <span class="badge <?= $status_badges[$status][0] ?>"><?= $status_badges[$status][1] ?></span>
                                    </td>
                                    <td style="position: sticky; right: 0; background: white; z-index: 1;">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="Редактировать"
                                                    onclick="editShop(<?= $shop['id'] ?>)"
                                                    data-bs-toggle="modal" data-bs-target="#editShopModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="admin.php?section=shops&delete_id=<?= $shop['id'] ?>" 
                                            class="btn btn-outline-danger" title="Удалить"
                                            onclick="return confirm('Удалить магазин?')">
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
                                    <i class="bi bi-shop fs-1 d-block mb-2"></i>
                                    Магазины не найдены
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
                            <a class="page-link" href="admin.php?section=shops&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($region_filter) ? '&region=' . urlencode($region_filter) : '' ?><?= !empty($status_filter) ? '&status=' . urlencode($status_filter) : '' ?>">
                                <i class="bi bi-chevron-double-left"></i>
                            </a>
                        </li>
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="admin.php?section=shops&page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($region_filter) ? '&region=' . urlencode($region_filter) : '' ?><?= !empty($status_filter) ? '&status=' . urlencode($status_filter) : '' ?>">
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
                            <a class="page-link" href="admin.php?section=shops&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($region_filter) ? '&region=' . urlencode($region_filter) : '' ?><?= !empty($status_filter) ? '&status=' . urlencode($status_filter) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php 
                        }
                        ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="admin.php?section=shops&page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($region_filter) ? '&region=' . urlencode($region_filter) : '' ?><?= !empty($status_filter) ? '&status=' . urlencode($status_filter) : '' ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="admin.php?section=shops&page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($region_filter) ? '&region=' . urlencode($region_filter) : '' ?><?= !empty($status_filter) ? '&status=' . urlencode($status_filter) : '' ?>">
                                <i class="bi bi-chevron-double-right"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="text-center text-muted mt-2">
                        Страница <?= $page ?> из <?= $total_pages ?> | Показано <?= count($shops) ?> из <?= $total_shops ?> магазинов
                    </div>
                </nav>
                <?php 
                }
                ?>
            </div>
        </div>
    </div>
</div>

 <div class="modal fade" id="editShopModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать магазин</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="../files/edit_shop.php" id="editShopForm">
                <div class="modal-body" style="max-height: 550px; overflow-y: auto;"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addShopModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить новый магазин</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body" style="max-height: 550px; overflow-y: auto;">
                    <input type="hidden" name="add_shop" value="1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Название магазина<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="Введите название" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Тип магазина</label>
                                <select class="form-select" name="type">
                                    <option value="main">Основной</option>
                                    <option value="branch" selected>Филиал</option>
                                    <option value="partner">Партнёрский</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Регион<span class="text-danger">*</span></label>
                                <select class="form-select" name="region" required>
                                    <option value="">Выберите регион</option>
                                    <option value="Москва">Москва</option>
                                    <option value="Санкт-Петербург">Санкт-Петербург</option>
                                    <option value="Казань">Казань</option>
                                    <option value="Новосибирск">Новосибирск</option>
                                    <option value="other">Другой</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Телефон<span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone" placeholder="+7 (XXX) XXX-XX-XX" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="email@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Адрес<span class="text-danger">*</span></label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Полный адрес магазина" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Площадь (м²)</label>
                                <input type="number" class="form-control" name="area" placeholder="0" step="0.1" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Количество сотрудников</label>
                                <input type="number" class="form-control" name="employees" placeholder="0" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Статус</label>
                                <select class="form-select" name="status">
                                    <option value="active" selected>Активен</option>
                                    <option value="inactive">Неактивен</option>
                                    <option value="maintenance">На обслуживании</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">График работы</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="time" class="form-control" name="schedule_start" placeholder="Начало работы">
                            </div>
                            <div class="col-6">
                                <input type="time" class="form-control" name="schedule_end" placeholder="Окончание работы">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Дополнительная информация о магазине"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить магазин</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editShop(shopId) 
{
    fetch(`../files/get_shop.php?id=${shopId}`)
        .then(response => {
            if (!response.ok) 
            {
                throw new Error('Ошибка сети: ' + response.status);
            }

            return response.json();
        })
        .then(data => {
            if (data.success && data.shop) 
            {
                let shop = data.shop;
                let form = document.getElementById('editShopForm');

                let shopType = shop.type || 'branch';
                let regionValue = shop.region || '';
                let regionOtherSelected = !['Москва','Санкт-Петербург','Казань','Новосибирск'].includes(regionValue);
                
                form.innerHTML = `
                <div class="modal-body" style="max-height: 550px; overflow-y: auto;">
                    <input type="hidden" name="shop_id" value="${shop.id}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Название магазина<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="${shop.name || ''}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Тип магазина</label>
                                <select class="form-select" name="type">
                                    <option value="main" ${shopType == 'main' ? 'selected' : ''}>Основной</option>
                                    <option value="branch" ${shopType == 'branch' ? 'selected' : ''}>Филиал</option>
                                    <option value="partner" ${shopType == 'partner' ? 'selected' : ''}>Партнёрский</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Регион<span class="text-danger">*</span></label>
                                <select class="form-select" name="region" required>
                                    <option value="">Выберите регион</option>
                                    <option value="Москва" ${regionValue == 'Москва' ? 'selected' : ''}>Москва</option>
                                    <option value="Санкт-Петербург" ${regionValue == 'Санкт-Петербург' ? 'selected' : ''}>Санкт-Петербург</option>
                                    <option value="Казань" ${regionValue == 'Казань' ? 'selected' : ''}>Казань</option>
                                    <option value="Новосибирск" ${regionValue == 'Новосибирск' ? 'selected' : ''}>Новосибирск</option>
                                    <option value="other" ${regionOtherSelected ? 'selected' : ''}>Другой</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Телефон<span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone" value="${shop.phone || ''}" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="${shop.email || ''}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Адрес<span class="text-danger">*</span></label>
                        <textarea class="form-control" name="address" rows="2" required>${shop.address || ''}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Площадь (м²)</label>
                                <input type="number" class="form-control" name="area" value="${shop.area || 0}" step="0.1" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Количество сотрудников</label>
                                <input type="number" class="form-control" name="employees" value="${shop.employees || 0}" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Статус</label>
                                <select class="form-select" name="status">
                                    <option value="active" ${(shop.status || 'active') == 'active' ? 'selected' : ''}>Активен</option>
                                    <option value="inactive" ${(shop.status || 'active') == 'inactive' ? 'selected' : ''}>Неактивен</option>
                                    <option value="maintenance" ${(shop.status || 'active') == 'maintenance' ? 'selected' : ''}>На обслуживании</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" name="description" rows="3">${shop.description || ''}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
                `;

                let regionSelect = form.querySelector('select[name="region"]');

                if (regionOtherSelected && regionValue) 
                {
                    let otherRegionInput = document.createElement('div');
                    otherRegionInput.className = 'mt-2';

                    otherRegionInput.innerHTML = `
                        <label class="form-label">Другой регион<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="other_region" value="${regionValue}" required>
                    `;

                    regionSelect.parentNode.appendChild(otherRegionInput);
                }
                
                regionSelect.addEventListener('change', function() 
                {
                    if (this.value === 'other') 
                    {
                        let otherRegionDiv = form.querySelector('[name="other_region"]')?.parentNode;

                        if (!otherRegionDiv) 
                        {
                            let otherRegionInput = document.createElement('div');
                            otherRegionInput.className = 'mt-2';

                            otherRegionInput.innerHTML = `
                                <label class="form-label">Другой регион<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="other_region" required>
                            `;

                            regionSelect.parentNode.appendChild(otherRegionInput);
                        }
                    } 
                    else 
                    {
                        let otherRegionDiv = form.querySelector('[name="other_region"]')?.parentNode;

                        if (otherRegionDiv) 
                        {
                            otherRegionDiv.remove();
                        }
                    }
                });
            } 
            else 
            {
                let form = document.getElementById('editShopForm');
                form.querySelector('.modal-body').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${data.error || 'Ошибка загрузки данных магазина'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let form = document.getElementById('editShopForm');

            form.querySelector('.modal-body').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Ошибка загрузки данных магазина: ${error.message}
                </div>
            `;
        });
}

function showOnMap() 
{
    alert('Функция "Просмотр на карте" в разработке');
}

function showSalesStats() 
{
    alert('Функция "Статистика продаж" в разработке');
}

function showEmployees() 
{
    alert('Функция "Сотрудники магазинов" в разработке');
}

function showSchedule() 
{
    alert('Функция "Графики работы" в разработке');
}

document.getElementById('editShopForm').addEventListener('submit', function(e) 
{
    let formData = new FormData(this);
    let submitBtn = this.querySelector('button[type="submit"]');

    let originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...';
    submitBtn.disabled = true;
    
    let regionSelect = this.querySelector('select[name="region"]');

    if (regionSelect.value === 'other') 
    {
        let otherRegionInput = this.querySelector('input[name="other_region"]');

        if (otherRegionInput) 
        {
            formData.set('region', otherRegionInput.value);
        }
    }

    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) 
        {
            location.reload();
        } 
        else 
        {
            throw new Error('Ошибка сервера');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при сохранении изменений');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>