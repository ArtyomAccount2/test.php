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
    <title>Аксессуары - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/accessories-styles.css">
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
    <div class="row mb-4 align-items-center" style="padding-top: 85px;">
        <div class="col-md-6">
            <h1 class="mb-0">Автоаксессуары</h1>
            <p class="text-muted">Найдите идеальные аксессуары для вашего автомобиля</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="#" class="btn btn-primary">
                <i class="bi bi-gift-fill"></i> Подарочные сертификаты
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="bi bi-funnel"></i> Фильтры</h5>
                    <div class="mb-4">
                        <h6 class="mb-3">Цена, ₽</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <input type="number" class="form-control form-control-sm" placeholder="От" style="width: 45%">
                            <input type="number" class="form-control form-control-sm" placeholder="До" style="width: 45%">
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="mb-3">Категории</h6>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Для салона
                                <span class="badge bg-primary rounded-pill">24</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Для экстерьера
                                <span class="badge bg-primary rounded-pill">18</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Электроника
                                <span class="badge bg-primary rounded-pill">12</span>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                Уход за авто
                                <span class="badge bg-primary rounded-pill">9</span>
                            </a>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="mb-3">Бренды</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="brand1" checked>
                            <label class="form-check-label" for="brand1">AutoStyle</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="brand2" checked>
                            <label class="form-check-label" for="brand2">CarMate</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="brand3">
                            <label class="form-check-label" for="brand3">CoverKing</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="brand4">
                            <label class="form-check-label" for="brand4">WeatherTech</label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h6 class="mb-3">Наличие</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="availability" id="avail1" checked>
                            <label class="form-check-label" for="avail1">Все товары</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="availability" id="avail2">
                            <label class="form-check-label" for="avail2">В наличии</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="availability" id="avail3">
                            <label class="form-check-label" for="avail3">Под заказ</label>
                        </div>
                    </div>
                    <button class="btn btn-primary w-100">Применить фильтры</button>
                    <button class="btn btn-outline-secondary w-100 mt-2">Сбросить</button>
                </div>
            </div>
        </div>
        <div class="col-lg-9 col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <span class="me-2 text-muted">Сортировка:</span>
                            <select class="form-select form-select-sm" style="width: auto;">
                                <option selected>По популярности</option>
                                <option>По цене (сначала дешевые)</option>
                                <option>По цене (сначала дорогие)</option>
                                <option>По новизне</option>
                                <option>По рейтингу</option>
                            </select>
                        </div>
                        <div class="text-muted">Найдено 42 товара</div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <div class="badge bg-danger position-absolute mt-2 ms-2">-15%</div>
                        <div class="card-img-top p-3">
                            <img src="../img/no-image.png" class="img-fluid" alt="Чехлы на сиденья">
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">AutoStyle</span>
                                <div class="small">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span>4.8</span>
                                </div>
                            </div>
                            <h5 class="card-title mb-2">Чехлы на сиденья Premium</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-danger fw-bold">4 290 ₽</span>
                                    <span class="text-decoration-line-through text-muted small ms-2">5 050 ₽</span>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <div class="badge bg-success position-absolute mt-2 ms-2">Новинка</div>
                        <div class="card-img-top p-3">
                            <img src="../img/no-image.png" class="img-fluid" alt="Коврики">
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">WeatherTech</span>
                                <div class="small">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span>4.9</span>
                                </div>
                            </div>
                            <h5 class="card-title mb-2">Коврики в салон 3D</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">6 790 ₽</span>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <div class="card-img-top p-3">
                            <img src="../img/no-image.png" class="img-fluid" alt="Органайзер">
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">CarMate</span>
                                <div class="small">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span>4.5</span>
                                </div>
                            </div>
                            <h5 class="card-title mb-2">Органайзер для багажника</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">3 490 ₽</span>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <div class="badge bg-info position-absolute mt-2 ms-2">Хит</div>
                        <div class="card-img-top p-3">
                            <img src="../img/no-image.png" class="img-fluid" alt="Ароматизатор">
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted small">Air Spencer</span>
                                <div class="small">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <span>4.7</span>
                                </div>
                            </div>
                            <h5 class="card-title mb-2">Ароматизатор CS-X3</h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">790 ₽</span>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>