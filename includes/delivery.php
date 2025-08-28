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
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5 pt-4">
    <h1 class="text-center mb-5" style="padding-top: 75px;">Оплата и доставка</h1>
    <div class="delivery-methods mb-5">
        <h2 class="text-center mb-4"><i class="bi bi-truck"></i> Способы доставки</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="delivery-method">
                    <div class="method-icon">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h3 class="method-title">Самовывоз</h3>
                    <p class="method-description">Заберите заказ самостоятельно из любого из наших магазинов в Калининграде</p>
                    <div class="method-details">
                        <p><strong>Срок:</strong> 1-2 часа после оформления</p>
                        <p><strong>Стоимость:</strong> бесплатно (зависит от типа продукции)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="delivery-method">
                    <div class="method-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <h3 class="method-title">Курьером по городу</h3>
                    <p class="method-description">Доставка курьером по адресу в пределах Калининграда</p>
                    <div class="method-details">
                        <p><strong>Срок:</strong> в день заказа или на следующий день</p>
                        <p><strong>Стоимость:</strong> 300 ₽ (бесплатно от 5000 ₽)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="delivery-method">
                    <div class="method-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h3 class="method-title">По области</h3>
                    <p class="method-description">Доставка в населенные пункты Калининградской области</p>
                    <div class="method-details">
                        <p><strong>Срок:</strong> 1-3 дня</p>
                        <p><strong>Стоимость:</strong> от 500 ₽ (зависит от удаленности)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="delivery-method">
                    <div class="method-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <h3 class="method-title">Почта России</h3>
                    <p class="method-description">Доставка в любой регион России через Почту России</p>
                    <div class="method-details">
                        <p><strong>Срок:</strong> 5-14 дней</p>
                        <p><strong>Стоимость:</strong> рассчитывается индивидуально</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="delivery-method">
                    <div class="method-icon">
                        <i class="bi bi-truck-flatbed"></i>
                    </div>
                    <h3 class="method-title">Транспортные компании</h3>
                    <p class="method-description">Доставка через СДЭК, Деловые Линии, ПЭК и другие ТК</p>
                    <div class="method-details">
                        <p><strong>Срок:</strong> 3-7 дней</p>
                        <p><strong>Стоимость:</strong> по тарифам ТК</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="payment-methods mb-5" id="payment">
        <h2 class="text-center mb-4"><i class="bi bi-credit-card"></i> Способы оплаты</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="payment-method">
                    <div class="method-icon">
                        <i class="bi bi-cash"></i>
                    </div>
                    <h3 class="method-title">Наличными</h3>
                    <p class="method-description">Оплата наличными при получении заказа в магазине или курьеру</p>
                    <div class="method-details">
                        <p><strong>Комиссия:</strong> нет</p>
                        <p><strong>Доступно:</strong> для физ. лиц</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="payment-method">
                    <div class="method-icon">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <h3 class="method-title">Картой онлайн</h3>
                    <p class="method-description">Оплата банковской картой Visa, Mastercard, МИР через безопасный платежный шлюз</p>
                    <div class="method-details">
                        <p><strong>Комиссия:</strong> нет</p>
                        <p><strong>Доступно:</strong> для всех</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="payment-method">
                    <div class="method-icon">
                        <i class="bi bi-phone"></i>
                    </div>
                    <h3 class="method-title">Онлайн-платежи</h3>
                    <p class="method-description">Оплата через СБП, Apple Pay, Google Pay, Яндекс.Деньги и другие системы</p>
                    <div class="method-details">
                        <p><strong>Комиссия:</strong> нет</p>
                        <p><strong>Доступно:</strong> для всех</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="payment-method">
                    <div class="method-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <h3 class="method-title">Безналичный расчет</h3>
                    <p class="method-description">Оплата по счету для юридических лиц и ИП с НДС</p>
                    <div class="method-details">
                        <p><strong>Комиссия:</strong> нет</p>
                        <p><strong>Доступно:</strong> для юр. лиц и ИП</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="payment-method">
                    <div class="method-icon">
                        <i class="bi bi-credit-card-2-back"></i>
                    </div>
                    <h3 class="method-title">Рассрочка и кредит</h3>
                    <p class="method-description">Оплата частями или в кредит от наших банков-партнеров</p>
                    <div class="method-details">
                        <p><strong>Комиссия:</strong> по условиям банка</p>
                        <p><strong>Доступно:</strong> для физ. лиц</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="delivery-terms mb-5">
        <h2 class="text-center mb-4"><i class="bi bi-info-circle"></i> Условия доставки</h2>
        <div class="accordion" id="deliveryTermsAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                        Сроки доставки
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show">
                    <div class="accordion-body">
                        <table class="delivery-table">
                            <thead>
                                <tr>
                                    <th>Способ доставки</th>
                                    <th>Срок</th>
                                    <th>Примечание</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Самовывоз</td>
                                    <td>1-2 часа</td>
                                    <td>При наличии товара на складе</td>
                                </tr>
                                <tr>
                                    <td>Курьером по городу</td>
                                    <td>1 день</td>
                                    <td>Доставка с 10:00 до 20:00</td>
                                </tr>
                                <tr>
                                    <td>По области</td>
                                    <td>1-3 дня</td>
                                    <td>Зависит от удаленности</td>
                                </tr>
                                <tr>
                                    <td>Почта России</td>
                                    <td>5-14 дней</td>
                                    <td>Срок зависит от региона</td>
                                </tr>
                                <tr>
                                    <td>Транспортные компании</td>
                                    <td>3-7 дней</td>
                                    <td>Срок зависит от ТК и региона</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                        Зоны доставки
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <div class="delivery-zones">
                            <div class="zone-item">
                                <div class="zone-color" style="background-color: #4CAF50;"></div>
                                <div class="zone-text">
                                    <h5>Зеленая зона</h5>
                                    <p>Центральные районы Калининграда. Доставка в день заказа. Стоимость: 300 ₽ (бесплатно от 5000 ₽)</p>
                                </div>
                            </div>
                            <div class="zone-item">
                                <div class="zone-color" style="background-color: #FFC107;"></div>
                                <div class="zone-text">
                                    <h5>Желтая зона</h5>
                                    <p>Отдаленные районы Калининграда. Доставка на следующий день. Стоимость: 400 ₽ (бесплатно от 7000 ₽)</p>
                                </div>
                            </div>
                            <div class="zone-item">
                                <div class="zone-color" style="background-color: #FF9800;"></div>
                                <div class="zone-text">
                                    <h5>Оранжевая зона</h5>
                                    <p>Ближайшие пригороды (20-50 км). Доставка 1-2 дня. Стоимость: 500-800 ₽</p>
                                </div>
                            </div>
                            <div class="zone-item">
                                <div class="zone-color" style="background-color: #F44336;"></div>
                                <div class="zone-text">
                                    <h5>Красная зона</h5>
                                    <p>Отдаленные районы области (50+ км). Доставка 2-3 дня. Стоимость: от 1000 ₽</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                        Возврат и обмен
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        <p>Вы можете вернуть или обменять товар в течение 14 дней с момента покупки при соблюдении следующих условий:</p>
                        <ul>
                            <li>Товар не был в употреблении</li>
                            <li>Сохранен товарный вид и упаковка</li>
                            <li>Имеются документы, подтверждающие покупку</li>
                            <li>Товар не входит в перечень невозвратных товаров</li>
                        </ul>
                        <p>Для возврата денежных средств за товар, оплаченный банковской картой, необходимо заполнить заявление. Деньги будут возвращены на карту в течение 3-10 рабочих дней.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="delivery-faq">
        <h2 class="text-center mb-4"><i class="bi bi-question-circle"></i> Частые вопросы</h2>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne">
                        Как отследить мой заказ?
                    </button>
                </h2>
                <div id="faqOne" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        После отправки заказа мы вышлем вам номер для отслеживания на указанную электронную почту или SMS. Вы можете отслеживать статус доставки на сайте выбранной транспортной компании или Почты России.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo">
                        Можно ли изменить адрес доставки после оформления заказа?
                    </button>
                </h2>
                <div id="faqTwo" class="accordion-collapse collapse">
                    <div class="accordion-body">
                        Да, вы можете изменить адрес доставки до момента отправки заказа. Позвоните нам по телефону +7 (4012) 65-65-65 или напишите на info@lal-auto.ru с указанием номера заказа и нового адреса.
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