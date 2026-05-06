<?php
$request_types = [
    'supplier' => [
        'table' => 'supplier_requests', 
        'name' => 'Поставщик', 
        'icon' => 'info',
        'headers' => ['ID', 'Компания', 'Контактное лицо', 'Телефон', 'Категория', 'Дата', 'Статус', 'Действия'],
        'fields' => ['id', 'company_name', 'contact_person', 'phone', 'product_category', 'created_at', 'status']
    ],
    'job' => [
        'table' => 'job_applications', 
        'name' => 'Вакансия', 
        'icon' => 'success',
        'headers' => ['ID', 'Вакансия', 'ФИО', 'Телефон', 'Дата', 'Статус', 'Действия'],
        'fields' => ['id', 'position', 'full_name', 'phone', 'created_at', 'status']
    ],
    'service' => [
        'table' => 'service_requests', 
        'name' => 'Автосервис', 
        'icon' => 'warning',
        'headers' => ['ID', 'Имя', 'Телефон', 'Автомобиль', 'Услуга', 'Дата/Время', 'Статус', 'Действия'],
        'fields' => ['id', 'name', 'phone', 'car', 'service_name', 'request_date', 'status']
    ],
    'contact' => [
        'table' => 'contact_messages', 
        'name' => 'Контакты', 
        'icon' => 'secondary',
        'headers' => ['ID', 'Имя', 'Email', 'Тема', 'Дата', 'Статус', 'Действия'],
        'fields' => ['id', 'name', 'email', 'subject', 'created_at', 'status']
    ],
    'support' => [
        'table' => 'support_requests', 
        'name' => 'Поддержка', 
        'icon' => 'danger',
        'headers' => ['ID', 'Тип проблемы', 'Email', 'Дата', 'Статус', 'Действия'],
        'fields' => ['id', 'problem_type', 'email', 'created_at', 'status']
    ]
];

$requests_data = [];

