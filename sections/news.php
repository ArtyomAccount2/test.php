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

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 3;
$offset = ($page - 1) * $per_page;

$status_filter = $_GET['status_filter'] ?? '';
$search = $_GET['search'] ?? '';

$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) 
{
    $where_conditions[] = "(title LIKE ? OR content LIKE ? OR author LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= 'sss';
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

$count_query = "SELECT COUNT(*) as total FROM news $where_sql";
$count_stmt = $conn->prepare($count_query);

if (!empty($params)) 
{
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_news = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_news / $per_page);

$query = "SELECT * FROM news $where_sql ORDER BY created_at DESC LIMIT ? OFFSET ?";
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
$news_items = [];

while ($row = $result->fetch_assoc()) 
{
    $news_items[] = $row;
}

$stats_stmt = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published, SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft FROM news");
$stats = $stats_stmt->fetch_assoc();
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <form method="GET" action="admin.php" class="row g-3 align-items-center">
                    <input type="hidden" name="section" value="news">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Поиск новостей..." value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">Найти</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="status_filter" onchange="this.form.submit()">
                            <option value="">Все статусы</option>
                            <option value="published" <?= $status_filter == 'published' ? 'selected' : '' ?>>Опубликованные</option>
                            <option value="draft" <?= $status_filter == 'draft' ? 'selected' : '' ?>>Черновики</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Всего новостей</h5>
                <h2 class="text-primary"><?= $stats['total'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Опубликовано</h5>
                <h2 class="text-success"><?= $stats['published'] ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Черновики</h5>
                <h2 class="text-secondary"><?= $stats['draft'] ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Список новостей</h5>
        <span class="badge bg-primary"><?= $total_news ?> новостей</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Заголовок</th>
                        <th width="120">Дата</th>
                        <th width="150">Автор</th>
                        <th width="120">Статус</th>
                        <th width="150">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (count($news_items) > 0)
                    {
                        foreach ($news_items as $news)
                        {
                    ?>
                    <tr>
                        <td><?= $news['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($news['title']) ?></strong>
                            <div class="text-truncate" style="max-width: 300px;">
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
                            <span class="text-muted">—</span>
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
        <?php 
        if ($total_pages > 1)
        {
        ?>
        <nav aria-label="Page navigation" class="mt-3 p-3">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=news&page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <i class="bi bi-chevron-double-left"></i>
                    </a>
                </li>
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=news&page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
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
                    <a class="page-link" href="admin.php?section=news&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <?= $i ?>
                    </a>
                </li>
                <?php 
                }
                ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=news&page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="admin.php?section=news&page=<?= $total_pages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($status_filter) ? '&status_filter=' . urlencode($status_filter) : '' ?>">
                        <i class="bi bi-chevron-double-right"></i>
                    </a>
                </li>
            </ul>
            <div class="text-center text-muted mt-2">
                Страница <?= $page ?> из <?= $total_pages ?> | Показано <?= count($news_items) ?> из <?= $total_news ?> новостей
            </div>
        </nav>
        <?php 
        }
        ?>
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
    
    let currentPage = new URLSearchParams(window.location.search).get('page') || 1;
    let pageInput = document.querySelector('input[name="page"]');

    if (!pageInput) 
    {
        pageInput = document.createElement('input');
        pageInput.type = 'hidden';
        pageInput.name = 'page';
        document.querySelector('#editNewsModal form').appendChild(pageInput);
    }

    pageInput.value = currentPage;
}
</script>