<?php
$error = '';
$success = '';
$edit_mode = false;
$category_type = '';
$display_name = '';
$description = '';

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

$return_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$return_search = $_GET['search'] ?? '';
$return_sort = $_GET['sort'] ?? 'name_asc';

if (isset($_GET['type'])) 
{
    $edit_mode = true;
    $category_type = $_GET['type'];
    $display_name = $display_names[$category_type] ?? ucfirst(str_replace('-', ' ', $category_type));
    $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM category_products WHERE category_type = ?");
    $check_stmt->bind_param("s", $category_type);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $products_count = $check_result->fetch_assoc()['count'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $category_type = trim($_POST['category_type']);
    $display_name = trim($_POST['display_name']);
    $description = trim($_POST['description']);
    $edit_mode = isset($_POST['edit_mode']) ? true : false;
    
    if (empty($category_type)) 
    {
        $error = 'Введите системное название категории';
    } 
    elseif (empty($display_name)) 
    {
        $error = 'Введите отображаемое название категории';
    }
    else 
    {
        if (!$edit_mode) 
        {
            $check_stmt = $conn->prepare("SELECT COUNT(*) as count FROM category_products WHERE category_type = ?");
            $check_stmt->bind_param("s", $category_type);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $products_count = $check_result->fetch_assoc()['count'];
            
            if ($products_count > 0) 
            {
                $error = 'Категория с таким системным названием уже существует в таблице товаров';
            } 
            else 
            {
                $insert_stmt = $conn->prepare("INSERT INTO category_products (category_type, title, price, stock) VALUES (?, ?, 0, 0)");
                $test_title = $display_name . ' (тестовый товар)';
                $insert_stmt->bind_param("ss", $category_type, $test_title);
                
                if ($insert_stmt->execute()) 
                {
                    $_SESSION['success_message'] = 'Категория "' . $display_name . '" успешно добавлена';
                }
                else 
                {
                    $error = 'Ошибка при создании категории: ' . $conn->error;
                }
            }
        } 
        else 
        {
            $_SESSION['success_message'] = 'Категория "' . $display_name . '" успешно обновлена';
        }
        
        if (empty($error)) 
        {
            $return_url = "admin.php?section=categories";

            if ($return_page > 1) 
            {
                $return_url .= "&page=" . $return_page;
            }

            if (!empty($return_search)) 
            {
                $return_url .= "&search=" . urlencode($return_search);
            }

            if (!empty($return_sort) && $return_sort != 'name_asc') 
            {
                $return_url .= "&sort=" . urlencode($return_sort);
            }
            
            echo '<script>window.location.href = "' . $return_url . '";</script>';
            exit();
        }
    }
}

$back_url = "admin.php?section=categories";

if ($return_page > 1) 
{
    $back_url .= "&page=" . $return_page;
}

if (!empty($return_search)) 
{
    $back_url .= "&search=" . urlencode($return_search);
}

if (!empty($return_sort) && $return_sort != 'name_asc') 
{
    $back_url .= "&sort=" . urlencode($return_sort);
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi <?= $edit_mode ? 'bi-pencil-square' : 'bi-plus-circle' ?> me-2"></i>
        <?= $edit_mode ? 'Редактирование категории' : 'Добавление категории' ?> (таблица category_products)
    </h2>
    <div class="d-flex gap-2">
        <a href="<?= $back_url ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            <span class="d-none d-sm-inline">Назад к списку</span>
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
?>
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Форма <?= $edit_mode ? 'редактирования' : 'добавления' ?> категории</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <?php 
            if ($edit_mode)
            {
            ?>
            <input type="hidden" name="edit_mode" value="1">
            <?php 
            }
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Основная информация<span class="text-danger">*</span></label>
                        <label class="form-label text-muted small mb-1">Системное название (для URL)</label>
                        <input type="text" 
                               class="form-control mb-2" 
                               name="category_type" 
                               placeholder="Например: motor-oil, brake-fluid" 
                               value="<?= htmlspecialchars($category_type) ?>" 
                               <?= $edit_mode ? 'readonly' : '' ?>
                               required>
                        <small class="text-muted d-block mb-2">Только латиница, дефисы, без пробелов</small>
                        <label class="form-label text-muted small mb-1">Отображаемое название</label>
                        <input type="text" 
                               class="form-control mb-2" 
                               name="display_name" 
                               placeholder="Например: Моторные масла" 
                               value="<?= htmlspecialchars($display_name) ?>" 
                               required>
                        <textarea class="form-control" name="description" placeholder="Описание категории (необязательно)" rows="3"><?= htmlspecialchars($description) ?></textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php 
                    if ($edit_mode && isset($products_count))
                    {
                    ?>
                    <div class="mb-3">
                        <label class="form-label">Статистика категории</label>
                        <?php
                        $stats_stmt = $conn->prepare("SELECT 
                            COUNT(*) as total,
                            SUM(CASE WHEN stock = 1 THEN 1 ELSE 0 END) as available,
                            COALESCE(AVG(price), 0) as avg_price
                            FROM category_products WHERE category_type = ?");
                        $stats_stmt->bind_param("s", $category_type);
                        $stats_stmt->execute();
                        $stats = $stats_stmt->get_result()->fetch_assoc();
                        ?>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-1"><strong>Всего товаров:</strong> <?= $stats['total'] ?? 0 ?> шт.</p>
                            <p class="mb-1"><strong>В наличии:</strong> <?= $stats['available'] ?? 0 ?> шт.</p>
                            <p class="mb-0"><strong>Средняя цена:</strong> <?= number_format($stats['avg_price'] ?? 0, 2, '.', ' ') ?> ₽</p>
                        </div>
                    </div>
                    <?php 
                    }
                    else
                    {
                    ?>
                    <div class="mb-3">
                        <label class="form-label">Примеры системных названий</label>
                        <div class="bg-light p-3 rounded">
                            <ul class="list-unstyled mb-0">
                                <li><i class="bi bi-dot text-primary"></i> <code>motor-oil</code> - Моторные масла</li>
                                <li><i class="bi bi-dot text-primary"></i> <code>transmission-oil</code> - Трансмиссионные масла</li>
                                <li><i class="bi bi-dot text-primary"></i> <code>brake-fluid</code> - Тормозные жидкости</li>
                                <li><i class="bi bi-dot text-primary"></i> <code>cooling-fluid</code> - Охлаждающие жидкости</li>
                                <li><i class="bi bi-dot text-primary"></i> <code>antifreeze</code> - Антифризы</li>
                                <li><i class="bi bi-dot text-primary"></i> <code>power-steering</code> - Жидкости ГУР</li>
                            </ul>
                        </div>
                    </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>          
            <hr>
            <div class="text-end">
                <a href="<?= $back_url ?>" class="btn btn-secondary me-2">
                    <i class="bi bi-x-circle me-1"></i>Отмена
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi <?= $edit_mode ? 'bi-check-lg' : 'bi-plus-circle' ?> me-1"></i>
                    <?= $edit_mode ? 'Сохранить изменения' : 'Добавить категорию' ?>
                </button>
            </div>
        </form>
    </div>
</div>