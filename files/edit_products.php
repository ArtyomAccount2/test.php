<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, category = ?, price = ?, quantity = ?, article = ?, image = ?, status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("sssdisssi", $name, $description, $category, $price, $quantity, $article, $image, $status, $product_id);
        
        if ($stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Товар успешно обновлен';
            echo '<script>window.location.href = "admin.php?section=products_catalog";</script>';
            exit();
        } 
        else 
        {
            $error = "Ошибка при обновлении товара: " . $conn->error;
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
        <i class="bi bi-pencil me-2"></i>Редактирование товара
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=products_catalog" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            <span class="d-none d-sm-inline">Назад к каталогу</span>
        </a>
        <a href="admin.php?section=products_catalog&delete_id=<?= $product_id ?>" 
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
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Основная информация<span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" name="name" value="<?= htmlspecialchars($product['name']) ?>" placeholder="Название товара" required>
                        <textarea class="form-control mb-2" name="description" placeholder="Описание" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        <select class="form-select" name="category" required>
                            <option value="">Выберите категорию</option>
                            <?php 
                            foreach ($categories as $cat)
                            {
                            ?>
                            <option value="<?= htmlspecialchars($cat['name']) ?>" 
                                <?= $product['category'] == $cat['name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
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
                            <input type="number" class="form-control" name="price" 
                                   value="<?= $product['price'] ?>" 
                                   step="0.01" min="0" placeholder="Цена" required>
                            <span class="input-group-text">₽</span>
                        </div>
                        <input type="number" class="form-control mb-2" name="quantity" value="<?= $product['quantity'] ?>" placeholder="Количество на складе" min="0">
                        <input type="text" class="form-control mb-2" name="article" value="<?= htmlspecialchars($product['article'] ?? '') ?>" placeholder="Артикул">
                        <div class="mt-2">
                            <label class="form-label">Статус</label>
                            <select class="form-select" name="status">
                                <option value="available" <?= $product['status'] == 'available' ? 'selected' : '' ?>>В наличии</option>
                                <option value="low" <?= $product['status'] == 'low' ? 'selected' : '' ?>>Мало</option>
                                <option value="out_of_stock" <?= $product['status'] == 'out_of_stock' ? 'selected' : '' ?>>Нет в наличии</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
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
                <a href="admin.php?section=products_catalog" class="btn btn-secondary me-2">
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
    document.getElementById('imageInput').required = true;
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