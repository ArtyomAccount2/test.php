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
    <title>Оплата и доставка - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/delivery-styles.css">
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

        let observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        let observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) 
                {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.delivery-method, .payment-method, .section-header').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });

        let sections = document.querySelectorAll('.delivery-section');
        let navLinks = document.querySelectorAll('.nav-link');

        window.addEventListener('scroll', () => {
            let current = '';

            sections.forEach(section => {
                let sectionTop = section.offsetTop;
                let sectionHeight = section.clientHeight;

                if (scrollY >= (sectionTop - 100)) 
                {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                
                if (link.getAttribute('href') === `#${current}`) 
                {
                    link.classList.add('active');
                }
            });
        });
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5">
    <div class="hero-section text-center mb-5" style="padding-top: 85px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Оплата и доставка</h1>
        <p class="lead text-muted mb-4">Удобные способы получения заказов и безопасные методы оплаты</p>
    </div>
    <nav class="delivery-nav">
        <div class="nav nav-pills justify-content-center flex-wrap">
            <a class="nav-link" href="#delivery">Доставка</a>
            <a class="nav-link" href="#payment">Оплата</a>
            <a class="nav-link" href="#terms">Условия</a>
            <a class="nav-link" href="#faq">Вопросы</a>
        </div>
    </nav>
    <div class="delivery-section" id="delivery">
        <div class="section-header text-center mb-5">
            <h2><i class="bi bi-truck me-2"></i>Способы доставки</h2>
            <p class="text-muted">Выберите наиболее удобный для вас вариант получения заказа</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="delivery-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h3 class="method-title">Самовывоз</h3>
                    </div>
                    <p class="method-description">Заберите заказ самостоятельно из любого из наших магазинов в Калининграде</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-clock text-primary"></i>
                            <div>
                                <strong>Срок:</strong> 1-2 часа после оформления
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-currency-dollar text-primary"></i>
                            <div>
                                <strong>Стоимость:</strong> бесплатно
                            </div>
                        </div>
                    </div>
                    <div class="method-badge badge bg-success">Быстро</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="delivery-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h3 class="method-title">Курьером по городу</h3>
                    </div>
                    <p class="method-description">Доставка курьером по адресу в пределах Калининграда</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-clock text-primary"></i>
                            <div>
                                <strong>Срок:</strong> в день заказа
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-currency-dollar text-primary"></i>
                            <div>
                                <strong>Стоимость:</strong> 300 ₽ (бесплатно от 5000 ₽)
                            </div>
                        </div>
                    </div>
                    <div class="method-badge badge bg-warning">Популярно</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="delivery-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h3 class="method-title">По области</h3>
                    </div>
                    <p class="method-description">Доставка в населенные пункты Калининградской области</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-clock text-primary"></i>
                            <div>
                                <strong>Срок:</strong> 1-3 дня
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-currency-dollar text-primary"></i>
                            <div>
                                <strong>Стоимость:</strong> от 500 ₽
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="delivery-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h3 class="method-title">Почта России</h3>
                    </div>
                    <p class="method-description">Доставка в любой регион России через Почту России</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-clock text-primary"></i>
                            <div>
                                <strong>Срок:</strong> 5-14 дней
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-currency-dollar text-primary"></i>
                            <div>
                                <strong>Стоимость:</strong> по тарифам
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="delivery-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-truck-flatbed"></i>
                        </div>
                        <h3 class="method-title">Транспортные компании</h3>
                    </div>
                    <p class="method-description">Доставка через СДЭК, Деловые Линии, ПЭК и другие ТК</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-clock text-primary"></i>
                            <div>
                                <strong>Срок:</strong> 3-7 дней
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-currency-dollar text-primary"></i>
                            <div>
                                <strong>Стоимость:</strong> по тарифам ТК
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="delivery-section" id="payment">
        <div class="section-header text-center mb-5">
            <h2><i class="bi bi-credit-card me-2"></i>Способы оплаты</h2>
            <p class="text-muted">Безопасные и удобные варианты оплаты ваших заказов</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="payment-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-cash"></i>
                        </div>
                        <h3 class="method-title">Наличными</h3>
                    </div>
                    <p class="method-description">Оплата наличными при получении заказа в магазине или курьеру</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-percent text-primary"></i>
                            <div>
                                <strong>Комиссия:</strong> нет
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-person text-primary"></i>
                            <div>
                                <strong>Доступно:</strong> для физ. лиц
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="payment-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-credit-card"></i>
                        </div>
                        <h3 class="method-title">Картой онлайн</h3>
                    </div>
                    <p class="method-description">Оплата банковской картой Visa, Mastercard, МИР через защищенный шлюз</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-percent text-primary"></i>
                            <div>
                                <strong>Комиссия:</strong> нет
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-people text-primary"></i>
                            <div>
                                <strong>Доступно:</strong> для всех
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="payment-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h3 class="method-title">Онлайн-платежи</h3>
                    </div>
                    <p class="method-description">Оплата через СБП, Apple Pay, Google Pay и другие системы</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-percent text-primary"></i>
                            <div>
                                <strong>Комиссия:</strong> нет
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-people text-primary"></i>
                            <div>
                                <strong>Доступно:</strong> для всех
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="payment-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="method-title">Безналичный расчет</h3>
                    </div>
                    <p class="method-description">Оплата по счету для юридических лиц и ИП с НДС</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-percent text-primary"></i>
                            <div>
                                <strong>Комиссия:</strong> нет
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-briefcase text-primary"></i>
                            <div>
                                <strong>Доступно:</strong> для юр. лиц и ИП
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="payment-method d-flex flex-column justify-content-between h-100">
                    <div class="method-header">
                        <div class="method-icon">
                            <i class="bi bi-credit-card-2-back"></i>
                        </div>
                        <h3 class="method-title">Рассрочка и кредит</h3>
                    </div>
                    <p class="method-description">Оплата частями или в кредит от банков-партнеров</p>
                    <div class="method-details">
                        <div class="detail-item">
                            <i class="bi bi-percent text-primary"></i>
                            <div>
                                <strong>Комиссия:</strong> по условиям банка
                            </div>
                        </div>
                        <div class="detail-item">
                            <i class="bi bi-person text-primary"></i>
                            <div>
                                <strong>Доступно:</strong> для физ. лиц
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="delivery-section" id="terms">
        <div class="section-header text-center mb-5">
            <h2><i class="bi bi-info-circle me-2"></i>Условия доставки</h2>
            <p class="text-muted">Подробная информация о сроках, зонах и условиях возврата</p>
        </div>
        <div class="terms-content">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="terms-card">
                        <h4 class="mb-4"><i class="bi bi-clock-history me-2"></i>Сроки доставки</h4>
                        <div class="table-responsive">
                            <table class="terms-table">
                                <thead>
                                    <tr>
                                        <th>Способ доставки</th>
                                        <th>Срок</th>
                                        <th>Условия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Самовывоз</td>
                                        <td>1-2 часа</td>
                                        <td>При наличии на складе</td>
                                    </tr>
                                    <tr>
                                        <td>Курьером</td>
                                        <td>1 день</td>
                                        <td>С 10:00 до 20:00</td>
                                    </tr>
                                    <tr>
                                        <td>По области</td>
                                        <td>1-3 дня</td>
                                        <td>По графику</td>
                                    </tr>
                                    <tr>
                                        <td>Почта России</td>
                                        <td>5-14 дней</td>
                                        <td>По регионам</td>
                                    </tr>
                                    <tr>
                                        <td>Транспортные компании</td>
                                        <td>3-7 дней</td>
                                        <td>Зависит от ТК</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="terms-card">
                        <h4 class="mb-4"><i class="bi bi-geo me-2"></i>Зоны доставки</h4>
                        <div class="zones-list">
                            <div class="zone-item">
                                <div class="zone-marker bg-success"></div>
                                <div class="zone-content">
                                    <h6>Зеленая зона</h6>
                                    <p class="mb-1">Центр Калининграда</p>
                                    <small class="text-muted">300 ₽ (бесплатно от 5000 ₽)</small>
                                </div>
                            </div>
                            <div class="zone-item">
                                <div class="zone-marker bg-warning"></div>
                                <div class="zone-content">
                                    <h6>Желтая зона</h6>
                                    <p class="mb-1">Окраины города</p>
                                    <small class="text-muted">400 ₽ (бесплатно от 7000 ₽)</small>
                                </div>
                            </div>
                            <div class="zone-item">
                                <div class="zone-marker bg-orange"></div>
                                <div class="zone-content">
                                    <h6>Оранжевая зона</h6>
                                    <p class="mb-1">Пригороды 20-50 км</p>
                                    <small class="text-muted">500-800 ₽</small>
                                </div>
                            </div>
                            <div class="zone-item">
                                <div class="zone-marker bg-danger"></div>
                                <div class="zone-content">
                                    <h6>Красная зона</h6>
                                    <p class="mb-1">Область 50+ км</p>
                                    <small class="text-muted">от 1000 ₽</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="delivery-section" id="faq">
        <div class="section-header text-center mb-5">
            <h2><i class="bi bi-question-circle me-2"></i>Частые вопросы</h2>
            <p class="text-muted">Ответы на самые популярные вопросы о доставке и оплате</p>
        </div>
        <div class="faq-content">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="true" aria-controls="faqOne">
                            Как отследить мой заказ?
                        </button>
                    </h3>
                    <div id="faqOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            После отправки заказа мы вышлем вам номер для отслеживания на указанную электронную почту или SMS. Вы можете отслеживать статус доставки на сайте выбранной транспортной компании или Почты России.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo" aria-expanded="false" aria-controls="faqTwo">
                            Можно ли изменить адрес доставки после оформления заказа?
                        </button>
                    </h3>
                    <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Да, вы можете изменить адрес доставки до момента отправки заказа. Позвоните нам по телефону +7 (4012) 65-65-65 или напишите на info@lal-auto.ru с указанием номера заказа и нового адреса.
                        </div>
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