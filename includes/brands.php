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

$brands = [
    [
        'name' => 'Bosch',
        'country' => 'Германия',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Мировой лидер в производстве автокомпонентов и систем',
        'stats' => ['Более 1000 товаров', 'Гарантия 2 года']
    ],
    [
        'name' => 'Castrol',
        'country' => 'Великобритания',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Ведущий производитель моторных масел и смазочных материалов',
        'stats' => ['Более 500 товаров', 'Одобрено OEM']
    ],
    [
        'name' => 'Mann-Filter',
        'country' => 'Германия',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Эксперты в области фильтрации для автомобильной промышленности',
        'stats' => ['Более 300 товаров', 'ОЕМ поставщик']
    ],
    [
        'name' => 'Brembo',
        'country' => 'Италия',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Мировой лидер в производстве тормозных систем',
        'stats' => ['Более 400 товаров', 'Спорт-кар качество']
    ],
    [
        'name' => 'Continental',
        'country' => 'Германия',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Инновационные шины и автокомпоненты',
        'stats' => ['Более 800 товаров', 'Немецкое качество']
    ],
    [
        'name' => 'Mobil',
        'country' => 'США',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Высококачественные моторные масла',
        'stats' => ['Более 400 товаров', 'Мировой лидер']
    ],
    [
        'name' => 'Valeo',
        'country' => 'Франция',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Системы комфорта и безопасности',
        'stats' => ['Более 550 товаров', 'Французские технологии']
    ],
    [
        'name' => 'ZF',
        'country' => 'Германия',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Трансмиссии и шасси',
        'stats' => ['Более 450 товаров', 'Инженерное превосходство']
    ],
    [
        'name' => 'Michelin',
        'country' => 'Франция',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Премиальные шины мирового уровня',
        'stats' => ['Более 250 товаров', 'Инновации в шинах']
    ],
    [
        'name' => 'Bridgestone',
        'country' => 'Япония',
        'category' => 'premium',
        'category_name' => 'Премиум',
        'description' => 'Высокотехнологичные шины',
        'stats' => ['Более 230 товаров', 'Японские технологии']
    ],

    [
        'name' => 'VW Original',
        'country' => 'Германия',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти Volkswagen Group',
        'stats' => ['Более 2000 товаров', 'Официальный дилер']
    ],
    [
        'name' => 'Toyota Original',
        'country' => 'Япония',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти Toyota Motor Corporation',
        'stats' => ['Более 1500 товаров', 'Гарантия качества']
    ],
    [
        'name' => 'BMW Original',
        'country' => 'Германия',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти BMW Group',
        'stats' => ['Более 1200 товаров', 'Сертифицировано']
    ],
    [
        'name' => 'Mercedes Original',
        'country' => 'Германия',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти Mercedes-Benz',
        'stats' => ['Более 1100 товаров', 'Премиум качество']
    ],
    [
        'name' => 'Hyundai Original',
        'country' => 'Корея',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти Hyundai',
        'stats' => ['Более 900 товаров', 'Официальный дилер']
    ],
    [
        'name' => 'Kia Original',
        'country' => 'Корея',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти Kia Motors',
        'stats' => ['Более 850 товаров', 'Гарантия качества']
    ],
    [
        'name' => 'Ford Original',
        'country' => 'США',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти Ford',
        'stats' => ['Более 1100 товаров', 'Американское качество']
    ],
    [
        'name' => 'Nissan Original',
        'country' => 'Япония',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти Nissan',
        'stats' => ['Более 950 товаров', 'Японская надежность']
    ],
    [
        'name' => 'Mazda Original',
        'country' => 'Япония',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти Mazda',
        'stats' => ['Более 800 товаров', 'Технологии Zoom-Zoom']
    ],
    [
        'name' => 'Honda Original',
        'country' => 'Япония',
        'category' => 'original',
        'category_name' => 'Оригинальные',
        'description' => 'Оригинальные запчасти Honda',
        'stats' => ['Более 850 товаров', 'Надежность Honda']
    ],

    [
        'name' => 'Febi Bilstein',
        'country' => 'Германия',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Качественные аналоги европейских автомобилей',
        'stats' => ['Более 800 товаров', 'Соответствие OE']
    ],
    [
        'name' => 'Blue Print',
        'country' => 'Япония',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Высококачественные аналоги для азиатских авто',
        'stats' => ['Более 600 товаров', 'Японское качество']
    ],
    [
        'name' => 'SWAG',
        'country' => 'Германия',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Немецкое качество по доступным ценам',
        'stats' => ['Более 500 товаров', 'Германские стандарты']
    ],
    [
        'name' => 'Mapco',
        'country' => 'Германия',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Надежные аналоги для европейских автомобилей',
        'stats' => ['Более 400 товаров', 'Проверенное качество']
    ],
    [
        'name' => 'Mahle',
        'country' => 'Германия',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Качественные аналоги для всех марок',
        'stats' => ['Более 700 товаров', 'Немецкие стандарты']
    ],
    [
        'name' => 'Hella',
        'country' => 'Германия',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Осветительные приборы и электроника',
        'stats' => ['Более 600 товаров', 'Инновации']
    ],
    [
        'name' => 'Bosch Car Service',
        'country' => 'Германия',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Сервисные решения и компоненты',
        'stats' => ['Более 500 товаров', 'Профессиональные решения']
    ],
    [
        'name' => 'NGK',
        'country' => 'Япония',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Свечи зажигания и датчики',
        'stats' => ['Более 300 товаров', 'Технологии зажигания']
    ],
    [
        'name' => 'Corteco',
        'country' => 'Германия',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Уплотнительные элементы и сальники высокого качества',
        'stats' => ['Более 350 товаров', 'Немецкая точность']
    ],
    [
        'name' => 'Behr',
        'country' => 'Германия',
        'category' => 'aftermarket',
        'category_name' => 'Аналоги',
        'description' => 'Системы охлаждения и отопления для автомобилей',
        'stats' => ['Более 450 товаров', 'Тепловые решения']
    ],

    [
        'name' => 'Трек',
        'country' => 'Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Ведущий российский производитель фильтров',
        'stats' => ['Более 200 товаров', 'Лучшая цена']
    ],
    [
        'name' => 'СтартВОЛЬТ',
        'country' => 'Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Российские аккумуляторы премиум-класса',
        'stats' => ['Более 50 товаров', 'Адаптированы к климату']
    ],
    [
        'name' => 'FENOX',
        'country' => 'Беларусь/Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Качественные автокомпоненты для СНГ рынка',
        'stats' => ['Более 300 товаров', 'Оптимальное соотношение']
    ],
    [
        'name' => 'БелМаг',
        'country' => 'Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Магнитолы и аудиосистемы российского производства',
        'stats' => ['Более 100 товаров', 'Лучшая цена']
    ],
    [
        'name' => 'Кама',
        'country' => 'Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Российские шины премиум-класса',
        'stats' => ['Более 150 товаров', 'Адаптированы к дорогам']
    ],
    [
        'name' => 'СОАТЭ',
        'country' => 'Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Автоэлектрика и компоненты',
        'stats' => ['Более 200 товаров', 'Надежность']
    ],
    [
        'name' => 'АВТОВАЗ',
        'country' => 'Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Оригинальные запчасти LADA',
        'stats' => ['Более 2000 товаров', 'Официальный поставщик']
    ],
    [
        'name' => 'ОМНИ',
        'country' => 'Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Аккумуляторы и электрооборудование',
        'stats' => ['Более 180 товаров', 'Российское качество']
    ],
    [
        'name' => 'КВЗ',
        'country' => 'Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Радиаторы и компоненты системы охлаждения',
        'stats' => ['Более 120 товаров', 'Проверенная надежность']
    ],
    [
        'name' => 'АГАТ',
        'country' => 'Россия',
        'category' => 'russia',
        'category_name' => 'Российские',
        'description' => 'Тормозные колодки и диски российского производства',
        'stats' => ['Более 180 товаров', 'Безопасность прежде всего']
    ]
];
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
                <div class="stat-number text-primary fw-bold fs-3"><?php echo count($brands); ?>+</div>
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
            <button class="btn btn-outline-primary filter-btn active" data-filter="all">Все <span class="badge bg-primary ms-1"><?php echo count($brands); ?></span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="premium">Премиум <span class="badge bg-primary ms-1">10</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="original">Оригинальные <span class="badge bg-primary ms-1">10</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="aftermarket">Аналоги <span class="badge bg-primary ms-1">10</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="russia">Российские <span class="badge bg-primary ms-1">10</span></button>
        </div>
    </div>
    <div class="brands-grid-section mb-5">
        <div class="row g-3 brands-grid">
            <?php 
            foreach ($brands as $brand)
            {
            ?>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="<?php echo $brand['category']; ?>">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="<?php echo $brand['name']; ?>" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name"><?php echo $brand['name']; ?></h5>
                        <p class="brand-country"><?php echo $brand['country']; ?></p>
                        <div class="brand-category <?php echo $brand['category']; ?>"><?php echo $brand['category_name']; ?></div>
                        <p class="brand-description"><?php echo $brand['description']; ?></p>
                        <div class="brand-stats">
                            <?php 
                            foreach ($brand['stats'] as $stat)
                            {
                            ?>
                            <span class="stat-item"><?php echo $stat; ?></span>
                            <?php 
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            } 
            ?>
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