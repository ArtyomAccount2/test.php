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
    <title>Масла и тех. жидкости - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/oils-styles.css">
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
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Главная</a></li>
            <li class="breadcrumb-item active" aria-current="page">Масла и тех. жидкости</li>
        </ol>
    </nav>
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="mb-0">Масла и технические жидкости</h1>
        </div>
        <div class="col-md-6 text-md-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#oilSelectorModal">
                <i class="bi bi-question-circle"></i> Подобрать масло
            </button>
        </div>
    </div>
    <div class="oil-categories mb-5">
        <h2 class="mb-4"><i class="bi bi-filter-square"></i> Категории</h2>
        <div class="row g-4">
            <?php
            $categories = [
                ['icon' => 'bi-droplet', 'title' => 'Моторные масла', 'count' => '124 товара', 'color' => 'primary'],
                ['icon' => 'bi-gear', 'title' => 'Трансмиссионные масла', 'count' => '56 товаров', 'color' => 'success'],
                ['icon' => 'bi-snow', 'title' => 'Тормозные жидкости', 'count' => '23 товара', 'color' => 'danger'],
                ['icon' => 'bi-water', 'title' => 'Охлаждающие жидкости', 'count' => '34 товара', 'color' => 'info'],
                ['icon' => 'bi-wind', 'title' => 'Жидкости ГУР', 'count' => '18 товаров', 'color' => 'warning'],
                ['icon' => 'bi-droplet-half', 'title' => 'Антифризы', 'count' => '42 товара', 'color' => 'secondary'],
                ['icon' => 'bi-brightness-high', 'title' => 'Специальные жидкости', 'count' => '31 товар', 'color' => 'dark'],
                ['icon' => 'bi-archive', 'title' => 'Комплекты', 'count' => '15 товаров', 'color' => 'primary']
            ];
            
            foreach ($categories as $category) 
            {
                echo '
                <div class="col-md-3 col-6">
                    <div class="category-card card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="category-icon text-'.$category['color'].' mb-3">
                                <i class="bi '.$category['icon'].'" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="category-title card-title">'.$category['title'].'</h5>
                            <div class="category-count text-muted small">'.$category['count'].'</div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 text-center">
                            <a href="#" class="btn btn-sm btn-outline-'.$category['color'].' stretched-link">Смотреть</a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
    <div class="filter-section mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-funnel"></i> Фильтры</h2>
            <div>
                <button class="btn btn-sm btn-outline-secondary me-2" id="resetFilters">Сбросить</button>
                <button class="btn btn-sm btn-primary" id="applyFilters">Применить</button>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="filter-group mb-3">
                    <label class="form-label filter-title">Бренд</label>
                    <select class="form-select">
                        <option selected>Все бренды</option>
                        <option>Castrol</option>
                        <option>Mobil</option>
                        <option>Liqui Moly</option>
                        <option>Shell</option>
                        <option>Total</option>
                        <option>Motul</option>
                        <option>ZIC</option>
                        <option>ELF</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group mb-3">
                    <label class="form-label filter-title">Вязкость</label>
                    <select class="form-select">
                        <option selected>Все</option>
                        <option>0W-20</option>
                        <option>0W-30</option>
                        <option>5W-30</option>
                        <option>5W-40</option>
                        <option>10W-40</option>
                        <option>15W-40</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group mb-3">
                    <label class="form-label filter-title">Тип</label>
                    <select class="form-select">
                        <option selected>Все</option>
                        <option>Синтетическое</option>
                        <option>Полусинтетическое</option>
                        <option>Минеральное</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="filter-group mb-3">
                    <label class="form-label filter-title">Объем</label>
                    <select class="form-select">
                        <option selected>Все</option>
                        <option>1 л</option>
                        <option>4 л</option>
                        <option>5 л</option>
                        <option>20 л</option>
                        <option>60 л</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="products-section mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-box-seam"></i> Товары</h2>
            <div class="d-flex">
                <div class="dropdown me-2">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        Сортировка
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                        <li><a class="dropdown-item" href="#">По популярности</a></li>
                        <li><a class="dropdown-item" href="#">По цене (возрастание)</a></li>
                        <li><a class="dropdown-item" href="#">По цене (убывание)</a></li>
                        <li><a class="dropdown-item" href="#">По названию</a></li>
                    </ul>
                </div>
                <div class="input-group" style="width: 200px;">
                    <input type="text" class="form-control" placeholder="Поиск...">
                    <button class="btn btn-outline-secondary" type="button"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <?php
            $products = [
                ['title' => 'Castrol EDGE 5W-30', 'art' => '15698E4', 'volume' => '4 л', 'price' => '3 890', 'stock' => true, 'hit' => true],
                ['title' => 'Mobil Super 3000 X1 5W-40', 'art' => '152343', 'volume' => '4 л', 'price' => '3 450', 'stock' => true, 'hit' => false],
                ['title' => 'Liqui Moly Special Tec AA 5W-30', 'art' => '1123DE', 'volume' => '5 л', 'price' => '4 210', 'stock' => true, 'hit' => true],
                ['title' => 'Shell Helix HX7 10W-40', 'art' => '87654F', 'volume' => '4 л', 'price' => '2 890', 'stock' => false, 'hit' => false],
                ['title' => 'Total Quartz 9000 5W-40', 'art' => 'TQ9000', 'volume' => '5 л', 'price' => '3 650', 'stock' => true, 'hit' => false],
                ['title' => 'Motul 8100 X-clean 5W-30', 'art' => 'M8100', 'volume' => '5 л', 'price' => '4 890', 'stock' => true, 'hit' => true],
                ['title' => 'ZIC X9 5W-30', 'art' => 'ZX9-5W30', 'volume' => '4 л', 'price' => '2 990', 'stock' => true, 'hit' => false],
                ['title' => 'ELF Evolution 900 NF 5W-40', 'art' => 'ELF900', 'volume' => '5 л', 'price' => '3 750', 'stock' => true, 'hit' => false]
            ];
            
            foreach ($products as $product) 
            {
                echo '
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="product-card card h-100">
                        '.($product['hit'] ? '<span class="badge bg-danger position-absolute top-0 start-0 m-2">Хит</span>' : '').'
                        <img src="../img/no-image.png" class="product-img card-img-top p-3" alt="'.$product['title'].'">
                        <div class="card-body">
                            <h5 class="product-title card-title">'.$product['title'].'</h5>
                            <p class="product-meta text-muted small mb-2">Арт. '.$product['art'].', '.$product['volume'].'</p>
                            <h4 class="product-price mb-3">'.$product['price'].' ₽</h4>
                            <p class="product-stock '.($product['stock'] ? 'text-success' : 'text-danger').' mb-3">
                                <i class="bi '.($product['stock'] ? 'bi-check-circle' : 'bi-x-circle').'"></i> 
                                '.($product['stock'] ? 'В наличии' : 'Нет в наличии').'
                            </p>
                            <div class="product-actions d-grid gap-2">
                                <button class="btn btn-sm '.($product['stock'] ? 'btn-primary' : 'btn-outline-secondary disabled').'">
                                    <i class="bi bi-cart-plus"></i> В корзину
                                </button>
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-info-circle"></i> Подробнее
                                </button>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Назад</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Вперед</a>
                </li>
            </ul>
        </nav>
    </div>
    <div class="specs-section mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="bi bi-card-list"></i> Спецификации масел</h2>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#specsTable">
                <i class="bi bi-arrows-collapse"></i> Свернуть/развернуть
            </button>
        </div>
        <div class="collapse show" id="specsTable">
            <div class="table-responsive">
                <table class="specs-table table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Спецификация</th>
                            <th>Описание</th>
                            <th>Применение</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>API SN/CF</strong></td>
                            <td>Для бензиновых и дизельных двигателей</td>
                            <td>Современные двигатели</td>
                        </tr>
                        <tr>
                            <td><strong>ACEA A3/B4</strong></td>
                            <td>Для высоконагруженных двигателей</td>
                            <td>Европейские автомобили</td>
                        </tr>
                        <tr>
                            <td><strong>VW 502.00/505.00</strong></td>
                            <td>Для двигателей VW, Audi, Skoda, Seat</td>
                            <td>Германские автомобили</td>
                        </tr>
                        <tr>
                            <td><strong>BMW Longlife-01</strong></td>
                            <td>Для двигателей BMW с увеличенным интервалом замены</td>
                            <td>BMW, Mini</td>
                        </tr>
                        <tr>
                            <td><strong>MB-Approval 229.5</strong></td>
                            <td>Для двигателей Mercedes-Benz</td>
                            <td>Mercedes-Benz</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="oilSelectorModal" tabindex="-1" aria-labelledby="oilSelectorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="oilSelectorModalLabel"><i class="bi bi-question-circle"></i> Подбор масла</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Марка автомобиля</label>
                        <select class="form-select">
                            <option selected disabled>Выберите марку</option>
                            <option>Audi</option>
                            <option>BMW</option>
                            <option>Mercedes-Benz</option>
                            <option>Volkswagen</option>
                            <option>Toyota</option>
                            <option>Honda</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Модель</label>
                        <select class="form-select" disabled>
                            <option selected disabled>Сначала выберите марку</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Год выпуска</label>
                        <select class="form-select" disabled>
                            <option selected disabled>Сначала выберите модель</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Двигатель</label>
                        <select class="form-select" disabled>
                            <option selected disabled>Сначала выберите год выпуска</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                <button type="button" class="btn btn-primary" disabled>Подобрать</button>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('resetFilters').addEventListener('click', function() 
{
    let selects = document.querySelectorAll('.filter-section select');

    selects.forEach(select => {
        select.selectedIndex = 0;
    });
});

document.getElementById('applyFilters').addEventListener('click', function() 
{
    alert('Фильтры применены! (это демо, в реальном приложении здесь будет AJAX запрос)');
});
</script>
</body>
</html>