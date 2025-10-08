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

$items_per_page = 12;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$all_products = [
    ['category' => 'фильтры', 'title' => 'Фильтр масляный Mann W914/2', 'price' => 1250, 'badge' => 'Новинка'],
    ['category' => 'тормозная система', 'title' => 'Тормозные колодки Brembo P85115', 'price' => 3890, 'old_price' => 4500, 'badge' => 'Акция'],
    ['category' => 'двигатель', 'title' => 'Свечи зажигания NGK BKR6E', 'price' => 850, 'badge' => 'Хит'],
    ['category' => 'трансмиссия', 'title' => 'Сцепление SACHS 3000 951 515', 'price' => 12500],
    ['category' => 'ходовая часть', 'title' => 'Амортизатор KYB 334302', 'price' => 4200, 'old_price' => 5100, 'badge' => 'Акция'],
    ['category' => 'электрика', 'title' => 'Аккумулятор VARTA Blue Dynamic E11', 'price' => 8900],
    ['category' => 'кузовные детали', 'title' => 'Фара правая Hyundai Solaris', 'price' => 15300, 'badge' => 'Новинка'],
    ['category' => 'масла и жидкости', 'title' => 'Моторное масло Mobil 1 5W-30', 'price' => 3800],
    ['category' => 'фильтры', 'title' => 'Воздушный фильтр Bosch F026400224', 'price' => 1100],
    ['category' => 'тормозная система', 'title' => 'Тормозной диск TRW DF4261', 'price' => 6700],
    ['category' => 'двигатель', 'title' => 'Ремень ГРМ Gates T420', 'price' => 2900, 'badge' => 'Хит'],
    ['category' => 'электрика', 'title' => 'Генератор Valeo 439730', 'price' => 18500],
    ['category' => 'система охлаждения', 'title' => 'Радиатор охлаждения Nissens 94170', 'price' => 11200, 'badge' => 'Новинка'],
    ['category' => 'рулевое управление', 'title' => 'Рулевая рейка ZF 800195058', 'price' => 24500],
    ['category' => 'система выпуска', 'title' => 'Глушитель Walker 55487', 'price' => 8900, 'badge' => 'Акция'],
    ['category' => 'фильтры', 'title' => 'Салонный фильтр Mahle LAK 521', 'price' => 950],
    ['category' => 'двигатель', 'title' => 'Термостат Wahler 3076.82D', 'price' => 3200],
    ['category' => 'трансмиссия', 'title' => 'Масло трансмиссионное Motul Gear 300 75W90', 'price' => 2800],
    ['category' => 'ходовая часть', 'title' => 'Стойка стабилизатора Lemforder 30672 01', 'price' => 1800],
    ['category' => 'тормозная система', 'title' => 'Тормозная жидкость ATE TYP 200', 'price' => 1200],
    ['category' => 'электрика', 'title' => 'Стартер Bosch 0986010280', 'price' => 14200, 'badge' => 'Хит'],
    ['category' => 'кузовные детали', 'title' => 'Бампер передний Toyota Corolla', 'price' => 18700],
    ['category' => 'масла и жидкости', 'title' => 'Антифриз G12++ Felix Prolong 5L', 'price' => 2100],
    ['category' => 'система охлаждения', 'title' => 'Помпа водяная Gates 42137', 'price' => 5400],
    ['category' => 'рулевое управление', 'title' => 'Наконечник рулевой тяги TRW JTE799', 'price' => 3200],
    ['category' => 'фильтры', 'title' => 'Топливный фильтр Knecht KL 169/2', 'price' => 1850],
    ['category' => 'двигатель', 'title' => 'Прокладка ГБЦ Victor Reinz 71-99718-01', 'price' => 6700],
    ['category' => 'трансмиссия', 'title' => 'Подшипник выжимной SACHS 3152 160 141', 'price' => 2900],
    ['category' => 'ходовая часть', 'title' => 'Пружина подвески Kilen 30221', 'price' => 8200],
    ['category' => 'тормозная система', 'title' => 'Суппорт тормозной ATE 24.0130-5701.2', 'price' => 15300],
    ['category' => 'электрика', 'title' => 'Катушка зажигания Bosch 0221504470', 'price' => 4100, 'badge' => 'Акция'],
    ['category' => 'кузовные детали', 'title' => 'Зеркало боковое левое VW Golf', 'price' => 8900],
    ['category' => 'масла и жидкости', 'title' => 'Масло для ГУР Ravenol PSF', 'price' => 1650],
    ['category' => 'система выпуска', 'title' => 'Лямбда-зонд Bosch 0258006546', 'price' => 11200],
    ['category' => 'рулевое управление', 'title' => 'Рулевой наконечник Lemforder 20275 01', 'price' => 3800],
    ['category' => 'фильтры', 'title' => 'Масляный фильтр Mahle OX 395D', 'price' => 950],
    ['category' => 'двигатель', 'title' => 'Ремень генератора Contitech 6PK1885', 'price' => 3200],
    ['category' => 'трансмиссия', 'title' => 'Фланец полуоси GKN 980112', 'price' => 12800],
    ['category' => 'ходовая часть', 'title' => 'Сайлентблок передний Febi 21372', 'price' => 2100],
    ['category' => 'тормозная система', 'title' => 'Тормозной шланг TRW BHA 513', 'price' => 2900],
    ['category' => 'электрика', 'title' => 'Датчик ABS Hella 6PT 009 107-791', 'price' => 5400],
    ['category' => 'кузовные детали', 'title' => 'Капот Ford Focus', 'price' => 23400],
    ['category' => 'масла и жидкости', 'title' => 'Тормозная жидкость Bosch ENV6', 'price' => 850],
    ['category' => 'система охлаждения', 'title' => 'Вентилятор радиатора Hella 8FV 003 501-021', 'price' => 16700],
    ['category' => 'рулевое управление', 'title' => 'Рулевая тяга Lemforder 24713 01', 'price' => 6200],
    ['category' => 'фильтры', 'title' => 'Воздушный фильтр Mann C 3698', 'price' => 1850],
    ['category' => 'двигатель', 'title' => 'Крышка клапана Elring 024.492', 'price' => 4900],
    ['category' => 'трансмиссия', 'title' => 'Поддон АКПП ZF 8HP', 'price' => 8900],
    ['category' => 'ходовая часть', 'title' => 'Опорный подшипник SKF VKBA 3564', 'price' => 3200]
];

