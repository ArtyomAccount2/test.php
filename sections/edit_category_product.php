<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    header("Location: ../index.php");
    exit();
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category_type = isset($_GET['category_type']) ? $_GET['category_type'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if (!$product_id || !$category_type) 
{
    $_SESSION['error_message'] = 'Не указан товар или категория';
    header("Location: admin.php?section=categories");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM category_products WHERE id = ? AND category_type = ?");
$stmt->bind_param("is", $product_id, $category_type);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) 
{
    $_SESSION['error_message'] = 'Товар не найден';
    header("Location: admin.php?section=category_products&category_type=" . urlencode($category_type));
    exit();
}

$display_names = [
    'antifreeze' => 'Антифризы',
    'brake-fluid' => 'Тормозные жидкости',
    'cooling-fluid' => 'Охлаждающие жидкости',
    'power-steering' => 'Жидкости ГУР',
    'special-fluid' => 'Специальные жидкости',
    'kit' => 'Комплекты',
    'transmission-oil' => 'Трансмиссионные масла',
    'motor-oil' => 'Моторные масла'
];

$category_name = $display_names[$category_type] ?? ucfirst(str_replace('-', ' ', $category_type));

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $title = trim($_POST['title']);
    $art = trim($_POST['art']);
    $volume = trim($_POST['volume']);
    $price = floatval($_POST['price']);
    $stock = isset($_POST['stock']) ? 1 : 0;
    $hit = isset($_POST['hit']) ? 1 : 0;
    $brand = trim($_POST['brand']);
    $type = trim($_POST['type']);
    $color = trim($_POST['color']);
    $viscosity = trim($_POST['viscosity']);
    $standard = trim($_POST['standard']);
    $application = trim($_POST['application']);
    $freezing = trim($_POST['freezing']);
    $dry_boil = trim($_POST['dry_boil']);
    $wet_boil = trim($_POST['wet_boil']);
    $contents = trim($_POST['contents']);
    $api = trim($_POST['api']);
    $acea = trim($_POST['acea']);
    
    $image = $product['image'];

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

            if (!empty($image) && $image != 'uploads/products/696392655986c.png' && file_exists('../' . $image)) 
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

    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') 
    {
        if (!empty($product['image']) && $product['image'] != 'uploads/products/696392655986c.png' && file_exists('../' . $product['image'])) 
        {
            unlink('../' . $product['image']);
        }

        $image = 'uploads/products/696392655986c.png';
    }
    
    if (empty($title) || $price <= 0) 
    {
        $error = 'Заполните обязательные поля (Название, Цена)';
    } 
    else 
    {
        $update_stmt = $conn->prepare("UPDATE category_products SET 
            title = ?, 
            art = ?, 
            volume = ?, 
            price = ?, 
            stock = ?, 
            hit = ?, 
            brand = ?, 
            type = ?, 
            color = ?, 
            viscosity = ?, 
            standard = ?, 
            application = ?, 
            freezing = ?, 
            dry_boil = ?, 
            wet_boil = ?, 
            contents = ?, 
            api = ?, 
            acea = ?, 
            image = ?,
            updated_at = NOW() 
            WHERE id = ?");
            
        $update_stmt->bind_param("sssdissssssssssssssi", 
            $title, $art, $volume, $price, $stock, $hit, $brand, $type, $color, 
            $viscosity, $standard, $application, $freezing, $dry_boil, $wet_boil, 
            $contents, $api, $acea, $image, $product_id
        );
        
        if ($update_stmt->execute()) 
        {
            $_SESSION['success_message'] = 'Товар успешно обновлен';
            echo '<script>window.location.href = "admin.php?section=category_products&category_type=' . urlencode($category_type) . '&page=' . $page . '";</script>';
            exit();
        } 
        else 
        {
            $error = "Ошибка при обновлении товара: " . $conn->error;
        }
    }
}

$brands_stmt = $conn->prepare("SELECT DISTINCT brand FROM category_products WHERE category_type = ? AND brand IS NOT NULL AND brand != '' ORDER BY brand");
$brands_stmt->bind_param("s", $category_type);
$brands_stmt->execute();
$brands_result = $brands_stmt->get_result();
$brands = [];

while ($row = $brands_result->fetch_assoc()) 
{
    $brands[] = $row['brand'];
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-pencil-square me-2"></i>Редактирование товара в категории
    </h2>
    <div class="d-flex gap-2">
        <a href="admin.php?section=category_products&category_type=<?= urlencode($category_type) ?>&page=<?= $page ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            <span class="d-none d-sm-inline">Назад к товарам</span>
        </a>
    </div>
</div>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Категория: <strong><?= htmlspecialchars($category_name) ?></strong> 
    <code class="ms-2"><?= htmlspecialchars($category_type) ?></code>
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
        <ul class="nav nav-tabs card-header-tabs" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#main" type="button" role="tab">
                    <i class="bi bi-info-circle me-1"></i>Основное
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tech-tab" data-bs-toggle="tab" data-bs-target="#tech" type="button" role="tab">
                    <i class="bi bi-gear me-1"></i>Технические характеристики
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="spec-tab" data-bs-toggle="tab" data-bs-target="#spec" type="button" role="tab">
                    <i class="bi bi-file-text me-1"></i>Спецификации
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="image-tab" data-bs-toggle="tab" data-bs-target="#image" type="button" role="tab">
                    <i class="bi bi-image me-1"></i>Изображение
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="page" value="<?= $page ?>">     
            <div class="tab-content" id="productTabsContent">
                <div class="tab-pane fade show active" id="main" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Название товара<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Артикул (ART)</label>
                                        <input type="text" class="form-control" name="art" value="<?= htmlspecialchars($product['art'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Объем</label>
                                        <input type="text" class="form-control" name="volume" value="<?= htmlspecialchars($product['volume'] ?? '') ?>" placeholder="например: 1 л, 5 кг">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Цена<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="price" value="<?= $product['price'] ?>" step="0.01" min="0" required>
                                            <span class="input-group-text">₽</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Бренд</label>
                                        <input type="text" class="form-control" name="brand" value="<?= htmlspecialchars($product['brand'] ?? '') ?>" list="brandsList">
                                        <datalist id="brandsList">
                                            <?php 
                                            foreach ($brands as $brand)
                                            {
                                            ?>
                                                <option value="<?= htmlspecialchars($brand) ?>">
                                            <?php 
                                            }
                                            ?>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">Тип</label>
                                        <input type="text" class="form-control" name="type" value="<?= htmlspecialchars($product['type'] ?? '') ?>" placeholder="например: G12, G13">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="stock" id="stockCheck" <?= $product['stock'] == 1 ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="stockCheck">В наличии</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="hit" id="hitCheck" <?= $product['hit'] == 1 ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="hitCheck">Хит продаж</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Информация</h6>
                                    <p class="small text-muted mb-1">ID товара: <strong><?= $product['id'] ?></strong></p>
                                    <p class="small text-muted mb-1">Категория: <code><?= htmlspecialchars($category_type) ?></code></p>
                                    <p class="small text-muted mb-1">Создан: <?= date('d.m.Y H:i', strtotime($product['created_at'])) ?></p>
                                    <?php 
                                    if (!empty($product['updated_at']) && $product['updated_at'] != '0000-00-00 00:00:00')
                                    {
                                    ?>
                                        <p class="small text-muted mb-0">Обновлен: <?= date('d.m.Y H:i', strtotime($product['updated_at'])) ?></p>
                                    <?php 
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tech" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Цвет</label>
                                <input type="text" class="form-control" name="color" value="<?= htmlspecialchars($product['color'] ?? '') ?>" placeholder="например: Красный, Зеленый">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Вязкость</label>
                                <input type="text" class="form-control" name="viscosity" value="<?= htmlspecialchars($product['viscosity'] ?? '') ?>" placeholder="например: 5W-30, 10W-40">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Стандарт</label>
                                <input type="text" class="form-control" name="standard" value="<?= htmlspecialchars($product['standard'] ?? '') ?>" placeholder="например: DOT 4, API SN">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Применение</label>
                                <input type="text" class="form-control" name="application" value="<?= htmlspecialchars($product['application'] ?? '') ?>" placeholder="например: МКПП, АКПП">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Температура замерзания</label>
                                <input type="text" class="form-control" name="freezing" value="<?= htmlspecialchars($product['freezing'] ?? '') ?>" placeholder="например: -40°C">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Точка кипения (сухая)</label>
                                <input type="text" class="form-control" name="dry_boil" value="<?= htmlspecialchars($product['dry_boil'] ?? '') ?>" placeholder="например: 260°C">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Точка кипения (влажная)</label>
                                <input type="text" class="form-control" name="wet_boil" value="<?= htmlspecialchars($product['wet_boil'] ?? '') ?>" placeholder="например: 165°C">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Состав</label>
                                <input type="text" class="form-control" name="contents" value="<?= htmlspecialchars($product['contents'] ?? '') ?>" placeholder="например: Масло 4л + фильтр">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="spec" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">API</label>
                                <input type="text" class="form-control" name="api" value="<?= htmlspecialchars($product['api'] ?? '') ?>" placeholder="например: SN/CF">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ACEA</label>
                                <input type="text" class="form-control" name="acea" value="<?= htmlspecialchars($product['acea'] ?? '') ?>" placeholder="например: A3/B4">
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-secondary">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>Спецификации API и ACEA используются для моторных масел
                        </small>
                    </div>
                </div>
                <div class="tab-pane fade" id="image" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Текущее изображение</label>
                                <?php 
                                $image_src = !empty($product['image']) ? '../' . $product['image'] : '../uploads/products/696392655986c.png';
                                $is_default = ($product['image'] == 'uploads/products/696392655986c.png' || empty($product['image']));
                                ?>
                                <div class="text-center p-3 border rounded">
                                    <img src="<?= $image_src ?>" alt="Товар" class="img-fluid" style="max-height: 200px;" onerror="this.src='../uploads/products/696392655986c.png'">
                                    <?php 
                                    if (!$is_default)
                                    {
                                    ?>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()">
                                            <i class="bi bi-trash"></i> Удалить изображение
                                        </button>
                                        <input type="hidden" id="remove_image_flag" name="remove_image" value="0">
                                    </div>
                                    <?php 
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Загрузить новое изображение</label>
                                <input type="file" class="form-control" name="image" accept="image/*" id="imageInput">
                                <small class="text-muted">Допустимые форматы: JPG, PNG, GIF, WebP. Максимальный размер: 5MB</small>
                                <div class="mt-2" id="imagePreview"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-end">
                <a href="admin.php?section=category_products&category_type=<?= urlencode($category_type) ?>&page=<?= $page ?>" class="btn btn-secondary me-2">
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
                <p>Вы уверены, что хотите удалить изображение товара?</p>
                <p class="text-muted small">После сохранения будет установлено изображение по умолчанию.</p>
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
    document.getElementById('remove_image_flag').value = '1';
    
    let currentImage = document.querySelector('#image .text-center img');

    if (currentImage) 
    {
        currentImage.style.opacity = '0.5';
    }
    
    bootstrap.Modal.getInstance(document.getElementById('confirmRemoveImage')).hide();
    alert('Изображение будет удалено после сохранения изменений');
}

document.querySelector('form').addEventListener('submit', function(e) 
{
    let price = document.querySelector('input[name="price"]');
    
    if (price.value <= 0) 
    {
        e.preventDefault();
        alert('Цена должна быть больше 0');
        price.focus();
        return false;
    }
    
    return true;
});
</script>