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
    <title>Автосервис - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/service-styles.css">
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
    <h1 class="text-center mb-5" style="padding-top: 85px;">Автосервис "Лал-Авто"</h1>
    <div class="row g-4 mb-5">
        <div class="col-lg-6">
            <div class="service-card h-100 p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary p-3 rounded-circle me-3">
                        <i class="bi bi-tools text-white fs-4"></i>
                    </div>
                    <h2 class="mb-0 text-primary">Наши услуги</h2>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="service-item p-3 rounded-3 border">
                            <div class="d-flex align-items-center mb-2">
                                <div class="service-icon bg-primary p-2 rounded-circle me-2">
                                    <i class="bi bi-search text-white"></i>
                                </div>
                                <h6 class="mb-0">Диагностика</h6>
                            </div>
                            <p class="small text-muted mb-0">Комплексная проверка всех систем</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item p-3 rounded-3 border">
                            <div class="d-flex align-items-center mb-2">
                                <div class="service-icon bg-success p-2 rounded-circle me-2">
                                    <i class="bi bi-gear-fill text-white"></i>
                                </div>
                                <h6 class="mb-0">Техобслуживание</h6>
                            </div>
                            <p class="small text-muted mb-0">Регулярное ТО по регламенту</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item p-3 rounded-3 border">
                            <div class="d-flex align-items-center mb-2">
                                <div class="service-icon bg-danger p-2 rounded-circle me-2">
                                    <i class="bi bi-lightning-charge text-white"></i>
                                </div>
                                <h6 class="mb-0">Двигатель</h6>
                            </div>
                            <p class="small text-muted mb-0">Ремонт и обслуживание ДВС</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item p-3 rounded-3 border">
                            <div class="d-flex align-items-center mb-2">
                                <div class="service-icon bg-warning p-2 rounded-circle me-2">
                                    <i class="bi bi-car-front text-dark"></i>
                                </div>
                                <h6 class="mb-0">Ходовая часть</h6>
                            </div>
                            <p class="small text-muted mb-0">Ремонт подвески и рулевого управления</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item p-3 rounded-3 border">
                            <div class="d-flex align-items-center mb-2">
                                <div class="service-icon bg-info p-2 rounded-circle me-2">
                                    <i class="bi bi-cpu text-white"></i>
                                </div>
                                <h6 class="mb-0">Электроника</h6>
                            </div>
                            <p class="small text-muted mb-0">Диагностика и ремонт электронных систем</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="service-item p-3 rounded-3 border">
                            <div class="d-flex align-items-center mb-2">
                                <div class="service-icon bg-secondary p-2 rounded-circle me-2">
                                    <i class="bi bi-circle text-white"></i>
                                </div>
                                <h6 class="mb-0">Шиномонтаж</h6>
                            </div>
                            <p class="small text-muted mb-0">Балансировка и сезонная смена шин</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-3 fs-4"></i>
                        <div>
                            <strong>Специальное предложение!</strong><br>
                            При записи онлайн - скидка 10% на диагностику
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-lightning-charge-fill text-warning me-2 fs-5"></i>
                        <h5 class="mb-0">Срочные услуги</h5>
                    </div>
                    <div class="small">
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Замена аккумулятора</h6>
                            <span class="fw-bold">30 мин</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <h6>Шиномонтаж</h6>
                            <span class="fw-bold">45 мин</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6>Компьютерная диагностика</h6>
                            <span class="fw-bold">20 мин</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="service-card h-100 p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary p-3 rounded-circle me-3">
                        <i class="bi bi-calendar-check text-white fs-4"></i>
                    </div>
                    <h2 class="mb-0 text-primary">Запись на сервис</h2>
                </div>
                <form id="serviceForm" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label fw-semibold">
                                <i class="bi bi-person me-1"></i>Ваше имя
                            </label>
                            <input type="text" class="form-control form-control-lg" id="name" placeholder="Иван Иванов" required>
                            <div class="invalid-feedback">Пожалуйста, введите ваше имя</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label fw-semibold">
                                <i class="bi bi-phone me-1"></i>Телефон
                            </label>
                            <input type="tel" class="form-control form-control-lg" id="phone" placeholder="+7 (900) 123-45-67" required>
                            <div class="invalid-feedback">Пожалуйста, введите телефон</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="car" class="form-label fw-semibold">
                            <i class="bi bi-car-front me-1"></i>Автомобиль
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control form-control-lg" id="car" placeholder="Марка, модель, год" required>
                        </div>
                        <div class="invalid-feedback">Пожалуйста, укажите автомобиль</div>
                    </div>
                    <div class="mb-3">
                        <label for="service" class="form-label fw-semibold">
                            <i class="bi bi-tools me-1"></i>Услуга
                        </label>
                        <select class="form-select form-select-lg" id="service" required>
                            <option value="" selected disabled>Выберите услугу</option>
                            <option value="diagnostics">Диагностика (от 1500₽)</option>
                            <option value="maintenance">Техническое обслуживание (от 3000₽)</option>
                            <option value="engine">Ремонт двигателя (от 5000₽)</option>
                            <option value="suspension">Ремонт ходовой части (от 2500₽)</option>
                            <option value="electronics">Электронные системы (от 2000₽)</option>
                            <option value="tires">Шиномонтаж (от 1000₽)</option>
                            <option value="other">Другая услуга</option>
                        </select>
                        <div class="invalid-feedback">Пожалуйста, выберите услугу</div>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label fw-semibold">
                            <i class="bi bi-calendar me-1"></i>Желаемая дата
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-clock"></i>
                            </span>
                            <input type="date" class="form-control form-control-lg" id="date" required>
                        </div>
                        <div class="invalid-feedback">Пожалуйста, выберите дату</div>
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label fw-semibold">
                            <i class="bi bi-chat-text me-1"></i>Дополнительная информация
                        </label>
                        <textarea class="form-control form-control-lg" id="message" rows="3" placeholder="Опишите проблему или особые пожелания..."></textarea>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="consent" required>
                        <label class="form-check-label small" for="consent">
                            Я согласен на обработку персональных данных
                        </label>
                        <div class="invalid-feedback">Необходимо ваше согласие</div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                        <i class="bi bi-calendar-check me-2"></i>Записаться онлайн
                    </button>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Мы перезвоним для подтверждения записи в течение 30 минут
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="service-advantages mb-5">
        <h2 class="text-center mb-4">Почему выбирают наш автосервис?</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="advantage-card text-center p-3">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h4>Опытные мастера</h4>
                    <p>Работают специалисты с опытом от 5 лет</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="advantage-card text-center p-3">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4>Гарантия качества</h4>
                    <p>Гарантия на все виды работ до 2 лет</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="advantage-card text-center p-3">
                    <div class="advantage-icon mb-3">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h4>Доступные цены</h4>
                    <p>Цены ниже рыночных на 10-15%</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>