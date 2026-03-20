<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");
require_once("../config/check_auth.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

$brands = [];
$sql = "SELECT * FROM `product_brands` ORDER BY name";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) 
{
    while ($row = $result->fetch_assoc())
    {
        $row['stats'] = json_decode($row['stats'], true);
        $brands[] = $row;
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
    <title>Торговые марки - Лал-Авто</title>
    <link rel="icon" href="../img/iconAuto.png" type="image/png" height="32">
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
        let isResizing = false;

        function equalizeBrandCards() 
        {
            if (isResizing) 
            {
                return;
            }
            
            let brandCards = document.querySelectorAll('.brand-card');

            brandCards.forEach(card => {
                card.style.height = 'auto';
            });

            let maxHeight = 0;
            
            brandCards.forEach(card => {
                if (card.offsetParent !== null && card.closest('.brand-item') && card.closest('.brand-item').style.display !== 'none') 
                {
                    let cardHeight = card.offsetHeight;

                    if (cardHeight > maxHeight) 
                    {
                        maxHeight = cardHeight;
                    }
                }
            });

            brandCards.forEach(card => {
                if (card.offsetParent !== null && card.closest('.brand-item') && card.closest('.brand-item').style.display !== 'none') 
                {
                    card.style.height = maxHeight + 'px';
                }
            });
        }

        function smoothEqualize() 
        {
            if (typeof requestAnimationFrame !== 'undefined') 
            {
                requestAnimationFrame(function() 
                {
                    equalizeBrandCards();
                });
            } 
            else 
            {
                setTimeout(equalizeBrandCards, 0);
            }
        }

        function scrollToFilters() 
        {
            let filtersSection = document.getElementById('filters-section');

            if (filtersSection) 
            {
                let offset = 50;
                let elementPosition = filtersSection.getBoundingClientRect().top;
                let offsetPosition = elementPosition + window.pageYOffset - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        }

        let resizeTimeout;
        
        window.addEventListener('resize', function() 
        {
            isResizing = true;
            clearTimeout(resizeTimeout);

            resizeTimeout = setTimeout(function() 
            {
                isResizing = false;
                smoothEqualize();
            }, 150);
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
                scrollToFilters();
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
                        scrollToFilters();
                    }
                });

                paginationElement.appendChild(prevLi);
                let startPage = Math.max(1, currentPage - 2);
                let endPage = Math.min(totalPages, startPage + 4);
                
                if (startPage > 1) 
                {
                    let firstLi = document.createElement('li');
                    firstLi.className = 'page-item';
                    firstLi.innerHTML = `<a class="page-link" href="#">1</a>`;
                    firstLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = 1;
                        showPage(currentPage);
                        scrollToFilters();
                    });
                    paginationElement.appendChild(firstLi);
                    
                    if (startPage > 2) 
                    {
                        let dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<a class="page-link" href="#">...</a>`;
                        paginationElement.appendChild(dotsLi);
                    }
                }

                for (let i = startPage; i <= endPage; i++) 
                {
                    let li = document.createElement('li');
                    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    li.innerHTML = `<a class="page-link" href="#">${i}</a>`;

                    li.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = i;
                        showPage(currentPage);
                        scrollToFilters();
                    });

                    paginationElement.appendChild(li);
                }
                
                if (endPage < totalPages) 
                {
                    if (endPage < totalPages - 1) 
                    {
                        let dotsLi = document.createElement('li');
                        dotsLi.className = 'page-item disabled';
                        dotsLi.innerHTML = `<a class="page-link" href="#">...</a>`;
                        paginationElement.appendChild(dotsLi);
                    }
                    
                    let lastLi = document.createElement('li');
                    lastLi.className = 'page-item';
                    lastLi.innerHTML = `<a class="page-link" href="#">${totalPages}</a>`;
                    lastLi.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = totalPages;
                        showPage(currentPage);
                        scrollToFilters();
                    });

                    paginationElement.appendChild(lastLi);
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
                        scrollToFilters();
                    }
                });

                paginationElement.appendChild(nextLi);
            }
        }
        
        function showPage(page) 
        {
            brandItems.forEach(item => {
                item.style.display = 'none';
            });
            
            let startIndex = (page - 1) * CARDS_PER_PAGE;
            let endIndex = startIndex + CARDS_PER_PAGE;
            let itemsToShow = visibleBrandItems.slice(startIndex, endIndex);

            itemsToShow.forEach((item) => {
                item.style.display = 'block';
            });

            setTimeout(function() 
            {
                smoothEqualize();
            }, 0);
            
            updatePagination();
        }

        function initializePage() 
        {
            initializeCounters();

            brandItems.forEach(item => {
                item.style.display = 'block';
            });

            smoothEqualize();

            visibleBrandItems = Array.from(brandItems);
            currentPage = 1;
            updatePagination();
            showPage(currentPage);

            setTimeout(function() 
            {
                brandItems.forEach(item => {
                    item.classList.add('visible');
                });
            }, 50);
        }

        window.addEventListener('load', function() 
        {
            initializePage();

            let images = document.querySelectorAll('.brand-logo');
            let imagesToLoad = images.length;
            let loadedImages = 0;
            
            if (imagesToLoad > 0) 
            {
                images.forEach(img => {
                    if (img.complete) 
                    {
                        loadedImages++;
                    }
                    else 
                    {
                        img.addEventListener('load', () => {
                            loadedImages++;

                            if (loadedImages === imagesToLoad) 
                            {
                                setTimeout(smoothEqualize, 50);
                            }
                        });
                    }
                });
                
                if (loadedImages === imagesToLoad) 
                {
                    setTimeout(smoothEqualize, 50);
                }
            }
        });

        if (typeof ResizeObserver !== 'undefined') 
        {
            let observer = new ResizeObserver(function() 
            {
                smoothEqualize();
            });
            
            document.querySelectorAll('.brand-card').forEach(card => {
                observer.observe(card);
            });
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
        <h1 class="display-5 fw-bold text-primary mb-3">Торговые марки</h1>
        <p class="lead text-muted mb-3">Официальный дилер ведущих мировых производителей автозапчастей</p>
        <div class="stats-row d-flex justify-content-center gap-4 flex-wrap mb-4">
            <div class="stat-hero-item">
                <div class="stat-number text-primary fw-bold fs-3"><?php echo count($brands); ?>+</div>
                <div class="stat-label text-muted">брендов</div>
            </div>
            <div class="stat-hero-item">
                <div class="stat-number text-primary fw-bold fs-3">10 000+</div>
                <div class="stat-label text-muted">товаров</div>
            </div>
            <div class="stat-hero-item">
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
    <div id="filters-section" class="filters-section mb-4">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <button class="btn btn-outline-primary filter-btn active" data-filter="all">Все <span class="badge bg-primary ms-1"><?php echo count($brands); ?></span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="premium">Премиум <span class="badge bg-primary ms-1">0</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="original">Оригинальные <span class="badge bg-primary ms-1">0</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="aftermarket">Аналоги <span class="badge bg-primary ms-1">0</span></button>
            <button class="btn btn-outline-primary filter-btn" data-filter="russia">Российские <span class="badge bg-primary ms-1">0</span></button>
        </div>
    </div>
    <div class="brands-grid-section mb-5">
        <div class="row g-3 brands-grid">
            <?php 
            foreach ($brands as $brand)
            {
            ?>
            <div class="col-xl-3 col-lg-3 col-md-6 brand-item" data-category="<?php echo htmlspecialchars($brand['category']); ?>">
                <div class="brand-card">
                    <div class="brand-logo-container">
                        <img src="../img/no-image.png" alt="<?php echo htmlspecialchars($brand['name']); ?>" class="brand-logo">
                    </div>
                    <div class="brand-info">
                        <h5 class="brand-name"><?php echo htmlspecialchars($brand['name']); ?></h5>
                        <p class="brand-country"><?php echo htmlspecialchars($brand['country']); ?></p>
                        <div class="brand-category <?php echo htmlspecialchars($brand['category']); ?>"><?php echo htmlspecialchars($brand['category_name']); ?></div>
                        <p class="brand-description"><?php echo htmlspecialchars($brand['description']); ?></p>
                        <div class="brand-stats">
                            <?php 
                            if (!empty($brand['stats']) && is_array($brand['stats'])) {
                                foreach ($brand['stats'] as $stat)
                                {
                            ?>
                            <span class="stat-item"><?php echo htmlspecialchars($stat); ?></span>
                            <?php 
                                }
                            }
                            ?>
                        </div>
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
            <h4 class="text-muted mb-3">Бренды не найдены</h4>
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