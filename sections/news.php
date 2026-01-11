<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_news'])) 
{
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author = $_SESSION['user'] ?? 'Админ';
    $status = $_POST['status'];
    $published_at = $status == 'published' ? date('Y-m-d') : null;
    
    if (!empty($title) && !empty($content)) 
    {
        $stmt = $conn->prepare("INSERT INTO news (title, content, author, status, published_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $title, $content, $author, $status, $published_at);
        
        if ($stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Новость успешно добавлена';
            echo '<script>window.location.href = "admin.php?section=news";</script>';
            exit();
        }
    }
}

if (isset($_GET['delete_id'])) 
{
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM news WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    $_SESSION['success_message'] = 'Новость удалена';
    echo '<script>window.location.href = "admin.php?section=news";</script>';
    exit();
}

if (isset($_GET['toggle_status'])) 
{
    $news_id = (int)$_GET['toggle_status'];
    $stmt = $conn->prepare("SELECT status FROM news WHERE id = ?");
    $stmt->bind_param("i", $news_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $news = $result->fetch_assoc();
    
    $new_status = $news['status'] == 'published' ? 'draft' : 'published';
    $published_at = $new_status == 'published' ? date('Y-m-d') : null;
    
    $stmt = $conn->prepare("UPDATE news SET status = ?, published_at = ? WHERE id = ?");
    $stmt->bind_param("ssi", $new_status, $published_at, $news_id);
    $stmt->execute();
    
    $_SESSION['success_message'] = 'Статус новости изменен';
    echo '<script>window.location.href = "admin.php?section=news";</script>';
    exit();
}

$stmt = $conn->prepare("SELECT * FROM news ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$news_items = [];

while ($row = $result->fetch_assoc()) 
{
    $news_items[] = $row;
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-newspaper me-2"></i>Управление новостями
    </h2>
    <div class="d-flex gap-2">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
            <i class="bi bi-plus-circle me-1"></i>
            <span class="d-none d-sm-inline">Добавить новость</span>
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
        <h5 class="mb-0">Список новостей</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Заголовок</th>
                        <th>Дата</th>
                        <th>Автор</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($news_items) > 0)
                    {
                    ?>
                        <?php 
                        foreach ($news_items as $news)
                        {
                        ?>
                        <tr>
                            <td><?= $news['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($news['title']) ?></strong>
                                <div class="text-truncate" style="max-width: 200px;">
                                    <small class="text-muted"><?= htmlspecialchars(substr($news['content'], 0, 100)) ?>...</small>
                                </div>
                            </td>
                            <td>
                                <?php 
                                if ($news['published_at'])
                                {
                                ?>
                                <?= date('d.m.Y', strtotime($news['published_at'])) ?>
                                <?php 
                                }
                                else
                                {
                                ?>
                                <span class="text-muted">Не опубликована</span>
                                <?php 
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($news['author']) ?></td>
                            <td>
                                <?php
                                $status_badges = [
                                    'published' => ['bg-success', 'Опубликовано'],
                                    'draft' => ['bg-secondary', 'Черновик']
                                ];
                                ?>
                                <span class="badge <?= $status_badges[$news['status']][0] ?>">
                                    <?= $status_badges[$news['status']][1] ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" 
                                            onclick="editNews(<?= $news['id'] ?>, '<?= htmlspecialchars(addslashes($news['title'])) ?>', '<?= htmlspecialchars(addslashes($news['content'])) ?>', '<?= $news['status'] ?>')"
                                            data-bs-toggle="modal" data-bs-target="#editNewsModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="admin.php?section=news&toggle_status=<?= $news['id'] ?>" 
                                    class="btn btn-outline-<?= $news['status'] == 'published' ? 'warning' : 'success' ?>"
                                    title="<?= $news['status'] == 'published' ? 'В черновики' : 'Опубликовать' ?>">
                                        <i class="bi bi-<?= $news['status'] == 'published' ? 'eye-slash' : 'eye' ?>"></i>
                                    </a>
                                    <a href="admin.php?section=news&delete_id=<?= $news['id'] ?>" 
                                    class="btn btn-outline-danger"
                                    onclick="return confirm('Удалить новость?')">
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
                            <i class="bi bi-newspaper fs-1 d-block mb-2"></i>
                            Новости не найдены
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

<div class="modal fade" id="addNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавить новость</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="add_news" value="1">
                    <div class="mb-3">
                        <label class="form-label">Заголовок<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" placeholder="Заголовок новости" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Содержание<span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" rows="10" placeholder="Текст новости..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Статус</label>
                        <select class="form-select" name="status">
                            <option value="draft">Черновик</option>
                            <option value="published">Опубликовать</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Добавить новость</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать новость</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="../files/edit_news.php">
                <div class="modal-body">
                    <input type="hidden" name="news_id" id="edit_news_id">
                    <div class="mb-3">
                        <label class="form-label">Заголовок<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_news_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Содержание<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_news_content" name="content" rows="10" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Статус</label>
                        <select class="form-select" id="edit_news_status" name="status">
                            <option value="draft">Черновик</option>
                            <option value="published">Опубликовать</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editNews(id, title, content, status) 
{
    document.getElementById('edit_news_id').value = id;
    document.getElementById('edit_news_title').value = title;
    document.getElementById('edit_news_content').value = content;
    document.getElementById('edit_news_status').value = status;
}
</script>