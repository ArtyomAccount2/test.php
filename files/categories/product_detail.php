<?php
error_reporting(E_ALL);
session_start();
require_once("../../config/link.php");
require_once("../../includes/category_functions.php");

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
        header("Location: ../../admin.php");
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

$back_url = '../../index.php';

if (isset($_GET['back']) && !empty($_GET['back'])) 
{
    $back_url = urldecode($_GET['back']);
    $_SESSION['last_back_url'] = $back_url;
} 
else if (isset($_SESSION['last_back_url'])) 
{
    $back_url = $_SESSION['last_back_url'];
}

$sql = "SELECT * FROM category_products WHERE id = ?";
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

$category_display = '';
$category_type = $product['category_type'];

$category_titles = [
    'antifreeze' => 'Антифризы',
    'brake-fluid' => 'Тормозные жидкости',
    'cooling-fluid' => 'Охлаждающие жидкости',
    'power-steering' => 'Жидкости ГУР',
    'special-fluid' => 'Специальные жидкости',
    'kit' => 'Комплекты',
    'transmission-oil' => 'Трансмиссионные масла',
    'motor-oil' => 'Моторные масла'
];

$category_display = isset($category_titles[$category_type]) ? $category_titles[$category_type] : $category_type;
$badge = '';

if ($product['hit']) 
{
    $badge = 'Хит продаж';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['title']) ?> - Лал-Авто</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/product-styles.css">
    <link rel="stylesheet" href="../../css/notifications-styles.css">
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
    });
    </script>
</head>
<body>

