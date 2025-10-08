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
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5 pt-4">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="mb-3" style="padding-top: 60px;">Торговые марки</h1>
            <p class="lead">Официальный дилер ведущих мировых производителей автозапчастей</p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <div class="search-container position-relative">
                <input type="text" id="brandSearch" placeholder="Поиск по брендам..." class="form-control form-control-lg search-input">
                <button class="btn btn-link search-clear" type="button" style="display: none;">
                    <i class="bi bi-x"></i>
                </button>
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <button class="btn btn-outline-primary filter-btn active" data-filter="all">Все</button>
                <button class="btn btn-outline-primary filter-btn" data-filter="premium">Премиум</button>
                <button class="btn btn-outline-primary filter-btn" data-filter="original">Оригинальные</button>
                <button class="btn btn-outline-primary filter-btn" data-filter="aftermarket">Аналоги</button>
                <button class="btn btn-outline-primary filter-btn" data-filter="russia">Российские</button>
            </div>
        </div>
    </div>
    <div class="row g-4 brands-grid">
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="premium">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Bosch" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Bosch</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Премиум</div>
                    <p class="brand-description">Мировой лидер в производстве автокомпонентов и систем</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 1000 товаров</span>
                        <span class="stat-item">Гарантия 2 года</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="premium">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Castrol" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Castrol</h5>
                    <p class="brand-country">Великобритания</p>
                    <div class="brand-category">Премиум</div>
                    <p class="brand-description">Ведущий производитель моторных масел и смазочных материалов</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 500 товаров</span>
                        <span class="stat-item">Одобрено OEM</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="premium">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Mann-Filter" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Mann-Filter</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Премиум</div>
                    <p class="brand-description">Эксперты в области фильтрации для автомобильной промышленности</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 300 товаров</span>
                        <span class="stat-item">ОЕМ поставщик</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="premium">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Brembo" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Brembo</h5>
                    <p class="brand-country">Италия</p>
                    <div class="brand-category">Премиум</div>
                    <p class="brand-description">Мировой лидер в производстве тормозных систем</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 400 товаров</span>
                        <span class="stat-item">Спорт-кар качество</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="original">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Volkswagen Original" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">VW Original</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Оригинальные</div>
                    <p class="brand-description">Оригинальные запчасти Volkswagen Group</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 2000 товаров</span>
                        <span class="stat-item">Официальный дилер</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="original">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Toyota Original" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Toyota Original</h5>
                    <p class="brand-country">Япония</p>
                    <div class="brand-category">Оригинальные</div>
                    <p class="brand-description">Оригинальные запчасти Toyota Motor Corporation</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 1500 товаров</span>
                        <span class="stat-item">Гарантия качества</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="original">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="BMW Original" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">BMW Original</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Оригинальные</div>
                    <p class="brand-description">Оригинальные запчасти BMW Group</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 1200 товаров</span>
                        <span class="stat-item">Сертифицировано</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="original">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Mercedes-Benz Original" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Mercedes Original</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Оригинальные</div>
                    <p class="brand-description">Оригинальные запчасти Mercedes-Benz</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 1100 товаров</span>
                        <span class="stat-item">Премиум качество</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="aftermarket">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Febi Bilstein" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Febi Bilstein</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Аналоги</div>
                    <p class="brand-description">Качественные аналоги европейских автомобилей</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 800 товаров</span>
                        <span class="stat-item">Соответствие OE</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="aftermarket">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Blue Print" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Blue Print</h5>
                    <p class="brand-country">Япония</p>
                    <div class="brand-category">Аналоги</div>
                    <p class="brand-description">Высококачественные аналоги для азиатских авто</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 600 товаров</span>
                        <span class="stat-item">Японское качество</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="aftermarket">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="SWAG" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">SWAG</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Аналоги</div>
                    <p class="brand-description">Немецкое качество по доступным ценам</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 500 товаров</span>
                        <span class="stat-item">Германские стандарты</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="aftermarket">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Mapco" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Mapco</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Аналоги</div>
                    <p class="brand-description">Надежные аналоги для европейских автомобилей</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 400 товаров</span>
                        <span class="stat-item">Проверенное качество</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="russia">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Трек" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Трек</h5>
                    <p class="brand-country">Россия</p>
                    <div class="brand-category">Российские</div>
                    <p class="brand-description">Ведущий российский производитель фильтров</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 200 товаров</span>
                        <span class="stat-item">Лучшая цена</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="russia">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="СтартВОЛЬТ" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">СтартВОЛЬТ</h5>
                    <p class="brand-country">Россия</p>
                    <div class="brand-category">Российские</div>
                    <p class="brand-description">Российские аккумуляторы премиум-класса</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 50 товаров</span>
                        <span class="stat-item">Адаптированы к климату</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="russia">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="FENOX" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">FENOX</h5>
                    <p class="brand-country">Беларусь/Россия</p>
                    <div class="brand-category">Российские</div>
                    <p class="brand-description">Качественные автокомпоненты для СНГ рынка</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 300 товаров</span>
                        <span class="stat-item">Оптимальное соотношение</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="russia">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="БелМаг" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">БелМаг</h5>
                    <p class="brand-country">Россия</p>
                    <div class="brand-category">Российские</div>
                    <p class="brand-description">Магнитолы и аудиосистемы российского производства</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 100 товаров</span>
                        <span class="stat-item">Лучшая цена</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="premium">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Continental" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Continental</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Премиум</div>
                    <p class="brand-description">Инновационные шины и автомобильные системы</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 700 товаров</span>
                        <span class="stat-item">Немецкие технологии</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="aftermarket">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Corteco" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Corteco</h5>
                    <p class="brand-country">Германия</p>
                    <div class="brand-category">Аналоги</div>
                    <p class="brand-description">Качественные уплотнители и сальники</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 350 товаров</span>
                        <span class="stat-item">Надежность</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-6 brand-item" data-category="russia">
            <div class="brand-card">
                <div class="brand-logo-container">
                    <img src="../img/no-image.png" alt="Катрен" class="brand-logo">
                </div>
                <div class="brand-info">
                    <h5 class="brand-name">Катрен</h5>
                    <p class="brand-country">Россия</p>
                    <div class="brand-category">Российские</div>
                    <p class="brand-description">Автохимия и технические жидкости</p>
                    <div class="brand-stats">
                        <span class="stat-item">Более 150 товаров</span>
                        <span class="stat-item">Эффективность</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <div class="brands-stats bg-light p-4 rounded">
                <h3 class="text-center mb-4">Статистика по брендам</h3>
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Брендов</div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-number">10 000+</div>
                        <div class="stat-label">Товаров</div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-number">15</div>
                        <div class="stat-label">Лет на рынке</div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Оригинальная продукция</div>
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
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let filterButtons = document.querySelectorAll('.filter-btn');
    let brandItems = document.querySelectorAll('.brand-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() 
        {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            let filter = this.getAttribute('data-filter');
            
            brandItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) 
                {
                    item.style.display = 'block';
                } 
                else 
                {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    let searchInput = document.getElementById('brandSearch');
    
    searchInput.addEventListener('input', function() 
    {
        let searchText = this.value.toLowerCase();
        
        brandItems.forEach(item => {
            let brandName = item.querySelector('.brand-name').textContent.toLowerCase();
            let brandDescription = item.querySelector('.brand-description').textContent.toLowerCase();
            
            if (brandName.includes(searchText) || brandDescription.includes(searchText)) 
            {
                item.style.display = 'block';
            } 
            else 
            {
                item.style.display = 'none';
            }
        });
    });
});
</script>
</body>
</html>