$filtered_products = $all_products;
$search_term = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

if ($search_term !== '' || $category_filter !== '') 
{
    $filtered_products = array_filter($all_products, function($product) use ($search_term, $category_filter) 
    {
        $matches_search = $search_term === '' || strpos(strtolower($product['title']), $search_term) !== false;
        $matches_category = $category_filter === '' || $category_filter === 'все категории' || $product['category'] === $category_filter;
        return $matches_search && $matches_category;
    });

    $filtered_products = array_values($filtered_products);
}

$total_items = count($filtered_products);
$total_pages = ceil($total_items / $items_per_page);
$start_index = ($current_page - 1) * $items_per_page;
$end_index = min($start_index + $items_per_page, $total_items);

$show_pagination = $total_pages > 1;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ассортимент - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/assortment-styles.css">
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

<div class="container my-5">
    <h1 class="text-center mb-5" style="padding-top: 85px;">Ассортимент автозапчастей</h1>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="search-container position-relative">
                <input type="text" id="partsSearch" placeholder="Поиск по каталогу..." class="form-control form-control-lg" 
                       value="<?php echo htmlspecialchars($search_term); ?>">
                <button class="btn btn-link search-clear" type="button" style="display: none;">
                    <i class="bi bi-x"></i>
                </button>
                <i class="bi bi-search search-icon"></i>
            </div>
        </div>
        <div class="col-md-4">
            <select id="categoryFilter" class="form-select form-select-lg">
                <option value="все категории" <?php echo $category_filter === '' || $category_filter === 'все категории' ? 'selected' : ''; ?>>Все категории</option>
                <option value="двигатель" <?php echo $category_filter === 'двигатель' ? 'selected' : ''; ?>>Двигатель</option>
                <option value="трансмиссия" <?php echo $category_filter === 'трансмиссия' ? 'selected' : ''; ?>>Трансмиссия</option>
                <option value="ходовая часть" <?php echo $category_filter === 'ходовая часть' ? 'selected' : ''; ?>>Ходовая часть</option>
                <option value="тормозная система" <?php echo $category_filter === 'тормозная система' ? 'selected' : ''; ?>>Тормозная система</option>
                <option value="электрика" <?php echo $category_filter === 'электрика' ? 'selected' : ''; ?>>Электрика</option>
                <option value="кузовные детали" <?php echo $category_filter === 'кузовные детали' ? 'selected' : ''; ?>>Кузовные детали</option>
                <option value="фильтры" <?php echo $category_filter === 'фильтры' ? 'selected' : ''; ?>>Фильтры</option>
                <option value="масла и жидкости" <?php echo $category_filter === 'масла и жидкости' ? 'selected' : ''; ?>>Масла и жидкости</option>
                <option value="система охлаждения" <?php echo $category_filter === 'система охлаждения' ? 'selected' : ''; ?>>Система охлаждения</option>
                <option value="система выпуска" <?php echo $category_filter === 'система выпуска' ? 'selected' : ''; ?>>Система выпуска</option>
                <option value="рулевое управление" <?php echo $category_filter === 'рулевое управление' ? 'selected' : ''; ?>>Рулевое управление</option>
            </select>
        </div>
        <div class="col-md-2">
            <button id="searchButton" class="btn btn-primary btn-lg w-100">
                <i class="bi bi-search"></i> Найти
            </button>
        </div>
    </div>
    
    <?php if ($search_term !== '' || $category_filter !== '')
    {
    ?>
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info">
                <?php 
                if ($search_term !== '' && $category_filter !== '' && $category_filter !== 'все категории')
                {
                ?>
                    Найдено <?php echo $total_items; ?> товаров по запросу "<?php echo htmlspecialchars($search_term); ?>" в категории "<?php echo htmlspecialchars($category_filter); ?>"
                <?php 
                }
                else if ($search_term !== '')
                {
                ?>
                    Найдено <?php echo $total_items; ?> товаров по запросу "<?php echo htmlspecialchars($search_term); ?>"
                <?php 
                }
                elseif ($category_filter !== '' && $category_filter !== 'все категории')
                { 
                ?>
                    Найдено <?php echo $total_items; ?> товаров в категории "<?php echo htmlspecialchars($category_filter); ?>"
                <?php 
                } 
                ?>

                <?php 
                if ($search_term !== '' || ($category_filter !== '' && $category_filter !== 'все категории'))
                {
                ?>
                    <a href="?" class="btn btn-sm btn-outline-secondary ms-2">Показать все товары</a>
                <?php 
                } 
                ?>
            </div>
        </div>
    </div>
    <?php 
    } 
    ?>
    
    <div class="row g-4" id="productsContainer">
        <?php 
        if ($total_items > 0)
        {
        ?>
            <?php 
            for ($i = $start_index; $i < $end_index; $i++)
            {
                $product = $filtered_products[$i];
            ?>
            <div class="col-lg-3 col-md-4 col-6 product-col">
                <div class="product-card" data-category="<?php echo $product['category']; ?>">
                    <?php 
                    if (isset($product['badge']))
                    { 
                    ?>
                        <div class="product-badge"><?php echo $product['badge']; ?></div>
                    <?php 
                    } 
                    ?>
                    <img src="../img/no-image.png" class="product-img" alt="Товар">
                    <div class="product-body">
                        <h5 class="product-title"><?php echo $product['title']; ?></h5>
                        <div class="product-price">
                            <?php 
                            if (isset($product['old_price']))
                            {
                            ?>
                                <span class="text-danger"><?php echo number_format($product['price'], 0, '', ' '); ?> ₽</span>
                                <small class="text-decoration-line-through text-muted"><?php echo number_format($product['old_price'], 0, '', ' '); ?> ₽</small>
                            <?php 
                            }
                            else
                            { 
                            ?>
                                <?php echo number_format($product['price'], 0, '', ' '); ?> ₽
                            <?php 
                            } 
                            ?>
                        </div>
                        <div class="product-actions">
                            <button class="btn btn-sm btn-outline-primary">В корзину</button>
                            <button class="btn btn-sm btn-outline-secondary">Подробнее</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            } 
            ?>
        <?php 
        }
        else
        { 
        ?>
            <div class="col-12 text-center mt-5">
                <h4>Товары не найдены</h4>
                <p>Попробуйте изменить параметры поиска</p>
                <a href="?" class="btn btn-primary">Показать все товары</a>
            </div>
        <?php 
        } 
        ?>
    </div>
    
    <?php 
    if ($show_pagination)
    { 
    ?>
    <nav aria-label="Page navigation" class="mt-5">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $current_page == 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo buildQueryString($current_page - 1, $search_term, $category_filter); ?>" tabindex="-1">Назад</a>
            </li>
            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $start_page + 4);
            
            if ($end_page - $start_page < 4) 
            {
                $start_page = max(1, $end_page - 4);
            }
            
            for ($page = $start_page; $page <= $end_page; $page++)
            { 
            ?>
                <li class="page-item <?php echo $page == $current_page ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo buildQueryString($page, $search_term, $category_filter); ?>"><?php echo $page; ?></a>
                </li>
            <?php 
            } 
            ?>
            <li class="page-item <?php echo $current_page == $total_pages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo buildQueryString($current_page + 1, $search_term, $category_filter); ?>">Вперед</a>
            </li>
        </ul>
    </nav>
    <div class="text-center text-muted mt-3">
        Страница <?php echo $current_page; ?> из <?php echo $total_pages; ?> | Показано <?php echo ($end_index - $start_index); ?> из <?php echo $total_items; ?> товаров
    </div>
    <?php 
    }
    else
    { 
    ?>
        <?php 
        if ($total_items > 0)
        { 
        ?>
        <div class="text-center text-muted mt-3">
            Показано <?php echo $total_items; ?> товаров
        </div>
        <?php 
        } 
        ?>
    <?php 
    } 
    ?>
