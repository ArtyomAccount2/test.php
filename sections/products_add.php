<?php
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $category = $_POST['category'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $article = trim($_POST['article']);
    $status = $_POST['status'];
    
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) 
    {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) 
        {
            $upload_dir = 'uploads/products/';

            if (!is_dir($upload_dir)) 
            {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) 
            {
                $image = 'uploads/products/' . $file_name;
            }
        }
    }
    
    if (empty($name) || empty($category) || $price <= 0) 
    {
        $error = 'Заполните обязательные поля (Название, Категория, Цена)';
    } 
    else 
    {
        $stmt = $conn->prepare("INSERT INTO products (name, description, category, price, quantity, article, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdisss", $name, $description, $category, $price, $quantity, $article, $image, $status);
        
        if ($stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Товар успешно добавлен';
            echo '<script>window.location.href = "admin.php?section=products_catalog";</script>';
            exit();
        } 
        else 
        {
            $error = "Ошибка при добавлении товара: " . $conn->error;
        }
    }
}

$categories_result = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = [];

while ($row = $categories_result->fetch_assoc()) 
{
    $categories[] = $row;
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-plus-circle me-2"></i>Добавление товара
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=products_catalog" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            <span class="d-none d-sm-inline">Назад к каталогу</span>
        </a>
    </div>
</div>

<?php 
if ($error)
{
?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $error ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
}

if ($success) 
{
?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $success ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
}
?>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Форма добавления товара</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Основная информация<span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="name" placeholder="Название товара" required>
                        <textarea class="form-control mb-2" name="description" placeholder="Описание" rows="3"></textarea>
                        <select class="form-select" name="category" required>
                            <option value="">Выберите категорию</option>
                            <?php 
                            foreach ($categories as $cat)
                            {
                            ?>
                            <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php  
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Цена и количество<span class="text-danger">*</span></label>
                        <div class="input-group mb-2">
                            <input type="number" class="form-control" name="price" placeholder="Цена" step="0.01" min="0" required>
                            <span class="input-group-text">₽</span>
                        </div>
                        <input type="number" class="form-control mb-2" name="quantity" placeholder="Количество на складе" min="0" value="0">
                        <input type="text" class="form-control" name="article" placeholder="Артикул">
                        <div class="mt-2">
                            <label class="form-label">Статус</label>
                            <select class="form-select" name="status">
                                <option value="available">В наличии</option>
                                <option value="low">Мало</option>
                                <option value="out_of_stock">Нет в наличии</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Изображение товара</label>
                <input type="file" class="form-control" name="image" accept="image/*">
                <small class="text-muted">Допустимые форматы: JPG, PNG, GIF. Максимальный размер: 5MB</small>
            </div>
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Добавить товар
                </button>
            </div>
        </form>
    </div>
</div>