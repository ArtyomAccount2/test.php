<?php
$search = $_GET['search'] ?? '';
$user_type = $_GET['type'] ?? 'all';
$sort_by = $_GET['sort'] ?? 'id_desc';

$where_conditions = ["login_users != 'admin'"];
$params = [];
$types = '';

if (!empty($search)) 
{
    $where_conditions[] = "(login_users LIKE ? OR email_users LIKE ? OR phone_users LIKE ? OR CONCAT(surname_users, ' ', name_users, ' ', patronymic_users) LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    $types .= 'ssss';
}

if ($user_type !== 'all') 
{
    $where_conditions[] = "user_type = ?";
    $params[] = $user_type;
    $types .= 's';
}

$where_sql = '';

if (!empty($where_conditions)) 
{
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

$order_by = 'id_users DESC';

switch ($sort_by) 
{
    case 'name_asc':
        $order_by = 'surname_users ASC, name_users ASC';
        break;
    case 'name_desc':
        $order_by = 'surname_users DESC, name_users DESC';
        break;
    case 'date_asc':
        $order_by = 'created_at ASC';
        break;
    case 'date_desc':
        $order_by = 'created_at DESC';
        break;
    case 'login_asc':
        $order_by = 'login_users ASC';
        break;
    case 'login_desc':
        $order_by = 'login_users DESC';
        break;
}

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$count_query = "SELECT COUNT(*) as total FROM users $where_sql";
$count_stmt = $conn->prepare($count_query);

if (!empty($params)) 
{
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

$query = "SELECT * FROM users $where_sql ORDER BY $order_by LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($query);

if (!empty($params)) 
{
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$users = [];

while ($row = $result->fetch_assoc()) 
{
    $users[] = $row;
}

$stats_stmt = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN user_type = 'physical' THEN 1 ELSE 0 END) as physical, SUM(CASE WHEN user_type = 'legal' THEN 1 ELSE 0 END) as legal, SUM(CASE WHEN organization_users IS NOT NULL AND organization_users != '' THEN 1 ELSE 0 END) as with_organization FROM users WHERE login_users != 'admin'");
$stats = $stats_stmt->fetch_assoc();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-people me-2"></i>Управление пользователями
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=users_add" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить пользователя</span>
        </a>
        <a href="admin.php?section=users_list&export=1" class="btn btn-outline-secondary">
            <i class="bi bi-download me-1"></i>
            <span class="d-none d-sm-inline">Экспорт</span>
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Всего пользователей</h5>
                <h2 class="text-primary"><?= $stats['total'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Физ. лица</h5>
                <h2 class="text-success"><?= $stats['physical'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Юр. лица</h5>
                <h2 class="text-warning"><?= $stats['legal'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">С организациями</h5>
                <h2 class="text-info"><?= $stats['with_organization'] ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <div class="row g-3">
            <div class="col-md-4">
                <form method="GET" action="admin.php" class="d-flex">
                    <input type="hidden" name="section" value="users_list">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Поиск пользователей..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit">Найти</button>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                <form method="GET" action="admin.php" class="d-flex">
                    <input type="hidden" name="section" value="users_list">
                    <?php 
                    if (!empty($search))
                    {
                    ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <?php 
                    }
                    ?>
                    <select class="form-select" name="type" onchange="this.form.submit()">
                        <option value="all" <?= $user_type == 'all' ? 'selected' : '' ?>>Все типы</option>
                        <option value="physical" <?= $user_type == 'physical' ? 'selected' : '' ?>>Физ. лица</option>
                        <option value="legal" <?= $user_type == 'legal' ? 'selected' : '' ?>>Юр. лица</option>
                    </select>
                </form>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-md-end gap-2">
                    <form method="GET" action="admin.php" class="d-flex">
                        <input type="hidden" name="section" value="users_list">
                        <?php 
                        if (!empty($search))
                        {
                        ?>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <?php 
                        }

                        if ($user_type !== 'all')
                        {
                        ?>
                        <input type="hidden" name="type" value="<?= $user_type ?>">
                        <?php 
                        }
                        ?>
                        <select class="form-select" name="sort" onchange="this.form.submit()">
                            <option value="id_desc" <?= $sort_by == 'id_desc' ? 'selected' : '' ?>>По ID (убыв.)</option>
                            <option value="id_asc" <?= $sort_by == 'id_asc' ? 'selected' : '' ?>>По ID (возр.)</option>
                            <option value="name_asc" <?= $sort_by == 'name_asc' ? 'selected' : '' ?>>По имени А-Я</option>
                            <option value="name_desc" <?= $sort_by == 'name_desc' ? 'selected' : '' ?>>По имени Я-А</option>
                            <option value="login_asc" <?= $sort_by == 'login_asc' ? 'selected' : '' ?>>По логину А-Я</option>
                            <option value="login_desc" <?= $sort_by == 'login_desc' ? 'selected' : '' ?>>По логину Я-А</option>
                            <option value="date_desc" <?= $sort_by == 'date_desc' ? 'selected' : '' ?>>Новые сначала</option>
                            <option value="date_asc" <?= $sort_by == 'date_asc' ? 'selected' : '' ?>>Старые сначала</option>
                        </select>
                    </form>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#columnsModal">
                        <i class="bi bi-sliders"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th class="d-none d-sm-table-cell">ФИО</th>
                        <th>Логин</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th class="d-none d-lg-table-cell">Телефон</th>
                        <th class="d-none d-xl-table-cell">Регион</th>
                        <th>Тип</th>
                        <th width="100">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($users) > 0)
                    {
                        foreach ($users as $user)
                        {
                        ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id_users']) ?></td>
                        <td class="d-none d-sm-table-cell">
                            <?php
                            $surname = htmlspecialchars($user['surname_users'] ?? '');
                            $name = htmlspecialchars($user['name_users'] ?? '');
                            $patronymic = htmlspecialchars($user['patronymic_users'] ?? '');
                            
                            if (empty($surname) && empty($name) && empty($patronymic)) 
                            {
                                echo '<span class="text-muted">Не указано</span>';
                            } 
                            else 
                            {
                                echo trim(implode(' ', [$surname, $name, $patronymic]));
                            }
                            
                            if (!empty($user['created_at']))
                            {
                            ?>
                            <br><small class="text-muted">С <?= date('d.m.Y', strtotime($user['created_at'])) ?></small>
                            <?php 
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($user['login_users']) ?></td>
                        <td class="d-none d-md-table-cell">
                            <?php 
                            if (!empty($user['email_users']))
                            {
                            ?>
                            <a href="mailto:<?= htmlspecialchars($user['email_users']) ?>"><?= htmlspecialchars($user['email_users']) ?></a>
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
                        <td class="d-none d-lg-table-cell">
                            <?php 
                            if (!empty($user['phone_users']))
                            {
                            ?>
                            <a href="tel:<?= htmlspecialchars($user['phone_users']) ?>"><?= htmlspecialchars($user['phone_users']) ?></a>
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
                        <td class="d-none d-xl-table-cell">
                            <?= !empty($user['region_users']) ? htmlspecialchars($user['region_users']) : '<span class="text-muted">—</span>' ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $user['user_type'] == 'physical' ? 'info' : 'warning' ?>">
                                <?= $user['user_type'] == 'physical' ? 'Физ.' : 'Юр.' ?>
                            </span>
                            <?php 
                            if (!empty($user['organization_users']))
                            {
                            ?>
                            <br><small class="text-muted"><?= htmlspecialchars(substr($user['organization_users'], 0, 20)) ?>...</small>
                            <?php 
                            }
                            ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="files/edit_user.php?id=<?= $user['id_users'] ?>" class="btn btn-outline-primary" title="Редактировать">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-info" title="Подробнее"
                                        onclick="viewUser(<?= $user['id_users'] ?>)" 
                                        data-bs-toggle="modal" data-bs-target="#userDetailsModal">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <a href="files/delete_user.php?id=<?= $user['id_users'] ?>" 
                                   class="btn btn-outline-danger" title="Удалить"
                                   onclick="return confirm('Удалить пользователя?')">
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
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            Пользователи не найдены
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
                <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=users_list&page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&type=<?= $user_type ?>&sort=<?= $sort_by ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);

                if ($start_page > 1)
                {
                ?>
                <li class="page-item"><a class="page-link" href="admin.php?section=users_list&page=1&search=<?= urlencode($search) ?>&type=<?= $user_type ?>&sort=<?= $sort_by ?>">1</a></li>
                    <?php 
                    if ($start_page > 2)
                    {
                    ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php 
                    }
                } 

                for ($i = $start_page; $i <= $end_page; $i++)
                {
                ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?section=users_list&page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= $user_type ?>&sort=<?= $sort_by ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                } 
                
                if ($end_page < $total_pages)
                {
                    if ($end_page < $total_pages - 1)
                    {
                    ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php 
                    }
                    ?>
                <li class="page-item">
                    <a class="page-link" href="admin.php?section=users_list&page=<?= $total_pages ?>&search=<?= urlencode($search) ?>&type=<?= $user_type ?>&sort=<?= $sort_by ?>">
                        <?= $total_pages ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $page == $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=users_list&page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&type=<?= $user_type ?>&sort=<?= $sort_by ?>">
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

<div class="modal fade" id="columnsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Выбор отображаемых колонок</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="columnsForm">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="colName" checked>
                        <label class="form-check-label" for="colName">ФИО</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="colEmail" checked>
                        <label class="form-check-label" for="colEmail">Email</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="colPhone" checked>
                        <label class="form-check-label" for="colPhone">Телефон</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="colRegion" checked>
                        <label class="form-check-label" for="colRegion">Регион</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="colOrganization">
                        <label class="form-check-label" for="colOrganization">Организация</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="colCreated">
                        <label class="form-check-label" for="colCreated">Дата регистрации</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary" onclick="applyColumns()">Применить</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="userDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подробная информация о пользователе</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="userDetailsContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <a href="#" id="editUserLink" class="btn btn-primary">Редактировать</a>
            </div>
        </div>
    </div>
</div>

<script>
function viewUser(userId) 
{
    fetch(`../files/get_user_details.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                let user = data.user;
                let content = document.getElementById('userDetailsContent');
                let editLink = document.getElementById('editUserLink');
                
                editLink.href = `files/edit_user.php?id=${userId}`;
                
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Основная информация</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>${user.id_users}</td>
                                </tr>
                                <tr>
                                    <td><strong>Логин:</strong></td>
                                    <td>${user.login_users}</td>
                                </tr>
                                <tr>
                                    <td><strong>Тип:</strong></td>
                                    <td>
                                        <span class="badge bg-${user.user_type == 'physical' ? 'info' : 'warning'}">
                                            ${user.user_type == 'physical' ? 'Физическое лицо' : 'Юридическое лицо'}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>ФИО:</strong></td>
                                    <td>${user.surname_users || ''} ${user.name_users || ''} ${user.patronymic_users || ''}</td>
                                </tr>
                                ${user.organization_users ? `
                                <tr>
                                    <td><strong>Организация:</strong></td>
                                    <td>${user.organization_users}</td>
                                </tr>
                                <tr>
                                    <td><strong>Тип организации:</strong></td>
                                    <td>${user.organizationType_users || '—'}</td>
                                </tr>
                                <tr>
                                    <td><strong>ИНН:</strong></td>
                                    <td>${user.TIN_users || '—'}</td>
                                </tr>
                                ` : ''}
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Контактная информация</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>${user.email_users || '—'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Телефон:</strong></td>
                                    <td>${user.phone_users || '—'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Регион:</strong></td>
                                    <td>${user.region_users || '—'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Город:</strong></td>
                                    <td>${user.city_users || '—'}</td>
                                </tr>
                                <tr>
                                    <td><strong>Адрес:</strong></td>
                                    <td>${user.address_users || '—'}</td>
                                </tr>
                                ${user.discountСardNumber_users ? `
                                <tr>
                                    <td><strong>Карта скидок:</strong></td>
                                    <td>${user.discountСardNumber_users}</td>
                                </tr>
                                ` : ''}
                                <tr>
                                    <td><strong>Дата регистрации:</strong></td>
                                    <td>${user.created_at ? new Date(user.created_at).toLocaleDateString('ru-RU') : '—'}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                `;
                
                content.innerHTML = html;
            } 
            else 
            {
                document.getElementById('userDetailsContent').innerHTML = `
                    <div class="alert alert-danger">
                        Ошибка загрузки данных пользователя
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('userDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    Ошибка загрузки данных
                </div>
            `;
        });
}

function applyColumns() 
{
    let columns = {
        name: document.getElementById('colName').checked,
        email: document.getElementById('colEmail').checked,
        phone: document.getElementById('colPhone').checked,
        region: document.getElementById('colRegion').checked,
        organization: document.getElementById('colOrganization').checked,
        created: document.getElementById('colCreated').checked
    };
    
    localStorage.setItem('userTableColumns', JSON.stringify(columns));
    updateTableColumns(columns);
    bootstrap.Modal.getInstance(document.getElementById('columnsModal')).hide();
}

function updateTableColumns(columns) 
{
    console.log('Columns updated:', columns);
}

document.addEventListener('DOMContentLoaded', function() 
{
    let savedColumns = localStorage.getItem('userTableColumns');

    if (savedColumns) 
    {
        let columns = JSON.parse(savedColumns);
        document.getElementById('colName').checked = columns.name;
        document.getElementById('colEmail').checked = columns.email;
        document.getElementById('colPhone').checked = columns.phone;
        document.getElementById('colRegion').checked = columns.region;
        document.getElementById('colOrganization').checked = columns.organization;
        document.getElementById('colCreated').checked = columns.created;
        
        updateTableColumns(columns);
    }
    
    let exportBtn = document.querySelector('[href*="export=1"]');

    if (exportBtn) 
    {
        exportBtn.addEventListener('click', function(e) 
        {
            e.preventDefault();
            exportToCSV();
        });
    }
});

function exportToCSV() 
{
    let params = new URLSearchParams(window.location.search);
    params.set('export', '1');
    
    window.location.href = `admin.php?${params.toString()}`;
}
</script>