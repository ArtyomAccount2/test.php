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

        let shopCards = document.querySelectorAll('.shop-card');

        shopCards.forEach(card => {
            card.addEventListener('mouseenter', function() 
            {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            card.addEventListener('mouseleave', function() 
            {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        let filterButtons = document.querySelectorAll('.filter-btn');
        let shopRows = document.querySelectorAll('.shop-row');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() 
            {
                let filter = this.getAttribute('data-filter');
                
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                shopRows.forEach(row => {
                    if (filter === 'all' || row.getAttribute('data-services').includes(filter)) 
                    {
                        row.style.display = '';
                        setTimeout(() => row.style.opacity = '1', 50);
                    } 
                    else 
                    {
                        row.style.opacity = '0';
                        setTimeout(() => row.style.display = 'none', 300);
                    }
                });
            });
        });
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5">
    <div class="hero-section text-center mb-5" style="padding-top: 85px;">
        <h1 class="display-4 fw-bold text-primary mb-3">Наши магазины</h1>
        <p class="lead text-muted">Сеть автомагазинов Лал-Авто в Калининграде и области</p>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="card shop-card featured-shop h-100">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Центральный магазин</h3>
                    <span class="badge bg-warning">Флагманский</span>
                </div>
                <div class="card-body">
                    <div class="shop-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-geo-alt-fill fs-5 text-primary me-3"></i>
                            <div>
                                <strong>г. Калининград, ул. Автомобильная, 12</strong>
                                <div class="text-muted small">Район: Центральный</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-clock-fill fs-5 text-primary me-3"></i>
                            <div>
                                <strong>Пн-Пт: 9:00-20:00</strong><br>
                                <strong>Сб-Вс: 10:00-18:00</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-telephone-fill fs-5 text-primary me-3"></i>
                            <div>
                                <strong>+7 (4012) 65-65-65</strong><br>
                                <span class="text-muted small">Многоканальный</span>
                            </div>
                        </div>
                        <div class="services-tags mt-3">
                            <span class="badge bg-light text-dark me-1">Автосервис</span>
                            <span class="badge bg-light text-dark me-1">Шиномонтаж</span>
                            <span class="badge bg-light text-dark me-1">Автохимия</span>
                            <span class="badge bg-light text-dark">Тюнинг</span>
                        </div>
                    </div>
                    <div class="shop-map">
                        <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A1234567890abcdef&amp;source=constructor" 
                                width="100%" height="300" frameborder="0" style="border-radius: 8px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shop-card h-100">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Магазин на Московском</h3>
                </div>
                <div class="card-body">
                    <div class="shop-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-geo-alt-fill fs-5 text-primary me-3"></i>
                            <div>
                                <strong>г. Калининград, Московский пр-т, 45</strong>
                                <div class="text-muted small">Район: Московский</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-clock-fill fs-5 text-primary me-3"></i>
                            <div>
                                <strong>Пн-Пт: 9:00-20:00</strong><br>
                                <strong>Сб-Вс: 10:00-18:00</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-telephone-fill fs-5 text-primary me-3"></i>
                            <div>
                                <strong>+7 (4012) 76-76-76</strong><br>
                                <span class="text-muted small">Многоканальный</span>
                            </div>
                        </div>
                        <div class="services-tags mt-3">
                            <span class="badge bg-light text-dark me-1">Автосервис</span>
                            <span class="badge bg-light text-dark me-1">Шиномонтаж</span>
                            <span class="badge bg-light text-dark">Автохимия</span>
                        </div>
                    </div>
                    <div class="shop-map">
                        <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A1234567890abcdef&amp;source=constructor" width="100%" height="300" frameborder="0" style="border-radius: 8px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="filters-section mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <button class="btn btn-outline-primary filter-btn active" data-filter="all">Все магазины</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="service">С автосервисом</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="tire">С шиномонтажем</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="chemistry">Автохимия</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="tuning">Тюнинг</button>
        </div>
    </div>
    <div class="additional-shops mb-5">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th><i class="bi bi-shop"></i> Магазин</th>
                        <th><i class="bi bi-geo-alt"></i> Адрес</th>
                        <th><i class="bi bi-telephone"></i> Телефон</th>
                        <th><i class="bi bi-clock"></i> Режим работы</th>
                        <th><i class="bi bi-tools"></i> Услуги</th>
                        <th><i class="bi bi-info-circle"></i> Дополнительно</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="shop-row" data-services="service,tire,chemistry,tuning">
                        <td>
                            <strong>Центральный</strong>
                            <div class="text-muted small">Флагманский</div>
                        </td>
                        <td>ул. Автомобильная, 12</td>
                        <td>+7 (4012) 65-65-65</td>
                        <td>Пн-Пт: 9:00-20:00<br>Сб-Вс: 10:00-18:00</td>
                        <td>
                            <span class="badge bg-primary me-1">Запчасти</span>
                            <span class="badge bg-success me-1">Сервис</span>
                            <span class="badge bg-info me-1">Шины</span>
                            <span class="badge bg-warning">Тюнинг</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Есть парковка</span>
                        </td>
                    </tr>
                    <tr class="shop-row" data-services="service,tire,chemistry">
                        <td>
                            <strong>Московский</strong>
                            <div class="text-muted small">Крупный</div>
                        </td>
                        <td>Московский пр-т, 45</td>
                        <td>+7 (4012) 76-76-76</td>
                        <td>Пн-Пт: 9:00-20:00<br>Сб-Вс: 10:00-18:00</td>
                        <td>
                            <span class="badge bg-primary me-1">Запчасти</span>
                            <span class="badge bg-success me-1">Сервис</span>
                            <span class="badge bg-info">Шины</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Есть парковка</span>
                        </td>
                    </tr>
                    <tr class="shop-row" data-services="service,tire">
                        <td>
                            <strong>Горького</strong>
                            <div class="text-muted small">Стандарт</div>
                        </td>
                        <td>ул. Горького, 15</td>
                        <td>+7 (4012) 87-87-87</td>
                        <td>Пн-Пт: 9:00-20:00<br>Сб-Вс: 10:00-18:00</td>
                        <td>
                            <span class="badge bg-primary me-1">Запчасти</span>
                            <span class="badge bg-info me-1">Шины</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">Ограниченная парковка</span>
                        </td>
                    </tr>
                    <tr class="shop-row" data-services="chemistry">
                        <td>
                            <strong>Приморский</strong>
                            <div class="text-muted small">Стандарт</div>
                        </td>
                        <td>ул. Приморская, 8</td>
                        <td>+7 (4012) 98-98-98</td>
                        <td>Пн-Пт: 9:00-20:00<br>Сб-Вс: 10:00-18:00</td>
                        <td>
                            <span class="badge bg-primary me-1">Запчасти</span>
                            <span class="badge bg-warning">Химия</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Есть парковка</span>
                        </td>
                    </tr>
                    <tr class="shop-row" data-services="service,tire,chemistry,tuning">
                        <td>
                            <strong>Советский</strong>
                            <div class="text-muted small">Крупный</div>
                        </td>
                        <td>Советский пр-т, 120</td>
                        <td>+7 (4012) 54-32-10</td>
                        <td>Пн-Пт: 8:00-19:00<br>Сб-Вс: 9:00-17:00</td>
                        <td>
                            <span class="badge bg-primary me-1">Запчасти</span>
                            <span class="badge bg-success me-1">Сервис</span>
                            <span class="badge bg-info me-1">Шины</span>
                            <span class="badge bg-warning">Тюнинг</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Есть парковка</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="features-section bg-light rounded-3 p-5 mb-5">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="feature-icon mb-3">
                    <i class="bi bi-truck fs-1 text-primary"></i>
                </div>
                <h5>Бесплатная доставка</h5>
                <p class="text-muted">При заказе от 3000₽ в пределах города</p>
            </div>
            <div class="col-md-3">
                <div class="feature-icon mb-3">
                    <i class="bi bi-shield-check fs-1 text-primary"></i>
                </div>
                <h5>Гарантия качества</h5>
                <p class="text-muted">На все товары и услуги</p>
            </div>
            <div class="col-md-3">
                <div class="feature-icon mb-3">
                    <i class="bi bi-arrow-repeat fs-1 text-primary"></i>
                </div>
                <h5>Легкий возврат</h5>
                <p class="text-muted">В течение 14 дней</p>
            </div>
            <div class="col-md-3">
                <div class="feature-icon mb-3">
                    <i class="bi bi-headset fs-1 text-primary"></i>
                </div>
                <h5>Поддержка 24/7</h5>
                <p class="text-muted">Консультации по телефону</p>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
</body>
</html>