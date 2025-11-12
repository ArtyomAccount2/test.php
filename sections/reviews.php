<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../files/logout.php");
    exit();
}

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
$query = "SELECT * FROM reviews";
$params = [];

if ($filter_status !== 'all') 
{
    $query .= " WHERE status = ?";
    $params[] = $filter_status;
}

$query .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) 
{
    $stmt->bind_param("s", $params[0]);
}

$stmt->execute();
$reviews_result = $stmt->get_result();
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

<?php unset($_SESSION['message']); ?>
<?php 
} 
?>

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
                        <th>Текст отзыва</th>
                        <th>Оценка</th>
                        <th>Дата</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($reviews_result->num_rows > 0) 
                    {
                    ?>
                        <?php 
                        while($review = $reviews_result->fetch_assoc())
                        {
                        ?>
                        <tr>
                            <td><?php echo $review['id']; ?></td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($review['name']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($review['email'] ?? '-'); ?></small>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;">
                                    <?php echo htmlspecialchars($review['text']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-warning">
                                    <?php 
                                    for($i = 1; $i <= 5; $i++) 
                                    {
                                        if ($i <= $review['rating']) 
                                        {
                                            echo '<i class="bi bi-star-fill"></i>';
                                        } 
                                        else 
                                        {
                                            echo '<i class="bi bi-star"></i>';
                                        }
                                    }
                                    ?>
                                </div>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></td>
                            <td>
                                <?php
                                $status_badges = [
                                    'pending' => 'bg-warning',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger'
                                ];
                                $status_labels = [
                                    'pending' => 'На модерации',
                                    'approved' => 'Одобрено',
                                    'rejected' => 'Отклонено'
                                ];
                                ?>
                                <span class="badge <?php echo $status_badges[$review['status']]; ?>">
                                    <?php echo $status_labels[$review['status']]; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editReviewModal<?php echo $review['id']; ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php 
                                    if ($review['status'] == 'pending')
                                    {
                                    ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="approve">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <button type="submit" class="btn btn-outline-success">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="reject">
                                            <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                            <button type="submit" class="btn btn-outline-warning">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    <?php 
                                    }
                                    ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Вы уверены, что хотите удалить этот отзыв?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
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
    </div>
</div>

<?php 
if ($reviews_result->num_rows > 0)
{
?>
    <?php 
    $reviews_result->data_seek(0); 
    ?>
    <?php 
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
    ?>
<?php 
}
?>