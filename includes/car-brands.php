<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");
require_once("../config/check_auth.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

$carBrands = [];
$sql = "SELECT * FROM car_brands ORDER BY name";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) 
{
    while ($row = $result->fetch_assoc()) 
    {
        $row['models'] = json_decode($row['models'], true);
        $carBrands[] = $row;
    }
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

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все марки автомобилей - Лал-Авто</title>
    <link rel="icon" href="../img/iconAuto.png" type="image/png" height="32">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/car-brands-styles.css">
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

        let CARDS_PER_PAGE = 12;
        let currentPage = 1;
        let totalPages = 1;
        let allBrandItems = document.querySelectorAll('.car-brand-item');
        let visibleBrandItems = [];
        let resizeTimeout;
        let isInitialized = false;
        let filterButtons = document.querySelectorAll('.filter-btn');
        let brandItems = Array.from(allBrandItems);
        let searchInput = document.getElementById('brandSearch');
        let searchClear = document.querySelector('.search-clear');
        let paginationElement = document.getElementById('pagination');
        let noResultsElement = document.getElementById('noResults');
        let modelsGridElement = document.getElementById('modelsGrid');
        
        function equalizeBrandCards() 
        {
            if (!modelsGridElement) 
            {
                return;
            }

            let cards = document.querySelectorAll('.car-brand-card');

            cards.forEach(card => {
                card.style.height = 'auto';
            });
            
            setTimeout(() => {
                let maxHeight = 0;
                let visibleCards = document.querySelectorAll('.car-brand-item[style*="display: block"] .car-brand-card');

                visibleCards.forEach(card => {
                    let height = card.offsetHeight;

                    if (height > maxHeight) 
                    {
                        maxHeight = height;
                    }
                });
                
                if (maxHeight > 0) 
                {
                    visibleCards.forEach(card => {
                        card.style.height = maxHeight + 'px';
                    });
                }
            }, 100);
        }
        
        function updateCategoryCounters(activeFilter, searchText) 
        {
            let categoryCounts = {
                'all': 0,
                'premium': 0,
                'luxury': 0,
                'mass': 0,
                'offroad': 0,
                'sport': 0,
                'electric': 0,
                'commercial': 0,
                'special': 0
            };

            brandItems.forEach(item => {
                let brandName = item.querySelector('.car-brand-name').textContent.toLowerCase();
                let brandDescription = item.querySelector('.car-brand-description').textContent.toLowerCase();
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
                    badge.textContent = categoryCounts[filter] || 0;
                }
            });
        }
        
        function filterBrands(category, searchText) 
        {
            visibleBrandItems = [];
            
            brandItems.forEach(item => {
                let brandName = item.querySelector('.car-brand-name').textContent.toLowerCase();
                let brandDescription = item.querySelector('.car-brand-description').textContent.toLowerCase();
                let itemCategory = item.getAttribute('data-category');
                
                let categoryMatch = category === 'all' || itemCategory === category;
                let searchMatch = !searchText || brandName.includes(searchText) || brandDescription.includes(searchText);
                
                if (categoryMatch && searchMatch) 
                {
                    visibleBrandItems.push(item);
                }

                item.style.display = 'none';
                item.style.opacity = '0';
                item.style.transform = 'scale(0.8)';
            });

            currentPage = 1;
            updatePagination();
            showPage(currentPage);
            updateCategoryCounters(category, searchText);

            setTimeout(() => {
                equalizeBrandCards();
            }, 350);
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

                if (modelsGridElement) 
                {
                    modelsGridElement.style.display = 'none';
                }
            } 
            else 
            {
                noResultsElement.style.display = 'none';

                if (modelsGridElement) 
                {
                    modelsGridElement.style.display = 'grid';
                }
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
            let itemsToShow = visibleBrandItems.slice(startIndex, endIndex);
            
            itemsToShow.forEach((item, index) => {
                setTimeout(() => {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'scale(1)';
                    }, 50);
                }, index * 30);
            });

            updatePagination();

            setTimeout(() => {
                equalizeBrandCards();

                if (page > 1) 
                {
                    window.scrollTo({
                        top: modelsGridElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            }, 350);
        }
        
        function initializePage() 
        {
            if (isInitialized) 
            {
                return;
            }
            
            updateCategoryCounters('all', '');
            filterBrands('all', '');
            
            setTimeout(equalizeBrandCards, 500);
            
            isInitialized = true;
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
        
        if (searchInput) 
        {
            searchInput.addEventListener('input', function() 
            {
                let searchText = this.value.toLowerCase();

                if (searchClear) 
                {
                    searchClear.style.display = searchText ? 'block' : 'none';
                }
                
                let activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
                filterBrands(activeFilter, searchText);
            });
        }
        
        if (searchClear) 
        {
            searchClear.addEventListener('click', function() 
            {
                searchInput.value = '';
                searchClear.style.display = 'none';

                let activeFilter = document.querySelector('.filter-btn.active').getAttribute('data-filter');
                filterBrands(activeFilter, '');
            });
        }

        window.addEventListener('resize', function() 
        {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                equalizeBrandCards();
            }, 250);
        });
        
        window.addEventListener('load', function() 
        {
            initializePage();

            setTimeout(() => {
                equalizeBrandCards();
            }, 1000);
        });

        if (document.readyState === 'complete') 
        {
            initializePage();
        } 
        else 
        {
            window.addEventListener('load', initializePage);
        }
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-4">
    <div class="hero-section text-center mb-4" style="padding-top: 105px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Все марки автомобилей</h1>
        <p class="lead text-muted mb-3">Подберите запчасти для вашего автомобиля по марке</p>
        <div class="stats-row d-flex justify-content-center gap-4 flex-wrap mb-4">
            <div class="stat-item">
                <div class="stat-number text-primary fw-bold fs-3"><?php echo count($carBrands); ?>+</div>
                <div class="stat-label text-muted">марок</div>
            </div>
            <div class="stat-item">
                <div class="stat-number text-primary fw-bold fs-3">50 000+</div>
                <div class="stat-label text-muted">запчастей</div>
            </div>
            <div class="stat-item">
                <div class="stat-number text-primary fw-bold fs-3">15 лет</div>
                <div class="stat-label text-muted">опыта</div>
            </div>
        </div>
    </div>
    <div class="search-section mb-4">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="search-container position-relative">
                    <input type="text" id="brandSearch" placeholder="Поиск по маркам автомобилей..." class="form-control search-input">
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
            <button class="btn btn-outline-primary filter-btn active" data-filter="all">Все <span class="badge bg-primary ms-1"><?php echo count($carBrands); ?></span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="premium">Премиум <span class="badge bg-primary ms-1">10</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="luxury">Люкс <span class="badge bg-primary ms-1">8</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="mass">Массовые <span class="badge bg-primary ms-1">15</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="offroad">Внедорожники <span class="badge bg-primary ms-1">5</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="sport">Спорт <span class="badge bg-primary ms-1">3</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="electric">Электрические <span class="badge bg-primary ms-1">1</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="commercial">Коммерческие <span class="badge bg-primary ms-1">2</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="special">Специальные <span class="badge bg-primary ms-1">1</span></button>
        </div>
    </div>
    <div class="brands-grid-section mb-5">
        <div class="row g-3 w-100" id="modelsGrid" style="display: grid;">
            <?php 
            foreach ($carBrands as $brand)
            {
            ?>
            <div class="col-xl-3 col-lg-3 col-md-6 car-brand-item w-100" data-category="<?php echo $brand['category']; ?>">
                <div class="car-brand-card">
                    <div class="car-brand-logo-container">
                        <img src="<?php echo $brand['image']; ?>" alt="<?php echo $brand['name']; ?>" class="car-brand-logo">
                    </div>
                    <div class="car-brand-info">
                        <h5 class="car-brand-name"><?php echo $brand['name']; ?></h5>
                        <p class="car-brand-country">
                            <i class="bi bi-geo-alt me-1"></i><?php echo $brand['country']; ?>
                        </p>
                        <div class="car-brand-category <?php echo $brand['category']; ?>">
                            <?php echo $brand['category_name']; ?>
                        </div>
                        <p class="car-brand-description"><?php echo $brand['description']; ?></p>
                        <div class="car-brand-models">
                            <small class="text-muted d-block mb-1">Популярные модели:</small>
                            <div class="models-tags">
                                <?php 
                                foreach ($brand['models'] as $model)
                                {
                                ?>
                                <span class="model-tag"><?php echo $model; ?></span>
                                <?php 
                                }
                                ?>
                            </div>
                        </div>
                        <a href="../includes/assortment.php?search=<?php echo urlencode(strtolower($brand['name'])); ?>" class="btn btn-outline-primary w-100 mt-3">
                            <i class="bi bi-search me-1"></i>Найти запчасти
                        </a>
                    </div>
                </div>
            </div>
            <?php 
            } 
            ?>
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
            <h4 class="text-muted mb-3">Марки не найдены</h4>
            <p class="text-muted">Попробуйте изменить параметры поиска или фильтрации</p>
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