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
    <title>Магазины - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/shops-styles.css">
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
    <h1 class="text-center mb-5" style="padding-top: 85px;">Наши магазины</h1>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shop-card h-100">
                <div class="card-header bg-primary text-white">
                    <h3>Центральный магазин</h3>
                </div>
                <div class="card-body">
                    <div class="shop-info mb-4">
                        <p><i class="bi bi-geo-alt-fill"></i> г. Калининград, ул. Автомобильная, 12</p>
                        <p><i class="bi bi-clock-fill"></i> Пн-Пт: 9:00-20:00, Сб-Вс: 10:00-18:00</p>
                        <p><i class="bi bi-telephone-fill"></i> +7 (4012) 65-65-65</p>
                    </div>
                    <div class="shop-map">
                        <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A1234567890abcdef&amp;source=constructor" width="100%" height="300" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shop-card h-100">
                <div class="card-header bg-primary text-white">
                    <h3>Магазин на Московском</h3>
                </div>
                <div class="card-body">
                    <div class="shop-info mb-4">
                        <p><i class="bi bi-geo-alt-fill"></i> г. Калининград, Московский пр-т, 45</p>
                        <p><i class="bi bi-clock-fill"></i> Пн-Пт: 9:00-20:00, Сб-Вс: 10:00-18:00</p>
                        <p><i class="bi bi-telephone-fill"></i> +7 (4012) 76-76-76</p>
                    </div>
                    <div class="shop-map">
                        <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A1234567890abcdef&amp;source=constructor" width="100%" height="300" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <h2 class="text-center mb-4">Все магазины сети</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-shop"></i> Магазин</th>
                        <th><i class="bi bi-geo-alt"></i> Адрес</th>
                        <th><i class="bi bi-telephone"></i> Телефон</th>
                        <th><i class="bi bi-clock"></i> Режим работы</th>
                        <th><i class="bi bi-tools"></i> Услуги</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Центральный</td>
                        <td>ул. Автомобильная, 12</td>
                        <td>+7 (4012) 65-65-65</td>
                        <td>Пн-Пт: 9:00-20:00<br>Сб-Вс: 10:00-18:00</td>
                        <td>Продажа запчастей, автосервис</td>
                    </tr>
                    <tr>
                        <td>Московский</td>
                        <td>Московский пр-т, 45</td>
                        <td>+7 (4012) 76-76-76</td>
                        <td>Пн-Пт: 9:00-20:00<br>Сб-Вс: 10:00-18:00</td>
                        <td>Продажа запчастей</td>
                    </tr>
                    <tr>
                        <td>Горького</td>
                        <td>ул. Горького, 15</td>
                        <td>+7 (4012) 87-87-87</td>
                        <td>Пн-Пт: 9:00-20:00<br>Сб-Вс: 10:00-18:00</td>
                        <td>Продажа запчастей, шиномонтаж</td>
                    </tr>
                    <tr>
                        <td>Приморский</td>
                        <td>ул. Приморская, 8</td>
                        <td>+7 (4012) 98-98-98</td>
                        <td>Пн-Пт: 9:00-20:00<br>Сб-Вс: 10:00-18:00</td>
                        <td>Продажа запчастей, автохимия</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>