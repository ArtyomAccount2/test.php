<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) 
{
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (!empty($name)) 
    {
        $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        
        if ($stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Категория добавлена';
            echo '<script>window.location.href = "admin.php?section=products_categories";</script>';
            exit();
        }
    }
}

if (isset($_GET['delete_id'])) 
{
    $delete_id = (int)$_GET['delete_id'];
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE category = (SELECT name FROM categories WHERE id = ?)");
    $check_stmt->bind_param("i", $delete_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();
    
    if ($check_result['count'] == 0) 
    {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $_SESSION['success_message'] = 'Категория удалена';
    } 
    else 
    {
        $_SESSION['error_message'] = 'Невозможно удалить категорию: есть товары в этой категории';
    }
    
    echo '<script>window.location.href = "admin.php?section=products_categories";</script>';
    exit();
}

$stmt = $conn->prepare("SELECT * FROM categories ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
$categories = [];

while ($row = $result->fetch_assoc()) 
{
    $categories[] = $row;
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-tags me-2"></i>Управление категориями
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

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Список категорий</h5>
            </div>
            <div class="card-body">
                <?php 
                if (count($categories) > 0) 
                {
                ?>
                <div class="list-group">
                    <?php 
                    foreach ($categories as $category)
                    {
                    ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($category['name']) ?></strong>
                            <?php 
                            if (!empty($category['description']))
                            {
                            ?>
                            <br><small class="text-muted"><?= htmlspecialchars($category['description']) ?></small>
                            <?php 
                            }
                            ?>
                        </div>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="editCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>', '<?= htmlspecialchars($category['description']) ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="admin.php?section=products_categories&delete_id=<?= $category['id'] ?>" 
                               class="btn btn-outline-danger" 
                               onclick="return confirm('Удалить категорию?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                    <?php 
                    }
                    ?>
                </div>
                <?php 
                }
                else
                {
                ?>
                <div class="text-center text-muted py-4">
                    <i class="bi bi-tags fs-1 d-block mb-2"></i>
                    Категории не найдены
                </div>
                <?php 
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0" id="formTitle">Добавить категорию</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="categoryForm">
                    <input type="hidden" name="category_id" id="category_id">
                    <input type="hidden" name="add_category" value="1">
                    <div class="mb-3">
                        <label class="form-label">Название категории<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="category_name" name="name" placeholder="Введите название" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" id="category_description" name="description" rows="3" placeholder="Описание категории"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" id="cancelEdit" style="display: none;">Отмена</button>
                        <button type="submit" class="btn btn-primary" id="submitButton">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editCategory(id, name, description) 
{
    document.getElementById('category_id').value = id;
    document.getElementById('category_name').value = name;
    document.getElementById('category_description').value = description;
    document.getElementById('formTitle').textContent = 'Редактирование категории';
    document.getElementById('submitButton').textContent = 'Сохранить';
    document.getElementById('cancelEdit').style.display = 'inline-block';
    document.getElementById('categoryForm').action = 'sections/edit_category.php';
}

document.getElementById('cancelEdit').addEventListener('click', function() 
{
    resetForm();
});

function resetForm() 
{
    document.getElementById('categoryForm').reset();
    document.getElementById('category_id').value = '';
    document.getElementById('formTitle').textContent = 'Добавить категорию';
    document.getElementById('submitButton').textContent = 'Добавить';
    document.getElementById('cancelEdit').style.display = 'none';
    document.getElementById('categoryForm').action = '';
}
</script>