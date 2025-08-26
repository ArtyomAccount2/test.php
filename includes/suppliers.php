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
    <title>Поставщикам - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/suppliers-styles.css">
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
            <h1 class="mb-3" style="padding-top: 60px;">Поставщикам</h1>
            <p class="lead">Сотрудничество с компанией Лал-Авто</p>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="cooperation-card text-center p-4">
                <div class="cooperation-icon mb-3">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h4>Стабильные заказы</h4>
                <p>Регулярные поставки и стабильный объем закупок</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="cooperation-card text-center p-4">
                <div class="cooperation-icon mb-3">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <h4>Своевременная оплата</h4>
                <p>Четкие сроки оплаты и прозрачные условия расчетов</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="cooperation-card text-center p-4">
                <div class="cooperation-icon mb-3">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h4>Долгосрочное партнерство</h4>
                <p>Работаем с проверенными поставщиками годами</p>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="requirements-section">
                <h3 class="mb-4"><i class="bi bi-check-circle"></i> Требования к поставщикам</h3>
                <div class="requirements-list">
                    <div class="requirement-item">
                        <div class="requirement-icon">
                            <i class="bi bi-1-circle"></i>
                        </div>
                        <div class="requirement-content">
                            <h5>Качество продукции</h5>
                            <p>Соответствие ГОСТ, ТУ и международным стандартам качества</p>
                        </div>
                    </div>
                    
                    <div class="requirement-item">
                        <div class="requirement-icon">
                            <i class="bi bi-2-circle"></i>
                        </div>
                        <div class="requirement-content">
                            <h5>Сертификация</h5>
                            <p>Наличие всех необходимых сертификатов и разрешительной документации</p>
                        </div>
                    </div>

                    <div class="requirement-item">
                        <div class="requirement-icon">
                            <i class="bi bi-3-circle"></i>
                        </div>
                        <div class="requirement-content">
                            <h5>Стабильность поставок</h5>
                            <p>Соблюдение согласованных сроков и объемов поставок</p>
                        </div>
                    </div>

                    <div class="requirement-item">
                        <div class="requirement-icon">
                            <i class="bi bi-4-circle"></i>
                        </div>
                        <div class="requirement-content">
                            <h5>Конкурентные цены</h5>
                            <p>Предложение рыночных цен и гибкие условия сотрудничества</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="cooperation-form-section">
                <h3 class="mb-4"><i class="bi bi-envelope"></i> Стать поставщиком</h3>
                <form id="supplierForm">
                    <div class="mb-3">
                        <label for="companyName" class="form-label">Название компании *</label>
                        <input type="text" class="form-control" id="companyName" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactPerson" class="form-label">Контактное лицо *</label>
                        <input type="text" class="form-control" id="contactPerson" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон *</label>
                        <input type="tel" class="form-control" id="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="productCategory" class="form-label">Категория товаров *</label>
                        <select class="form-select" id="productCategory" required>
                            <option value="" selected disabled>Выберите категорию</option>
                            <option>Автозапчасти</option>
                            <option>Масла и жидкости</option>
                            <option>Аксессуары</option>
                            <option>Автохимия</option>
                            <option>Шины и диски</option>
                            <option>Инструменты</option>
                            <option>Другое</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">О компании и предлагаемой продукции</label>
                        <textarea class="form-control" id="message" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Прайс-лист (опционально)</label>
                        <input type="file" class="form-control" id="file">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agree" required>
                        <label class="form-check-label" for="agree">Я согласен на обработку персональных данных</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-send"></i> Отправить заявку
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <div class="partners-section">
                <h3 class="mb-4 text-center"><i class="bi bi-people"></i> Наши партнеры</h3>
                <div class="partners-grid">
                    <div class="partner-item">
                        <img src="../img/no-image.png" alt="Bosch" class="partner-logo">
                        <span class="partner-name">Bosch</span>
                    </div>
                    <div class="partner-item">
                        <img src="../img/no-image.png" alt="Castrol" class="partner-logo">
                        <span class="partner-name">Castrol</span>
                    </div>
                    <div class="partner-item">
                        <img src="../img/no-image.png" alt="Mobil" class="partner-logo">
                        <span class="partner-name">Mobil</span>
                    </div>
                    <div class="partner-item">
                        <img src="../img/no-image.png" alt="Brembo" class="partner-logo">
                        <span class="partner-name">Brembo</span>
                    </div>
                    <div class="partner-item">
                        <img src="../img/no-image.png" alt="Mann-Filter" class="partner-logo">
                        <span class="partner-name">Mann-Filter</span>
                    </div>
                    <div class="partner-item">
                        <img src="../img/no-image.png" alt="NGK" class="partner-logo">
                        <span class="partner-name">NGK</span>
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
</body>
</html>