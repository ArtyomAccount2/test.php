<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) 
{
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $duration = intval($_POST['duration']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    
    if (!empty($name) && !empty($category) && $price > 0) 
    {
        $stmt = $conn->prepare("INSERT INTO services (name, category, price, duration, description, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $name, $category, $price, $duration, $description, $status);
        
        if ($stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Услуга успешно добавлена';
            echo '<script>window.location.href = "admin.php?section=service";</script>';
            exit();
        }
    }
}

if (isset($_GET['delete_id'])) 
{
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $_SESSION['success_message'] = 'Услуга удалена';
    echo '<script>window.location.href = "admin.php?section=service";</script>';
    exit();
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 1;
$offset = ($page - 1) * $per_page;

$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status_filter'] ?? '';
$search = $_GET['search'] ?? '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) 
{
    $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param]);
    $types .= 'ss';
}

if (!empty($category_filter)) 
{
    $where_conditions[] = "category = ?";
    $params[] = $category_filter;
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

$count_query = "SELECT COUNT(*) as total FROM services $where_sql";
$count_stmt = $conn->prepare($count_query);

if (!empty($params)) 
{
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_services = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_services / $per_page);

$query = "SELECT * FROM services $where_sql ORDER BY category, name LIMIT ? OFFSET ?";
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
$services = [];

while ($row = $result->fetch_assoc()) 
{
    $services[] = $row;
}

$stats_stmt = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active, SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive, COALESCE(AVG(price), 0) as avg_price FROM services");
$stats = $stats_stmt->fetch_assoc();

$categories_stmt = $conn->query("SELECT DISTINCT category FROM services WHERE category IS NOT NULL ORDER BY category");
$categories = [];

while ($row = $categories_stmt->fetch_assoc()) 
{
    $categories[] = $row['category'];
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-tools me-2"></i>Управление автосервисом
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить услугу</span>
        </button>
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
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <form method="GET" action="admin.php" class="row g-3 align-items-center">
                    <input type="hidden" name="section" value="service">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Поиск услуг..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">Найти</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="category" onchange="this.form.submit()">
                            <option value="">Все категории</option>
                            <?php 
                            foreach ($categories as $cat)
                            {
                            ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category_filter == $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                            <?php 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status_filter" onchange="this.form.submit()">
                            <option value="">Все статусы</option>
                            <option value="active" <?= $status_filter == 'active' ? 'selected' : '' ?>>Активные</option>
                            <option value="inactive" <?= $status_filter == 'inactive' ? 'selected' : '' ?>>Неактивные</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Всего услуг</h5>
                <h2 class="text-primary"><?= $stats['total'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Активные</h5>
                <h2 class="text-success"><?= $stats['active'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Неактивные</h5>
                <h2 class="text-secondary"><?= $stats['inactive'] ?></h2>
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
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Список услуг автосервиса</h5>
        <span class="badge bg-primary"><?= $total_services ?> услуг</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Название услуги</th>
                        <th>Категория</th>
                        <th width="100">Цена</th>
                        <th width="100">Длительность</th>
                        <th width="100">Статус</th>
                        <th width="120">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($services) > 0)
                    {
                        foreach ($services as $service)
                        {
                        ?>
                        <tr>
                            <td><?= $service['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($service['name']) ?></strong>
                                <?php 
                                if (!empty($service['description'])) 
                                {
                                ?>
                                <br><small class="text-muted"><?= htmlspecialchars(substr($service['description'], 0, 100)) ?>...</small>
                                <?php 
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($service['category']) ?></td>
                            <td><?= number_format($service['price'], 2, '.', ' ') ?> ₽</td>
                            <td>
                                <?php 
                                if ($service['duration']) 
                                {
                                ?>
                                <?= $service['duration'] ?> мин.
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
                                <span class="badge bg-<?= $service['status'] == 'active' ? 'success' : 'secondary' ?>">
                                    <?= $service['status'] == 'active' ? 'Активно' : 'Неактивно' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary"
                                            onclick="editService(<?= $service['id'] ?>)"
                                            data-bs-toggle="modal" data-bs-target="#editServiceModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="admin.php?section=service&delete_id=<?= $service['id'] ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($category_filter) ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>&page=<?= $page ?>" 
                                    class="btn btn-outline-danger"
                                    onclick="return confirm('Удалить услугу?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
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
                            <i class="bi bi-tools fs-1 d-block mb-2"></i>
                            Услуги не найдены
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
                    <a class="page-link" href="admin.php?section=service&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($category_filter) ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=service&page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($category_filter) ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
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
                    <a class="page-link" href="admin.php?section=service&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($category_filter) ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=service&page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($category_filter) ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=service&page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($category_filter) ? '&category=' . urlencode($category_filter) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $page ?> из <?= $total_pages ?> | Показано <?= count($services) ?> из <?= $total_services ?> услуг
            </div>
        </nav>
        <?php 
        }
        ?>
    </div>
</div>

<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить услугу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="add_service" value="1">
                    <div class="mb-3">
                        <label class="form-label">Название услуги<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="Название услуги" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Категория<span class="text-danger">*</span></label>
                        <select class="form-select" name="category" required>
                            <option value="">Выберите категорию</option>
                            <option value="Техническое обслуживание">Техническое обслуживание</option>
                            <option value="Диагностика">Диагностика</option>
                            <option value="Ремонт двигателя">Ремонт двигателя</option>
                            <option value="Ремонт ходовой">Ремонт ходовой</option>
                            <option value="Кузовные работы">Кузовные работы</option>
                            <option value="Электрика">Электрика</option>
                            <option value="Шиномонтаж">Шиномонтаж</option>
                            <option value="Другое">Другое</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Цена<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="price" placeholder="Цена" step="0.01" min="0" required>
                                    <span class="input-group-text">₽</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Длительность (минут)</label>
                                <input type="number" class="form-control" name="duration" placeholder="0" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Описание услуги"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Статус</label>
                        <select class="form-select" name="status">
                            <option value="active">Активно</option>
                            <option value="inactive">Неактивно</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить услугу</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать услугу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="../files/edit_service.php" id="editServiceForm">
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editService(serviceId) 
{
    let urlParams = new URLSearchParams(window.location.search);
    let currentPage = urlParams.get('page') || 1;
    
    fetch(`../files/get_service.php?id=${serviceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                let service = data.service;
                let form = document.getElementById('editServiceForm');
                
                form.innerHTML = `
                <div class="modal-body">
                    <input type="hidden" name="service_id" value="${service.id}">
                    <input type="hidden" name="page" value="${currentPage}">
                    <div class="mb-3">
                        <label class="form-label">Название услуги<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="${service.name}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Категория<span class="text-danger">*</span></label>
                        <select class="form-select" name="category" required>
                            <option value="">Выберите категорию</option>
                            <option value="Техническое обслуживание" ${service.category == 'Техническое обслуживание' ? 'selected' : ''}>Техническое обслуживание</option>
                            <option value="Диагностика" ${service.category == 'Диагностика' ? 'selected' : ''}>Диагностика</option>
                            <option value="Ремонт двигателя" ${service.category == 'Ремонт двигателя' ? 'selected' : ''}>Ремонт двигателя</option>
                            <option value="Ремонт ходовой" ${service.category == 'Ремонт ходовой' ? 'selected' : ''}>Ремонт ходовой</option>
                            <option value="Кузовные работы" ${service.category == 'Кузовные работы' ? 'selected' : ''}>Кузовные работы</option>
                            <option value="Электрика" ${service.category == 'Электрика' ? 'selected' : ''}>Электрика</option>
                            <option value="Шиномонтаж" ${service.category == 'Шиномонтаж' ? 'selected' : ''}>Шиномонтаж</option>
                            <option value="Другое" ${service.category == 'Другое' ? 'selected' : ''}>Другое</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Цена<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="price" value="${service.price}" step="0.01" min="0" required>
                                    <span class="input-group-text">₽</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Длительность (минут)</label>
                                <input type="number" class="form-control" name="duration" value="${service.duration || ''}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" name="description" rows="3">${service.description || ''}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Статус</label>
                        <select class="form-select" name="status">
                            <option value="active" ${service.status == 'active' ? 'selected' : ''}>Активно</option>
                            <option value="inactive" ${service.status == 'inactive' ? 'selected' : ''}>Неактивно</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
                `;

                let search = urlParams.get('search') || '';
                let category = urlParams.get('category') || '';
                let status_filter = urlParams.get('status_filter') || '';
                
                localStorage.setItem('service_filters', JSON.stringify({
                    search: search,
                    category: category,
                    status_filter: status_filter,
                    page: currentPage
                }));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка загрузки данных услуги');
        });
}
</script>