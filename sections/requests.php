<?php
$supplier_requests = [];
$job_applications = [];
$service_requests = [];
$contact_messages = [];
$support_requests = [];

$supplier_query = "SELECT * FROM supplier_requests ORDER BY created_at DESC";
$supplier_result = $conn->query($supplier_query);

if ($supplier_result && $supplier_result->num_rows > 0) 
{
    while ($row = $supplier_result->fetch_assoc()) 
    {
        $supplier_requests[] = $row;
    }
}

$job_query = "SELECT * FROM job_applications ORDER BY created_at DESC";
$job_result = $conn->query($job_query);

if ($job_result && $job_result->num_rows > 0) 
{
    while ($row = $job_result->fetch_assoc()) 
    {
        $job_applications[] = $row;
    }
}

$service_query = "SELECT * FROM service_requests ORDER BY created_at DESC";
$service_result = $conn->query($service_query);

if ($service_result && $service_result->num_rows > 0) 
{
    while ($row = $service_result->fetch_assoc()) 
    {
        $service_requests[] = $row;
    }
}

$contact_query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$contact_result = $conn->query($contact_query);

if ($contact_result && $contact_result->num_rows > 0) 
{
    while ($row = $contact_result->fetch_assoc()) 
    {
        $contact_messages[] = $row;
    }
}

$support_query = "SELECT * FROM support_requests ORDER BY created_at DESC";
$support_result = $conn->query($support_query);

if ($support_result && $support_result->num_rows > 0) 
{
    while ($row = $support_result->fetch_assoc()) 
    {
        $support_requests[] = $row;
    }
}

$all_requests = [];

foreach ($supplier_requests as $item) 
{
    $item['request_type'] = 'supplier';
    $item['type_name'] = 'Поставщик';
    $item['table_name'] = 'supplier_requests';
    $all_requests[] = $item;
}

foreach ($job_applications as $item) 
{
    $item['request_type'] = 'job';
    $item['type_name'] = 'Вакансия';
    $item['table_name'] = 'job_applications';
    $all_requests[] = $item;
}

foreach ($service_requests as $item) 
{
    $item['request_type'] = 'service';
    $item['type_name'] = 'Автосервис';
    $item['table_name'] = 'service_requests';
    $all_requests[] = $item;
}

foreach ($contact_messages as $item) 
{
    $item['request_type'] = 'contact';
    $item['type_name'] = 'Контакты';
    $item['table_name'] = 'contact_messages';
    $all_requests[] = $item;
}

foreach ($support_requests as $item) 
{
    $item['request_type'] = 'support';
    $item['type_name'] = 'Поддержка';
    $item['table_name'] = 'support_requests';
    $all_requests[] = $item;
}

