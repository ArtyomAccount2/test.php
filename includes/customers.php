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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
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

        let loyaltyCards = document.querySelectorAll('.loyalty-card');

        loyaltyCards.forEach(card => {
            card.addEventListener('mouseenter', function() 
            {
                this.style.transform = 'translateY(-10px) scale(1.05)';
            });
            card.addEventListener('mouseleave', function() 
            {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        let progressItems = document.querySelectorAll('.progress-item');

        progressItems.forEach(item => {
            let progressBar = item.querySelector('.progress-bar');
            let targetWidth = progressBar.getAttribute('data-progress');
            setTimeout(() => {
                progressBar.style.width = targetWidth + '%';
            }, 300);
        });

        let accordionItems = document.querySelectorAll('.accordion-item');

        accordionItems.forEach(item => {
            item.addEventListener('mouseenter', function() 
            {
                this.style.transform = 'translateX(5px)';
            });
            item.addEventListener('mouseleave', function() 
            {
                this.style.transform = 'translateX(0)';
            });
        });
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5 pt-4">
    <div class="hero-customers text-center mb-5">
        <h1 class="display-4 fw-bold text-primary mb-3" style="padding-top: 60px;">Покупателям</h1>
        <p class="lead-text fs-5 text-muted">Всё, что нужно знать о покупках в Лал-Авто</p>
        <div class="stats-cards d-flex justify-content-center gap-4 mt-4 flex-wrap">
            <div class="stat-card bg-primary text-white rounded-3 p-3">
                <div class="stat-number fw-bold fs-2">50,000+</div>
                <div class="stat-label">товаров в каталоге</div>
            </div>
            <div class="stat-card bg-success text-white rounded-3 p-3">
                <div class="stat-number fw-bold fs-2">12</div>
                <div class="stat-label">магазинов</div>
            </div>
            <div class="stat-card bg-warning text-white rounded-3 p-3">
                <div class="stat-number fw-bold fs-2">8</div>
                <div class="stat-label">лет на рынке</div>
            </div>
            <div class="stat-card bg-info text-white rounded-3 p-3">
                <div class="stat-number fw-bold fs-2">24/7</div>
                <div class="stat-label">поддержка</div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="info-card text-center p-4 h-100">
                <div class="info-icon mb-3">
                    <i class="bi bi-truck fs-1"></i>
                </div>
                <h4 class="fw-bold mb-3">Быстрая доставка</h4>
                <p class="text-muted mb-4">Доставка по Калининграду за 1 день, по области - 1-3 дня. Бесплатная доставка от 5000 ₽</p>
                <a href="delivery.php" class="btn btn-primary btn-lg w-100">Подробнее о доставке</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card text-center p-4 h-100">
                <div class="info-icon mb-3">
                    <i class="bi bi-credit-card fs-1"></i>
                </div>
                <h4 class="fw-bold mb-3">Удобная оплата</h4>
                <p class="text-muted mb-4">Наличные, карты, онлайн-платежи, безналичный расчет. Рассрочка и кредит доступны</p>
                <a href="delivery.php#payment" class="btn btn-primary btn-lg w-100">Способы оплаты</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card text-center p-4 h-100">
                <div class="info-icon mb-3">
                    <i class="bi bi-shield-check fs-1"></i>
                </div>
                <h4 class="fw-bold mb-3">Гарантия качества</h4>
                <p class="text-muted mb-4">Только оригинальные запчасти и сертифицированные товары. Гарантия до 2 лет</p>
                <a href="#guarantee" class="btn btn-primary btn-lg w-100">Гарантийные условия</a>
            </div>
        </div>
    </div>
    <div class="loyalty-program bg-gradient-primary rounded-4 p-5 text-white mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-3">Программа лояльности</h2>
                <p class="fs-5 mb-4">Получайте бонусы за каждую покупку и экономьте до 15%</p>
                <div class="loyalty-levels row g-3">
                    <div class="col-md-4">
                        <div class="loyalty-card bg-white text-dark rounded-3 p-3 text-center h-100 d-flex flex-column justify-content-around">
                            <div class="level-badge bg-warning text-white rounded-circle mx-auto mb-3">1</div>
                            <h5 class="fw-bold">Стандарт</h5>
                            <p class="small text-muted">При покупке от 5,000 ₽</p>
                            <div class="discount fw-bold text-primary fs-4">5%</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="loyalty-card bg-white text-dark rounded-3 p-3 text-center h-100 d-flex flex-column justify-content-around">
                            <div class="level-badge bg-success text-white rounded-circle mx-auto mb-3">2</div>
                            <h5 class="fw-bold">Премиум</h5>
                            <p class="small text-muted">При покупке от 50,000 ₽</p>
                            <div class="discount fw-bold text-primary fs-4">10%</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="loyalty-card bg-white text-dark rounded-3 p-3 text-center h-100 d-flex flex-column justify-content-around">
                            <div class="level-badge bg-danger text-white rounded-circle mx-auto mb-3">3</div>
                            <h5 class="fw-bold">VIP</h5>
                            <p class="small text-muted">При покупке от 100,000 ₽</p>
                            <div class="discount fw-bold text-primary fs-4">15%</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="loyalty-cta bg-white rounded-3 p-4 text-dark">
                    <h4 class="fw-bold mb-3">Получите карту</h4>
                    <p class="text-muted mb-4">Оформите карту лояльности в любом магазине</p>
                    <a href="shops.php" class="btn btn-primary btn-lg w-100">Найти магазин</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-5 mb-5">
        <div class="col-lg-6">
            <div class="faq-section">
                <h2 class="mb-4 fw-bold"><i class="bi bi-question-circle text-primary me-2"></i>Частые вопросы</h2>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button rounded-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Как подобрать запчасти для моего автомобиля?
                            </button>
                        </h3>
                        <div id="faq1" class="accordion-collapse collapse show">
                            <div class="accordion-body bg-light rounded-bottom-3">
                                Вы можете воспользоваться нашим онлайн-каталогом, указав марку, модель и год выпуска автомобиля. Также наши консультанты всегда готовы помочь по телефону +7 (4012) 65-65-65 или в любом из наших магазинов.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Есть ли скидки для постоянных клиентов?
                            </button>
                        </h3>
                        <div id="faq2" class="accordion-collapse collapse">
                            <div class="accordion-body bg-light rounded-bottom-3">
                                Да, у нас действует программа лояльности. При покупке карты постоянного клиента вы получаете скидку 5% на все покупки. При суммарных покупках от 50 000 ₽ скидка увеличивается до 7%, от 100 000 ₽ - до 10%. Также регулярно проводятся акции и специальные предложения.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Можно ли вернуть товар?
                            </button>
                        </h3>
                        <div id="faq3" class="accordion-collapse collapse">
                            <div class="accordion-body bg-light rounded-bottom-3">
                                Да, в течение 14 дней с момента покупки при сохранении товарного вида, упаковки и наличии чека. Некоторые товары (личные предметы, расходники) возврату не подлежат. Подробности уточняйте у наших менеджеров.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Предоставляете ли вы установку запчастей?
                            </button>
                        </h3>
                        <div id="faq4" class="accordion-collapse collapse">
                            <div class="accordion-body bg-light rounded-bottom-3">
                                Да, в наших сервисных центрах вы можете заказать профессиональную установку приобретенных запчастей. Мы предоставляем гарантию как на запчасти, так и на работы по установке.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="benefits-section">
                <h2 class="mb-4 fw-bold"><i class="bi bi-star text-primary me-2"></i>Наши преимущества</h2>
                <div class="benefits-grid">
                    <div class="benefit-item bg-white rounded-3 p-4 mb-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="benefit-icon me-3">
                                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Широкий ассортимент</h5>
                                <p class="text-muted mb-0">Более 50 000 наименований запчастей и аксессуаров для всех марок автомобилей</p>
                            </div>
                        </div>
                    </div>
                    <div class="benefit-item bg-white rounded-3 p-4 mb-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="benefit-icon me-3">
                                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Профессиональные консультации</h5>
                                <p class="text-muted mb-0">Опытные менеджеры с техническим образованием помогут с подбором</p>
                            </div>
                        </div>
                    </div>
                    <div class="benefit-item bg-white rounded-3 p-4 mb-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="benefit-icon me-3">
                                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Сервисный центр</h5>
                                <p class="text-muted mb-0">Современное оборудование и квалифицированные специалисты для установки и обслуживания</p>
                            </div>
                        </div>
                    </div>
                    <div class="benefit-item bg-white rounded-3 p-4 mb-3 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="benefit-icon me-3">
                                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Техническая поддержка</h5>
                                <p class="text-muted mb-0">Круглосуточная помощь в установке и эксплуатации приобретенных товаров</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-5" id="guarantee">
        <div class="col-12">
            <div class="guarantee-section bg-white rounded-4 p-5 shadow-sm">
                <h2 class="text-center mb-5 fw-bold"><i class="bi bi-shield-check text-primary me-2"></i>Гарантийные обязательства</h2>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="guarantee-card bg-light rounded-3 p-4 h-100">
                            <h4 class="fw-bold text-primary mb-3">Что покрывается гарантией:</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Заводские дефекты и брак</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Несоответствие техническим характеристикам</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Преждевременный износ при правильной эксплуатации</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Конструктивные недостатки</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="guarantee-card bg-light rounded-3 p-4 h-100">
                            <h4 class="fw-bold text-primary mb-3">Условия гарантии:</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-clock-fill text-warning me-2"></i>Срок гарантии: 6-24 месяца (зависит от товара)</li>
                                <li class="mb-2"><i class="bi bi-receipt text-warning me-2"></i>Наличие товарного чека или документа покупки</li>
                                <li class="mb-2"><i class="bi bi-gear-fill text-warning me-2"></i>Соблюдение правил эксплуатации</li>
                                <li class="mb-2"><i class="bi bi-tools text-warning me-2"></i>Установка квалифицированными специалистами</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="purchase-process bg-light rounded-4 p-5">
        <h2 class="text-center mb-5 fw-bold">Процесс покупки</h2>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="process-step text-center">
                    <div class="step-number bg-primary text-white rounded-circle mx-auto mb-3">1</div>
                    <h5 class="fw-bold">Подбор товара</h5>
                    <p class="text-muted">Онлайн или с помощью консультанта</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="process-step text-center">
                    <div class="step-number bg-success text-white rounded-circle mx-auto mb-3">2</div>
                    <h5 class="fw-bold">Оформление заказа</h5>
                    <p class="text-muted">Выбор способа доставки и оплаты</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="process-step text-center">
                    <div class="step-number bg-warning text-white rounded-circle mx-auto mb-3">3</div>
                    <h5 class="fw-bold">Получение товара</h5>
                    <p class="text-muted">Самовывоз или доставка</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="process-step text-center">
                    <div class="step-number bg-info text-white rounded-circle mx-auto mb-3">4</div>
                    <h5 class="fw-bold">Гарантийное обслуживание</h5>
                    <p class="text-muted">Поддержка и сервис</p>
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