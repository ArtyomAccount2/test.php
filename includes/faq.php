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
                <section id="warranty" class="faq-section mb-5">
                    <h3 class="mb-4"><i class="bi bi-shield-check"></i> Гарантия</h3>
                    <div class="accordion" id="warrantyAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#warranty1">
                                    На какие товары предоставляется гарантия?
                                </button>
                            </h2>
                            <div id="warranty1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <p>Мы предоставляем гарантию на следующие категории товаров:</p>
                                    <ul>
                                        <li><strong>Автозапчасти:</strong> от 6 месяцев до 3 лет в зависимости от производителя</li>
                                        <li><strong>Аккумуляторы:</strong> 1-2 года гарантии</li>
                                        <li><strong>Шины и диски:</strong> гарантия от производителя + проверка балансировки</li>
                                        <li><strong>Автохимия и масла:</strong> гарантия соответствия качеству</li>
                                        <li><strong>Электрооборудование:</strong> 1 год гарантии</li>
                                    </ul>
                                    <p>Точный срок гарантии указан в гарантийном талоне и на упаковке товара.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#warranty2">
                                    Как воспользоваться гарантией?
                                </button>
                            </h2>
                            <div id="warranty2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Для оформления гарантийного случая:</p>
                                    <ol>
                                        <li>Сохраните товарный и кассовый чеки</li>
                                        <li>Не нарушайте правила эксплуатации товара</li>
                                        <li>Обратитесь в любой наш магазин с товаром и документами</li>
                                        <li>Наш специалист проведет диагностику</li>
                                        <li>При подтверждении гарантийного случая произведем замену или возврат</li>
                                    </ol>
                                    <p>Гарантия не распространяется на повреждения, вызванные неправильной установкой или эксплуатацией.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#warranty3">
                                    Какие условия гарантийного обслуживания?
                                </button>
                            </h2>
                            <div id="warranty3" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Условия гарантийного обслуживания:</p>
                                    <ul>
                                        <li>Гарантия действует только при наличии чека и гарантийного талона</li>
                                        <li>Товар должен быть в оригинальной упаковке без следов misuse</li>
                                        <li>Гарантия не покрывает естественный износ и расходные материалы</li>
                                        <li>Для шин - гарантия распространяется на производственные дефекты</li>
                                        <li>Диагностика проводится в течение 3-5 рабочих дней</li>
                                    </ul>
                                    <p>Подробные условия смотрите в гарантийном талоне конкретного товара.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="service" class="faq-section mb-5">
                    <h3 class="mb-4"><i class="bi bi-tools"></i> Автосервис</h3>
                    <div class="accordion" id="serviceAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#service1">
                                    Какие услуги предоставляет ваш автосервис?
                                </button>
                            </h2>
                            <div id="service1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <p>Наш автосервис предлагает полный спектр услуг:</p>
                                    <ul>
                                        <li><strong>Техническое обслуживание:</strong> замена масла, фильтров, жидкостей</li>
                                        <li><strong>Ремонт двигателя:</strong> диагностика, ремонт, замена запчастей</li>
                                        <li><strong>Тормозная система:</strong> замена колодок, дисков, тормозной жидкости</li>
                                        <li><strong>Подвеска и рулевое:</strong> диагностика, замена амортизаторов, ШРУСов</li>
                                        <li><strong>Электрика:</strong> диагностика, ремонт электрооборудования</li>
                                        <li><strong>Шиномонтаж:</strong> сезонная замена, ремонт, балансировка</li>
                                        <li><strong>Кузовной ремонт:</strong> покраска, рихтовка, полировка</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#service2">
                                    Нужна ли запись в автосервис?
                                </button>
                            </h2>
                            <div id="service2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Да, запись в автосервис желательна по нескольким причинам:</p>
                                    <ul>
                                        <li>Гарантируем время приема в удобное для вас время</li>
                                        <li>Подготовим необходимые запчасти заранее</li>
                                        <li>Снизим время ожидания ремонта</li>
                                        <li>Обеспечим наличие свободных мастеров</li>
                                    </ul>
                                    <p>Записаться можно:</p>
                                    <ul>
                                        <li>По телефону: +7 (4012) 65-65-65</li>
                                        <li>Через онлайн-форму на сайте</li>
                                        <li>Лично в любом нашем магазине</li>
                                        <li>Через мобильное приложение</li>
                                    </ul>
                                    <p>При срочном ремонте принимаем без записи по возможности.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#service3">
                                    Сколько времени занимает ремонт?
                                </button>
                            </h2>
                            <div id="service3" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Время ремонта зависит от сложности работ:</p>
                                    <ul>
                                        <li><strong>Замена масла и фильтров:</strong> 30-60 минут</li>
                                        <li><strong>Шиномонтаж:</strong> 40-60 минут на комплект</li>
                                        <li><strong>Замена тормозных колодок:</strong> 1-2 часа</li>
                                        <li><strong>Диагностика:</strong> 30-60 минут</li>
                                        <li><strong>Ремонт подвески:</strong> 2-4 часа</li>
                                        <li><strong>Капитальный ремонт двигателя:</strong> 3-7 дней</li>
                                        <li><strong>Кузовной ремонт:</strong> от 1 дня до недели</li>
                                    </ul>
                                    <p>Точное время вам назовут после диагностики. При наличии всех запчастей ремонт выполняется быстрее.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#service4">
                                    Даете ли вы гарантию на работы?
                                </button>
                            </h2>
                            <div id="service4" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Да, мы предоставляем гарантию на все виды работ:</p>
                                    <ul>
                                        <li><strong>Гарантия на работы:</strong> 6-12 месяцев в зависимости от вида ремонта</li>
                                        <li><strong>Гарантия на запчасти:</strong> согласно гарантии производителя</li>
                                        <li><strong>Диагностика:</strong> гарантия точности диагностики</li>
                                    </ul>
                                    <p>Условия гарантии:</p>
                                    <ul>
                                        <li>Гарантия действует при соблюдении правил эксплуатации</li>
                                        <li>Требуется предоставление акта выполненных работ</li>
                                        <li>Гарантия не распространяется на износ расходных материалов</li>
                                        <li>Бесплатное устранение недостатков в гарантийный период</li>
                                    </ul>
                                    <p>Все гарантийные обязательства фиксируются в договоре.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="account" class="faq-section mb-5">
                    <h3 class="mb-4"><i class="bi bi-person"></i> Аккаунт</h3>
                    <div class="accordion" id="accountAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#account1">
                                    Как создать учетную запись?
                                </button>
                            </h2>
                            <div id="account1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <p>Создать учетную запись можно несколькими способами:</p>
                                    <ol>
                                        <li><strong>При оформлении заказа:</strong> система предложит создать аккаунт автоматически</li>
                                        <li><strong>Через страницу регистрации:</strong> нажмите "Войти" → "Регистрация"</li>
                                        <li><strong>Через социальные сети:</strong> используйте быструю регистрацию через VK, Google или Яндекс</li>
                                    </ol>
                                    <p>Для регистрации потребуется:</p>
                                    <ul>
                                        <li>Email адрес</li>
                                        <li>Пароль (не менее 6 символов)</li>
                                        <li>Контактный телефон</li>
                                        <li>ФИО для обращений</li>
                                    </ul>
                                    <p>После регистрации вам будет доступен личный кабинет с историей заказов.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#account2">
                                    Что делать, если забыл пароль?
                                </button>
                            </h2>
                            <div id="account2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Если вы забыли пароль:</p>
                                    <ol>
                                        <li>Перейдите на страницу входа</li>
                                        <li>Нажмите "Забыли пароль?"</li>
                                        <li>Введите email, указанный при регистрации</li>
                                        <li>Проверьте почту - мы вышлем ссылку для сброса пароля</li>
                                        <li>Перейдите по ссылке и установите новый пароль</li>
                                    </ol>
                                    <p>Если email недоступен:</p>
                                    <ul>
                                        <li>Обратитесь в техподдержку по телефону +7 (4012) 65-65-65</li>
                                        <li>Подготовьте данные для идентификации (ФИО, телефон, данные заказов)</li>
                                        <li>Мы поможем восстановить доступ к аккаунту</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#account3">
                                    Какие преимущества у зарегистрированных пользователей?
                                </button>
                            </hh2>
                            <div id="account3" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Зарегистрированные пользователи получают:</p>
                                    <ul>
                                        <li><strong>История заказов:</strong> доступ ко всем предыдущим заказам</li>
                                        <li><strong>Быстрое оформление:</strong> сохраненные данные для доставки</li>
                                        <li><strong>Персональные скидки:</strong> накопительная система скидок</li>
                                        <li><strong>Избранные товары:</strong> возможность сохранять товары для будущих покупок</li>
                                        <li><strong>Уведомления:</strong> о статусе заказов и акциях</li>
                                        <li><strong>Кэшбэк бонусами:</strong> возврат до 5% от суммы покупок</li>
                                        <li><strong>Предзаказ товаров:</strong> доступ к предварительному заказу дефицитных запчастей</li>
                                        <li><strong>Персональный менеджер:</strong> для постоянных клиентов</li>
                                    </ul>
                                    <p>Регистрация бесплатна и занимает менее 2 минут.</p>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#account4">
                                    Как изменить данные в профиле?
                                </button>
                            </h2>
                            <div id="account4" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p>Чтобы изменить данные профиля:</p>
                                    <ol>
                                        <li>Войдите в личный кабинет</li>
                                        <li>Перейдите в раздел "Мой профиль" или "Настройки"</li>
                                        <li>Нажмите "Редактировать" рядом с нужным полем</li>
                                        <li>Внесите изменения и сохраните</li>
                                    </ol>
                                    <p>Какие данные можно изменить:</p>
                                    <ul>
                                        <li>Контактные данные (телефон, email)</li>
                                        <li>Личные данные (ФИО, дата рождения)</li>
                                        <li>Адреса доставки (несколько адресов)</li>
                                        <li>Настройки уведомлений</li>
                                        <li>Пароль учетной записи</li>
                                    </ul>
                                    <p>Некоторые данные (как email) могут потребовать подтверждения.</p>
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
<script src="../js/script.js"></script>
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