usort($all_requests, function($a, $b) 
{
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

if (isset($_POST['update_status'])) 
{
    $table = $_POST['table'];
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $update_query = "UPDATE $table SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $status, $id);
    
    if ($stmt->execute()) 
    {
        $_SESSION['success_message'] = 'Статус обновлен';
    } 
    else 
    {
        $_SESSION['error_message'] = 'Ошибка обновления статуса';
    }

    $stmt->close();
    
    $redirect_url = "admin.php?section=requests&tab=" . ($_GET['tab'] ?? 'all');

    if (isset($_GET['all_page'])) $redirect_url .= "&all_page=" . $_GET['all_page'];
    if (isset($_GET['supplier_page'])) $redirect_url .= "&supplier_page=" . $_GET['supplier_page'];
    if (isset($_GET['job_page'])) $redirect_url .= "&job_page=" . $_GET['job_page'];
    if (isset($_GET['service_page'])) $redirect_url .= "&service_page=" . $_GET['service_page'];
    if (isset($_GET['contact_page'])) $redirect_url .= "&contact_page=" . $_GET['contact_page'];
    if (isset($_GET['support_page'])) $redirect_url .= "&support_page=" . $_GET['support_page'];
    
    echo '<script>window.location.href = "' . $redirect_url . '";</script>';
    exit();
}

if (isset($_GET['delete'])) 
{
    $table = $_GET['table'];
    $id = $_GET['id'];
    
    $delete_query = "DELETE FROM $table WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) 
    {
        $_SESSION['success_message'] = 'Заявка удалена';
    } 
    else 
    {
        $_SESSION['error_message'] = 'Ошибка удаления';
    }

    $stmt->close();
    
    $redirect_url = "admin.php?section=requests&tab=" . ($_GET['tab'] ?? 'all');

    if (isset($_GET['all_page'])) $redirect_url .= "&all_page=" . $_GET['all_page'];
    if (isset($_GET['supplier_page'])) $redirect_url .= "&supplier_page=" . $_GET['supplier_page'];
    if (isset($_GET['job_page'])) $redirect_url .= "&job_page=" . $_GET['job_page'];
    if (isset($_GET['service_page'])) $redirect_url .= "&service_page=" . $_GET['service_page'];
    if (isset($_GET['contact_page'])) $redirect_url .= "&contact_page=" . $_GET['contact_page'];
    if (isset($_GET['support_page'])) $redirect_url .= "&support_page=" . $_GET['support_page'];
    
    echo '<script>window.location.href = "' . $redirect_url . '";</script>';
    exit();
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'all';
$per_page = 5;

$all_page = isset($_GET['all_page']) ? (int)$_GET['all_page'] : 1;
$all_total = count($all_requests);
$all_total_pages = ceil($all_total / $per_page);
$all_offset = ($all_page - 1) * $per_page;
$paginated_all = array_slice($all_requests, $all_offset, $per_page);

$supplier_page = isset($_GET['supplier_page']) ? (int)$_GET['supplier_page'] : 1;
$job_page = isset($_GET['job_page']) ? (int)$_GET['job_page'] : 1;
$service_page = isset($_GET['service_page']) ? (int)$_GET['service_page'] : 1;
$contact_page = isset($_GET['contact_page']) ? (int)$_GET['contact_page'] : 1;
$support_page = isset($_GET['support_page']) ? (int)$_GET['support_page'] : 1;

function paginateArray($array, $page, $per_page) 
{
    $offset = ($page - 1) * $per_page;
    return array_slice($array, $offset, $per_page);
}

function getTotalPages($array, $per_page) 
{
    return ceil(count($array) / $per_page);
}

$paginated_suppliers = paginateArray($supplier_requests, $supplier_page, $per_page);
$paginated_jobs = paginateArray($job_applications, $job_page, $per_page);
$paginated_services = paginateArray($service_requests, $service_page, $per_page);
$paginated_contacts = paginateArray($contact_messages, $contact_page, $per_page);
$paginated_supports = paginateArray($support_requests, $support_page, $per_page);
$supplier_total_pages = getTotalPages($supplier_requests, $per_page);
$job_total_pages = getTotalPages($job_applications, $per_page);
$service_total_pages = getTotalPages($service_requests, $per_page);
$contact_total_pages = getTotalPages($contact_messages, $per_page);
$support_total_pages = getTotalPages($support_requests, $per_page);

$total_count = count($supplier_requests) + count($job_applications) + count($service_requests) + count($contact_messages) + count($support_requests);

function formatField($value, $default = 'Не указано') 
{
    if (empty($value) || $value === '' || $value === null) 
    {
        return '<span class="text-muted">' . $default . '</span>';
    }

    return htmlspecialchars($value);
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-envelope-paper me-2"></i>Управление заявками
    </h2>
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
    <div class="col-md-2 col-6 mb-2">
        <div class="card text-center <?= $active_tab == 'all' ? 'border-primary bg-primary bg-opacity-10' : '' ?>" style="cursor: pointer;" onclick="window.location.href='admin.php?section=requests&tab=all'">
            <div class="card-body py-2">
                <h5 class="card-title mb-0 small">Всего</h5>
                <h3 class="text-primary mb-0"><?= $total_count ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6 mb-2">
        <div class="card text-center <?= $active_tab == 'suppliers' ? 'border-info bg-info bg-opacity-10' : '' ?>" style="cursor: pointer;" onclick="window.location.href='admin.php?section=requests&tab=suppliers'">
            <div class="card-body py-2">
                <h5 class="card-title mb-0 small">Поставщики</h5>
                <h3 class="text-info mb-0"><?= count($supplier_requests) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6 mb-2">
        <div class="card text-center <?= $active_tab == 'jobs' ? 'border-success bg-success bg-opacity-10' : '' ?>" style="cursor: pointer;" onclick="window.location.href='admin.php?section=requests&tab=jobs'">
            <div class="card-body py-2">
                <h5 class="card-title mb-0 small">Вакансии</h5>
                <h3 class="text-success mb-0"><?= count($job_applications) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6 mb-2">
        <div class="card text-center <?= $active_tab == 'service' ? 'border-warning bg-warning bg-opacity-10' : '' ?>" style="cursor: pointer;" onclick="window.location.href='admin.php?section=requests&tab=service'">
            <div class="card-body py-2">
                <h5 class="card-title mb-0 small">Автосервис</h5>
                <h3 class="text-warning mb-0"><?= count($service_requests) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6 mb-2">
        <div class="card text-center <?= $active_tab == 'contacts' ? 'border-secondary bg-secondary bg-opacity-10' : '' ?>" style="cursor: pointer;" onclick="window.location.href='admin.php?section=requests&tab=contacts'">
            <div class="card-body py-2">
                <h5 class="card-title mb-0 small">Контакты</h5>
                <h3 class="text-secondary mb-0"><?= count($contact_messages) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-6 mb-2">
        <div class="card text-center <?= $active_tab == 'support' ? 'border-danger bg-danger bg-opacity-10' : '' ?>" style="cursor: pointer;" onclick="window.location.href='admin.php?section=requests&tab=support'">
            <div class="card-body py-2">
                <h5 class="card-title mb-0 small">Поддержка</h5>
                <h3 class="text-danger mb-0"><?= count($support_requests) ?></h3>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-<?= 
                $active_tab == 'suppliers' ? 'building' : 
                ($active_tab == 'jobs' ? 'briefcase' : 
                ($active_tab == 'service' ? 'wrench' : 
                ($active_tab == 'contacts' ? 'envelope' : 
                ($active_tab == 'support' ? 'headset' : 'envelope-paper')))) 
            ?> me-2"></i>
            <?= 
                $active_tab == 'suppliers' ? 'Заявки от поставщиков' : 
                ($active_tab == 'jobs' ? 'Отклики на вакансии' : 
                ($active_tab == 'service' ? 'Заявки на автосервис' : 
                ($active_tab == 'contacts' ? 'Сообщения с контактов' : 
                ($active_tab == 'support' ? 'Заявки в поддержку' : 'Все заявки')))) 
            ?>
        </h5>
        <span class="badge bg-primary">
            <?= 
                $active_tab == 'suppliers' ? count($supplier_requests) : 
                ($active_tab == 'jobs' ? count($job_applications) : 
                ($active_tab == 'service' ? count($service_requests) : 
                ($active_tab == 'contacts' ? count($contact_messages) : 
                ($active_tab == 'support' ? count($support_requests) : $total_count)))) 
            ?> записей
        </span>
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
                        <th width="150">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($paginated_all) > 0)
                    {
                    ?>
                        <?php 
                        foreach ($paginated_all as $item)
                        {
                        ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $item['request_type'] == 'supplier' ? 'info' : 
                                        ($item['request_type'] == 'job' ? 'success' : 
                                        ($item['request_type'] == 'service' ? 'warning' : 
                                        ($item['request_type'] == 'contact' ? 'secondary' : 'danger'))) 
                                    ?>">
                                        <?= $item['type_name'] ?>
                                    </span>
                                </td>
                                <td>
                                    <strong>
                                        <?php
                                        if ($item['request_type'] == 'supplier') 
                                        {
                                            echo formatField($item['company_name']);
                                        } 
                                        else if ($item['request_type'] == 'job') 
                                        {
                                            echo formatField($item['position']);
                                        } 
                                        else if ($item['request_type'] == 'service') 
                                        {
                                            echo formatField($item['service_name']);
                                        } 
                                        else if ($item['request_type'] == 'contact') 
                                        {
                                            echo formatField($item['name']);
                                        } 
                                        else if ($item['request_type'] == 'support') 
                                        {
                                            echo formatField($item['problem_type']);
                                        }
                                        ?>
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        <?php
                                        if ($item['request_type'] == 'supplier') 
                                        {
                                            echo formatField($item['contact_person']);
                                        } 
                                        else if ($item['request_type'] == 'job') 
                                        {
                                            echo formatField($item['full_name']);
                                        } 
                                        else if ($item['request_type'] == 'service') 
                                        {
                                            echo formatField($item['name']);
                                        } 
                                        else if ($item['request_type'] == 'contact') 
                                        {
                                            echo formatField($item['subject']);
                                        } 
                                        else if ($item['request_type'] == 'support') 
                                        {
                                            echo formatField($item['email']);
                                        }
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <?php
                                    if ($item['request_type'] == 'supplier') 
                                    {
                                        echo formatField($item['phone']) . '<br>' . formatField($item['email']);
                                    } 
                                    else if ($item['request_type'] == 'job') 
                                    {
                                        echo formatField($item['phone']) . '<br>' . formatField($item['email']);
                                    } 
                                    else if ($item['request_type'] == 'service') 
                                    {
                                        echo formatField($item['phone']);
                                    } 
                                    else if ($item['request_type'] == 'contact') 
                                    {
                                        echo formatField($item['email']) . '<br>' . formatField($item['phone']);
                                    } 
                                    else if ($item['request_type'] == 'support') 
                                    {
                                        echo formatField($item['email']);
                                    }
                                    ?>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($item['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $item['status'] == 'new' ? 'warning' : ($item['status'] == 'approved' || $item['status'] == 'processed' || $item['status'] == 'resolved' ? 'success' : ($item['status'] == 'rejected' || $item['status'] == 'cancelled' || $item['status'] == 'closed' ? 'danger' : 'secondary')) 
                                    ?>">
                                        <?= 
                                            $item['status'] == 'new' ? 'Новая' : 
                                            ($item['status'] == 'in_review' ? 'На рассмотрении' :
                                            ($item['status'] == 'approved' ? 'Одобрена' :
                                            ($item['status'] == 'rejected' ? 'Отклонена' :
                                            ($item['status'] == 'reviewed' ? 'Рассмотрен' :
                                            ($item['status'] == 'interview' ? 'Приглашен' :
                                            ($item['status'] == 'processed' ? 'Обработана' :
                                            ($item['status'] == 'cancelled' ? 'Отменена' :
                                            ($item['status'] == 'read' ? 'Прочитано' :
                                            ($item['status'] == 'replied' ? 'Ответлено' :
                                            ($item['status'] == 'in_progress' ? 'В работе' :
                                            ($item['status'] == 'resolved' ? 'Решена' :
                                            ($item['status'] == 'closed' ? 'Закрыта' : ucfirst($item['status'])))))))))))))
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info view-<?= $item['request_type'] ?>" 
                                            data-id="<?= $item['id'] ?>"
                                            <?php 
                                            if ($item['request_type'] == 'supplier')
                                            {
                                            ?>
                                                data-company="<?= htmlspecialchars($item['company_name']) ?>"
                                                data-person="<?= htmlspecialchars($item['contact_person']) ?>"
                                                data-phone="<?= htmlspecialchars($item['phone']) ?>"
                                                data-email="<?= htmlspecialchars($item['email']) ?>"
                                                data-category="<?= htmlspecialchars($item['product_category']) ?>"
                                                data-message="<?= htmlspecialchars($item['message']) ?>"
                                                data-file="<?= $item['price_file'] ?>"
                                            <?php 
                                            }
                                            else if ($item['request_type'] == 'job')
                                            {
                                            ?>
                                                data-position="<?= htmlspecialchars($item['position']) ?>"
                                                data-name="<?= htmlspecialchars($item['full_name']) ?>"
                                                data-phone="<?= htmlspecialchars($item['phone']) ?>"
                                                data-email="<?= htmlspecialchars($item['email']) ?>"
                                                data-message="<?= htmlspecialchars($item['message']) ?>"
                                                data-file="<?= $item['resume_file'] ?>"
                                            <?php 
                                            }
                                            else if ($item['request_type'] == 'service')
                                            {
                                            ?>
                                                data-name="<?= htmlspecialchars($item['name']) ?>"
                                                data-phone="<?= htmlspecialchars($item['phone']) ?>"
                                                data-car="<?= htmlspecialchars($item['car']) ?>"
                                                data-service="<?= htmlspecialchars($item['service_name']) ?>"
                                                data-price="<?= number_format($item['service_price'], 0, ',', ' ') ?>"
                                                data-date="<?= date('d.m.Y', strtotime($item['request_date'])) ?>"
                                                data-time="<?= $item['request_time'] ?>"
                                                data-message="<?= htmlspecialchars($item['message']) ?>"
                                            <?php 
                                            }
                                            else if ($item['request_type'] == 'contact')
                                            {
                                            ?>
                                                data-name="<?= htmlspecialchars($item['name']) ?>"
                                                data-email="<?= htmlspecialchars($item['email']) ?>"
                                                data-phone="<?= htmlspecialchars($item['phone']) ?>"
                                                data-subject="<?= htmlspecialchars($item['subject']) ?>"
                                                data-message="<?= htmlspecialchars($item['message']) ?>"
                                            <?php
                                            }
                                            else if ($item['request_type'] == 'support')
                                            {
                                            ?>
                                                data-type="<?= htmlspecialchars($item['problem_type']) ?>"
                                                data-email="<?= htmlspecialchars($item['email']) ?>"
                                                data-url="<?= htmlspecialchars($item['url']) ?>"
                                                data-description="<?= htmlspecialchars($item['description']) ?>"
                                                data-file="<?= $item['screenshot'] ?>"
                                            <?php 
                                            }
                                            ?>>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="table" value="<?= $item['table_name'] ?>">
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <select name="status" class="form-select form-select-sm d-inline-block" style="width: 110px" onchange="this.form.submit()">
                                                <?php 
                                                if ($item['request_type'] == 'supplier')
                                                {
                                                ?>
                                                    <option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новая</option>
                                                    <option value="in_review" <?= $item['status'] == 'in_review' ? 'selected' : '' ?>>На рассмотрении</option>
                                                    <option value="approved" <?= $item['status'] == 'approved' ? 'selected' : '' ?>>Одобрена</option>
                                                    <option value="rejected" <?= $item['status'] == 'rejected' ? 'selected' : '' ?>>Отклонена</option>
                                                <?php 
                                                }
                                                else if ($item['request_type'] == 'job')
                                                {
                                                ?>
                                                    <option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новый</option>
                                                    <option value="reviewed" <?= $item['status'] == 'reviewed' ? 'selected' : '' ?>>Рассмотрен</option>
                                                    <option value="interview" <?= $item['status'] == 'interview' ? 'selected' : '' ?>>Приглашен</option>
                                                    <option value="rejected" <?= $item['status'] == 'rejected' ? 'selected' : '' ?>>Отклонен</option>
                                                <?php 
                                                }
                                                else if ($item['request_type'] == 'service')
                                                {
                                                ?>
                                                    <option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новая</option>
                                                    <option value="processed" <?= $item['status'] == 'processed' ? 'selected' : '' ?>>Обработана</option>
                                                    <option value="cancelled" <?= $item['status'] == 'cancelled' ? 'selected' : '' ?>>Отменена</option>
                                                <?php 
                                                }
                                                else if ($item['request_type'] == 'contact')
                                                {
                                                ?>
                                                    <option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новое</option>
                                                    <option value="read" <?= $item['status'] == 'read' ? 'selected' : '' ?>>Прочитано</option>
                                                    <option value="replied" <?= $item['status'] == 'replied' ? 'selected' : '' ?>>Ответлено</option>
                                                <?php 
                                                }
                                                else if ($item['request_type'] == 'support')
                                                {
                                                ?>
                                                    <option value="new" <?= $item['status'] == 'new' ? 'selected' : '' ?>>Новая</option>
                                                    <option value="in_progress" <?= $item['status'] == 'in_progress' ? 'selected' : '' ?>>В работе</option>
                                                    <option value="resolved" <?= $item['status'] == 'resolved' ? 'selected' : '' ?>>Решена</option>
                                                    <option value="closed" <?= $item['status'] == 'closed' ? 'selected' : '' ?>>Закрыта</option>
                                                <?php 
                                                }
                                                ?>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                        <a href="admin.php?section=requests&delete=1&table=<?= $item['table_name'] ?>&id=<?= $item['id'] ?>&tab=all&all_page=<?= $all_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить заявку?')">
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
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Нет заявок
                            </td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
            <?php 
            if ($all_total > 6)
            {
            ?>
            <nav class="mt-3 p-3">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $all_page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=all&all_page=<?= $all_page - 1 ?>">«</a>
                    </li>
                    <?php 
                    for ($i = 1; $i <= $all_total_pages; $i++)
                    {
                    ?>
                        <li class="page-item <?= $i == $all_page ? 'active' : '' ?>">
                            <a class="page-link" href="admin.php?section=requests&tab=all&all_page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php 
                    }
                    ?>
                    <li class="page-item <?= $all_page >= $all_total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=all&all_page=<?= $all_page + 1 ?>">»</a>
                    </li>
                </ul>
                <div class="text-center text-muted mt-2 small">
                    Страница <?= $all_page ?> из <?= $all_total_pages ?> | Всего: <?= $all_total ?> записей
                </div>
            </nav>
            <?php 
            }
            ?>
        </div>
        <div class="table-responsive" <?= $active_tab != 'suppliers' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Компания</th>
                        <th>Контактное лицо</th>
                        <th>Телефон</th>
                        <th>Категория</th>
                        <th width="120">Дата</th>
                        <th width="100">Статус</th>
                        <th width="150">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($paginated_suppliers) > 0)
                    {
                    ?>
                        <?php 
                        foreach ($paginated_suppliers as $request)
                        {
                        ?>
                            <tr>
                                <td><?= $request['id'] ?></td>
                                <td><strong><?= formatField($request['company_name']) ?></strong></td>
                                <td><?= formatField($request['contact_person']) ?></td>
                                <td><?= formatField($request['phone']) ?></td>
                                <td><?= formatField($request['product_category']) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($request['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $request['status'] == 'new' ? 'warning' : 
                                        ($request['status'] == 'in_review' ? 'info' : 
                                        ($request['status'] == 'approved' ? 'success' : 'danger')) 
                                    ?>">
                                        <?= 
                                            $request['status'] == 'new' ? 'Новая' : 
                                            ($request['status'] == 'in_review' ? 'На рассмотрении' : 
                                            ($request['status'] == 'approved' ? 'Одобрена' : 'Отклонена')) 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info view-supplier" 
                                            data-id="<?= $request['id'] ?>"
                                            data-company="<?= htmlspecialchars($request['company_name']) ?>" 
                                            data-person="<?= htmlspecialchars($request['contact_person']) ?>" 
                                            data-phone="<?= htmlspecialchars($request['phone']) ?>" 
                                            data-email="<?= htmlspecialchars($request['email']) ?>" 
                                            data-category="<?= htmlspecialchars($request['product_category']) ?>" 
                                            data-message="<?= htmlspecialchars($request['message']) ?>" 
                                            data-file="<?= $request['price_file'] ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="table" value="supplier_requests">
                                            <input type="hidden" name="id" value="<?= $request['id'] ?>">
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" style="width: 110px;" onchange="this.form.submit()">
                                                <option value="new" <?= $request['status'] == 'new' ? 'selected' : '' ?>>Новая</option>
                                                <option value="in_review" <?= $request['status'] == 'in_review' ? 'selected' : '' ?>>На рассмотрении</option>
                                                <option value="approved" <?= $request['status'] == 'approved' ? 'selected' : '' ?>>Одобрена</option>
                                                <option value="rejected" <?= $request['status'] == 'rejected' ? 'selected' : '' ?>>Отклонена</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                        <a href="admin.php?section=requests&delete=1&table=supplier_requests&id=<?= $request['id'] ?>&tab=suppliers&supplier_page=<?= $supplier_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить заявку?')">
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
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Нет заявок от поставщиков
                            </td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
            <?php 
            if (count($supplier_requests) > 6)
            {
            ?>
            <nav class="mt-3 p-3">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $supplier_page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=suppliers&supplier_page=<?= $supplier_page - 1 ?>">«</a>
                    </li>
                    <?php 
                    for ($i = 1; $i <= $supplier_total_pages; $i++)
                    {
                    ?>
                        <li class="page-item <?= $i == $supplier_page ? 'active' : '' ?>">
                            <a class="page-link" href="admin.php?section=requests&tab=suppliers&supplier_page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php 
                    }
                    ?>
                    <li class="page-item <?= $supplier_page >= $supplier_total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=suppliers&supplier_page=<?= $supplier_page + 1 ?>">»</a>
                    </li>
                </ul>
                <div class="text-center text-muted mt-2 small">
                    Страница <?= $supplier_page ?> из <?= $supplier_total_pages ?> | Всего: <?= count($supplier_requests) ?> записей
                </div>
            </nav>
            <?php 
            }
            ?>
        </div>
        <div class="table-responsive" <?= $active_tab != 'jobs' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Вакансия</th>
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th width="120">Дата</th>
                        <th width="100">Статус</th>
                        <th width="150">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($paginated_jobs) > 0)
                    {
                    ?>
                        <?php 
                        foreach ($paginated_jobs as $job)
                        {
                        ?>
                            <tr>
                                <td><?= $job['id'] ?></td>
                                <td><strong><?= formatField($job['position']) ?></strong></td>
                                <td><?= formatField($job['full_name']) ?></td>
                                <td><?= formatField($job['phone']) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($job['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $job['status'] == 'new' ? 'warning' : 
                                        ($job['status'] == 'reviewed' ? 'info' : 
                                        ($job['status'] == 'interview' ? 'primary' : 'danger')) 
                                    ?>">
                                        <?= 
                                            $job['status'] == 'new' ? 'Новый' : 
                                            ($job['status'] == 'reviewed' ? 'Рассмотрен' : 
                                            ($job['status'] == 'interview' ? 'Приглашен' : 'Отклонен')) 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info view-job" 
                                            data-id="<?= $job['id'] ?>"
                                            data-position="<?= htmlspecialchars($job['position']) ?>" 
                                            data-name="<?= htmlspecialchars($job['full_name']) ?>" 
                                            data-phone="<?= htmlspecialchars($job['phone']) ?>" 
                                            data-email="<?= htmlspecialchars($job['email']) ?>" 
                                            data-message="<?= htmlspecialchars($job['message']) ?>" 
                                            data-file="<?= $job['resume_file'] ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="table" value="job_applications">
                                            <input type="hidden" name="id" value="<?= $job['id'] ?>">
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" style="width: 110px;" onchange="this.form.submit()">
                                                <option value="new" <?= $job['status'] == 'new' ? 'selected' : '' ?>>Новый</option>
                                                <option value="reviewed" <?= $job['status'] == 'reviewed' ? 'selected' : '' ?>>Рассмотрен</option>
                                                <option value="interview" <?= $job['status'] == 'interview' ? 'selected' : '' ?>>Приглашен</option>
                                                <option value="rejected" <?= $job['status'] == 'rejected' ? 'selected' : '' ?>>Отклонен</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                        <a href="admin.php?section=requests&delete=1&table=job_applications&id=<?= $job['id'] ?>&tab=jobs&job_page=<?= $job_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить отклик?')">
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
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Нет откликов на вакансии
                            </td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
            <?php 
            if (count($job_applications) > 6)
            {
            ?>
            <nav class="mt-3 p-3">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $job_page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=jobs&job_page=<?= $job_page - 1 ?>">«</a>
                    </li>
                    <?php 
                    for ($i = 1; $i <= $job_total_pages; $i++) 
                    {
                    ?>
                        <li class="page-item <?= $i == $job_page ? 'active' : '' ?>">
                            <a class="page-link" href="admin.php?section=requests&tab=jobs&job_page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php 
                    }
                    ?>
                    <li class="page-item <?= $job_page >= $job_total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=jobs&job_page=<?= $job_page + 1 ?>">»</a>
                    </li>
                </ul>
                <div class="text-center text-muted mt-2 small">
                    Страница <?= $job_page ?> из <?= $job_total_pages ?> | Всего: <?= count($job_applications) ?> записей
                </div>
            </nav>
            <?php 
            }
            ?>
        </div>
        <div class="table-responsive" <?= $active_tab != 'service' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Автомобиль</th>
                        <th>Услуга</th>
                        <th>Дата/Время</th>
                        <th width="100">Статус</th>
                        <th width="150">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($paginated_services) > 0)
                    {
                    ?>
                        <?php 
                        foreach ($paginated_services as $service)
                        {
                        ?>
                            <tr>
                                <td><?= $service['id'] ?></td>
                                <td><strong><?= formatField($service['name']) ?></strong></td>
                                <td><?= formatField($service['phone']) ?></td>
                                <td><?= formatField($service['car']) ?></td>
                                <td><?= formatField($service['service_name']) ?></td>
                                <td><?= date('d.m.Y', strtotime($service['request_date'])) ?> <?= $service['request_time'] ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $service['status'] == 'new' ? 'warning' : 
                                        ($service['status'] == 'processed' ? 'success' : 'danger') 
                                    ?>">
                                        <?= $service['status'] == 'new' ? 'Новая' : ($service['status'] == 'processed' ? 'Обработана' : 'Отменена') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info view-service" 
                                            data-id="<?= $service['id'] ?>"
                                            data-name="<?= htmlspecialchars($service['name']) ?>" 
                                            data-phone="<?= htmlspecialchars($service['phone']) ?>" 
                                            data-car="<?= htmlspecialchars($service['car']) ?>" 
                                            data-service="<?= htmlspecialchars($service['service_name']) ?>" 
                                            data-price="<?= number_format($service['service_price'], 0, ',', ' ') ?>" 
                                            data-date="<?= date('d.m.Y', strtotime($service['request_date'])) ?>" 
                                            data-time="<?= $service['request_time'] ?>" 
                                            data-message="<?= htmlspecialchars($service['message']) ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="table" value="service_requests">
                                            <input type="hidden" name="id" value="<?= $service['id'] ?>">
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" style="width: 110px;" onchange="this.form.submit()">
                                                <option value="new" <?= $service['status'] == 'new' ? 'selected' : '' ?>>Новая</option>
                                                <option value="processed" <?= $service['status'] == 'processed' ? 'selected' : '' ?>>Обработана</option>
                                                <option value="cancelled" <?= $service['status'] == 'cancelled' ? 'selected' : '' ?>>Отменена</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                        <a href="admin.php?section=requests&delete=1&table=service_requests&id=<?= $service['id'] ?>&tab=service&service_page=<?= $service_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить заявку?')">
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
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Нет заявок на сервис
                            </td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
            <?php 
            if (count($service_requests) > 6)
            {
            ?>
            <nav class="mt-3 p-3">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $service_page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=service&service_page=<?= $service_page - 1 ?>">«</a>
                    </li>
                    <?php 
                    for ($i = 1; $i <= $service_total_pages; $i++)
                    {
                    ?>
                        <li class="page-item <?= $i == $service_page ? 'active' : '' ?>">
                            <a class="page-link" href="admin.php?section=requests&tab=service&service_page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php 
                    }
                    ?>
                    <li class="page-item <?= $service_page >= $service_total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=service&service_page=<?= $service_page + 1 ?>">»</a>
                    </li>
                </ul>
                <div class="text-center text-muted mt-2 small">
                    Страница <?= $service_page ?> из <?= $service_total_pages ?> | Всего: <?= count($service_requests) ?> записей
                </div>
            </nav>
            <?php 
            }
            ?>
        </div>
        <div class="table-responsive" <?= $active_tab != 'contacts' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Тема</th>
                        <th width="120">Дата</th>
                        <th width="100">Статус</th>
                        <th width="150">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($paginated_contacts) > 0)
                    {
                    ?>
                        <?php 
                        foreach ($paginated_contacts as $contact)
                        {
                        ?>
                            <tr>
                                <td><?= $contact['id'] ?></td>
                                <td><strong><?= formatField($contact['name']) ?></strong></td>
                                <td><?= formatField($contact['email']) ?></td>
                                <td><?= formatField($contact['subject']) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($contact['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $contact['status'] == 'new' ? 'warning' : 
                                        ($contact['status'] == 'read' ? 'info' : 'success') 
                                    ?>">
                                        <?= $contact['status'] == 'new' ? 'Новое' : ($contact['status'] == 'read' ? 'Прочитано' : 'Ответлено') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info view-contact" 
                                            data-id="<?= $contact['id'] ?>"
                                            data-name="<?= htmlspecialchars($contact['name']) ?>" 
                                            data-email="<?= htmlspecialchars($contact['email']) ?>" 
                                            data-phone="<?= htmlspecialchars($contact['phone']) ?>" 
                                            data-subject="<?= htmlspecialchars($contact['subject']) ?>" 
                                            data-message="<?= htmlspecialchars($contact['message']) ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="table" value="contact_messages">
                                            <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" style="width: 110px;" onchange="this.form.submit()">
                                                <option value="new" <?= $contact['status'] == 'new' ? 'selected' : '' ?>>Новое</option>
                                                <option value="read" <?= $contact['status'] == 'read' ? 'selected' : '' ?>>Прочитано</option>
                                                <option value="replied" <?= $contact['status'] == 'replied' ? 'selected' : '' ?>>Ответлено</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                        <a href="admin.php?section=requests&delete=1&table=contact_messages&id=<?= $contact['id'] ?>&tab=contacts&contact_page=<?= $contact_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить сообщение?')">
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
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Нет сообщений
                            </td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
            <?php 
            if (count($contact_messages) > 6)
            {
            ?>
            <nav class="mt-3 p-3">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $contact_page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=contacts&contact_page=<?= $contact_page - 1 ?>">«</a>
                    </li>
                    <?php 
                    for ($i = 1; $i <= $contact_total_pages; $i++)
                    {
                    ?>
                        <li class="page-item <?= $i == $contact_page ? 'active' : '' ?>">
                            <a class="page-link" href="admin.php?section=requests&tab=contacts&contact_page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php 
                    }
                    ?>
                    <li class="page-item <?= $contact_page >= $contact_total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=contacts&contact_page=<?= $contact_page + 1 ?>">»</a>
                    </li>
                </ul>
                <div class="text-center text-muted mt-2 small">
                    Страница <?= $contact_page ?> из <?= $contact_total_pages ?> | Всего: <?= count($contact_messages) ?> записей
                </div>
            </nav>
            <?php 
            }
            ?>
        </div>
        <div class="table-responsive" <?= $active_tab != 'support' ? 'style="display: none;"' : '' ?>>
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Тип проблемы</th>
                        <th>Email</th>
                        <th width="120">Дата</th>
                        <th width="100">Статус</th>
                        <th width="150">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($paginated_supports) > 0)
                    {
                    ?>
                        <?php 
                        foreach ($paginated_supports as $support)
                        {
                        ?>
                            <tr>
                                <td><?= $support['id'] ?></td>
                                <td><strong><?= formatField($support['problem_type']) ?></strong></td>
                                <td><?= formatField($support['email']) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($support['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $support['status'] == 'new' ? 'warning' : 
                                        ($support['status'] == 'in_progress' ? 'info' : 
                                        ($support['status'] == 'resolved' ? 'success' : 'secondary')) 
                                    ?>">
                                        <?= 
                                            $support['status'] == 'new' ? 'Новая' : 
                                            ($support['status'] == 'in_progress' ? 'В работе' : 
                                            ($support['status'] == 'resolved' ? 'Решена' : 'Закрыта')) 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-info view-support" 
                                            data-id="<?= $support['id'] ?>"
                                            data-type="<?= htmlspecialchars($support['problem_type']) ?>" 
                                            data-email="<?= htmlspecialchars($support['email']) ?>" 
                                            data-url="<?= htmlspecialchars($support['url']) ?>" 
                                            data-description="<?= htmlspecialchars($support['description']) ?>" 
                                            data-file="<?= $support['screenshot'] ?>">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form method="POST" style="display: inline-block;">
                                            <input type="hidden" name="table" value="support_requests">
                                            <input type="hidden" name="id" value="<?= $support['id'] ?>">
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" style="width: 110px;" onchange="this.form.submit()">
                                                <option value="new" <?= $support['status'] == 'new' ? 'selected' : '' ?>>Новая</option>
                                                <option value="in_progress" <?= $support['status'] == 'in_progress' ? 'selected' : '' ?>>В работе</option>
                                                <option value="resolved" <?= $support['status'] == 'resolved' ? 'selected' : '' ?>>Решена</option>
                                                <option value="closed" <?= $support['status'] == 'closed' ? 'selected' : '' ?>>Закрыта</option>
                                            </select>
                                            <input type="hidden" name="update_status" value="1">
                                        </form>
                                        <a href="admin.php?section=requests&delete=1&table=support_requests&id=<?= $support['id'] ?>&tab=support&support_page=<?= $support_page ?>" class="btn btn-outline-danger" onclick="return confirm('Удалить заявку?')">
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
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Нет заявок в поддержку
                            </td>
                        </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
            <?php 
            if (count($support_requests) > 6)
            {
            ?>
            <nav class="mt-3 p-3">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $support_page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=support&support_page=<?= $support_page - 1 ?>">«</a>
                    </li>
                    <?php 
                    for ($i = 1; $i <= $support_total_pages; $i++)
                    {
                    ?>
                        <li class="page-item <?= $i == $support_page ? 'active' : '' ?>">
                            <a class="page-link" href="admin.php?section=requests&tab=support&support_page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php 
                    }
                    ?>
                    <li class="page-item <?= $support_page >= $support_total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="admin.php?section=requests&tab=support&support_page=<?= $support_page + 1 ?>">»</a>
                    </li>
                </ul>
                <div class="text-center text-muted mt-2 small">
                    Страница <?= $support_page ?> из <?= $support_total_pages ?> | Всего: <?= count($support_requests) ?> записей
                </div>
            </nav>
            <?php 
            }
            ?>
        </div>
    </div>
</div>

<div id="modalContainer"></div>

<script>
function createModal(title, content, bgColor = 'primary') 
{
    let modalId = 'dynamicModal_' + Date.now();
    let modalHtml = `
        <div class="modal fade" id="${modalId}" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-${bgColor} text-white">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                        ${content}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    let container = document.getElementById('modalContainer');
    container.innerHTML = modalHtml;

    let modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
    
    document.getElementById(modalId).addEventListener('hidden.bs.modal', function() 
    {
        container.innerHTML = '';
    });
}

document.querySelectorAll('.view-supplier').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `
            <div class="row mb-2"><div class="col-4 fw-bold">Компания:</div><div class="col-8">${d.company || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Контактное лицо:</div><div class="col-8">${d.person || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Телефон:</div><div class="col-8">${d.phone || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Email:</div><div class="col-8">${d.email || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Категория:</div><div class="col-8">${d.category || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Сообщение:</div><div class="col-8">${d.message ? d.message.replace(/\n/g, '<br>') : '<span class="text-muted">Не указано</span>'}</div></div>
            ${d.file ? `<div class="row mb-2"><div class="col-4 fw-bold">Прайс-лист:</div><div class="col-8"><a href="../uploads/suppliers/${d.file}" target="_blank" class="btn btn-sm btn-outline-primary">Скачать</a></div></div>` : ''}
        `;

        createModal('Детали заявки от поставщика', content, 'info');
    });
});

document.querySelectorAll('.view-job').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `
            <div class="row mb-2"><div class="col-4 fw-bold">Вакансия:</div><div class="col-8">${d.position || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">ФИО:</div><div class="col-8">${d.name || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Телефон:</div><div class="col-8">${d.phone || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Email:</div><div class="col-8">${d.email || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Сообщение:</div><div class="col-8">${d.message ? d.message.replace(/\n/g, '<br>') : '<span class="text-muted">Не указано</span>'}</div></div>
            ${d.file ? `<div class="row mb-2"><div class="col-4 fw-bold">Резюме:</div><div class="col-8"><a href="../uploads/resumes/${d.file}" target="_blank" class="btn btn-sm btn-outline-success">Скачать</a></div></div>` : ''}
        `;

        createModal('Детали отклика на вакансию', content, 'success');
    });
});

document.querySelectorAll('.view-service').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `
            <div class="row mb-2"><div class="col-4 fw-bold">Имя:</div><div class="col-8">${d.name || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Телефон:</div><div class="col-8">${d.phone || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Автомобиль:</div><div class="col-8">${d.car || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Услуга:</div><div class="col-8">${d.service || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Цена:</div><div class="col-8">${d.price ? d.price + ' ₽' : '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Дата:</div><div class="col-8">${d.date || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Время:</div><div class="col-8">${d.time || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Сообщение:</div><div class="col-8">${d.message ? d.message.replace(/\n/g, '<br>') : '<span class="text-muted">Не указано</span>'}</div></div>
        `;

        createModal('Детали заявки на сервис', content, 'warning');
    });
});

document.querySelectorAll('.view-contact').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `
            <div class="row mb-2"><div class="col-4 fw-bold">Имя:</div><div class="col-8">${d.name || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Email:</div><div class="col-8">${d.email || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Телефон:</div><div class="col-8">${d.phone || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Тема:</div><div class="col-8">${d.subject || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Сообщение:</div><div class="col-8">${d.message ? d.message.replace(/\n/g, '<br>') : '<span class="text-muted">Не указано</span>'}</div></div>
        `;

        createModal('Сообщение с контактов', content, 'secondary');
    });
});

document.querySelectorAll('.view-support').forEach(btn => {
    btn.addEventListener('click', function() 
    {
        let d = this.dataset;
        let content = `
            <div class="row mb-2"><div class="col-4 fw-bold">Тип проблемы:</div><div class="col-8">${d.type || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Email:</div><div class="col-8">${d.email || '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">URL:</div><div class="col-8">${d.url ? `<a href="${d.url}" target="_blank">${d.url}</a>` : '<span class="text-muted">Не указано</span>'}</div></div>
            <div class="row mb-2"><div class="col-4 fw-bold">Описание:</div><div class="col-8">${d.description ? d.description.replace(/\n/g, '<br>') : '<span class="text-muted">Не указано</span>'}</div></div>
            ${d.file ? `<div class="row mb-2"><div class="col-4 fw-bold">Скриншот:</div><div class="col-8"><a href="../uploads/support/${d.file}" target="_blank" class="btn btn-sm btn-outline-danger">Просмотреть</a></div></div>` : ''}
        `;

        createModal('Заявка в поддержку', content, 'danger');
    });
});
</script>