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

$products = [
    ['id' => 1, 'name' => 'Чехлы на сиденья Premium', 'brand' => 'AutoStyle', 'price' => 4290, 'old_price' => 5050, 'rating' => 4.8, 'badge' => 'danger', 'badge_text' => '-15%'],
    ['id' => 2, 'name' => 'Коврики в салон 3D', 'brand' => 'WeatherTech', 'price' => 6790, 'old_price' => 0, 'rating' => 4.9, 'badge' => 'success', 'badge_text' => 'Новинка'],
    ['id' => 3, 'name' => 'Органайзер для багажника', 'brand' => 'CarMate', 'price' => 3490, 'old_price' => 0, 'rating' => 4.5, 'badge' => '', 'badge_text' => ''],
    ['id' => 4, 'name' => 'Ароматизатор CS-X3', 'brand' => 'Air Spencer', 'price' => 790, 'old_price' => 0, 'rating' => 4.7, 'badge' => 'info', 'badge_text' => 'Хит'],
    ['id' => 5, 'name' => 'Автохолодильник 12V', 'brand' => 'CoolMaster', 'price' => 8990, 'old_price' => 10500, 'rating' => 4.6, 'badge' => 'warning', 'badge_text' => 'Акция'],
    ['id' => 6, 'name' => 'Видеорегистратор 4K', 'brand' => 'RoadEye', 'price' => 12490, 'old_price' => 0, 'rating' => 4.8, 'badge' => 'success', 'badge_text' => 'Новинка'],
    ['id' => 7, 'name' => 'Чехол на руль из кожи', 'brand' => 'SteeringPro', 'price' => 2190, 'old_price' => 0, 'rating' => 4.4, 'badge' => 'info', 'badge_text' => 'Хит'],
    ['id' => 8, 'name' => 'Компрессор автомобильный', 'brand' => 'AirForce', 'price' => 3590, 'old_price' => 4490, 'rating' => 4.7, 'badge' => 'danger', 'badge_text' => '-20%'],
    ['id' => 9, 'name' => 'Держатель магнитный', 'brand' => 'PhoneMount', 'price' => 1290, 'old_price' => 0, 'rating' => 4.3, 'badge' => '', 'badge_text' => ''],
    ['id' => 10, 'name' => 'Парктроник 8 датчиков', 'brand' => 'ParkMaster', 'price' => 7890, 'old_price' => 0, 'rating' => 4.9, 'badge' => 'success', 'badge_text' => 'Новинка'],
    ['id' => 11, 'name' => 'Автоодеяло с подогревом', 'brand' => 'ComfortCar', 'price' => 5490, 'old_price' => 0, 'rating' => 4.6, 'badge' => 'info', 'badge_text' => 'Хит'],
    ['id' => 12, 'name' => 'Набор автомобильных инструментов', 'brand' => 'ToolPro', 'price' => 6990, 'old_price' => 8200, 'rating' => 4.5, 'badge' => 'warning', 'badge_text' => 'Акция'],
];

$sort = $_GET['sort'] ?? 'popular';

