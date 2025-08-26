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
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } 
        else 
        {
            $_SESSION['login_error'] = true;
            $_SESSION['error_message'] = "Неверный логин или пароль!";
            $_SESSION['form_data'] = $_POST;
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    }
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ассортимент - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/assortment-styles.css">
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

<?php 
    require_once("header.php"); 
?>

<div class="container my-5">
    <h1 class="text-center mb-5" style="padding-top: 85px;">Ассортимент автозапчастей</h1>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="search-container position-relative">
                <input type="text" id="partsSearch" placeholder="Поиск по каталогу..." class="form-control form-control-lg">
                <button class="btn btn-link search-clear" type="button" style="display: none;">
                    <i class="bi bi-x"></i>
                </button>
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>
        <div class="col-md-6">
            <select class="form-select form-select-lg">
                <option selected>Все категории</option>
                <option>Двигатель</option>
                <option>Трансмиссия</option>
                <option>Ходовая часть</option>
                <option>Тормозная система</option>
                <option>Электрика</option>
                <option>Кузовные детали</option>
                <option>Фильтры</option>
                <option>Масла и жидкости</option>
            </select>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-3 col-md-4 col-6">
            <div class="product-card">
                <div class="product-badge">Новинка</div>
                <img src="../img/no-image.png" class="product-img" alt="Товар">
                <div class="product-body">
                    <h5 class="product-title">Фильтр масляный Mann W914/2</h5>
                    <div class="product-price">1 250 ₽</div>
                    <div class="product-actions">
                        <button class="btn btn-sm btn-outline-primary">В корзину</button>
                        <button class="btn btn-sm btn-outline-secondary">Подробнее</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6">
            <div class="product-card">
                <div class="product-badge">Акция</div>
                <img src="../img/no-image.png" class="product-img" alt="Товар">
                <div class="product-body">
                    <h5 class="product-title">Тормозные колодки Brembo P85115</h5>
                    <div class="product-price">
                        <span class="text-danger">3 890 ₽</span>
                        <small class="text-decoration-line-through text-muted">4 500 ₽</small>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-sm btn-outline-primary">В корзину</button>
                        <button class="btn btn-sm btn-outline-secondary">Подробнее</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav aria-label="Page navigation" class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Назад</a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item">
                <a class="page-link" href="#">Вперед</a>
            </li>
        </ul>
    </nav>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>