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
    <title>Покупателям - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/customers-styles.css">
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
            <h1 class="mb-3" style="padding-top: 60px;">Покупателям</h1>
            <p class="lead">Вся информация для наших клиентов</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="info-card h-100">
                <div class="info-card-icon bg-primary">
                    <i class="bi bi-truck"></i>
                </div>
                <h3>Доставка</h3>
                <p>Узнайте все о способах и условиях доставки ваших заказов</p>
                <a href="delivery.php" class="btn btn-outline-primary">Подробнее</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="info-card h-100">
                <div class="info-card-icon bg-success">
                    <i class="bi bi-credit-card"></i>
                </div>
                <h3>Оплата</h3>
                <p>Различные способы оплаты для вашего удобства</p>
                <a href="delivery.php#payment" class="btn btn-outline-success">Подробнее</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="info-card h-100">
                <div class="info-card-icon bg-warning">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <h3>Возврат и обмен</h3>
                <p>Условия возврата и обмена товаров</p>
                <a href="return.php" class="btn btn-outline-warning">Подробнее</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="info-card h-100">
                <div class="info-card-icon bg-info">
                    <i class="bi bi-percent"></i>
                </div>
                <h3>Скидки и акции</h3>
                <p>Текущие акции и программы лояльности</p>
                <a href="promotions.php" class="btn btn-outline-info">Подробнее</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="info-card h-100">
                <div class="info-card-icon bg-danger">
                    <i class="bi bi-patch-question"></i>
                </div>
                <h3>FAQ</h3>
                <p>Ответы на часто задаваемые вопросы</p>
                <a href="faq.php" class="btn btn-outline-danger">Подробнее</a>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="info-card h-100">
                <div class="info-card-icon bg-secondary">
                    <i class="bi bi-person-plus"></i>
                </div>
                <h3>Корпоративным клиентам</h3>
                <p>Специальные условия для организаций</p>
                <a href="corporate.php" class="btn btn-outline-secondary">Подробнее</a>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto">
            <div class="card faq-section">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="bi bi-question-circle"></i> Частые вопросы</h3>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne">
                                    Как узнать, подойдет ли запчасть к моему автомобилю?
                                </button>
                            </h2>
                            <div id="faqOne" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    Вы можете воспользоваться нашим онлайн-каталогом, указав марку, модель и год выпуска вашего автомобиля. Также наши консультанты всегда готовы помочь вам по телефону или в магазине. Для точного подбора рекомендуется знать VIN-код автомобиля.
                                </div>
                            </div>
                        </div>
                        <!-- Другие вопросы -->
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