<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $login = $_POST['login'];
    $password = $_POST['password'];
    $redirect_url = $_POST['redirect_url'] ?? $_SERVER['REQUEST_URI'];

    if (strtolower($login) === 'admin' && strtolower($password) === 'admin') 
    {
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = 'admin';
        unset($_SESSION['login_error']);
        unset($_SESSION['error_message']);
        header("Location: ../admin.php");
        exit();
    }
    else
    {
        $stmt = $conn->prepare("SELECT * FROM users WHERE LOWER(login_users) = LOWER(?) AND LOWER(password_users) = LOWER(?)");
        $stmt->bind_param("ss", $login, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
        {
            $row = $result->fetch_assoc();
            $_SESSION['loggedin'] = true;
            $_SESSION['user'] = !empty($row['surname_users']) ? $row['surname_users'] . " " . $row['name_users'] . " " . $row['patronymic_users'] : $row['person_users'];
            unset($_SESSION['login_error']);
            unset($_SESSION['error_message']);
            header("Location: " . $redirect_url);
            exit();
        } 
        else 
        {
            $_SESSION['login_error'] = true;
            $_SESSION['error_message'] = "Неверный логин или пароль!";
            $_SESSION['form_data'] = $_POST;
            header("Location: " . $redirect_url);
            exit();
        }
    }
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$back_url = '../index.php';

if (isset($_GET['back'])) 
{
    $back_url = urldecode($_GET['back']);
} 
else if (isset($_SESSION['last_assortment_page'])) 
{
    $last_page = $_SESSION['last_assortment_page'];
    $query_params = [];
    
    if ($last_page['page'] > 1) 
    {
        $query_params['page'] = $last_page['page'];
    }
    
    if (!empty($last_page['search'])) 
    {
        $query_params['search'] = $last_page['search'];
    }
    
    if (!empty($last_page['category']) && $last_page['category'] !== 'все категории') 
    {
        $query_params['category'] = $last_page['category'];
    }
}

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$back_url = '../index.php';

if (!isset($_SESSION['redirect_url'])) 
{
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
}

$sql = "SELECT * FROM products WHERE id = ? AND status = 'available'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) 
{
    header("Location: " . $back_url);
    exit();
}

$date = date('d F Y', strtotime($product['created_at']));
$product_type = $product['product_type'] ?? 'part';
$badge = '';

if (!empty($product['badge'])) 
{
    switch($product['badge']) 
    {
        case 'danger':
            $badge = 'Акция';
            break;
        case 'success':
            $badge = 'Новинка';
            break;
        case 'info':
            $badge = 'Хит';
            break;
        case 'warning':
            $badge = 'Спецпредложение';
            break;
        default:
            $badge = $product['badge'];
    }
} 
else 
{
    $title_lower = strtolower($product['name']);
    $content_lower = !empty($product['description']) ? strtolower($product['description']) : '';

    if (!empty($product['old_price']) && $product['old_price'] > $product['price']) 
    {
        $badge = 'Акция';
    } 
    else if (strpos($title_lower, 'нов') !== false || strpos($content_lower, 'новый') !== false) 
    {
        $badge = 'Новинка';
    } 
    else if (strpos($title_lower, 'хит') !== false || strpos($content_lower, 'хит') !== false) 
    {
        $badge = 'Хит продаж';
    } 
    else if ($product['hit'] == 1) 
    {
        $badge = 'Хит';
    }
}

$discount = 0;

if (!empty($product['old_price']) && $product['old_price'] > 0 && $product['price'] < $product['old_price']) 
{
    $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
}

$badge_class = '';

if (!empty($product['badge'])) 
{
    $badge_class = $product['badge'];
} 
else if ($badge == 'Акция') 
{
    $badge_class = 'danger';
} 
else if ($badge == 'Новинка') 
{
    $badge_class = 'success';
} 
else if ($badge == 'Хит' || $badge == 'Хит продаж') 
{
    $badge_class = 'info';
}

$page_title = htmlspecialchars($product['name']);

switch($product_type) 
{
    case 'oil':
        $page_title .= ' - Масла и технические жидкости';
        break;
    case 'accessory':
        $page_title .= ' - Аксессуары';
        break;
    default:
        $page_title .= ' - Автозапчасти';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/product-styles.css">
    <link rel="stylesheet" href="../css/notifications-styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        <?php 
        if (isset($_SESSION['login_error'])) 
        { 
        ?>
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();

            <?php unset($_SESSION['login_error']); ?>
        <?php 
        } 
        ?>

        let backUrl = "<?= htmlspecialchars($back_url) ?>";
        sessionStorage.setItem('last_product_back_url', backUrl);
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<main class="product-single-page">
    <section class="py-5" style="margin-top: 70px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <a href="<?= htmlspecialchars($back_url) ?>" class="back-to-products" id="backToProducts">
                        <i class="bi bi-arrow-left"></i> Вернуться назад
                    </a>
                    <article class="product-single-article">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="product-single-img mb-4 position-relative">
                                    <img src="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : '../img/no-image.png' ?>" class="img-fluid rounded-3" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='../img/no-image.png'">
                                    <?php 
                                    if($badge)
                                    {
                                    ?>
                                        <div class="product-badge <?= $badge_class ? 'product-badge-' . $badge_class : '' ?>">
                                            <?= $badge ?>
                                            <?php 
                                            if($badge == 'Акция' && $discount > 0)
                                            {
                                            ?>
                                                <span class="discount-percentage">-<?= $discount ?>%</span>
                                            <?php 
                                            }
                                            ?>
                                        </div>
                                    <?php 
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center mb-3">
                                    <h1 class="product-single-title mb-0"><?= htmlspecialchars($product['name']) ?></h1>
                                    <span class="product-type-badge product-type-<?= $product_type ?>">
                                        <?php 
                                        switch($product_type) 
                                        {
                                            case 'oil':
                                                echo 'Масло';
                                                break;
                                            case 'accessory':
                                                echo 'Аксессуар';
                                                break;
                                            default:
                                                echo 'Запчасть';
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="product-meta mb-4">
                                    <div class="product-meta-item">
                                        <i class="bi bi-grid-3x3-gap-fill"></i>
                                        <span><?= htmlspecialchars(ucfirst($product['category'] ?? 'Без категории')) ?></span>
                                    </div>
                                    <?php 
                                    if(!empty($product['article']))
                                    {
                                    ?>
                                    <div class="product-meta-item">
                                        <i class="bi bi-box-seam"></i>
                                        <span>Артикул: <?= htmlspecialchars($product['article']) ?></span>
                                    </div>
                                    <?php 
                                    }
                                    
                                    if(!empty($product['brand']))
                                    {
                                    ?>
                                    <div class="product-meta-item">
                                        <i class="bi bi-tag"></i>
                                        <span>Бренд: <?= htmlspecialchars($product['brand']) ?></span>
                                    </div>
                                    <?php 
                                    }
                                    ?>
                                    <div class="product-meta-item <?= $product['quantity'] > 0 ? 'text-success' : 'text-danger' ?>">
                                        <i class="bi <?= $product['quantity'] > 0 ? 'bi-check-circle' : 'bi-x-circle' ?>"></i>
                                        <span><?= $product['quantity'] > 0 ? 'В наличии (' . $product['quantity'] . ' шт.)' : 'Нет в наличии' ?></span>
                                    </div>
                                </div>
                                <?php 
                                if(!empty($product['description'])) 
                                {
                                ?>
                                <div class="product-description mb-4">
                                    <h5>Описание</h5>
                                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                                </div>
                                <?php 
                                }
                                ?>
                                <div class="price-section mb-4">
                                    <div class="current-price h2 text-primary mb-1">
                                        <?= number_format($product['price'], 0, '', ' ') ?> ₽
                                    </div>
                                    <?php 
                                    if(!empty($product['old_price']) && $product['old_price'] > $product['price'])
                                    {
                                    ?>
                                        <div class="old-price h5 text-muted mb-0">
                                            <del><?= number_format($product['old_price'], 0, '', ' ') ?> ₽</del>
                                            <?php 
                                            if($discount > 0)
                                            {
                                            ?>
                                                <span class="badge bg-danger ms-2">-<?= $discount ?>%</span>
                                            <?php 
                                            }
                                            ?>
                                        </div>
                                    <?php 
                                    }
                                    ?>
                                </div>
                                <div class="product-actions mb-5">
                                    <?php 
                                    if($product['quantity'] > 0)
                                    {
                                    ?>
                                        <?php 
                                        if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                        {
                                        ?>
                                            <form method="POST" action="cart.php" class="d-inline add-to-cart-form">
                                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                                                <input type="hidden" name="product_image" value="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : '../img/no-image.png' ?>">
                                                <input type="hidden" name="price" value="<?= $product['price'] ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="product_type" value="<?= $product_type ?>">
                                                <input type="hidden" name="back_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                                <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg px-4 add-to-cart-btn">
                                                    <i class="bi bi-cart-plus me-2"></i>
                                                    <span class="btn-text">Добавить в корзину</span>
                                                </button>
                                            </form>
                                            <form method="POST" action="../profile.php" class="d-inline ms-2">
                                                <input type="hidden" name="wishlist_action" value="1">
                                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                                                <input type="hidden" name="product_image" value="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : '../img/no-image.png' ?>">
                                                <input type="hidden" name="price" value="<?= $product['price'] ?>">
                                                <input type="hidden" name="back_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-lg px-4">
                                                    <i class="bi bi-heart"></i>
                                                </button>
                                            </form>
                                        <?php 
                                        }
                                        else
                                        {
                                        ?>
                                            <button class="btn btn-primary btn-lg px-4" data-bs-toggle="modal" data-bs-target="#loginModal">
                                                <i class="bi bi-cart-plus me-2"></i>
                                                Добавить в корзину
                                            </button>
                                        <?php
                                        }
                                        ?>
                                    <?php 
                                    }
                                    else
                                    { 
                                    ?>
                                        <button class="btn btn-secondary btn-lg px-4" disabled>
                                            <i class="bi bi-cart-x me-2"></i>
                                            Нет в наличии
                                        </button>
                                    <?php 
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="product-specs mb-5">
                            <h3 class="mb-4">Характеристики</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="spec-group">
                                        <div class="spec-group-title">Основная информация</div>
                                        <div class="spec-item">
                                            <strong>Категория:</strong>
                                            <span><?= htmlspecialchars(ucfirst($product['category'] ?? 'Не указана')) ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <strong>Тип товара:</strong>
                                            <span>
                                                <?php 
                                                switch($product_type) 
                                                {
                                                    case 'oil':
                                                        echo 'Моторное масло / Техническая жидкость';
                                                        break;
                                                    case 'accessory':
                                                        echo 'Аксессуар';
                                                        break;
                                                    default:
                                                        echo 'Автозапчасть';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <?php 
                                        if(!empty($product['article']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Артикул:</strong>
                                            <span><?= htmlspecialchars($product['article']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        ?>
                                        <div class="spec-item">
                                            <strong>Наличие:</strong>
                                            <span class="<?= $product['quantity'] > 0 ? 'text-success' : 'text-danger' ?>">
                                                <?= $product['quantity'] > 0 ? 'В наличии (' . $product['quantity'] . ' шт.)' : 'Нет в наличии' ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="spec-group">
                                        <div class="spec-group-title">Цена и статус</div>
                                        <div class="spec-item">
                                            <strong>Цена:</strong>
                                            <span class="text-primary fw-bold"><?= number_format($product['price'], 0, '', ' ') ?> ₽</span>
                                        </div>
                                        <?php 
                                        if(!empty($product['old_price']) && $product['old_price'] > $product['price'])
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Старая цена:</strong>
                                            <span class="text-muted"><del><?= number_format($product['old_price'], 0, '', ' ') ?> ₽</del></span>
                                            <?php 
                                            if($discount > 0)
                                            {
                                            ?>
                                                <span class="badge bg-danger ms-2">-<?= $discount ?>%</span>
                                            <?php 
                                            }
                                            ?>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if(!empty($product['badge'])) 
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Метка:</strong>
                                            <span class="badge bg-<?= $product['badge'] ?>"><?= htmlspecialchars($product['badge']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if($product['hit'] == 1)
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Хит продаж:</strong>
                                            <span class="badge bg-warning text-dark">Да</span>
                                        </div>
                                        <?php 
                                        }
                                        ?>
                                        <div class="spec-item">
                                            <strong>Добавлен:</strong>
                                            <span><?= date('d.m.Y', strtotime($product['created_at'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            if($product_type == 'oil')
                            {
                            ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="spec-group">
                                        <div class="spec-group-title">Характеристики масла</div>
                                        <div class="row">
                                            <?php 
                                            if(!empty($product['brand']))
                                            {
                                            ?>
                                            <div class="col-md-4">
                                                <div class="spec-item">
                                                    <strong>Бренд:</strong>
                                                    <span><?= htmlspecialchars($product['brand']) ?></span>
                                                </div>
                                            </div>
                                            <?php  
                                            }
                                            
                                            if(!empty($product['viscosity']))
                                            {
                                            ?>
                                            <div class="col-md-4">
                                                <div class="spec-item">
                                                    <strong>Вязкость:</strong>
                                                    <span><?= htmlspecialchars($product['viscosity']) ?></span>
                                                </div>
                                            </div>
                                            <?php 
                                            }
                                            
                                            if(!empty($product['oil_type']))
                                            {
                                            ?>
                                            <div class="col-md-4">
                                                <div class="spec-item">
                                                    <strong>Тип масла:</strong>
                                                    <span><?= htmlspecialchars($product['oil_type']) ?></span>
                                                </div>
                                            </div>
                                            <?php 
                                            }
                                            
                                            if(!empty($product['volume']))
                                            {
                                            ?>
                                            <div class="col-md-4">
                                                <div class="spec-item">
                                                    <strong>Объем:</strong>
                                                    <span><?= htmlspecialchars($product['volume']) ?></span>
                                                </div>
                                            </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            }
 
                            if($product_type == 'accessory' && !empty($product['brand']))
                            {
                            ?>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="spec-group">
                                        <div class="spec-group-title">Информация об аксессуаре</div>
                                        <div class="spec-item">
                                            <strong>Бренд:</strong>
                                            <span><?= htmlspecialchars($product['brand']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            }
                            ?>
                        </div>
                        <div class="product-tags mt-5 pt-4">
                            <span class="me-2"><i class="bi bi-tags"></i> Метки:</span>
                            <?php 
                            if(!empty($product['category']))
                            {
                            ?>
                            <a href="<?= $product_type == 'oil' ? 'oils.php' : ($product_type == 'accessory' ? 'accessories.php' : 'assortment.php') ?>?category=<?= urlencode($product['category']) ?>" class="btn btn-sm btn-outline-secondary me-2">
                                <?= htmlspecialchars(ucfirst($product['category'])) ?>
                            </a>
                            <?php 
                            }

                            if(!empty($product['brand']))
                            {
                            ?>
                            <a href="<?= $product_type == 'oil' ? 'oils.php' : ($product_type == 'accessory' ? 'accessories.php' : 'assortment.php') ?>?brand=<?= urlencode($product['brand']) ?>" class="btn btn-sm btn-outline-secondary me-2">
                                <?= htmlspecialchars($product['brand']) ?>
                            </a>
                            <?php 
                            }
                            ?>
                            <a href="<?= $product_type == 'oil' ? 'oils.php' : ($product_type == 'accessory' ? 'accessories.php' : 'assortment.php') ?>" class="btn btn-sm btn-outline-secondary me-2">
                                Все <?= $product_type == 'oil' ? 'масла' : ($product_type == 'accessory' ? 'аксессуары' : 'товары') ?>
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<div id="cartNotification" class="notification">
    <i class="bi bi-check-circle-fill"></i>
    <span>Товар добавлен в корзину!</span>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let savedBackUrl = sessionStorage.getItem('last_product_back_url');
    let backLink = document.getElementById('backToProducts');
    
    if (savedBackUrl && backLink) 
    {
        backLink.href = savedBackUrl;
    }
    
    let addToCartForms = document.querySelectorAll('.add-to-cart-form');
    
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) 
        {
            e.preventDefault();
            
            let submitButton = this.querySelector('.add-to-cart-btn');

            if (!submitButton) 
            {
                return;
            }

            let originalWidth = submitButton.offsetWidth + 'px';
            let originalHeight = submitButton.offsetHeight + 'px';
            let originalHtml = submitButton.innerHTML;
            let originalDisabled = submitButton.disabled;

            submitButton.style.minWidth = originalWidth;
            submitButton.style.minHeight = originalHeight;
            submitButton.style.width = originalWidth;
            submitButton.classList.add('btn-loading');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="btn-text">Добавляем...</span>';

            showNotification('Товар добавляется...', 'info');

            let formData = new FormData(this);

            fetch('ajax_add_to_cart.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    showNotification(data.message, 'success');
                    updateCartCounter(data.cart_count);
                } 
                else 
                {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Ошибка сети', 'error');
            })
            .finally(() => {
                setTimeout(() => {
                    submitButton.classList.remove('btn-loading');
                    submitButton.disabled = originalDisabled;
                    submitButton.innerHTML = originalHtml;
                    submitButton.style.minWidth = '';
                    submitButton.style.minHeight = '';
                    submitButton.style.width = '';
                }, 1500);
            });
        });
    });

    function showNotification(message, type = 'success') 
    {
        let notification = document.getElementById('cartNotification');
        
        if (!notification) 
        {
            notification = document.createElement('div');
            notification.id = 'cartNotification';
            notification.className = 'notification';
            document.body.appendChild(notification);
        }

        let icon = 'bi-check-circle-fill';
        let bgColor = '#28a745';
        let textColor = 'white';
        
        if (type === 'error') 
        {
            icon = 'bi-exclamation-triangle-fill';
            bgColor = '#dc3545';
        } 
        else if (type === 'info') 
        {
            icon = 'bi-info-circle-fill';
            bgColor = '#17a2b8';
        }
        
        notification.innerHTML = `<i class="bi ${icon}"></i><span>${message}</span>`;
        notification.style.background = bgColor;
        notification.style.color = textColor;
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
    
    function updateCartCounter(newCount = null) 
    {
        let cartCounter = document.getElementById('cartCounter');

        if (cartCounter) 
        {
            if (newCount !== null) 
            {
                cartCounter.textContent = newCount;
            } 
            else 
            {
                let currentCount = parseInt(cartCounter.textContent) || 0;
                cartCounter.textContent = currentCount + 1;
            }

            cartCounter.style.transform = 'scale(1.3)';
            
            setTimeout(() => {
                cartCounter.style.transform = 'scale(1)';
            }, 300);
        }
    }

    let productArticle = document.querySelector('.product-single-article');

    if (productArticle) 
    {
        productArticle.style.opacity = '0';

        setTimeout(() => {
            productArticle.style.transition = 'opacity 0.5s ease';
            productArticle.style.opacity = '1';
        }, 100);
    }
});
</script>
</body>
</html>