<div class="flex-grow-1">
    <nav class="navbar navbar-expand-xl navbar-light bg-light shadow-sm fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="../../index.php"><img src="../../img/Auto.png" alt="Лал-Авто" height="75"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="../../index.php">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="../../includes/oils.php?sort=default&page=1">Масла и тех. жидкости</a>
                    </li>
                </ul>
                <div class="ms-xl-3 ms-lg-2 ms-md-1">
                    <?php 
                    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) 
                    {
                    ?>
                        <div class="d-flex flex-column flex-md-row align-items-center">
                            <p class="mb-0 text-center text-md-end me-md-2" style="font-size: 0.9em; white-space: nowrap;">
                                <strong><?= htmlspecialchars($_SESSION['user']); ?></strong>
                            </p>
                            <a href="../../profile.php" class="profile-button w-md-auto text-decoration-none">
                                <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                            </a>
                        </div>
                    <?php 
                    } 
                    else 
                    {
                    ?>
                        <div class="d-flex flex-wrap flex-md-nowrap">
                            <a href="#" class="btn btn-primary button-link w-md-auto mx-1" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-box-arrow-in-right"></i>
                                Войти
                            </a>
                            <a href="#" class="btn btn-primary button-link w-md-auto" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="bi bi-r-circle"></i>
                                Зарегистрироваться
                            </a>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="loginModalLabel">Авторизация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" id="username" placeholder="Введите логин" required value="<?= htmlspecialchars($form_data['login'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Введите пароль" required value="<?= htmlspecialchars($form_data['password'] ?? '') ?>">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="rememberMe" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                        </div>
                        <?php 
                        if (isset($_SESSION['error_message'])) 
                        {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($_SESSION['error_message']); ?>
                            </div>
                        <?php 
                            unset($_SESSION['error_message']);
                        }
                        ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Войти
                        </button>
                        <a href="#" class="btn btn-link">Забыли пароль?</a>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="registerModalLabel">Регистрация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <a href="../../individuel.php" type="button" class="btn btn-primary mb-2" id="individualsBtn">
                        <i class="bi bi-person-add"></i> Физические лица
                    </a>
                    <div id="individualsInfo" class="registration-info">
                        <p>- если Вы - физическое лицо, пройдите регистрацию. Регистрация возможна как при наличии карты скидок, так и при её отсутствии.</p>
                    </div>
                    <a href="../../legalEntity.php" type="button" class="btn btn-primary mb-2" id="legalEntitiesBtn">
                        <i class="bi bi-person-add"></i> Юридические лица и ИП
                    </a>
                    <div id="legalEntitiesInfo" class="registration-info">
                        <p>- если Вы - представитель организации, учреждения, предприятия или фирмы, заполните данную форму регистрации.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>

    <main class="product-single-page">
        <section class="py-5" style="margin-top: 70px;">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <a href="<?= htmlspecialchars($back_url) ?>" class="back-to-products">
                            <i class="bi bi-arrow-left"></i> Вернуться назад
                        </a>
                        <article class="product-single-article">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="product-single-img mb-4 position-relative">
                                        <img src="<?= !empty($product['image']) ? '../../' . htmlspecialchars($product['image']) : '../../img/no-image.png' ?>" class="img-fluid rounded-3" alt="<?= htmlspecialchars($product['title']) ?>" onerror="this.src='../../img/no-image.png'">
                                        <?php 
                                        if($badge)
                                        {
                                        ?>
                                            <div class="product-badge <?= $badge == 'Акция' ? 'product-badge-sale' : '' ?>">
                                                <?= $badge ?>
                                            </div>
                                        <?php 
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="product-meta mb-4">
                                        <div class="product-meta-item">
                                            <i class="bi bi-grid-3x3-gap-fill"></i>
                                            <span><?= htmlspecialchars($category_display) ?></span>
                                        </div>
                                        <div class="product-meta-item">
                                            <i class="bi bi-box-seam"></i>
                                            <span>Артикул: <?= !empty($product['art']) ? htmlspecialchars($product['art']) : 'PROD-' . str_pad($product['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                        </div>
                                        <div class="product-meta-item <?= $product['stock'] ? 'text-success' : 'text-danger' ?>">
                                            <i class="bi <?= $product['stock'] ? 'bi-check-circle' : 'bi-x-circle' ?>"></i>
                                            <span><?= $product['stock'] ? 'В наличии' : 'Нет в наличии' ?></span>
                                        </div>
                                    </div>
                                    <h1 class="product-single-title mb-4"><?= htmlspecialchars($product['title']) ?></h1>
                                    <div class="price-section mb-4">
                                        <div class="current-price h3 text-primary mb-1">
                                            <?= number_format($product['price'], 0, '', ' ') ?> ₽
                                        </div>
                                    </div>
                                    <div class="product-actions mb-5">
                                        <?php 
                                        if($product['stock'])
                                        {
                                        ?>
                                            <?php 
                                            if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true)
                                            {
                                            ?>
                                                <form method="POST" action="../../includes/add_to_cart.php" class="d-inline add-to-cart-form">
                                                    <input type="hidden" name="category_product_id" value="<?= $product['id'] ?>">
                                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['title']) ?>">
                                                    <input type="hidden" name="product_image" value="<?= !empty($product['image']) ? htmlspecialchars($product['image']) : '../../img/no-image.png' ?>">
                                                    <input type="hidden" name="price" value="<?= $product['price'] ?>">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <input type="hidden" name="product_type" value="<?= $product['category_type'] ?>">
                                                    <input type="hidden" name="back_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                                                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg px-4 add-to-cart-btn">
                                                        <i class="bi bi-cart-plus me-2"></i>
                                                        <span class="btn-text">Добавить в корзину</span>
                                                    </button>
                                                </form>
                                                <form method="POST" action="../../profile.php" class="d-inline ms-2">
                                                    <input type="hidden" name="wishlist_action" value="1">
                                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['title']) ?>">
                                                    <input type="hidden" name="product_image" value="<?= '../../' . (!empty($product['image']) ? htmlspecialchars($product['image']) : 'img/no-image.png') ?>">
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
                                        <div class="spec-item">
                                            <strong>Категория:</strong>
                                            <span><?= htmlspecialchars($category_display) ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <strong>Артикул:</strong>
                                            <span><?= !empty($product['art']) ? htmlspecialchars($product['art']) : 'PROD-' . str_pad($product['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                        </div>
                                        <div class="spec-item">
                                            <strong>Бренд:</strong>
                                            <span><?= !empty($product['brand']) ? htmlspecialchars($product['brand']) : 'Не указан' ?></span>
                                        </div>
                                        <?php 
                                        if(!empty($product['volume']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Объем:</strong>
                                            <span><?= htmlspecialchars($product['volume']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if($category_type == 'antifreeze' && !empty($product['type']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Тип:</strong>
                                            <span><?= htmlspecialchars($product['type']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if($category_type == 'antifreeze' && !empty($product['color']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Цвет:</strong>
                                            <span><?= htmlspecialchars($product['color']) ?></span>
                                        </div>
                                        <?php
                                        }
                                        
                                        if($category_type == 'antifreeze' && !empty($product['freezing']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Замерзание:</strong>
                                            <span><?= htmlspecialchars($product['freezing']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if($category_type == 'brake-fluid' && !empty($product['standard']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Стандарт:</strong>
                                            <span><?= htmlspecialchars($product['standard']) ?></span>
                                        </div>
                                        <?php
                                        }
                                        
                                        if($category_type == 'brake-fluid' && !empty($product['dry_boil'])) 
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Темп. кипения (сухая):</strong>
                                            <span><?= htmlspecialchars($product['dry_boil']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if($category_type == 'brake-fluid' && !empty($product['wet_boil']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Темп. кипения (влажная):</strong>
                                            <span><?= htmlspecialchars($product['wet_boil']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if(in_array($category_type, ['motor-oil', 'transmission-oil']) && !empty($product['viscosity']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Вязкость:</strong>
                                            <span><?= htmlspecialchars($product['viscosity']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if(in_array($category_type, ['motor-oil', 'transmission-oil']) && !empty($product['type']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Тип масла:</strong>
                                            <span><?= htmlspecialchars($product['type']) ?></span>
                                        </div>
                                        <?php 
                                        } 
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="spec-item">
                                            <strong>Цена:</strong>
                                            <span><?= number_format($product['price'], 0, '', ' ') ?> ₽</span>
                                        </div>
                                        <div class="spec-item">
                                            <strong>Наличие:</strong>
                                            <span class="<?= $product['stock'] ? 'text-success' : 'text-danger' ?>">
                                                <?= $product['stock'] ? 'В наличии' : 'Нет в наличии' ?>
                                            </span>
                                        </div>
                                        <?php 
                                        if($product['hit'])
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Популярность:</strong>
                                            <span class="text-danger">Хит продаж</span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if($category_type == 'kit' && !empty($product['contents']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Состав:</strong>
                                            <span><?= htmlspecialchars($product['contents']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if($category_type == 'special-fluid' && !empty($product['application']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>Применение:</strong>
                                            <span><?= htmlspecialchars($product['application']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if($category_type == 'motor-oil' && !empty($product['api']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>API:</strong>
                                            <span><?= htmlspecialchars($product['api']) ?></span>
                                        </div>
                                        <?php 
                                        }
                                        
                                        if($category_type == 'motor-oil' && !empty($product['acea']))
                                        {
                                        ?>
                                        <div class="spec-item">
                                            <strong>ACEA:</strong>
                                            <span><?= htmlspecialchars($product['acea']) ?></span>
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
                            <div class="product-tags mt-5 pt-4">
                                <span class="me-2"><i class="bi bi-tags"></i> Метки:</span>
                                <a href="<?= basename($back_url) ?>" class="btn btn-sm btn-outline-secondary me-2"><?= htmlspecialchars($category_display) ?></a>
                                <a href="../../includes/oils.php" class="btn btn-sm btn-outline-secondary me-2">Все товары</a>
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
    require_once("../../includes/footer.php"); 
?>

<script src="../../js/bootstrap.bundle.min.js"></script>
<script src="../../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
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

            let originalHtml = submitButton.innerHTML;
            submitButton.classList.add('btn-loading');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="btn-text">Добавляем...</span>';

            let formData = new FormData(this);

            fetch('../../includes/add_to_cart.php', {
                method: 'POST',
                body: formData,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    showNotification(data.message, 'success');
                    
                    if (data.cart_count !== undefined) 
                    {
                        updateCartCounter(data.cart_count);
                    }
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
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalHtml;
                }, 1500);
            });
        });
    });
    
    function showNotification(message, type = 'success') 
    {
        let notification = document.getElementById('cartNotification');
        let icon = type === 'success' ? 'bi-check-circle-fill' : (type === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill');
        let bgColor = type === 'success' ? '#28a745' : (type === 'error' ? '#dc3545' : '#17a2b8');
        
        notification.innerHTML = `<i class="bi ${icon}"></i><span>${message}</span>`;
        notification.style.background = bgColor;
        notification.classList.add('show');
        
        setTimeout(() => notification.classList.remove('show'), 3000);
    }
    
    function updateCartCounter(newCount) 
    {
        let cartCounter = document.getElementById('cartCounter');
        
        if (cartCounter) 
        {
            cartCounter.textContent = newCount;
            cartCounter.style.transform = 'scale(1.3)';
            setTimeout(() => cartCounter.style.transform = 'scale(1)', 300);
        }
    }
});
</script>
</body>
</html>