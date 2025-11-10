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

        let CARDS_PER_PAGE = 8;
        let currentPage = 1;
        let totalPages = 1;
        let allBrandItems = document.querySelectorAll('.brand-item');
        let visibleBrandItems = [];

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
        let paginationElement = document.getElementById('pagination');
        let noResultsElement = document.getElementById('noResults');

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
            visibleBrandItems = [];
            
            brandItems.forEach(item => {
                let brandName = item.querySelector('.brand-name').textContent.toLowerCase();
                let brandDescription = item.querySelector('.brand-description').textContent.toLowerCase();
                let itemCategory = item.getAttribute('data-category');
                
                let categoryMatch = category === 'all' || itemCategory === category;
                let searchMatch = !searchText || brandName.includes(searchText) || brandDescription.includes(searchText);
                
                if (categoryMatch && searchMatch) 
                {
                    visibleBrandItems.push(item);
                }
            });
            
            currentPage = 1;
            updatePagination();
            showPage(currentPage);
            updateCategoryCounters(category, searchText);
            setTimeout(equalizeBrandCards, 350);
        }

        function updatePagination() 
        {
            totalPages = Math.ceil(visibleBrandItems.length / CARDS_PER_PAGE);
            paginationElement.innerHTML = '';
            
            if (visibleBrandItems.length <= CARDS_PER_PAGE) 
            {
                paginationElement.style.display = 'none';
            } 
            else 
            {
                paginationElement.style.display = 'flex';
            }

            if (visibleBrandItems.length === 0) 
            {
                noResultsElement.style.display = 'block';
            } 
            else 
            {
                noResultsElement.style.display = 'none';
            }
            
            if (totalPages > 1) 
            {
                let prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Предыдущая">‹</a>`;
                prevLi.addEventListener('click', (e) => {
                    e.preventDefault();

                    if (currentPage > 1) 
                    {
                        currentPage--;
                        showPage(currentPage);
                    }
                });
                paginationElement.appendChild(prevLi);

                for (let i = 1; i <= totalPages; i++) 
                {
                    let li = document.createElement('li');
                    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    li.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = i;
                        showPage(currentPage);
                    });
                    paginationElement.appendChild(li);
                }
                
                let nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Следующая">›</a>`;
                nextLi.addEventListener('click', (e) => {
                    e.preventDefault();

                    if (currentPage < totalPages) 
                    {
                        currentPage++;
                        showPage(currentPage);
                    }
                });
                paginationElement.appendChild(nextLi);
            }
        }
        
        function showPage(page) 
        {
            brandItems.forEach(item => {
                item.style.display = 'none';
                item.style.opacity = '0';
                item.style.transform = 'scale(0.8)';
            });
            
            let startIndex = (page - 1) * CARDS_PER_PAGE;
            let endIndex = startIndex + CARDS_PER_PAGE;
            
            visibleBrandItems.slice(startIndex, endIndex).forEach((item, index) => {
                setTimeout(() => {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 50);
                }, index * 50);
            });
            
            updatePagination();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            equalizeBrandCards();
        }

        function initializePage() 
        {
            initializeCounters();
            filterBrands('all', '');
            equalizeBrandCards();
        }

        window.addEventListener('load', initializePage);
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="aftermarket">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="aftermarket">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="aftermarket">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="aftermarket">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="russia">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="russia">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="russia">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="russia">
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
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Continental" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Continental</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Инновационные шины и автокомпоненты</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 800 товаров</span>
                            <span class="stat-item">Немецкое качество</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Mobil" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Mobil</h5>
                        <p class="brand-country">США</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Высококачественные моторные масла</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 400 товаров</span>
                            <span class="stat-item">Мировой лидер</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Hyundai Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Hyundai Original</h5>
                        <p class="brand-country">Корея</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти Hyundai</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 900 товаров</span>
                            <span class="stat-item">Официальный дилер</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Kia Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Kia Original</h5>
                        <p class="brand-country">Корея</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти Kia Motors</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 850 товаров</span>
                            <span class="stat-item">Гарантия качества</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="aftermarket">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Mahle" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Mahle</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category aftermarket">Аналоги</div>
                        <p class="brand-description">Качественные аналоги для всех марок</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 700 товаров</span>
                            <span class="stat-item">Немецкие стандарты</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="aftermarket">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Hella" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Hella</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category aftermarket">Аналоги</div>
                        <p class="brand-description">Осветительные приборы и электроника</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 600 товаров</span>
                            <span class="stat-item">Инновации</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="russia">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Кама" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Кама</h5>
                        <p class="brand-country">Россия</p>
                        <div class="brand-category russia">Российские</div>
                        <p class="brand-description">Российские шины премиум-класса</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 150 товаров</span>
                            <span class="stat-item">Адаптированы к дорогам</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="russia">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="СОАТЭ" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">СОАТЭ</h5>
                        <p class="brand-country">Россия</p>
                        <div class="brand-category russia">Российские</div>
                        <p class="brand-description">Автоэлектрика и компоненты</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 200 товаров</span>
                            <span class="stat-item">Надежность</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Valeo" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Valeo</h5>
                        <p class="brand-country">Франция</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Системы комфорта и безопасности</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 550 товаров</span>
                            <span class="stat-item">Французские технологии</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="ZF" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">ZF</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Трансмиссии и шасси</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 450 товаров</span>
                            <span class="stat-item">Инженерное превосходство</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Ford Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Ford Original</h5>
                        <p class="brand-country">США</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти Ford</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 1100 товаров</span>
                            <span class="stat-item">Американское качество</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Nissan Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Nissan Original</h5>
                        <p class="brand-country">Япония</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти Nissan</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 950 товаров</span>
                            <span class="stat-item">Японская надежность</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="aftermarket">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Bosch Car Service" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Bosch Car Service</h5>
                        <p class="brand-country">Германия</p>
                        <div class="brand-category aftermarket">Аналоги</div>
                        <p class="brand-description">Сервисные решения и компоненты</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 500 товаров</span>
                            <span class="stat-item">Профессиональные решения</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="aftermarket">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="NGK" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">NGK</h5>
                        <p class="brand-country">Япония</p>
                        <div class="brand-category aftermarket">Аналоги</div>
                        <p class="brand-description">Свечи зажигания и датчики</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 300 товаров</span>
                            <span class="stat-item">Технологии зажигания</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="russia">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="АВТОВАЗ" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">АВТОВАЗ</h5>
                        <p class="brand-country">Россия</p>
                        <div class="brand-category russia">Российские</div>
                        <p class="brand-description">Оригинальные запчасти LADA</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 2000 товаров</span>
                            <span class="stat-item">Официальный поставщик</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="russia">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="ОМНИ" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">ОМНИ</h5>
                        <p class="brand-country">Россия</p>
                        <div class="brand-category russia">Российские</div>
                        <p class="brand-description">Аккумуляторы и электрооборудование</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 180 товаров</span>
                            <span class="stat-item">Российское качество</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Michelin" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Michelin</h5>
                        <p class="brand-country">Франция</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Премиальные шины мирового уровня</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 250 товаров</span>
                            <span class="stat-item">Инновации в шинах</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="premium">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Bridgestone" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Bridgestone</h5>
                        <p class="brand-country">Япония</p>
                        <div class="brand-category premium">Премиум</div>
                        <p class="brand-description">Высокотехнологичные шины</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 230 товаров</span>
                            <span class="stat-item">Японские технологии</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Mazda Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Mazda Original</h5>
                        <p class="brand-country">Япония</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти Mazda</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 800 товаров</span>
                            <span class="stat-item">Технологии Zoom-Zoom</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="original">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="Honda Original" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name">Honda Original</h5>
                        <p class="brand-country">Япония</p>
                        <div class="brand-category original">Оригинальные</div>
                        <p class="brand-description">Оригинальные запчасти Honda</p>
                        <div class="brand-stats">
                            <span class="stat-item">Более 850 товаров</span>
                            <span class="stat-item">Надежность Honda</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pagination-section mt-5">
                <nav aria-label="Навигация по страницам">
                    <ul class="pagination justify-content-center" id="pagination">
                    </ul>
                </nav>
            </div>
            <div class="no-results text-center py-5" id="noResults" style="display: none;">
                <div class="no-results-icon mb-3">
                    <i class="bi bi-search display-1 text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">Бренды не найдены</h4>
                <p class="text-muted">Попробуйте изменить параметры поиска или фильтрации</p>
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