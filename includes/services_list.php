<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");
require_once("../config/check_auth.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$from_service_page = strpos($referer, 'service.php') !== false;
$selected_category = isset($_GET['category']) ? urldecode($_GET['category']) : '';

if (!empty($selected_category)) 
{
    $_SESSION['filtered_category'] = $selected_category;
    $_SESSION['came_from_service'] = $from_service_page;
}

$query = "SELECT * FROM services WHERE status = 'active'";

if (!empty($selected_category)) 
{
    $query .= " AND category = '" . $conn->real_escape_string($selected_category) . "'";
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
        $stmt = $conn->prepare("SELECT * FROM users WHERE LOWER(login_users) = LOWER(?)");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) 
        {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password_users'])) 
            {
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

$query .= " ORDER BY category, name";
$result = $conn->query($query);
$services = [];

if ($result->num_rows > 0) 
{
    while ($row = $result->fetch_assoc()) 
    {
        $services[] = $row;
    }
}

$grouped_services = [];

foreach ($services as $service) 
{
    $category = $service['category'] ?: 'Другие услуги';

    if (!isset($grouped_services[$category])) 
    {
        $grouped_services[$category] = [];
    }

    $grouped_services[$category][] = $service;
}

if (empty($selected_category)) 
{
    unset($_SESSION['filtered_category']);
    unset($_SESSION['came_from_service']);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все услуги - Автосервис Лал-Авто</title>
    <link rel="icon" href="../img/iconAuto.png" type="image/png">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/service-styles.css">
    <link rel="stylesheet" href="../css/services-list-styles.css">
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

<div class="services-header mb-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center" style="padding-top: 90px;">
            <div>
                <h1 class="display-5 fw-bold mb-3">Все услуги автосервиса</h1>
                <p class="lead mb-0">Профессиональное обслуживание и ремонт автомобилей любой сложности</p>
            </div>
            <a href="service.php" class="btn btn-primary btn-lg">
                <i class="bi bi-calendar-check me-2"></i>Записаться онлайн
            </a>
        </div>
    </div>
</div>
<div class="container mb-5">
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stats-card">
                <i class="bi bi-tools mb-3 fs-1"></i>
                <h3><?= count($services) ?></h3>
                <p>Всего услуг</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stats-card bg-primary">
                <i class="bi bi-folder2-open mb-3 fs-1"></i>
                <h3><?= count($grouped_services) ?></h3>
                <p>Категорий услуг</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <?php 
            $min_price = !empty($services) ? min(array_column($services, 'price')) : 0;
            $max_price = !empty($services) ? max(array_column($services, 'price')) : 0;
            ?>
            <div class="stats-card bg-primary">
                <i class="bi bi-cash-stack mb-3 fs-1"></i>
                <h3><?= number_format($min_price, 0, ',', ' ') ?> - <?= number_format($max_price, 0, ',', ' ') ?> ₽</h3>
                <p>Диапазон цен</p>
            </div>
        </div>
    </div>
    <div class="filter-section">
        <h5 class="mb-3">
            <i class="bi bi-funnel me-2 text-primary"></i>Фильтр по категориям
        </h5>
        <div class="d-flex flex-wrap">
            <span class="filter-badge <?php echo empty($selected_category) ? 'active' : ''; ?>" data-filter="all" data-from-service="<?php echo isset($_SESSION['came_from_service']) && $_SESSION['came_from_service'] ? 'true' : 'false'; ?>">
                Все услуги
            </span>
            <?php 
            foreach (array_keys($grouped_services) as $category)
            {
            ?>
                <span class="filter-badge <?php echo $selected_category == $category ? 'active' : ''; ?>" data-filter="<?= htmlspecialchars($category) ?>">
                    <?= htmlspecialchars($category) ?>
                </span>
            <?php
            }
            ?>
        </div>
    </div>
    <div id="services-list">
        <?php 
        foreach ($grouped_services as $category => $category_services)
        {
        ?>
            <div class="category-section" data-category="<?= htmlspecialchars($category) ?>">
                <h2 class="category-title">
                    <i class="bi bi-folder2-open me-2 text-primary"></i><?= htmlspecialchars($category) ?>
                    <span class="badge bg-primary ms-2"><?= count($category_services) ?></span>
                </h2>
                <div class="row g-4">
                    <?php 
                    foreach ($category_services as $service)
                    {
                    ?>
                        <div class="col-lg-6">
                            <div class="service-card" data-service-id="<?= $service['id'] ?>">
                                <div class="service-card-header">
                                    <div class="service-card-icon">
                                        <?php
                                        $icon = 'bi-gear-fill';

                                        switch (strtolower($service['category'])) 
                                        {
                                            case 'диагностика':
                                                $icon = 'bi-search';
                                                break;
                                            case 'двигатель':
                                            case 'ремонт двигателя':
                                                $icon = 'bi-lightning-charge';
                                                break;
                                            case 'тормозная система':
                                                $icon = 'bi-stop-circle';
                                                break;
                                            case 'ремонт ходовой':
                                            case 'ходовая часть':
                                                $icon = 'bi-car-front';
                                                break;
                                            case 'электрика':
                                                $icon = 'bi-cpu';
                                                break;
                                            case 'шиномонтаж':
                                                $icon = 'bi-circle';
                                                break;
                                            case 'детейлинг':
                                            case 'кузовные работы':
                                                $icon = 'bi-droplet';
                                                break;
                                            case 'кондиционер':
                                                $icon = 'bi-snow';
                                                break;
                                            default:
                                                $icon = 'bi-gear-fill';
                                        }
                                        ?>
                                        <i class="bi <?= $icon ?>"></i>
                                    </div>
                                    <div class="service-card-title">
                                        <h3><?= htmlspecialchars($service['name']) ?></h3>
                                        <span class="service-card-category">
                                            <i class="bi bi-tag"></i><?= htmlspecialchars($service['category'] ?: 'Другое') ?>
                                        </span>
                                    </div>
                                </div>
                                <?php 
                                if (!empty($service['description']))
                                {
                                ?>
                                    <div class="service-card-description">
                                        <?= htmlspecialchars($service['description']) ?>
                                    </div>
                                <?php 
                                }
                                ?>
                                <div class="service-card-footer">
                                    <div class="service-card-price">
                                        <?= number_format($service['price'], 0, ',', ' ') ?> ₽
                                        <?php 
                                        if ($service['duration'])
                                        {
                                        ?>
                                            <small>/услуга</small>
                                        <?php 
                                        }
                                        ?>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="service-card-duration me-3">
                                            <i class="bi bi-clock"></i><?= $service['duration'] ?> мин
                                        </span>
                                        <a href="service.php?service_id=<?= $service['id'] ?>&service_name=<?= urlencode($service['name']) ?>&service_price=<?= $service['price'] ?>&locked=1" class="btn btn-sm btn-primary">
                                            <i class="bi bi-calendar-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php 
                    }
                    ?>
                </div>
            </div>
        <?php 
        }
        ?>
    </div>
    <div class="service-advantages mt-5">
        <h2 class="text-center mb-4">Почему выбирают нас</h2>
        <div class="row g-3">
            <div class="col-lg-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h6>Сертифицированные мастера</h6>
                    <p class="small text-muted mb-0">Специалисты с опытом работы от 5 лет</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h6>Гарантия 12 месяцев</h6>
                    <p class="small text-muted mb-0">На все виды работ и запчасти</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h6>Прозрачные цены</h6>
                    <p class="small text-muted mb-0">Фиксированная стоимость без доплат</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="advantage-card text-center">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h6>Срочный ремонт</h6>
                    <p class="small text-muted mb-0">Выезд мастера в течение часа</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
function updateFiltersFromURL() 
{
    let urlParams = new URLSearchParams(window.location.search);
    let categoryFromUrl = urlParams.get('category');
    let filterBadges = document.querySelectorAll('.filter-badge');
    let categorySections = document.querySelectorAll('.category-section');
    
    filterBadges.forEach(badge => {
        badge.classList.remove('active');
    });
    
    if (categoryFromUrl) 
    {
        let categoryFound = false;

        filterBadges.forEach(badge => {
            let badgeFilter = decodeURIComponent(badge.dataset.filter);
            let urlCategory = decodeURIComponent(categoryFromUrl);

            if (badgeFilter === urlCategory) 
            {
                badge.classList.add('active');
                categoryFound = true;
            }
        });
        
        if (!categoryFound) 
        {
            let allBadge = document.querySelector('.filter-badge[data-filter="all"]');

            if (allBadge) 
            {
                allBadge.classList.add('active');
            }
        }
        
        categorySections.forEach(section => {
            if (categoryFromUrl && section.dataset.category === categoryFromUrl) 
            {
                section.style.display = 'block';
            } 
            else if (!categoryFromUrl) 
            {
                section.style.display = 'block';
            } 
            else 
            {
                section.style.display = 'none';
            }
        });
    } 
    else 
    {
        let allBadge = document.querySelector('.filter-badge[data-filter="all"]');

        if (allBadge) 
        {
            allBadge.classList.add('active');
        }

        categorySections.forEach(section => {
            section.style.display = 'block';
        });
    }
}

document.addEventListener('DOMContentLoaded', function() 
{
    let filterBadges = document.querySelectorAll('.filter-badge');
    let categorySections = document.querySelectorAll('.category-section');
    let urlParams = new URLSearchParams(window.location.search);
    let categoryFromUrl = urlParams.get('category');
    let cameFromService = false;
    
    filterBadges.forEach(badge => {
        if (badge.dataset.filter === 'all' && badge.dataset.fromService === 'true') 
        {
            cameFromService = true;
        }
    });
    
    updateFiltersFromURL();
    
    if (categoryFromUrl) 
    {
        setTimeout(() => {
            let categorySection = document.querySelector('.category-section');
            if (categorySection) {
                categorySection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
    
    filterBadges.forEach(badge => {
        badge.addEventListener('click', function() 
        {
            let filter = this.dataset.filter;
            let fromService = this.dataset.fromService === 'true';
            
            if (filter === 'all' && fromService) 
            {
                window.location.href = window.location.pathname;
                return;
            }
            
            let newUrl = new URL(window.location.href);

            if (filter === 'all') 
            {
                newUrl.searchParams.delete('category');
            } 
            else 
            {
                newUrl.searchParams.set('category', filter);
            }

            window.history.pushState({}, '', newUrl);
            filterBadges.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            categorySections.forEach(section => {
                if (filter === 'all' || section.dataset.category === filter) 
                {
                    section.style.display = 'block';
                    section.style.animation = 'fadeInUp 0.5s ease forwards';
                } 
                else 
                {
                    section.style.display = 'none';
                }
            });
        });
    });
    
    window.addEventListener('popstate', function() 
    {
        updateFiltersFromURL();
        let newUrlParams = new URLSearchParams(window.location.search);
        let newCategory = newUrlParams.get('category');
        let filterBadgesLocal = document.querySelectorAll('.filter-badge');
        let categorySectionsLocal = document.querySelectorAll('.category-section');
        
        if (newCategory) 
        {
            filterBadgesLocal.forEach(badge => {
                if (badge.dataset.filter === newCategory) 
                {
                    badge.classList.add('active');
                } 
                else 
                {
                    badge.classList.remove('active');
                }
            });
            categorySectionsLocal.forEach(section => {
                if (section.dataset.category === newCategory) 
                {
                    section.style.display = 'block';
                } 
                else 
                {
                    section.style.display = 'none';
                }
            });
        } 
        else 
        {
            filterBadgesLocal.forEach(badge => {
                if (badge.dataset.filter === 'all') 
                {
                    badge.classList.add('active');
                } 
                else 
                {
                    badge.classList.remove('active');
                }
            });

            categorySectionsLocal.forEach(section => {
                section.style.display = 'block';
            });
        }
    });

    let urlParamsForService = new URLSearchParams(window.location.search);
    let serviceId = urlParamsForService.get('service');
    
    if (serviceId) 
    {
        let serviceCard = document.querySelector(`[data-service-id="${serviceId}"]`);

        if (serviceCard) 
        {
            setTimeout(() => {
                serviceCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                serviceCard.classList.add('border-primary');
                serviceCard.style.boxShadow = '0 0 0 3px rgba(0,123,255,0.25)';
                
                setTimeout(() => {
                    serviceCard.classList.remove('border-primary');
                    serviceCard.style.boxShadow = '';
                }, 3000);
            }, 500);
        }
    }

    function equalizeCardHeights() 
    {
        let cards = document.querySelectorAll('.service-card');
        cards.forEach(card => card.style.height = 'auto');
        let rows = document.querySelectorAll('.row.g-4');

        rows.forEach(row => {
            let cardsInRow = row.querySelectorAll('.col-lg-6');

            if (cardsInRow.length > 1) 
            {
                let maxHeight = 0;

                cardsInRow.forEach(col => {
                    let card = col.querySelector('.service-card');

                    if (card) 
                    {
                        maxHeight = Math.max(maxHeight, card.offsetHeight);
                    }
                });
                cardsInRow.forEach(col => {
                    let card = col.querySelector('.service-card');

                    if (card) 
                    {
                        card.style.height = maxHeight + 'px';
                    }
                });
            }
        });
    }

    window.addEventListener('load', equalizeCardHeights);
    window.addEventListener('resize', equalizeCardHeights);
});
</script>
</body>
</html>