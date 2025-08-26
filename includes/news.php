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
    <title>Новости компании - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/news-styles.css">
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

<main class="news-page">
    <section class="news-hero py-5" style="margin-top: 105px;">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="display-4 fw-bold mb-4">Новости компании</h1>
                    <p class="lead">Будьте в курсе последних событий и акций нашего автоцентра</p>
                </div>
            </div>
        </div>
    </section>
    <section class="news-list py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <div class="news-img-container">
                            <img src="../img/no-image.png" class="news-img" alt="Открытие нового магазина">
                            <div class="news-badge">Новое</div>
                        </div>
                        <div class="news-body">
                            <div class="news-date">
                                <i class="bi bi-calendar"></i> 15 мая 2025
                            </div>
                            <h3 class="news-title">Открытие нового магазина на Московском проспекте</h3>
                            <p class="news-excerpt">Мы рады сообщить об открытии нашего нового магазина автозапчастей в центре Калининграда. Теперь у нас еще больше товаров и удобное расположение.</p>
                            <a href="news-single.php?id=1" class="read-more">
                                Читать далее <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <div class="news-img-container">
                            <img src="../img/no-image.png" class="news-img" alt="Акция на масла">
                            <div class="news-badge news-badge-sale">Акция</div>
                        </div>
                        <div class="news-body">
                            <div class="news-date">
                                <i class="bi bi-calendar"></i> 3 мая 2025
                            </div>
                            <h3 class="news-title">Специальная акция на моторные масла до конца месяца</h3>
                            <p class="news-excerpt">Только в мае скидка 15% на все моторные масла Castrol и Mobil. Акция действует при покупке от 5 литров.</p>
                            <a href="news-single.php?id=2" class="read-more">
                                Читать далее <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="news-card">
                        <div class="news-img-container">
                            <img src="../img/no-image.png" class="news-img" alt="Новые поставки">
                        </div>
                        <div class="news-body">
                            <div class="news-date">
                                <i class="bi bi-calendar"></i> 28 апреля 2025
                            </div>
                            <h3 class="news-title">Поступили новые запчасти для японских автомобилей</h3>
                            <p class="news-excerpt">В нашем ассортименте появились оригинальные запчасти для Toyota, Honda и Nissan. Гарантия качества и доступные цены.</p>
                            <a href="news-single.php?id=3" class="read-more">
                                Читать далее <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </section>
</main>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>