</div>

<?php 
    require_once("footer.php"); 
?>

<?php
function buildQueryString($page, $search, $category) 
{
    $params = [];

    if ($page > 1) 
    {
        $params['page'] = $page;
    }

    if (!empty($search)) 
    {
        $params['search'] = $search;
    }

    if (!empty($category) && $category !== 'все категории') 
    {
        $params['category'] = $category;
    }

    return http_build_query($params);
}
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    function performSearch() 
    {
        let searchTerm = document.getElementById('partsSearch').value.trim();
        let categoryFilter = document.getElementById('categoryFilter').value;
        let params = new URLSearchParams();

        if (searchTerm) 
        {
            params.set('search', searchTerm);
        }

        if (categoryFilter && categoryFilter !== 'все категории') 
        {
            params.set('category', categoryFilter);
        }
        
        window.location.href = '?' + params.toString();
    }

    function resetSearch() 
    {
        document.getElementById('partsSearch').value = '';
        document.getElementById('categoryFilter').value = 'все категории';
        document.querySelector('.search-clear').style.display = 'none';
        window.location.href = '?';
    }

    document.getElementById('searchButton').addEventListener('click', performSearch);
        
    document.getElementById('partsSearch').addEventListener('keypress', function(e) 
    {
        if (e.key === 'Enter') 
        {
            performSearch();
        }
    });

    document.getElementById('categoryFilter').addEventListener('change', function() 
    {
        performSearch();
    });

    document.querySelector('.search-clear').addEventListener('click', resetSearch);

    document.getElementById('partsSearch').addEventListener('input', function() 
    {
        document.querySelector('.search-clear').style.display = this.value ? 'block' : 'none';
    });

    if (document.getElementById('partsSearch').value) 
    {
        document.querySelector('.search-clear').style.display = 'block';
    }
});
</script>
</body>
</html>