<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) 
{
    header("Location: admin.php?section=products_catalog");
    exit();
}

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
    $oil_type = trim($_POST['oil_type'] ?? '');
    $volume = trim($_POST['volume'] ?? '');
    $hit = isset($_POST['hit']) ? 1 : 0;

    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

    $image = $_POST['current_image'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) 
    {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_type = mime_content_type($_FILES['image']['tmp_name']);
        
        if (in_array($file_type, $allowed_types)) 
        {
            $upload_dir = '../uploads/products/';
            if (!is_dir($upload_dir)) 
            {
                mkdir($upload_dir, 0777, true);
            }

            if (!empty($image) && file_exists('../' . $image)) 
            {
                unlink('../' . $image);
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
        $stmt = $conn->prepare("UPDATE products SET 
            name = ?, 
            description = ?, 
            category = ?, 
            price = ?, 
            quantity = ?, 
            article = ?, 
            image = ?, 
            status = ?,
            product_type = ?,
            brand = ?,
            viscosity = ?,
            oil_type = ?,
            volume = ?,
            hit = ?,
            updated_at = NOW() 
            WHERE id = ?");
            
        $stmt->bind_param("sssdissssssssii", 
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
            $hit,
            $product_id
        );
        
        if ($stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Товар успешно обновлен';
            
            if (isset($_SESSION['products_filters'])) 
            {
                $filters = $_SESSION['products_filters'];
            }
            
            $redirect_url = "admin.php?section=products_catalog&page=" . $page;
            
            if (!empty($filters['search'])) 
            {
                $redirect_url .= "&search=" . urlencode($filters['search']);
            }
            if (!empty($filters['category']) && $filters['category'] !== 'all') 
            {
                $redirect_url .= "&category=" . urlencode($filters['category']);
            }
            if (!empty($filters['price_min'])) 
            {
                $redirect_url .= "&price_min=" . urlencode($filters['price_min']);
            }
            if (!empty($filters['price_max'])) 
            {
                $redirect_url .= "&price_max=" . urlencode($filters['price_max']);
            }
            if (!empty($filters['quantity_filter'])) 
            {
                $redirect_url .= "&quantity_filter=" . urlencode($filters['quantity_filter']);
            }
            if (!empty($filters['status_filter'])) 
            {
                $redirect_url .= "&status_filter=" . urlencode($filters['status_filter']);
            }
            
            echo "<script>window.location.href = '$redirect_url';</script>";
            exit();
        } 
        else 
        {
            $error = "Ошибка при обновлении товара: " . $conn->error;
        }
    }
}

if (isset($_SERVER['HTTP_REFERER']))
{
    $referer = parse_url($_SERVER['HTTP_REFERER']);

    if (isset($referer['query'])) 
    {
        parse_str($referer['query'], $query_params);

        if (isset($query_params['section']) && $query_params['section'] == 'products_catalog') 
        {
            $_SESSION['products_filters'] = [
                'search' => $query_params['search'] ?? '',
                'category' => $query_params['category'] ?? 'all',
                'price_min' => $query_params['price_min'] ?? '',
                'price_max' => $query_params['price_max'] ?? '',
                'quantity_filter' => $query_params['quantity_filter'] ?? '',
                'status_filter' => $query_params['status_filter'] ?? '',
                'page' => $query_params['page'] ?? 1
            ];
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
        <i class="bi bi-pencil me-2"></i>Редактирование товара (таблица products)
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=products_catalog&page=<?= $page ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            <span class="d-none d-sm-inline">Назад к каталогу</span>
        </a>
        <a href="admin.php?section=products_catalog&delete_id=<?= $product_id ?>&page=<?= $page ?>" 
           class="btn btn-outline-danger"
           onclick="return confirm('Удалить этот товар?')">
            <i class="bi bi-trash me-1"></i>
            <span class="d-none d-sm-inline">Удалить</span>
        </a>
    </div>
</div>
<?php 
if (isset($error))
{
?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $error ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
}
?>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Редактирование товара #<?= $product['id'] ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($product['image'] ?? '') ?>">
            <input type="hidden" name="page" value="<?= $page ?>">
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
                                <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Категория<span class="text-danger">*</span></label>
                                <select class="form-select" name="category" required>
                                    <option value="">Выберите категорию</option>
                                    <?php 
                                    foreach ($categories as $cat)
                                    {
                                        $selected = ($product['category'] == $cat) ? 'selected' : '';
                                        echo '<option value="' . htmlspecialchars($cat) . '" ' . $selected . '>' . htmlspecialchars($cat) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Тип товара</label>
                                <select class="form-select" name="product_type">
                                    <option value="part" <?= ($product['product_type'] ?? '') == 'part' ? 'selected' : '' ?>>Запчасть</option>
                                    <option value="oil" <?= ($product['product_type'] ?? '') == 'oil' ? 'selected' : '' ?>>Масло</option>
                                    <option value="accessory" <?= ($product['product_type'] ?? '') == 'accessory' ? 'selected' : '' ?>>Аксессуар</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Цена<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="price" 
                                           value="<?= $product['price'] ?>" 
                                           step="0.01" min="0" required>
                                    <span class="input-group-text">₽</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Количество на складе</label>
                                <input type="number" class="form-control" name="quantity" value="<?= $product['quantity'] ?>" min="0">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Артикул</label>
                                <input type="text" class="form-control" name="article" value="<?= htmlspecialchars($product['article'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Статус</label>
                                <select class="form-select" name="status">
                                    <option value="available" <?= ($product['status'] ?? '') == 'available' ? 'selected' : '' ?>>В наличии</option>
                                    <option value="low" <?= ($product['status'] ?? '') == 'low' ? 'selected' : '' ?>>Мало</option>
                                    <option value="out_of_stock" <?= ($product['status'] ?? '') == 'out_of_stock' ? 'selected' : '' ?>>Нет в наличии</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Описание</label>
                        <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="tab-pane fade" id="details" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Бренд</label>
                                <input type="text" class="form-control" name="brand" value="<?= htmlspecialchars($product['brand'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Вязкость (для масел)</label>
                                <input type="text" class="form-control" name="viscosity" value="<?= htmlspecialchars($product['viscosity'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Тип масла</label>
                                <select class="form-select" name="oil_type">
                                    <option value="">Не выбрано</option>
                                    <option value="Синтетическое" <?= ($product['oil_type'] ?? '') == 'Синтетическое' ? 'selected' : '' ?>>Синтетическое</option>
                                    <option value="Полусинтетическое" <?= ($product['oil_type'] ?? '') == 'Полусинтетическое' ? 'selected' : '' ?>>Полусинтетическое</option>
                                    <option value="Минеральное" <?= ($product['oil_type'] ?? '') == 'Минеральное' ? 'selected' : '' ?>>Минеральное</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Объем</label>
                                <input type="text" class="form-control" name="volume" value="<?= htmlspecialchars($product['volume'] ?? '') ?>" placeholder="например: 4 л">
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="hit" id="hitCheck" <?= ($product['hit'] ?? 0) ? 'checked' : '' ?>>
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
                        <?php 
                        if (!empty($product['image']))
                        {
                        ?>
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <img src="../<?= htmlspecialchars($product['image']) ?>" alt="Текущее изображение" style="max-width: 150px; max-height: 150px;" class="img-thumbnail me-3">
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()">
                                        <i class="bi bi-trash"></i> Удалить изображение
                                    </button>
                                    <input type="hidden" id="remove_image_flag" name="remove_image" value="0">
                                </div>
                            </div>
                        </div>
                        <?php 
                        }
                        ?>
                        <input type="file" class="form-control" name="image" accept="image/*" id="imageInput">
                        <small class="text-muted">Допустимые форматы: JPG, PNG, GIF, WebP. Максимальный размер: 5MB</small>
                        <div class="mt-2" id="imagePreview"></div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Дополнительная информация</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Дата создания</label>
                            <input type="text" class="form-control" value="<?= date('d.m.Y H:i', strtotime($product['created_at'])) ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <label class="form-label">Дата обновления</label>
                            <input type="text" class="form-control" value="<?= !empty($product['updated_at']) ? date('d.m.Y H:i', strtotime($product['updated_at'])) : '—' ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <a href="admin.php?section=products_catalog&page=<?= $page ?>" class="btn btn-secondary me-2">
                    Отмена
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="confirmRemoveImage" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить изображение товара?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" onclick="confirmRemoveImage()">Удалить</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('imageInput').addEventListener('change', function(e) 
{
    let preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (this.files && this.files[0]) 
    {
        let reader = new FileReader();
        
        reader.onload = function(e) 
        {
            let img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
            img.className = 'img-thumbnail mt-2';
            preview.appendChild(img);
        }
        
        reader.readAsDataURL(this.files[0]);
    }
});

function removeImage() 
{
    let modal = new bootstrap.Modal(document.getElementById('confirmRemoveImage'));
    modal.show();
}

function confirmRemoveImage() 
{
    let currentImage = document.querySelector('img.img-thumbnail');

    if (currentImage) 
    {
        currentImage.style.display = 'none';
    }
    
    document.getElementById('remove_image_flag').value = '1';

    bootstrap.Modal.getInstance(document.getElementById('confirmRemoveImage')).hide();
    alert('Изображение будет удалено после сохранения изменений');
}

document.querySelector('form').addEventListener('submit', function(e) 
{
    let price = document.querySelector('input[name="price"]');
    let quantity = document.querySelector('input[name="quantity"]');
    
    if (price.value <= 0) 
    {
        e.preventDefault();
        alert('Цена должна быть больше 0');
        price.focus();
        return false;
    }
    
    if (quantity.value < 0) 
    {
        e.preventDefault();
        alert('Количество не может быть отрицательным');
        quantity.focus();
        return false;
    }
    
    return true;
});

document.querySelector('input[name="quantity"]').addEventListener('change', function() 
{
    let quantity = parseInt(this.value);
    let statusSelect = document.querySelector('select[name="status"]');
    
    if (quantity <= 0) 
    {
        statusSelect.value = 'out_of_stock';
    } 
    else if (quantity < 10) 
    {
        statusSelect.value = 'low';
    } 
    else 
    {
        statusSelect.value = 'available';
    }
});
</script>