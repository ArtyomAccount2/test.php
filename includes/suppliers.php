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
            header("Location: ../index.php");
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
        <div class="col-12">
            <h1 class="mb-3" style="padding-top: 60px;">Поставщикам</h1>
            <p class="lead">Информация для поставщиков автозапчастей и аксессуаров</p>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="supplier-info-card">
                <h3 class="mb-4"><i class="bi bi-handshake"></i> Сотрудничество</h3>
                <p>Компания "Лал-Авто" заинтересована в расширении круга поставщиков качественных автозапчастей, масел и аксессуаров.</p>
                <p>Мы рассматриваем предложения от производителей и официальных дистрибьюторов автокомпонентов.</p>
                <p><strong>Наши приоритеты:</strong></p>
                <ul>
                    <li>Оригинальные запчасти и аналоги проверенных производителей</li>
                    <li>Конкурентные цены и гибкие условия поставки</li>
                    <li>Наличие сертификатов соответствия</li>
                    <li>Гарантия качества продукции</li>
                    <li>Стабильность поставок</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="supplier-info-card">
                <h3 class="mb-4"><i class="bi bi-card-checklist"></i> Требования к поставщикам</h3>
                <p><strong>Для начала сотрудничества необходимо предоставить:</strong></p>
                <ol>
                    <li>Реквизиты компании (сканы учредительных документов)</li>
                    <li>Прайс-лист в электронном виде (Excel, PDF)</li>
                    <li>Информацию о условиях работы (скидки, отсрочка платежа)</li>
                    <li>Сроки и условия поставки</li>
                    <li>Сертификаты на продукцию (если требуется)</li>
                    <li>Образцы продукции или каталоги</li>
                </ol>
                <p>Все документы можно отправить на электронную почту <a href="mailto:suppliers@lal-auto.ru">suppliers@lal-auto.ru</a> или привезти в наш офис по предварительной договоренности.</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="supplier-form-card">
                <h3 class="mb-4 text-center"><i class="bi bi-envelope"></i> Форма для поставщиков</h3>
                <form id="supplierForm">
                    <div class="mb-3">
                        <label for="supplierName" class="form-label">Название компании *</label>
                        <input type="text" class="form-control" id="supplierName" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplierContact" class="form-label">Контактное лицо *</label>
                        <input type="text" class="form-control" id="supplierContact" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplierPhone" class="form-label">Телефон *</label>
                        <input type="tel" class="form-control" id="supplierPhone" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplierEmail" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="supplierEmail" required>
                    </div>
                    <div class="mb-3">
                        <label for="supplierProducts" class="form-label">Какие товары предлагаете? *</label>
                        <textarea class="form-control" id="supplierProducts" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="supplierBrands" class="form-label">Какие бренды представляете?</label>
                        <textarea class="form-control" id="supplierBrands" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="supplierFiles" class="form-label">Прикрепить файлы (прайс, реквизиты)</label>
                        <input type="file" class="form-control" id="supplierFiles" multiple>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="supplierAgree" required>
                        <label class="form-check-label" for="supplierAgree">Я согласен на обработку персональных данных</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-send"></i> Отправить заявку
                    </button>
                </form>
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