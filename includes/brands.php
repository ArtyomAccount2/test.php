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

        function equalizeBrandCards() 
        {
            let brandCards = document.querySelectorAll('.brand-card');
            let maxHeight = 0;

            brandCards.forEach(card => {
                card.style.height = 'auto';
            });

            brandCards.forEach(card => {
                if (card.offsetHeight > maxHeight) 
                {
                    maxHeight = card.offsetHeight;
                }
            });
            
            brandCards.forEach(card => {
                if (card.offsetParent !== null)
                {
                    card.style.height = maxHeight + 'px';
                }
            });
        }

        let resizeTimeout;
        
        window.addEventListener('resize', function() 
        {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(equalizeBrandCards, 250);
        });

        let filterButtons = document.querySelectorAll('.filter-btn');
        let brandItems = document.querySelectorAll('.brand-item');
        let searchInput = document.getElementById('brandSearch');
        let searchClear = document.querySelector('.search-clear');

        function initializeCounters() 
        {
            updateCategoryCounters('all', '');
        }
        
        function updateCategoryCounters(activeFilter, searchText) 
        {
            let categoryCounts = {
                'all': 0,
                'premium': 0,
                'original': 0,
                'aftermarket': 0,
                'russia': 0
            };

            brandItems.forEach(item => {
                let brandName = item.querySelector('.brand-name').textContent.toLowerCase();
                let brandDescription = item.querySelector('.brand-description').textContent.toLowerCase();
                let itemCategory = item.getAttribute('data-category');
                
                let searchMatch = !searchText || brandName.includes(searchText) || brandDescription.includes(searchText);
                
                if (searchMatch) 
                {
                    categoryCounts[itemCategory]++;
                    categoryCounts.all++;
                }
            });

            filterButtons.forEach(button => {
                let filter = button.getAttribute('data-filter');
                let badge = button.querySelector('.badge');

                if (badge) 
                {
                    badge.textContent = categoryCounts[filter];
                }
            });
        }
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() 
            {
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                let filter = this.getAttribute('data-filter');
                filterBrands(filter, searchInput.value.toLowerCase());
            });
        });
        
        searchInput.addEventListener('input', function() 
        {
            let searchText = this.value.toLowerCase();
            searchClear.style.display = searchText ? 'block' : 'none';
            
            let activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
            filterBrands(activeFilter, searchText);
        });
        
        searchClear.addEventListener('click', function() 
        {
            searchInput.value = '';
            searchClear.style.display = 'none';
            let activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
            filterBrands(activeFilter, '');
        });
        
        function filterBrands(category, searchText) 
        {
            let visibleCount = 0;
            
            brandItems.forEach(item => {
                let brandName = item.querySelector('.brand-name').textContent.toLowerCase();
                let brandDescription = item.querySelector('.brand-description').textContent.toLowerCase();
                let itemCategory = item.getAttribute('data-category');
                
                let categoryMatch = category === 'all' || itemCategory === category;
                let searchMatch = !searchText || brandName.includes(searchText) || brandDescription.includes(searchText);
                
                if (categoryMatch && searchMatch) 
                {
                    item.style.display = 'block';
                    visibleCount++;
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 50);
                } 
                else 
                {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.style.display = 'none';
                    }, 300);
                }
            });
            
            updateCategoryCounters(category, searchText);
            setTimeout(equalizeBrandCards, 350);
        }

        window.addEventListener('load', function() 
        {
            initializeCounters();
            equalizeBrandCards();
        });

        window.addEventListener('resize', equalizeBrandCards);
        setTimeout(equalizeBrandCards, 100);
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-4">
    <div class="hero-section text-center mb-4" style="padding-top: 105px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Торговые марки</h1>
        <p class="lead text-muted mb-3">Официальный дилер ведущих мировых производителей автозапчастей</p>
        <div class="stats-row d-flex justify-content-center gap-4 flex-wrap mb-4">
            <div class="stat-item">
                <div class="stat-number text-primary fw-bold fs-3">20+</div>
                <div class="stat-label text-muted">брендов</div>
            </div>
            <div class="stat-item">
                <div class="stat-number text-primary fw-bold fs-3">10 000+</div>
                <div class="stat-label text-muted">товаров</div>
            </div>
            <div class="stat-item">
                <div class="stat-number text-primary fw-bold fs-3">15 лет</div>
                <div class="stat-label text-muted">на рынке</div>
            </div>
        </div>
    </div>
    <div class="search-section mb-4">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="search-container position-relative">
                    <input type="text" id="brandSearch" placeholder="Поиск по брендам..." class="form-control search-input">
                    <button class="btn btn-link search-clear" type="button" style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 5;">
                        <i class="bi bi-x"></i>
                    </button>
                    <i class="bi bi-search search-icon"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="filters-section mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <button class="btn btn-outline-primary filter-btn active" data-filter="all">Все <span class="badge bg-primary ms-1">20</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="premium">Премиум <span class="badge bg-primary ms-1">4</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="original">Оригинальные <span class="badge bg-primary ms-1">4</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="aftermarket">Аналоги <span class="badge bg-primary ms-1">4</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="russia">Российские <span class="badge bg-primary ms-1">4</span></button>
        </div>
    </div>
    <div class="brands-grid-section mb-5">
        <div class="row g-3 brands-grid">
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Bosch" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Bosch</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Мировой лидер в производстве автокомпонентов и систем</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 1000 товаров</span>
                            <span class="stat-item">Гарантия 2 года</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Castrol" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Castrol</h5>
                        <p class="brand-country">Великобритания</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Ведущий производитель моторных масел и смазочных материалов</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 500 товаров</span>
                            <span class="stat-item">Одобрено OEM</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Mann-Filter" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Mann-Filter</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Эксперты в области фильтрации для автомобильной промышленности</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 300 товаров</span>
                            <span class="stat-item">ОЕМ поставщик</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Brembo" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Brembo</h5>
                        <p class="brand-country">Италия</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Мировой лидер в производстве тормозных систем</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 400 товаров</span>
                            <span class="stat-item">Спорт-кар качество</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Volkswagen Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">VW Original</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти Volkswagen Group</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 2000 товаров</span>
                            <span class="stat-item">Официальный дилер</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Toyota Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Toyota Original</h5>
                        <p class="brand-country">Япония</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти Toyota Motor Corporation</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 1500 товаров</span>
                            <span class="stat-item">Гарантия качества</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="BMW Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">BMW Original</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти BMW Group</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 1200 товаров</span>
                            <span class="stat-item">Сертифицировано</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Mercedes-Benz Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Mercedes Original</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти Mercedes-Benz</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 1100 товаров</span>
                            <span class="stat-item">Премиум качество</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="aftermarket">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Febi Bilstein" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Febi Bilstein</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category aftermarket">Аналоги</div>
                        <p class="brand-description">Качественные аналоги европейских автомобилей</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 800 товаров</span>
                            <span class="stat-item">Соответствие OE</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="aftermarket">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Blue Print" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Blue Print</h5>
                        <p class="brand-country">Япония</p>
                        <div class="brand-category aftermarket">Аналоги</div>
                        <p class="brand-description">Высококачественные аналоги для азиатских авто</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 600 товаров</span>
                            <span class="stat-item">Японское качество</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="aftermarket">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="SWAG" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">SWAG</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category aftermarket">Аналоги</div>
                        <p class="brand-description">Немецкое качество по доступным ценам</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 500 товаров</span>
                            <span class="stat-item">Германские стандарты</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="aftermarket">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Mapco" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Mapco</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category aftermarket">Аналоги</div>
                        <p class="brand-description">Надежные аналоги для европейских автомобилей</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 400 товаров</span>
                            <span class="stat-item">Проверенное качество</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="russia">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Трек" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Трек</h5>
                        <p class="brand-country">Россия</p>
                        <div class="brand-category russia">Российские</div>
                        <p class="brand-description">Ведущий российский производитель фильтров</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 200 товаров</span>
                            <span class="stat-item">Лучшая цена</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="russia">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="СтартВОЛЬТ" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">СтартВОЛЬТ</h5>
                        <p class="brand-country">Россия</p>
                        <div class="brand-category russia">Российские</div>
                        <p class="brand-description">Российские аккумуляторы премиум-класса</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 50 товаров</span>
                            <span class="stat-item">Адаптированы к климату</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="russia">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="FENOX" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">FENOX</h5>
                        <p class="brand-country">Беларусь/Россия</p>
                        <div class="brand-category russia">Российские</div>
                        <p class="brand-description">Качественные автокомпоненты для СНГ рынка</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 300 товаров</span>
                            <span class="stat-item">Оптимальное соотношение</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 brand-item" data-category="russia">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="БелМаг" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">БелМаг</h5>
                        <p class="brand-country">Россия</p>
                        <div class="brand-category russia">Российские</div>
                        <p class="brand-description">Магнитолы и аудиосистемы российского производства</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 100 товаров</span>
                            <span class="stat-item">Лучшая цена</span>
                        </div>
                    </div>
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
</body>
</html>