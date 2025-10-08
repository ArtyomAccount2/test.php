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
        <div class="col-12 text-center">
            <h1 class="mb-3" style="padding-top: 60px;">Покупателям</h1>
            <p class="lead">Всё, что нужно знать о покупках в Лал-Авто</p>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="info-card text-center p-4">
                <div class="info-icon mb-3">
                    <i class="bi bi-truck"></i>
                </div>
                <h4>Быстрая доставка</h4>
                <p>Доставка по Калининграду за 1 день, по области - 1-3 дня. Бесплатная доставка от 5000 ₽</p>
                <a href="delivery.php" class="btn btn-outline-primary mt-2">Подробнее</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card text-center p-4">
                <div class="info-icon mb-3">
                    <i class="bi bi-credit-card"></i>
                </div>
                <h4>Удобная оплата</h4>
                <p>Наличные, карты, онлайн-платежи, безналичный расчет. Рассрочка и кредит доступны</p>
                <a href="delivery.php#payment" class="btn btn-outline-primary mt-2">Подробнее</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card text-center p-4">
                <div class="info-icon mb-3">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h4>Гарантия качества</h4>
                <p>Только оригинальные запчасти и сертифицированные товары. Гарантия до 2 лет</p>
                <a href="#guarantee" class="btn btn-outline-primary mt-2">Подробнее</a>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="faq-section">
                <h3 class="mb-4"><i class="bi bi-question-circle"></i> Частые вопросы</h3>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Как подобрать запчасти для моего автомобиля?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show">
                            <div class="accordion-body">
                                Вы можете воспользоваться нашим онлайн-каталогом, указав марку, модель и год выпуска автомобиля. Также наши консультанты всегда готовы помочь по телефону +7 (4012) 65-65-65.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Есть ли скидки для постоянных клиентов?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Да, у нас действует программа лояльности. При покупке карты постоянного клиента вы получаете скидку 5% на все покупки. При суммарных покупках от 50 000 ₽ скидка увеличивается до 7%, от 100 000 ₽ - до 10%.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Можно ли вернуть товар?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse">
                            <div class="accordion-body">
                                Да, в течение 14 дней с момента покупки при сохранении товарного вида, упаковки и наличии чека. Некоторые товары (личные предметы, расходники) возврату не подлежат.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="benefits-section">
                <h3 class="mb-4"><i class="bi bi-star"></i> Преимущества покупок у нас</h3>
                <div class="benefits-list">
                    <div class="benefit-item d-flex align-items-center mb-3">
                        <div class="benefit-icon me-3">
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                        <div>
                            <h5>Широкий ассортимент</h5>
                            <p class="mb-0">Более 50 000 наименований запчастей и аксессуаров</p>
                        </div>
                    </div>
                    <div class="benefit-item d-flex align-items-center mb-3">
                        <div class="benefit-icon me-3">
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                        <div>
                            <h5>Профессиональные консультации</h5>
                            <p class="mb-0">Опытные менеджеры помогут с подбором</p>
                        </div>
                    </div>
                    <div class="benefit-item d-flex align-items-center mb-3">
                        <div class="benefit-icon me-3">
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                        <div>
                            <h5>Сервисный центр</h5>
                            <p class="mb-0">Установка и гарантийное обслуживание</p>
                        </div>
                    </div>
                    <div class="benefit-item d-flex align-items-center mb-3">
                        <div class="benefit-icon me-3">
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                        <div>
                            <h5>Техническая поддержка</h5>
                            <p class="mb-0">Помощь в установке и эксплуатации</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5" id="guarantee">
        <div class="col-12">
            <div class="guarantee-section p-4">
                <h3 class="mb-4 text-center"><i class="bi bi-shield-check"></i> Гарантийные обязательства</h3>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Что покрывается гарантией:</h5>
                        <ul>
                            <li>Заводские дефекты и брак</li>
                            <li>Несоответствие техническим характеристикам</li>
                            <li>Преждевременный износ при правильной эксплуатации</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5>Условия гарантии:</h5>
                        <ul>
                            <li>Срок гарантии: 6-24 месяца (зависит от товара)</li>
                            <li>Наличие товарного чека или документа покупки</li>
                            <li>Соблюдение правил эксплуатации</li>
                            <li>Установка квалифицированными специалистами</li>
                        </ul>
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
</body>
</html>