switch($sort) 
{
    case 'price_asc':
        usort($products, function($a, $b) 
        {
            return $a['price'] - $b['price'];
        });
        break;
    case 'price_desc':
        usort($products, function($a, $b) 
        {
            return $b['price'] - $a['price'];
        });
        break;
    case 'rating':
        usort($products, function($a, $b) 
        {
            return $b['rating'] - $a['rating'];
        });
        break;
    case 'new':
        usort($products, function($a, $b) 
        {
            return $b['id'] - $a['id'];
        });
        break;
    default:
        usort($products, function($a, $b) 
        {
            return $b['rating'] - $a['rating'];
        });
        break;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Аксессуары - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/accessories-styles.css">
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

        document.getElementById('sortSelect').addEventListener('change', function() 
        {
            let sortValue = this.value;
            let url = new URL(window.location.href);
            url.searchParams.set('sort', sortValue);
            window.location.href = url.toString();
        });
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5 pt-4">
    <div class="row mb-4 align-items-center" style="padding-top: 85px;">
        <div class="col-md-6">
            <h1 class="mb-0">Автоаксессуары</h1>
            <p class="text-muted">Найдите идеальные аксессуары для вашего автомобиля</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="#" class="btn btn-primary">
                <i class="bi bi-gift-fill"></i> Подарочные сертификаты
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="border-0 shadow-sm">
                <div class="card-body p-3">
                    <h5 class="card-title mb-4"><i class="bi bi-funnel"></i> Фильтры</h5>
                    <div class="mb-4">
                        <h6 class="mb-3">Цена, ₽</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <input type="number" class="form-control form-control-sm" placeholder="От" style="width: 45%">
                            <input type="number" class="form-control form-control-sm" placeholder="До" style="width: 45%">
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="mb-3">Категории</h6>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Для салона
                                <span class="badge bg-primary rounded-pill">24</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Для экстерьера
                                <span class="badge bg-primary rounded-pill">18</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Электроника
                                <span class="badge bg-primary rounded-pill">12</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Уход за авто
                                <span class="badge bg-primary rounded-pill">9</span>
                            </a>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="mb-3">Бренды</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="brand1" checked>
                            <label class="form-check-label" for="brand1">AutoStyle</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="brand2" checked>
                            <label class="form-check-label" for="brand2">CarMate</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="brand3">
                            <label class="form-check-label" for="brand3">CoverKing</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="brand4">
                            <label class="form-check-label" for="brand4">WeatherTech</label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="mb-3">Наличие</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="availability" id="avail1" checked>
                            <label class="form-check-label" for="avail1">Все товары</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="availability" id="avail2">
                            <label class="form-check-label" for="avail2">В наличии</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="availability" id="avail3">
                            <label class="form-check-label" for="avail3">Под заказ</label>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100">Применить фильтры</button>
                    <button class="btn btn-outline-secondary w-100 mt-2">Сбросить</button>
                </div>
            </div>
            <div class="border-0 shadow-sm mt-3">
                <div class="card-body p-3">
                    <h6 class="card-title mb-3"><i class="bi bi-star"></i> Популярные категории</h6>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm text-start">
                            <i class="bi bi-car-front me-2"></i>Автокосметика
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm text-start">
                            <i class="bi bi-phone me-2"></i>Держатели для телефонов
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm text-start">
                            <i class="bi bi-camera-video me-2"></i>Видеорегистраторы
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm text-start">
                            <i class="bi bi-brightness-high me-2"></i>Автолампы
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-8">
            <div class="border-0 shadow-sm mb-4">
                <div class="card-body p-2">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <span class="me-2 text-muted">Сортировка:</span>
                            <select class="form-select form-select-sm" id="sortSelect" style="width: auto;">
                                <option value="popular" <?php echo $sort === 'popular' ? 'selected' : ''; ?>>По популярности</option>
                                <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>По цене (сначала дешевые)</option>
                                <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>По цене (сначала дорогие)</option>
                                <option value="new" <?php echo $sort === 'new' ? 'selected' : ''; ?>>По новизне</option>
                                <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>По рейтингу</option>
                            </select>
                        </div>
                        <div class="text-muted">Найдено <?php echo count($products); ?> товаров</div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <?php 
                foreach($products as $product)
                {
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <?php 
                        if(!empty($product['badge']))
                        { 
                        ?>
                        <div class="badge bg-<?php echo $product['badge']; ?> position-absolute mt-2 ms-2"><?php echo $product['badge_text']; ?></div>
                        <?php 
                        }
                        ?>
                        <div class="card-img-top p-3">
                            <img src="../img/no-image.png" class="img-fluid" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small"><?php echo $product['brand']; ?></span>
                                <div class="small">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span><?php echo $product['rating']; ?></span>
                                </div>
                            </div>
                            <h5 class="card-title mb-2"><?php echo $product['name']; ?></h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php 
                                    if($product['old_price'] > 0)
                                    {
                                    ?>
                                        <span class="text-danger fw-bold"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</span>
                                        <span class="text-decoration-line-through text-muted small ms-2"><?php echo number_format($product['old_price'], 0, '', ' '); ?> ₽</span>
                                    <?php 
                                    }
                                    else
                                    {
                                    ?>
                                        <span class="fw-bold"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</span>
                                    <?php 
                                    } 
                                    ?>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                } 
                ?>
            </div>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>