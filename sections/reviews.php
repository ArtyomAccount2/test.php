<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) 
{
    $action = $_POST['action'];
    $review_id = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;
    
    switch ($action) 
    {
        case 'approve':
            $stmt = $conn->prepare("UPDATE reviews SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $review_id);
            $stmt->execute();
            $_SESSION['message'] = 'Отзыв одобрен';
            break;
            
        case 'reject':
            $stmt = $conn->prepare("UPDATE reviews SET status = 'rejected' WHERE id = ?");
            $stmt->bind_param("i", $review_id);
            $stmt->execute();
            $_SESSION['message'] = 'Отзыв отклонен';
            break;
            
        case 'delete':
            $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->bind_param("i", $review_id);
            $stmt->execute();
            $_SESSION['message'] = 'Отзыв удален';
            break;
            
        case 'edit':
            $name = trim($_POST['name']);
            $text = trim($_POST['text']);
            $rating = (int)$_POST['rating'];
            $status = $_POST['status'];
            $email = trim($_POST['email'] ?? '');
            
            $stmt = $conn->prepare("UPDATE reviews SET name = ?, text = ?, rating = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssisi", $name, $text, $rating, $status, $review_id);
            $stmt->execute();
            $_SESSION['message'] = 'Отзыв обновлен';
            break;
    }
    
    echo '<script>window.location.href = "admin.php?section=reviews";</script>';
    exit();
}

$filter_status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

$where_conditions = [];
$params = [];
$types = '';

if ($filter_status !== 'all') 
{
    $where_conditions[] = "status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

if (!empty($search)) 
{
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR text LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= 'sss';
}

$where_sql = '';
if (!empty($where_conditions)) 
{
    $where_sql = 'WHERE ' . implode(' AND ', $where_conditions);
}

$query = "SELECT * FROM reviews $where_sql ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

if (!empty($params)) 
{
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$reviews_result = $stmt->get_result();

$stats_stmt = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending, SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved, SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected, AVG(CASE WHEN status = 'approved' THEN rating ELSE NULL END) as avg_rating FROM reviews");
$stats = $stats_stmt->fetch_assoc();
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-chat-square-text me-2"></i>Управление отзывами
    </h2>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-filter me-1"></i>
                <span class="d-none d-sm-inline">Фильтр: </span>
                <?php 
                $status_labels = [
                    'all' => 'Все',
                    'pending' => 'На модерации',
                    'approved' => 'Одобренные',
                    'rejected' => 'Отклоненные'
                ];
                echo $status_labels[$filter_status];
                ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="admin.php?section=reviews&status=all">Все</a></li>
                <li><a class="dropdown-item" href="admin.php?section=reviews&status=pending">На модерации</a></li>
                <li><a class="dropdown-item" href="admin.php?section=reviews&status=approved">Одобренные</a></li>
                <li><a class="dropdown-item" href="admin.php?section=reviews&status=rejected">Отклоненные</a></li>
            </ul>
        </div>
        
        <form method="GET" action="admin.php" class="d-flex">
            <input type="hidden" name="section" value="reviews">
            <input type="hidden" name="status" value="<?= $filter_status ?>">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Поиск..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
if (isset($_SESSION['message']))
{ 
?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $_SESSION['message']; ?>
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
                <h5 class="card-title">Всего</h5>
                <h2 class="text-primary"><?= $stats['total'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">На модерации</h5>
                <h2 class="text-warning"><?= $stats['pending'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Одобренные</h5>
                <h2 class="text-success"><?= $stats['approved'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Средний рейтинг</h5>
                <h2 class="text-info"><?= number_format($stats['avg_rating'] ?? 0, 1) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Список отзывов</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th class="d-none d-xl-table-cell">Текст отзыва</th>
                        <th class="d-none d-md-table-cell">Оценка</th>
                        <th class="d-none d-lg-table-cell">Дата</th>
                        <th class="d-none d-sm-table-cell">Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($reviews_result->num_rows > 0) 
                    {
                        while($review = $reviews_result->fetch_assoc())
                        {
                            $status_badges = [
                                'pending' => ['bg-warning', 'На модерации'],
                                'approved' => ['bg-success', 'Одобрено'],
                                'rejected' => ['bg-danger', 'Отклонено']
                            ];
                    ?>
                    <tr>
                        <td><?= $review['id'] ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($review['name']) ?></div>
                            <small class="text-muted d-none d-md-block"><?= htmlspecialchars($review['email'] ?? '-') ?></small>
                            <div class="d-block d-sm-none">
                                <small class="text-muted">
                                    <?php 
                                    for($i = 1; $i <= 5; $i++) 
                                    {
                                        echo $i <= $review['rating'] ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-warning"></i>';
                                    }
                                    ?>
                                </small>
                                <br>
                                <span class="badge <?= $status_badges[$review['status']][0] ?>">
                                    <?= $status_badges[$review['status']][1] ?>
                                </span>
                            </div>
                        </td>
                        <td class="d-none d-xl-table-cell">
                            <div class="text-truncate" style="max-width: 200px;">
                                <?= htmlspecialchars($review['text']) ?>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <div class="text-warning">
                                <?php 
                                for($i = 1; $i <= 5; $i++) 
                                {
                                    echo $i <= $review['rating'] ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
                                }
                                ?>
                                <small class="text-muted ms-1"><?= $review['rating'] ?>/5</small>
                            </div>
                        </td>
                        <td class="d-none d-lg-table-cell"><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></td>
                        <td class="d-none d-sm-table-cell">
                            <span class="badge <?= $status_badges[$review['status']][0] ?>">
                                <?= $status_badges[$review['status']][1] ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editReviewModal<?= $review['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php 
                                if ($review['status'] == 'pending')
                                {
                                ?>
                                    <button type="button" class="btn btn-outline-success" onclick="approveReview(<?= $review['id'] ?>)">
                                        <i class="bi bi-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" onclick="rejectReview(<?= $review['id'] ?>)">
                                        <i class="bi bi-x"></i>
                                    </button>
                                <?php 
                                }
                                ?>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteReview(<?= $review['id'] ?>)">
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
                            <i class="bi bi-chat-square fs-1 d-block mb-2"></i>
                            Отзывы не найдены
                        </td>
                    </tr>
                    <?php 
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php 
        if ($reviews_result->num_rows > 0)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3">
            <ul class="pagination justify-content-center">
                <li class="page-item">
                    <a class="page-link" href="admin.php?section=reviews&status=<?= $filter_status ?>&page=1">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="admin.php?section=reviews&status=<?= $filter_status ?>&page=2">
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

<?php 
if ($reviews_result->num_rows > 0)
{
    $reviews_result->data_seek(0); 
    while($review = $reviews_result->fetch_assoc())
    {
?>
<div class="modal fade" id="editReviewModal<?php echo $review['id']; ?>" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование отзыва #<?php echo $review['id']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Имя</label>
                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($review['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Текст отзыва</label>
                        <textarea class="form-control" name="text" rows="4" required><?php echo htmlspecialchars($review['text']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Оценка</label>
                        <select class="form-select" name="rating" required>
                            <?php 
                            for($i = 1; $i <= 5; $i++)
                            {
                            ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == $review['rating'] ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> звезд<?php echo $i > 1 ? 'ы' : 'а'; ?>
                                </option>
                            <?php 
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Статус</label>
                        <select class="form-select" name="status" required>
                            <option value="pending" <?php echo $review['status'] == 'pending' ? 'selected' : ''; ?>>На модерации</option>
                            <option value="approved" <?php echo $review['status'] == 'approved' ? 'selected' : ''; ?>>Одобрено</option>
                            <option value="rejected" <?php echo $review['status'] == 'rejected' ? 'selected' : ''; ?>>Отклонено</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
    }
}
?>

<script>
function approveReview(reviewId) 
{
    if (confirm('Одобрить этот отзыв?')) 
    {
        let form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="approve">
            <input type="hidden" name="review_id" value="${reviewId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectReview(reviewId) 
{
    if (confirm('Отклонить этот отзыв?')) 
    {
        let form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="review_id" value="${reviewId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteReview(reviewId) 
{
    if (confirm('Удалить этот отзыв?')) 
    {
        let form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="review_id" value="${reviewId}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

document.addEventListener('DOMContentLoaded', function() 
{
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>