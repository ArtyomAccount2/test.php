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
    <title>Частые вопросы - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/faq-styles.css">
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
            <h1 class="mb-3" style="padding-top: 60px;">Частые вопросы (FAQ)</h1>
            <p class="lead">Ответы на самые популярные вопросы наших клиентов</p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="search-section mb-4">
                <div class="input-group">
                    <input type="text" class="form-control form-control-lg" placeholder="Поиск по вопросам..." id="faqSearch">
                    <button class="btn btn-primary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="faq-categories mb-5">
                <h3 class="mb-4">Категории вопросов</h3>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#ordering" class="btn btn-outline-primary">Заказы и доставка</a>
                    <a href="#payment" class="btn btn-outline-primary">Оплата</a>
                    <a href="#products" class="btn btn-outline-primary">Товары</a>
                    <a href="#warranty" class="btn btn-outline-primary">Гарантия</a>
                    <a href="#service" class="btn btn-outline-primary">Автосервис</a>
                    <a href="#account" class="btn btn-outline-primary">Аккаунт</a>
                </div>
            </div>
            <div class="faq-content">
                <section id="ordering" class="faq-section mb-5">
                    <h3 class="mb-4"><i class="bi bi-cart"></i> Заказы и доставка</h3>
                    <div class="accordion" id="orderingAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#ordering1">
                                    Как оформить заказ?
                                </button>
                            </h2>
                            <div id="ordering1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <p>Для оформления заказа:</p>
                                    <ol>
                                        <li>Выберите нужные товары и добавьте их в корзину</li>
                                        <li>Перейдите в корзину и проверьте состав заказа</li>
                                        <li>Заполните данные для доставки</li>
                                        <li>Выберите способ оплаты</li>
                                        <li>Подтвердите заказ</li>
                                    </ol>
                                    <p>После оформления с вами свяжется менеджер для подтверждения заказа.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ordering2">
                                    Сколько стоит доставка?
                                </button>
                            </h2>
                            <div id="ordering2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Стоимость доставки зависит от:</p>
                                    <ul>
                                        <li><strong>Самовывоз:</strong> бесплатно из любого магазина</li>
                                        <li><strong>Курьером по Калининграду:</strong> 300 ₽ (бесплатно от 5000 ₽)</li>
                                        <li><strong>По области:</strong> от 500 ₽ в зависимости от удаленности</li>
                                        <li><strong>В другие регионы:</strong> по тарифам транспортных компаний</li>
                                    </ul>
                                    <p>Точную стоимость доставки вы увидите при оформлении заказа.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#ordering3">
                                    Как отследить мой заказ?
                                </button>
                            </h2>
                            <div id="ordering3" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>После отправки заказа мы вышлем вам номер для отслеживания:</p>
                                    <ul>
                                        <li>На email, указанный при оформлении</li>
                                        <li>В SMS на ваш телефон</li>
                                        <li>В личном кабинете на сайте</li>
                                    </ul>
                                    <p>С этим номером вы можете отслеживать статус доставки на сайте транспортной компании или Почты России.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="payment" class="faq-section mb-5">
                    <h3 class="mb-4"><i class="bi bi-credit-card"></i> Оплата</h3>
                    <div class="accordion" id="paymentAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#payment1">
                                    Какие способы оплаты доступны?
                                </button>
                            </h2>
                            <div id="payment1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <p>Мы принимаем:</p>
                                    <ul>
                                        <li><strong>Наличными:</strong> при получении в магазине или курьеру</li>
                                        <li><strong>Банковские карты:</strong> Visa, Mastercard, МИР</li>
                                        <li><strong>Онлайн-платежи:</strong> СБП, Apple Pay, Google Pay</li>
                                        <li><strong>Безналичный расчет:</strong> для юридических лиц</li>
                                        <li><strong>Рассрочка и кредит:</strong> от банков-партнеров</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#payment2">
                                    Безопасна ли оплата картой онлайн?
                                </button>
                            </h2>
                            <div id="payment2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Да, оплата картой через наш сайт абсолютно безопасна:</p>
                                    <ul>
                                        <li>Используется защищенное SSL-соединение</li>
                                        <li>Данные карт обрабатываются через сертифицированный платежный шлюз</li>
                                        <li>Мы не храним данные ваших карт</li>
                                        <li>Все операции защищены по стандарту PCI DSS</li>
                                    </ul>
                                    <p>Вы можете быть уверены в безопасности ваших платежных данных.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="products" class="faq-section mb-5">
                    <h3 class="mb-4"><i class="bi bi-box"></i> Товары</h3>
                    <div class="accordion" id="productsAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#products1">
                                    Как подобрать запчасти для моего автомобиля?
                                </button>
                            </h2>
                            <div id="products1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <p>Есть несколько способов подбора запчастей:</p>
                                    <ol>
                                        <li><strong>По VIN-коду:</strong> самый точный способ, укажите VIN в поиске</li>
                                        <li><strong>По марке и модели:</strong> выберите марку, модель, год выпуска и двигатель</li>
                                        <li><strong>По артикулу:</strong> если знаете точный артикул производителя</li>
                                        <li><strong>С помощью консультанта:</strong> позвоните +7 (4012) 65-65-65</li>
                                    </ol>
                                    <p>Наши менеджеры помогут подобрать правильные запчасти для вашего автомобиля.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#products2">
                                    Все ли товары есть в наличии?
                                </button>
                            </h2>
                            <div id="products2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>На сайте указано актуальное наличие товаров:</p>
                                    <ul>
                                        <li><span class="text-success"><strong>В наличии:</strong></span> товар есть на складе</li>
                                        <li><span class="text-warning"><strong>Под заказ:</strong></span> доставка 1-7 дней</li>
                                        <li><span class="text-danger"><strong>Нет в наличии:</strong></span> временно отсутствует</li>
                                    </ul>
                                    <p>Точное наличие и сроки поставки уточняйте у менеджеров по телефону.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="support-section mt-5">
                    <div class="card bg-light">
                        <div class="card-body text-center p-5">
                            <h3 class="mb-3">Не нашли ответ на свой вопрос?</h3>
                            <p class="mb-4">Наши специалисты всегда готовы помочь вам</p>
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                <a href="tel:+74012656565" class="btn btn-primary">
                                    <i class="bi bi-telephone"></i> +7 (4012) 65-65-65
                                </a>
                                <a href="contacts.php" class="btn btn-outline-primary">
                                    <i class="bi bi-envelope"></i> Написать нам
                                </a>
                                <a href="#" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#chatModal">
                                    <i class="bi bi-chat"></i> Онлайн-чат
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let searchInput = document.getElementById('faqSearch');
    let accordionItems = document.querySelectorAll('.accordion-item');
    
    searchInput.addEventListener('input', function() 
    {
        let searchText = this.value.toLowerCase();
        
        accordionItems.forEach(item => {
            let question = item.querySelector('.accordion-button').textContent.toLowerCase();
            let answer = item.querySelector('.accordion-body').textContent.toLowerCase();
            
            if (question.includes(searchText) || answer.includes(searchText)) 
            {
                item.style.display = 'block';

                let collapse = new bootstrap.Collapse(item.querySelector('.accordion-collapse'));
                collapse.show();
            } 
            else 
            {
                item.style.display = 'none';
            }
        });
    });
});
</script>
</body>
</html>