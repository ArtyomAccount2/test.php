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
            header("Location: ../index.php");
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
    <title>Торговые марки - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/brands-styles.css">
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

<div class="container my-5 pt-4">
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="mb-3" style="padding-top: 60px;">Торговые марки</h1>
            <p class="lead">Бренды, представленные в нашем ассортименте</p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="search-container position-relative">
                <input type="text" id="brandSearch" placeholder="Поиск по брендам..." class="form-control form-control-lg">
                <button class="btn btn-link search-clear" type="button" style="display: none;">
                    <i class="bi bi-x"></i>
                </button>
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>
        <div class="col-md-6">
            <select class="form-select form-select-lg">
                <option selected>Все категории</option>
                <option>Оригинальные запчасти</option>
                <option>Аналоги</option>
                <option>Масла и жидкости</option>
                <option>Аксессуары</option>
                <option>Автохимия</option>
            </select>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" class="brand-logo" alt="Bosch">
                </div>
                <div class="brand-name">Bosch</div>
                <div class="brand-category">Оригинальные запчасти, Аналоги</div>
                <a href="brand-single.php?id=1" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3 col-xl-2">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" class="brand-logo" alt="Castrol">
                </div>
                <div class="brand-name">Castrol</div>
                <div class="brand-category">Масла и жидкости</div>
                <a href="brand-single.php?id=2" class="stretched-link"></a>
            </div>
        </div>
        <!-- Остальные бренды -->
    </div>
    <div class="brand-letters mt-5">
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
            <a href="#" class="brand-letter active">Все</a>
            <a href="#" class="brand-letter">A</a>
            <a href="#" class="brand-letter">B</a>
            <a href="#" class="brand-letter">C</a>
            <!-- Остальные буквы -->
        </div>
    </div> 
    <div class="brand-categories mt-5">
        <h3 class="mb-4 text-center">Категории брендов</h3>
        <div class="row g-3">
            <div class="col-md-4">
                <a href="#" class="category-card">
                    <div class="category-icon">
                        <i class="bi bi-gear"></i>
                    </div>
                    <h4>Оригинальные запчасти</h4>
                    <p>Официальные поставщики автокомпонентов</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="category-card">
                    <div class="category-icon">
                        <i class="bi bi-gear-wide"></i>
                    </div>
                    <h4>Аналоги</h4>
                    <p>Качественные аналоги оригинальных запчастей</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="#" class="category-card">
                    <div class="category-icon">
                        <i class="bi bi-droplet"></i>
                    </div>
                    <h4>Масла и жидкости</h4>
                    <p>Производители масел и технических жидкостей</p>
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>