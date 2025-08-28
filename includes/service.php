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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Автосервис - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/service-styles.css">
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
    <h1 class="text-center mb-5" style="padding-top: 85px;">Автосервис "Лал-Авто"</h1>
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="service-card h-100 p-4">
                <h2 class="mb-4">Наши услуги</h2>
                <ul class="service-list">
                    <li><i class="bi bi-check-circle-fill text-primary"></i> Диагностика автомобиля</li>
                    <li><i class="bi bi-check-circle-fill text-primary"></i> Техническое обслуживание</li>
                    <li><i class="bi bi-check-circle-fill text-primary"></i> Ремонт двигателя</li>
                    <li><i class="bi bi-check-circle-fill text-primary"></i> Ремонт ходовой части</li>
                    <li><i class="bi bi-check-circle-fill text-primary"></i> Электронные системы</li>
                    <li><i class="bi bi-check-circle-fill text-primary"></i> Шиномонтаж и балансировка</li>
                    <li><i class="bi bi-check-circle-fill text-primary"></i> Кузовной ремонт</li>
                    <li><i class="bi bi-check-circle-fill text-primary"></i> Покраска автомобиля</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="service-card h-100 p-4">
                <h2 class="mb-4">Записаться на сервис</h2>
                <form id="serviceForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ваше имя</label>
                        <input type="text" class="form-control" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="tel" class="form-control" id="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="car" class="form-label">Марка и модель автомобиля</label>
                        <input type="text" class="form-control" id="car" required>
                    </div>
                    <div class="mb-3">
                        <label for="service" class="form-label">Услуга</label>
                        <select class="form-select" id="service" required>
                            <option value="" selected disabled>Выберите услугу</option>
                            <option value="diagnostics">Диагностика</option>
                            <option value="maintenance">ТО</option>
                            <option value="engine">Ремонт двигателя</option>
                            <option value="suspension">Ремонт ходовой</option>
                            <option value="electronics">Электронные системы</option>
                            <option value="tires">Шиномонтаж</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Желаемая дата</label>
                        <input type="date" class="form-control" id="date" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Записаться</button>
                </form>
            </div>
        </div>
    </div>
    <div class="service-advantages mb-5">
        <h2 class="text-center mb-4">Почему выбирают наш автосервис?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="advantage-card text-center p-3">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h4>Опытные мастера</h4>
                    <p>Работают специалисты с опытом от 5 лет</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="advantage-card text-center p-3">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4>Гарантия качества</h4>
                    <p>Гарантия на все виды работ до 2 лет</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="advantage-card text-center p-3">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h4>Доступные цены</h4>
                    <p>Цены ниже рыночных на 10-15%</p>
                </div>
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