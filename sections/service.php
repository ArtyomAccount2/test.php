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

$stmt = $conn->prepare("SELECT * FROM services ORDER BY category, name");
$stmt->execute();
$result = $stmt->get_result();
$services = [];

while ($row = $result->fetch_assoc()) 
{
    $services[] = $row;
}

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

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Список услуг автосервиса</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название услуги</th>
                        <th>Категория</th>
                        <th>Цена</th>
                        <th>Длительность</th>
                        <th>Статус</th>
                        <th>Действия</th>
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
                                    <a href="admin.php?section=service&delete_id=<?= $service['id'] ?>" 
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
    fetch(`../files/get_service.php?id=${serviceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                const service = data.service;
                const form = document.getElementById('editServiceForm');
                form.innerHTML = `
                <div class="modal-body">
                    <input type="hidden" name="service_id" value="${service.id}">
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
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка загрузки данных услуги');
        });
}
</script>