<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");
require_once("../config/check_auth.php");

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
        $_SESSION['login_error'] = true;
        $_SESSION['error_message'] = "Неверный логин или пароль!";
        header("Location: " . $_SERVER['REQUEST_URI']);
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
            $_SESSION['user_id'] = $row['id_users'];
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

$featured_shop_query = "SELECT * FROM shops WHERE featured = 1 ORDER BY id LIMIT 2";
$featured_result = $conn->query($featured_shop_query);
$featured_shops = [];

while ($row = $featured_result->fetch_assoc()) 
{
    $featured_shops[] = $row;
}

if (count($featured_shops) < 2) 
{
    $main_shops_query = "SELECT * FROM shops WHERE type = 'main' ORDER BY id LIMIT 2";
    $main_result = $conn->query($main_shops_query);
    $featured_shops = [];

    while ($row = $main_result->fetch_assoc()) 
    {
        $featured_shops[] = $row;
    }
}

$all_shops_query = "SELECT * FROM shops ORDER BY CASE WHEN type = 'main' THEN 1 WHEN featured = 1 THEN 2 ELSE 3 END, id";
$all_shops_result = $conn->query($all_shops_query);
$all_shops = [];

while ($row = $all_shops_result->fetch_assoc()) 
{
    $all_shops[] = $row;
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

function getServicesArray($services_string) 
{
    if (empty($services_string)) 
    {
        return [];
    }

    return explode(',', $services_string);
}

function getServiceBadgeClass($service) 
{
    $classes = [
        'Запчасти' => 'bg-primary',
        'Сервис' => 'bg-success',
        'Шины' => 'bg-info',
        'Тюнинг' => 'bg-warning',
        'Химия' => 'bg-warning',
        'Автохимия' => 'bg-warning'
    ];

    return $classes[$service] ?? 'bg-secondary';
}

function getParkingBadgeClass($parking) 
{
    return $parking == 'Есть парковка' ? 'bg-success' : 'bg-secondary';
}

function formatSchedule($schedule) 
{
    if (empty($schedule)) 
    {
        return 'Пн-Пт: 9:00-20:00<br>Сб-Вс: 10:00-18:00';
    }

    return str_replace(';', '<br>', $schedule);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазины - Лал-Авто</title>
    <link rel="icon" href="../img/iconAuto.png" type="image/png" height="32">
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

        function generateMobileCards() 
        {
            let container = document.getElementById('mobile-shops-container');
            let rows = document.querySelectorAll('.desktop-row');
            
            container.innerHTML = '';
            
            rows.forEach((row, index) => {
                let cells = row.querySelectorAll('td');
                let services = row.getAttribute('data-services');
                
                let card = document.createElement('div');
                card.className = 'col-12 mobile-row';
                card.setAttribute('data-services', services);
                
                card.innerHTML = `
                    <div class="card shop-card-mobile h-100">
                        <div class="card-header">
                            <h6 class="text-white mb-0">${cells[0].innerHTML}</h6>
                        </div>
                        <div class="card-body">
                            <div class="shop-info-mobile">
                                <div class="info-row">
                                    <i class="bi bi-geo-alt-fill info-icon"></i>
                                    <div class="info-content">
                                        <strong>Адрес:</strong><br>
                                        ${cells[1].textContent}
                                    </div>
                                </div>
                                <div class="info-row">
                                    <i class="bi bi-telephone-fill info-icon"></i>
                                    <div class="info-content">
                                        <strong>Телефон:</strong><br>
                                        ${cells[2].textContent}
                                    </div>
                                </div>
                                <div class="info-row">
                                    <i class="bi bi-clock-fill info-icon"></i>
                                    <div class="info-content">
                                        <strong>Режим работы:</strong><br>
                                        ${cells[3].innerHTML.replace(/<br>/g, ', ')}
                                    </div>
                                </div>
                                <div class="info-row">
                                    <i class="bi bi-tools info-icon"></i>
                                    <div class="info-content">
                                        <strong>Услуги:</strong><br>
                                        <div class="mt-1">${cells[4].innerHTML}</div>
                                    </div>
                                </div>
                                <div class="info-row">
                                    <i class="bi bi-info-circle info-icon"></i>
                                    <div class="info-content">
                                        <strong>Дополнительно:</strong><br>
                                        <div class="mt-1">${cells[5].innerHTML}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                container.appendChild(card);
            });
        }

        generateMobileCards();

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
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() 
            {
                let filter = this.getAttribute('data-filter');

                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                document.querySelectorAll('.desktop-row').forEach(row => {
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

                document.querySelectorAll('.mobile-row').forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-services').includes(filter)) 
                    {
                        card.style.display = 'block';
                        setTimeout(() => card.style.opacity = '1', 50);
                    }
                    else 
                    {
                        card.style.opacity = '0';
                        setTimeout(() => card.style.display = 'none', 300);
                    }
                });
            });
        });

        window.addEventListener('resize', function() 
        {
            generateMobileCards();
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
        <?php 
        $counter = 0;

        foreach ($featured_shops as $shop)
        {
            $counter++;
            $services = getServicesArray($shop['services']);
            $is_first = ($counter == 1);
        ?>
        <div class="col-lg-6">
            <div class="card shop-card <?= $is_first ? 'featured-shop' : '' ?> h-100">
                <div class="card-header <?= $is_first ? 'bg-gradient-primary' : 'bg-primary' ?> text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><?= htmlspecialchars($shop['name']) ?></h3>
                    <?php 
                    if ($is_first)
                    {
                    ?>
                        <span class="badge bg-warning">Флагманский</span>
                    <?php
                    }
                    ?>
                </div>
                <div class="card-body">
                    <div class="shop-info mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-geo-alt-fill fs-5 text-primary me-3"></i>
                            <div>
                                <strong><?= htmlspecialchars($shop['address']) ?></strong>
                                <div class="text-muted small">Район: <?= htmlspecialchars($shop['region']) ?></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-clock-fill fs-5 text-primary me-3"></i>
                            <div>
                                <?= formatSchedule($shop['schedule']) ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-telephone-fill fs-5 text-primary me-3"></i>
                            <div>
                                <strong><?= htmlspecialchars($shop['phone']) ?></strong><br>
                                <span class="text-muted small"><?= $shop['email'] ? htmlspecialchars($shop['email']) : 'Многоканальный' ?></span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-people-fill fs-5 text-primary me-3"></i>
                            <strong>Количество сотрудников: <?= htmlspecialchars($shop['employees']) ?></strong>
                        </div>
                        <?php 
                        if (!empty($services))
                        {
                        ?>
                        <div class="services-tags mt-3">
                            <?php 
                            foreach ($services as $service)
                            {
                            ?>
                                <span class="badge bg-light text-dark me-1"><?= htmlspecialchars(trim($service)) ?></span>
                            <?php 
                            }
                            ?>
                        </div>
                        <?php 
                        }
                        ?>
                    </div>
                    <div class="shop-map">
                        <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A1234567890abcdef&amp;source=constructor" width="100%" height="300" frameborder="0" style="border-radius: 8px;"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        }
        ?>
    </div>
    <div class="filters-section mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <button class="btn btn-outline-primary filter-btn active" data-filter="all">Все магазины</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="Сервис">С автосервисом</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="Шины">С шиномонтажем</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="Химия">Автохимия</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="Тюнинг">Тюнинг</button>
        </div>
    </div>
    <div class="additional-shops mb-5">
        <div class="desktop-table">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="bi bi-shop"></i> Магазин</th>
                            <th><i class="bi bi-geo-alt"></i> Адрес</th>
                            <th><i class="bi bi-telephone"></i> Телефон</th>
                            <th><i class="bi bi-clock"></i> Режим работы</th>
                            <th><i class="bi bi-tools"></i> Площадь</th>
                            <th><i class="bi bi-tools"></i> Услуги</th>
                            <th><i class="bi bi-info-circle"></i> Дополнительно</th>
                        </tr>
                    </thead>
                    <tbody id="desktop-shops-body">
                        <?php 
                        foreach ($all_shops as $shop)
                        {
                            $services = getServicesArray($shop['services']);
                            $services_lower = array_map('strtolower', $services);
                            $data_services = implode(',', $services_lower);
                            $shop_type_label = '';

                            if ($shop['type'] == 'main') 
                            {
                                $shop_type_label = 'Флагманский';
                            } 
                            else if ($shop['featured'] == 1) 
                            {
                                $shop_type_label = 'Крупный';
                            } 
                            else 
                            {
                                $shop_type_label = 'Стандарт';
                            }
                        ?>
                        <tr class="shop-row desktop-row" data-services="<?= htmlspecialchars($data_services) ?>">
                            <td>
                                <strong><?= htmlspecialchars($shop['name']) ?></strong>
                                <div class="text-secondary small"><?= $shop_type_label ?></div>
                            </td>
                            <td><?= htmlspecialchars($shop['address']) ?></td>
                            <td><?= htmlspecialchars($shop['phone']) ?></td>
                            <td><?= formatSchedule($shop['schedule']) ?></td>
                            <td><?= formatSchedule($shop['area']) ?> м²</td>
                            <td>
                                <?php 
                                foreach ($services as $service)
                                {
                                ?>
                                    <span class="badge <?= getServiceBadgeClass(trim($service)) ?> me-1"><?= htmlspecialchars(trim($service)) ?></span>
                                <?php 
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if (!empty($shop['parking']))
                                {
                                ?>
                                    <span class="badge <?= getParkingBadgeClass($shop['parking']) ?>"><?= htmlspecialchars($shop['parking']) ?></span>
                                <?php 
                                }
                                ?>
                            </td>
                        </tr>
                        <?php 
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mobile-cards">
            <div class="row g-3" id="mobile-shops-container"></div>
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