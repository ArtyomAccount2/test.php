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
    $product_type = $_POST['product_type'] ?? 'part';
    $brand = trim($_POST['brand'] ?? '');
    $viscosity = trim($_POST['viscosity'] ?? '');
    $oil_type = $_POST['oil_type'] ?? '';
    $volume = trim($_POST['volume'] ?? '');
    $hit = isset($_POST['hit']) ? 1 : 0;
    
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) 
    {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
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
        $stmt = $conn->prepare("INSERT INTO products (name, description, category, price, quantity, article, image, status, product_type, brand, viscosity, oil_type, volume, hit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");   
        $stmt->bind_param("sssdissssssssi", 
            $name, 
            $description, 
            $category, 
            $price, 
            $quantity, 
            $article, 
            $image, 
            $status,
            $product_type,
            $brand,
            $viscosity,
            $oil_type,
            $volume,
            $hit
        );
        
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

$categories_result = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category");
$categories = [];

while ($row = $categories_result->fetch_assoc()) 
{
    $categories[] = $row['category'];
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-plus-circle me-2"></i>Добавление товара (таблица products)
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
            <ul class="nav nav-tabs mb-3" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#main" type="button" role="tab">Основное</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">Детали</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="image-tab" data-bs-toggle="tab" data-bs-target="#image" type="button" role="tab">Изображение</button>
                </li>
            </ul>
            <div class="tab-content" id="productTabsContent">
                <div class="tab-pane fade show active" id="main" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Название товара<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Категория<span class="text-danger">*</span></label>
                                <select class="form-select" name="category" required>
                                    <option value="">Выберите категорию</option>
                                    <?php 
                                    foreach ($categories as $cat)
                                    {
                                        echo '<option value="' . htmlspecialchars($cat) . '">' . htmlspecialchars($cat) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Тип товара</label>
                                <select class="form-select" name="product_type">
                                    <option value="part">Запчасть</option>
                                    <option value="oil">Масло</option>
                                    <option value="accessory">Аксессуар</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Цена<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                                    <span class="input-group-text">₽</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Количество на складе</label>
                                <input type="number" class="form-control" name="quantity" min="0" value="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Артикул</label>
                                <input type="text" class="form-control" name="article">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Статус</label>
                                <select class="form-select" name="status">
                                    <option value="available">В наличии</option>
                                    <option value="low">Мало</option>
                                    <option value="out_of_stock">Нет в наличии</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" name="description" rows="4"></textarea>
                    </div>
                </div>
                <div class="tab-pane fade" id="details" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Бренд</label>
                                <input type="text" class="form-control" name="brand">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Вязкость (для масел)</label>
                                <input type="text" class="form-control" name="viscosity">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Тип масла</label>
                                <select class="form-select" name="oil_type">
                                    <option value="">Не выбрано</option>
                                    <option value="Синтетическое">Синтетическое</option>
                                    <option value="Полусинтетическое">Полусинтетическое</option>
                                    <option value="Минеральное">Минеральное</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Объем</label>
                                <input type="text" class="form-control" name="volume" placeholder="например: 4 л">
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="hit" id="hitCheck">
                                    <label class="form-check-label" for="hitCheck">
                                        Хит продаж
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="image" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label">Изображение товара</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Допустимые форматы: JPG, PNG, GIF, WebP. Максимальный размер: 5MB</small>
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Добавить товар
                </button>
            </div>
        </form>
    </div>
</div>