foreach ($request_types as $type => $config) 
{
    $result = $conn->query("SELECT * FROM {$config['table']} ORDER BY created_at DESC");
    $requests_data[$type] = $result && $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$all_requests = [];

foreach ($requests_data as $type => $items) 
{
    foreach ($items as $item) 
    {
        $item['request_type'] = $type;
        $item['type_name'] = $request_types[$type]['name'];
        $item['table_name'] = $request_types[$type]['table'];
        $all_requests[] = $item;
    }
}

usort($all_requests, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

if (isset($_POST['update_status'])) 
{
    $stmt = $conn->prepare("UPDATE {$_POST['table']} SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['status'], $_POST['id']);
    $_SESSION[$stmt->execute() ? 'success_message' : 'error_message'] = $stmt->execute() ? 'Статус обновлен' : 'Ошибка обновления статуса';
    $stmt->close();
    
    $redirect = "admin.php?section=requests&tab=" . ($_GET['tab'] ?? 'all');

    foreach (['all_page', 'supplier_page', 'job_page', 'service_page', 'contact_page', 'support_page'] as $param) 
    {
        if (isset($_GET[$param])) $redirect .= "&$param={$_GET[$param]}";
    }

    echo "<script>window.location.href = '$redirect';</script>";
    exit();
}

if (isset($_GET['delete'])) 
{
    $stmt = $conn->prepare("DELETE FROM {$_GET['table']} WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $_SESSION[$stmt->execute() ? 'success_message' : 'error_message'] = $stmt->execute() ? 'Заявка удалена' : 'Ошибка удаления';
    $stmt->close();
    
    $redirect = "admin.php?section=requests&tab=" . ($_GET['tab'] ?? 'all');

    foreach (['all_page', 'supplier_page', 'job_page', 'service_page', 'contact_page', 'support_page'] as $param) 
    {
        if (isset($_GET[$param])) $redirect .= "&$param={$_GET[$param]}";
    }

    echo "<script>window.location.href = '$redirect';</script>";
    exit();
}

$active_tab = $_GET['tab'] ?? 'all';
$per_page = 5;
$all_page = isset($_GET['all_page']) ? (int)$_GET['all_page'] : 1;
$all_total = count($all_requests);
$all_total_pages = ceil($all_total / $per_page);
$paginated_all = array_slice($all_requests, ($all_page - 1) * $per_page, $per_page);
$paginated_data = [];
$total_pages = [];

foreach ($request_types as $type => $config) 
{
    $page_key = "{$type}_page";
    $page = isset($_GET[$page_key]) ? (int)$_GET[$page_key] : 1;
    $$page_key = $page;
    $paginated_data[$type] = array_slice($requests_data[$type], ($page - 1) * $per_page, $per_page);
    $total_pages[$type] = ceil(count($requests_data[$type]) / $per_page);
}

$total_count = count($all_requests);

function formatField($value, $default = 'Не указано') 
{
    return empty($value) && $value !== '0' && $value !== 0 ? '<span class="text-muted">' . $default . '</span>' : htmlspecialchars($value);
}

function getStatusBadge($status, $type = null) 
{
    $status_map = [
        'new' => ['text' => 'Новая', 'color' => 'warning'],
        'in_review' => ['text' => 'На рассмотрении', 'color' => 'info'],
        'approved' => ['text' => 'Одобрена', 'color' => 'success'],
        'rejected' => ['text' => 'Отклонена', 'color' => 'danger'],
        'reviewed' => ['text' => 'Рассмотрен', 'color' => 'info'],
        'interview' => ['text' => 'Приглашен', 'color' => 'primary'],
        'processed' => ['text' => 'Обработана', 'color' => 'success'],
        'cancelled' => ['text' => 'Отменена', 'color' => 'danger'],
        'read' => ['text' => 'Прочитано', 'color' => 'info'],
        'replied' => ['text' => 'Ответлено', 'color' => 'success'],
        'in_progress' => ['text' => 'В работе', 'color' => 'info'],
        'resolved' => ['text' => 'Решена', 'color' => 'success'],
        'closed' => ['text' => 'Закрыта', 'color' => 'secondary']
    ];

    $status_info = $status_map[$status] ?? ['text' => ucfirst($status), 'color' => 'secondary'];
    return "<span class='badge bg-{$status_info['color']}'>{$status_info['text']}</span>";
}

function showAlert($type, $message) 
{

    if (isset($_SESSION[$message])) 
    {
        echo "<div class='alert alert-$type alert-dismissible fade show' role='alert'>{$_SESSION[$message]}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
        unset($_SESSION[$message]);
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-envelope-paper me-2"></i>Управление заявками</h2>
</div>
<?php 
    showAlert('success', 'success_message');
    showAlert('danger', 'error_message');
?>
<div class="row mb-4">
    <div class="col-md-2 col-6 mb-2">
        <div class="card text-center h-100 <?= $active_tab == 'all' ? 'border-primary bg-primary bg-opacity-10' : '' ?>" 
             style="cursor: pointer;" onclick="window.location.href='admin.php?section=requests&tab=all'">
            <div class="card-body py-2" id="CardBody">
                <h5 class="card-title mb-0 small">Всего</h5>
                <h3 class="text-primary mb-0"><?= $total_count ?></h3>
            </div>
        </div>
    </div>
    <?php 
    foreach ($request_types as $type => $config)
    {
    ?>
    <div class="col-md-2 col-6 mb-2">
        <div class="card text-center h-100 <?= $active_tab == $type ? "border-{$config['icon']} bg-{$config['icon']} bg-opacity-10" : '' ?>" 
             style="cursor: pointer;" onclick="window.location.href='admin.php?section=requests&tab=<?= $type ?>'">
            <div class="card-body py-2">
                <h5 class="card-title mb-0 small"><?= $config['name'] ?></h5>
                <h3 class="text-<?= $config['icon'] ?> mb-0"><?= count($requests_data[$type]) ?></h3>
            </div>
        </div>
    </div>
    <?php 
    }
    ?>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-<?= $active_tab == 'all' ? 'envelope-paper' : ($active_tab == 'supplier' ? 'building' : ($active_tab == 'job' ? 'briefcase' : ($active_tab == 'service' ? 'wrench' : ($active_tab == 'contact' ? 'envelope' : 'headset')))) ?> me-2"></i>
            <?= $active_tab == 'all' ? 'Все заявки' : $request_types[$active_tab]['name'] ?>
        </h5>
        <span class="badge bg-primary"><?= $active_tab == 'all' ? $total_count : count($requests_data[$active_tab]) ?> записей</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" <?= $active_tab != 'all' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th width="100">Тип</th>
                        <th>Название / Компания / ФИО</th>
                        <th>Контакт</th>
                        <th width="120">Дата</th>
                        <th width="100">Статус</th>
                        <th class="text-center" width="150">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($paginated_all))
                    {
                    ?>
                        <?php 
                        foreach ($paginated_all as $item)
                        {
                            $type = $item['request_type'];
                            $config = $request_types[$type];
                        ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><span class="badge bg-<?= $config['icon'] ?>"><?= $item['type_name'] ?></span></td>
                            <td>
                                <strong><?php
                                    if ($type == 'supplier') 
                                    {
                                        echo formatField($item['company_name']);
                                    }
                                    else if ($type == 'job') 
                                    {
                                        echo formatField($item['position']);
                                    }
                                    else if ($type == 'service') 
                                    {
                                        echo formatField($item['service_name']);
                                    }
                                    else if ($type == 'contact') 
                                    {
                                        echo formatField($item['name']);
                                    }
                                    else 
                                    {
                                        echo formatField($item['problem_type']);
                                    }
                                ?></strong><br>
                                <small class="text-muted"><?php
                                    if ($type == 'supplier') 
                                    {   
                                        echo formatField($item['contact_person']);
                                    }
                                    else if ($type == 'job') 
                                    {
                                        echo formatField($item['full_name']);
                                    }
                                    else if ($type == 'service') 
                                    {
                                        echo formatField($item['name']);
                                    }
                                    else if ($type == 'contact') 
                                    {
                                        echo formatField($item['subject']);
                                    }
                                    else 
                                    {
                                        echo formatField($item['email']);
                                    }
                                ?></small>
                            </td>
                            <td><?php
                                if ($type == 'supplier' || $type == 'job') 
                                {
                                    echo formatField($item['phone']) . '<br>' . formatField($item['email']);
                                }
                                else if ($type == 'service') 
                                {
                                    echo formatField($item['phone']);
                                }
                                else if ($type == 'contact') 
                                {
                                    echo formatField($item['email']) . '<br>' . formatField($item['phone']);
                                }
                                else 
                                {
                                    echo formatField($item['email']);
                                }
                            ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></td>
                            <td><?= getStatusBadge($item['status']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info view-<?= $type ?>" 
                                        data-id="<?= $item['id'] ?>"
                                        data-type="<?= $type ?>"
                                        <?php 
                                        foreach ($config['fields'] as $field)
                                        {
                                        ?>
                                            data-<?= str_replace('_', '-', $field) ?>="<?= htmlspecialchars($item[$field]) ?>"
                                        <?php 
                                        }
                                        ?>
                                        data-message="<?= htmlspecialchars($item['message'] ?? '') ?>"
                                        data-file="<?= $item['price_file'] ?? $item['resume_file'] ?? $item['screenshot'] ?? '' ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="table" value="<?= $config['table'] ?>">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <select name="status" class="form-select form-select-sm" style="width: 110px" onchange="this.form.submit()">
                                            <?php 
                                            $status_options = [
                                                'supplier' => ['new' => 'Новая', 'in_review' => 'На рассмотрении', 'approved' => 'Одобрена', 'rejected' => 'Отклонена'],
                                                'job' => ['new' => 'Новый', 'reviewed' => 'Рассмотрен', 'interview' => 'Приглашен', 'rejected' => 'Отклонен'],
                                                'service' => ['new' => 'Новая', 'processed' => 'Обработана', 'cancelled' => 'Отменена'],
                                                'contact' => ['new' => 'Новое', 'read' => 'Прочитано', 'replied' => 'Ответлено'],
                                                'support' => ['new' => 'Новая', 'in_progress' => 'В работе', 'resolved' => 'Решена', 'closed' => 'Закрыта']
                                            ];

                                            foreach ($status_options[$type] as $val => $label)
                                            {
                                            ?>
                                                <option value="<?= $val ?>" <?= $item['status'] == $val ? 'selected' : '' ?>><?= $label ?></option>
                                            <?php 
                                            }
                                            ?>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                    <a href="admin.php?section=requests&delete=1&table=<?= $config['table'] ?>&id=<?= $item['id'] ?>&tab=all&all_page=<?= $all_page ?>" 
                                       class="btn btn-outline-danger" onclick="return confirm('Удалить заявку?')">
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
                        <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Нет заявок</td></tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
        if ($all_total_pages > 1)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3" <?= $active_tab != 'all' ? 'style="display: none;"' : '' ?>>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $all_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=all&all_page=1">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $all_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=all&all_page=<?= $all_page - 1 ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php
                $start_page = max(1, $all_page - 2);
                $end_page = min($all_total_pages, $all_page + 2);

                for ($i = $start_page; $i <= $end_page; $i++)
                {
                ?>
                <li class="page-item <?= $i == $all_page ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=all&all_page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $all_page >= $all_total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=all&all_page=<?= $all_page + 1 ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $all_page >= $all_total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=all&all_page=<?= $all_total_pages ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $all_page ?> из <?= $all_total_pages ?> | Показано <?= min($per_page, count($paginated_all)) ?> из <?= $all_total ?> записей
            </div>
        </nav>
        <?php 
        }
        ?>
        <div class="table-responsive" <?= $active_tab != 'supplier' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th width="50">ID</th><th>Компания</th><th>Контактное лицо</th><th>Телефон</th><th>Категория</th><th width="120">Дата</th><th width="100">Статус</th><th width="150">Действия</th></tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($paginated_data['supplier']))
                    {
                    ?>
                        <?php 
                        foreach ($paginated_data['supplier'] as $item)
                        {
                        ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><strong><?= formatField($item['company_name']) ?></strong></td>
                            <td><?= formatField($item['contact_person']) ?></td>
                            <td><?= formatField($item['phone']) ?></td>
                            <td><?= formatField($item['product_category']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></td>
                            <td><?= getStatusBadge($item['status']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info view-supplier" data-id="<?= $item['id'] ?>" data-company="<?= htmlspecialchars($item['company_name']) ?>" data-person="<?= htmlspecialchars($item['contact_person']) ?>" data-phone="<?= htmlspecialchars($item['phone']) ?>" data-email="<?= htmlspecialchars($item['email']) ?>" data-category="<?= htmlspecialchars($item['product_category']) ?>" data-message="<?= htmlspecialchars($item['message']) ?>" data-file="<?= $item['price_file'] ?>"><i class="bi bi-eye"></i></button>
                                    <form method="POST" style="display: inline-block;"><input type="hidden" name="table" value="supplier_requests"><input type="hidden" name="id" value="<?= $item['id'] ?>"><select name="status" class="form-select form-select-sm" style="width: 110px" onchange="this.form.submit()"><option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новая</option><option value="in_review" <?= $item['status'] == 'in_review' ? 'selected' : '' ?>>На рассмотрении</option><option value="approved" <?= $item['status'] == 'approved' ? 'selected' : '' ?>>Одобрена</option><option value="rejected" <?= $item['status'] == 'rejected' ? 'selected' : '' ?>>Отклонена</option></select><input type="hidden" name="update_status" value="1"></form>
                                    <a href="admin.php?section=requests&delete=1&table=supplier_requests&id=<?= $item['id'] ?>&tab=supplier&supplier_page=<?= $supplier_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить заявку?')"><i class="bi bi-trash"></i></a>
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
                        <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Нет заявок от поставщиков</td></tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
        if (count($requests_data['supplier']) > $per_page)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3" <?= $active_tab != 'supplier' ? 'style="display: none;"' : '' ?>>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $supplier_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=supplier&supplier_page=1">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $supplier_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=supplier&supplier_page=<?= $supplier_page - 1 ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php
                $start_page = max(1, $supplier_page - 2);
                $end_page = min($total_pages['supplier'], $supplier_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++)
                {
                ?>
                <li class="page-item <?= $i == $supplier_page ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=supplier&supplier_page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $supplier_page >= $total_pages['supplier'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=supplier&supplier_page=<?= $supplier_page + 1 ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $supplier_page >= $total_pages['supplier'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=supplier&supplier_page=<?= $total_pages['supplier'] ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $supplier_page ?> из <?= $total_pages['supplier'] ?> | Всего: <?= count($requests_data['supplier']) ?> записей
            </div>
        </nav>
        <?php 
        }
        ?>
        <div class="table-responsive" <?= $active_tab != 'job' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th width="50">ID</th><th>Вакансия</th><th>ФИО</th><th>Телефон</th><th width="120">Дата</th><th width="100">Статус</th><th width="150">Действия</th></tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($paginated_data['job']))
                    {
                    ?>
                        <?php 
                        foreach ($paginated_data['job'] as $item)
                        {
                        ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><strong><?= formatField($item['position']) ?></strong></td>
                            <td><?= formatField($item['full_name']) ?></td>
                            <td><?= formatField($item['phone']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></td>
                            <td><?= getStatusBadge($item['status']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info view-job" data-id="<?= $item['id'] ?>" data-position="<?= htmlspecialchars($item['position']) ?>" data-name="<?= htmlspecialchars($item['full_name']) ?>" data-phone="<?= htmlspecialchars($item['phone']) ?>" data-email="<?= htmlspecialchars($item['email']) ?>" data-message="<?= htmlspecialchars($item['message']) ?>" data-file="<?= $item['resume_file'] ?>"><i class="bi bi-eye"></i></button>
                                    <form method="POST" style="display: inline-block;"><input type="hidden" name="table" value="job_applications"><input type="hidden" name="id" value="<?= $item['id'] ?>"><select name="status" class="form-select form-select-sm" style="width: 110px" onchange="this.form.submit()"><option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новый</option><option value="reviewed" <?= $item['status'] == 'reviewed' ? 'selected' : '' ?>>Рассмотрен</option><option value="interview" <?= $item['status'] == 'interview' ? 'selected' : '' ?>>Приглашен</option><option value="rejected" <?= $item['status'] == 'rejected' ? 'selected' : '' ?>>Отклонен</option></select><input type="hidden" name="update_status" value="1"></form>
                                    <a href="admin.php?section=requests&delete=1&table=job_applications&id=<?= $item['id'] ?>&tab=job&job_page=<?= $job_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить отклик?')"><i class="bi bi-trash"></i></a>
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
                        <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Нет откликов на вакансии</td></tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
        if (count($requests_data['job']) > $per_page)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3" <?= $active_tab != 'job' ? 'style="display: none;"' : '' ?>>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $job_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=job&job_page=1">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $job_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=job&job_page=<?= $job_page - 1 ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php
                $start_page = max(1, $job_page - 2);
                $end_page = min($total_pages['job'], $job_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++)
                {
                ?>
                <li class="page-item <?= $i == $job_page ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=job&job_page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $job_page >= $total_pages['job'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=job&job_page=<?= $job_page + 1 ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $job_page >= $total_pages['job'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=job&job_page=<?= $total_pages['job'] ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $job_page ?> из <?= $total_pages['job'] ?> | Всего: <?= count($requests_data['job']) ?> записей
            </div>
        </nav>
        <?php 
        }
        ?>
        <div class="table-responsive" <?= $active_tab != 'service' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th width="50">ID</th><th>Имя</th><th>Телефон</th><th>Автомобиль</th><th>Услуга</th><th>Дата/Время</th><th width="100">Статус</th><th width="150">Действия</th></tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($paginated_data['service']))
                    {
                    ?>
                        <?php 
                        foreach ($paginated_data['service'] as $item)
                        {
                        ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><strong><?= formatField($item['name']) ?></strong></td>
                            <td><?= formatField($item['phone']) ?></td>
                            <td><?= formatField($item['car']) ?></td>
                            <td><?= formatField($item['service_name']) ?></td>
                            <td><?= date('d.m.Y', strtotime($item['request_date'])) ?> <?= $item['request_time'] ?></td>
                            <td><?= getStatusBadge($item['status']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info view-service" data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['name']) ?>" data-phone="<?= htmlspecialchars($item['phone']) ?>" data-car="<?= htmlspecialchars($item['car']) ?>" data-service="<?= htmlspecialchars($item['service_name']) ?>" data-price="<?= number_format($item['service_price'], 0, ',', ' ') ?>" data-date="<?= date('d.m.Y', strtotime($item['request_date'])) ?>" data-time="<?= $item['request_time'] ?>" data-message="<?= htmlspecialchars($item['message']) ?>"><i class="bi bi-eye"></i></button>
                                    <form method="POST" style="display: inline-block;"><input type="hidden" name="table" value="service_requests"><input type="hidden" name="id" value="<?= $item['id'] ?>"><select name="status" class="form-select form-select-sm" style="width: 110px" onchange="this.form.submit()"><option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новая</option><option value="processed" <?= $item['status'] == 'processed' ? 'selected' : '' ?>>Обработана</option><option value="cancelled" <?= $item['status'] == 'cancelled' ? 'selected' : '' ?>>Отменена</option></select><input type="hidden" name="update_status" value="1"></form>
                                    <a href="admin.php?section=requests&delete=1&table=service_requests&id=<?= $item['id'] ?>&tab=service&service_page=<?= $service_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить заявку?')"><i class="bi bi-trash"></i></a>
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
                        <tr><td colspan="8" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Нет заявок на сервис</td></tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
        if (count($requests_data['service']) > $per_page)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3" <?= $active_tab != 'service' ? 'style="display: none;"' : '' ?>>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $service_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=service&service_page=1">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $service_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=service&service_page=<?= $service_page - 1 ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php
                $start_page = max(1, $service_page - 2);
                $end_page = min($total_pages['service'], $service_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++)
                {
                ?>
                <li class="page-item <?= $i == $service_page ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=service&service_page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $service_page >= $total_pages['service'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=service&service_page=<?= $service_page + 1 ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $service_page >= $total_pages['service'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=service&service_page=<?= $total_pages['service'] ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $service_page ?> из <?= $total_pages['service'] ?> | Всего: <?= count($requests_data['service']) ?> записей
            </div>
        </nav>
        <?php 
        }
        ?>
        <div class="table-responsive" <?= $active_tab != 'contact' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th width="50">ID</th><th>Имя</th><th>Email</th><th>Тема</th><th width="120">Дата</th><th width="100">Статус</th><th width="150">Действия</th></tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($paginated_data['contact']))
                    {
                    ?>
                        <?php 
                        foreach ($paginated_data['contact'] as $item)
                        {
                        ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><strong><?= formatField($item['name']) ?></strong></td>
                            <td><?= formatField($item['email']) ?></td>
                            <td><?= formatField($item['subject']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></td>
                            <td><?= getStatusBadge($item['status']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info view-contact" data-id="<?= $item['id'] ?>" data-name="<?= htmlspecialchars($item['name']) ?>" data-email="<?= htmlspecialchars($item['email']) ?>" data-phone="<?= htmlspecialchars($item['phone']) ?>" data-subject="<?= htmlspecialchars($item['subject']) ?>" data-message="<?= htmlspecialchars($item['message']) ?>"><i class="bi bi-eye"></i></button>
                                    <form method="POST" style="display: inline-block;"><input type="hidden" name="table" value="contact_messages"><input type="hidden" name="id" value="<?= $item['id'] ?>"><select name="status" class="form-select form-select-sm" style="width: 110px" onchange="this.form.submit()"><option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новое</option><option value="read" <?= $item['status'] == 'read' ? 'selected' : '' ?>>Прочитано</option><option value="replied" <?= $item['status'] == 'replied' ? 'selected' : '' ?>>Ответлено</option></select><input type="hidden" name="update_status" value="1"></form>
                                    <a href="admin.php?section=requests&delete=1&table=contact_messages&id=<?= $item['id'] ?>&tab=contact&contact_page=<?= $contact_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить сообщение?')"><i class="bi bi-trash"></i></a>
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
                        <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Нет сообщений</td></tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
        if (count($requests_data['contact']) > $per_page)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3" <?= $active_tab != 'contact' ? 'style="display: none;"' : '' ?>>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $contact_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=contact&contact_page=1">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $contact_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=contact&contact_page=<?= $contact_page - 1 ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php
                $start_page = max(1, $contact_page - 2);
                $end_page = min($total_pages['contact'], $contact_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++)
                {
                ?>
                <li class="page-item <?= $i == $contact_page ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=contact&contact_page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $contact_page >= $total_pages['contact'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=contact&contact_page=<?= $contact_page + 1 ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $contact_page >= $total_pages['contact'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=contact&contact_page=<?= $total_pages['contact'] ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $contact_page ?> из <?= $total_pages['contact'] ?> | Всего: <?= count($requests_data['contact']) ?> записей
            </div>
        </nav>
        <?php
        }
        ?>
        <div class="table-responsive" <?= $active_tab != 'support' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th width="50">ID</th><th>Тип проблемы</th><th>Email</th><th width="120">Дата</th><th width="100">Статус</th><th width="150">Действия</th></tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($paginated_data['support']))
                    {
                    ?>
                        <?php 
                        foreach ($paginated_data['support'] as $item)
                        {
                        ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><strong><?= formatField($item['problem_type']) ?></strong></td>
                            <td><?= formatField($item['email']) ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></td>
                            <td><?= getStatusBadge($item['status']) ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info view-support" data-id="<?= $item['id'] ?>" data-type="<?= htmlspecialchars($item['problem_type']) ?>" data-email="<?= htmlspecialchars($item['email']) ?>" data-url="<?= htmlspecialchars($item['url']) ?>" data-description="<?= htmlspecialchars($item['description']) ?>" data-file="<?= $item['screenshot'] ?>"><i class="bi bi-eye"></i></button>
                                    <form method="POST" style="display: inline-block;"><input type="hidden" name="table" value="support_requests"><input type="hidden" name="id" value="<?= $item['id'] ?>"><select name="status" class="form-select form-select-sm" style="width: 110px" onchange="this.form.submit()"><option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новая</option><option value="in_progress" <?= $item['status'] == 'in_progress' ? 'selected' : '' ?>>В работе</option><option value="resolved" <?= $item['status'] == 'resolved' ? 'selected' : '' ?>>Решена</option><option value="closed" <?= $item['status'] == 'closed' ? 'selected' : '' ?>>Закрыта</option></select><input type="hidden" name="update_status" value="1"></form>
                                    <a href="admin.php?section=requests&delete=1&table=support_requests&id=<?= $item['id'] ?>&tab=support&support_page=<?= $support_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить заявку?')"><i class="bi bi-trash"></i></a>
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
                        <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Нет заявок в поддержку</td></tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
        if (count($requests_data['support']) > $per_page)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3" <?= $active_tab != 'support' ? 'style="display: none;"' : '' ?>>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $support_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=support&support_page=1">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $support_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=support&support_page=<?= $support_page - 1 ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php
                $start_page = max(1, $support_page - 2);
                $end_page = min($total_pages['support'], $support_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++)
                {
                ?>
                <li class="page-item <?= $i == $support_page ? 'active' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=support&support_page=<?= $i ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $support_page >= $total_pages['support'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=support&support_page=<?= $support_page + 1 ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $support_page >= $total_pages['support'] ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=requests&tab=support&support_page=<?= $total_pages['support'] ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $support_page ?> из <?= $total_pages['support'] ?> | Всего: <?= count($requests_data['support']) ?> записей
            </div>
        </nav>
        <?php 
        }
        ?>
    </div>
</div>

<div id="modalContainer"></div>

<script>
function createModal(title, content, bgColor = 'primary') 
{
    let modalId = 'dynamicModal_' + Date.now();
    let modalHtml = `<div class="modal fade" id="${modalId}" tabindex="-1" data-bs-backdrop="static"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content"><div class="modal-header bg-${bgColor} text-white"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body" style="max-height: 500px; overflow-y: auto;">${content}</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button></div></div></div></div>`;
    
    document.getElementById('modalContainer').innerHTML = modalHtml;

    let modal = new bootstrap.Modal(document.getElementById(modalId));

    modal.show();
    document.getElementById(modalId).addEventListener('hidden.bs.modal', () => document.getElementById('modalContainer').innerHTML = '');
}

function formatField(value, defaultVal = 'Не указано') 
{
    return value ? value.replace(/\n/g, '<br>') : '<span class="text-muted">' + defaultVal + '</span>';
}

document.querySelectorAll('.view-supplier').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `<div class="row mb-2"><div class="col-4 fw-bold">Компания:</div><div class="col-8">${formatField(d.company)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Контактное лицо:</div><div class="col-8">${formatField(d.person)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Телефон:</div><div class="col-8">${formatField(d.phone)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Email:</div><div class="col-8">${formatField(d.email)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Категория:</div><div class="col-8">${formatField(d.category)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Сообщение:</div><div class="col-8">${formatField(d.message)}</div></div>
            ${d.file ? `<div class="row mb-2"><div class="col-4 fw-bold">Прайс-лист:</div><div class="col-8"><a href="../uploads/suppliers/${d.file}" target="_blank" class="btn btn-sm btn-outline-primary">Скачать</a></div></div>` : ''}`;
        createModal('Детали заявки от поставщика', content, 'info');
    });
});

document.querySelectorAll('.view-job').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `<div class="row mb-2"><div class="col-4 fw-bold">Вакансия:</div><div class="col-8">${formatField(d.position)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">ФИО:</div><div class="col-8">${formatField(d.name)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Телефон:</div><div class="col-8">${formatField(d.phone)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Email:</div><div class="col-8">${formatField(d.email)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Сообщение:</div><div class="col-8">${formatField(d.message)}</div></div>
            ${d.file ? `<div class="row mb-2"><div class="col-4 fw-bold">Резюме:</div><div class="col-8"><a href="../uploads/resumes/${d.file}" target="_blank" class="btn btn-sm btn-outline-success">Скачать</a></div></div>` : ''}`;
        createModal('Детали отклика на вакансию', content, 'success');
    });
});

document.querySelectorAll('.view-service').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `<div class="row mb-2"><div class="col-4 fw-bold">Имя:</div><div class="col-8">${formatField(d.name)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Телефон:</div><div class="col-8">${formatField(d.phone)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Автомобиль:</div><div class="col-8">${formatField(d.car)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Услуга:</div><div class="col-8">${formatField(d.service)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Цена:</div><div class="col-8">${d.price ? d.price + ' ₽' : '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Дата:</div><div class="col-8">${formatField(d.date)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Время:</div><div class="col-8">${formatField(d.time)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Сообщение:</div><div class="col-8">${formatField(d.message)}</div></div>`;
        createModal('Детали заявки на сервис', content, 'warning');
    });
});

document.querySelectorAll('.view-contact').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `<div class="row mb-2"><div class="col-4 fw-bold">Имя:</div><div class="col-8">${formatField(d.name)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Email:</div><div class="col-8">${formatField(d.email)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Телефон:</div><div class="col-8">${formatField(d.phone)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Тема:</div><div class="col-8">${formatField(d.subject)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Сообщение:</div><div class="col-8">${formatField(d.message)}</div></div>`;
        createModal('Сообщение с контактов', content, 'secondary');
    });
});

document.querySelectorAll('.view-support').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `<div class="row mb-2"><div class="col-4 fw-bold">Тип проблемы:</div><div class="col-8">${formatField(d.type)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Email:</div><div class="col-8">${formatField(d.email)}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">URL:</div><div class="col-8">${d.url ? `<a href="${d.url}" target="_blank">${d.url}</a>` : '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Описание:</div><div class="col-8">${formatField(d.description)}</div></div>
            ${d.file ? `<div class="row mb-2"><div class="col-4 fw-bold">Скриншот:</div><div class="col-8"><a href="../uploads/support/${d.file}" target="_blank" class="btn btn-sm btn-outline-danger">Просмотреть</a></div></div>` : ''}`;
        createModal('Заявка в поддержку', content, 'danger');
    });
});
</script>