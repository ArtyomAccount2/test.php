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
    <title>Политика конфиденциальности - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/privacy-styles.css">
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
    <div class="row mb-4">
        <div class="col-12" style="padding-top: 60px;">
            <h1 class="display-5 fw-bold text-primary mb-3">Политика конфиденциальности</h1>
            <p class="lead text-muted">Последнее обновление: 15 мая 2025 года</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="privacy-content">
                <section class="mb-5">
                    <h2 class="mb-3">1. Общие положения</h2>
                    <p>1.1. Настоящая Политика конфиденциальности (далее — Политика) разработана в соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ "О персональных данных" и определяет порядок обработки персональных данных и меры по обеспечению безопасности персональных данных в ООО "Лал-Авто" (далее — Оператор).</p>
                    <p>1.2. Оператор ставит своей важнейшей целью и условием осуществления своей деятельности соблюдение прав и свобод человека и гражданина при обработке его персональных данных, в том числе защиты прав на неприкосновенность частной жизни, личную и семейную тайну.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">2. Основные понятия</h2>
                    <p>2.1. <strong>Персональные данные</strong> - любая информация, относящаяся к прямо или косвенно определенному или определяемому физическому лицу (субъекту персональных данных).</p>
                    <p>2.2. <strong>Обработка персональных данных</strong> - любое действие (операция) или совокупность действий (операций), совершаемых с использованием средств автоматизации или без использования таких средств с персональными данными.</p>
                    <p>2.3. <strong>Конфиденциальность персональных данных</strong> - обязательное для соблюдения Оператором или иным получившим доступ к персональным данным лицом требование не допускать их распространения без согласия субъекта персональных данных.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">3. Цели сбора персональных данных</h2>
                    <p>3.1. Оператор обрабатывает персональные данные пользователей в следующих целях:</p>
                    <ul>
                        <li>Обработка заказов и предоставление услуг</li>
                        <li>Обратная связь с пользователями</li>
                        <li>Отправка информационных материалов и рекламных рассылок</li>
                        <li>Проведение маркетинговых исследований</li>
                        <li>Улучшение качества обслуживания</li>
                        <li>Выполнение обязательств по договорам</li>
                    </ul>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">4. Состав персональных данных</h2>
                    <p>4.1. Оператор может обрабатывать следующие персональные данные:</p>
                    <ul>
                        <li>Фамилия, имя, отчество</li>
                        <li>Контактный телефон</li>
                        <li>Адрес электронной почты</li>
                        <li>Почтовый адрес</li>
                        <li>Реквизиты документов (для юридических лиц)</li>
                        <li>Информация о заказах и покупках</li>
                        <li>Технические данные (IP-адрес, cookies, данные браузера)</li>
                    </ul>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">5. Принципы обработки персональных данных</h2>
                    <p>5.1. Обработка персональных данных осуществляется на основе следующих принципов:</p>
                    <ul>
                        <li>Законности и справедливости</li>
                        <li>Ограничения обработки достижением конкретных целей</li>
                        <li>Соответствия содержания и объема обрабатываемых данных заявленным целям</li>
                        <li>Достоверности и актуальности данных</li>
                        <li>Хранения данных не дольше требуемого времени</li>
                        <li>Конфиденциальности и безопасности</li>
                    </ul>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">6. Сроки хранения персональных данных</h2>
                    <p>6.1. Сроки хранения персональных данных определяются в соответствии с целями обработки и требованиями законодательства.</p>
                    <p>6.2. Персональные данные уничтожаются при:</p>
                    <ul>
                        <li>Достижении целей обработки</li>
                        <li>Истечении сроков хранения</li>
                        <li>Отзыве согласия на обработку</li>
                        <li>Выявлении неправомерной обработки</li>
                    </ul>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">7. Права субъектов персональных данных</h2>
                    <p>7.1. Субъект персональных данных имеет право:</p>
                    <ul>
                        <li>Получать информацию об обработке своих данных</li>
                        <li>Требовать уточнения, блокирования или уничтожения данных</li>
                        <li>Отозвать согласие на обработку</li>
                        <li>Обжаловать действия или бездействие Оператора</li>
                        <li>Защищать свои права законными способами</li>
                    </ul>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">8. Меры защиты персональных данных</h2>
                    <p>8.1. Оператор принимает необходимые организационные и технические меры для защиты персональных данных от неправомерного или случайного доступа, уничтожения, изменения, блокирования, копирования, распространения.</p>
                    <p>8.2. Защита данных обеспечивается с помощью:</p>
                    <ul>
                        <li>Систем контроля доступа</li>
                        <li>Шифрования передаваемых данных</li>
                        <li>Антивирусной защиты</li>
                        <li>Регулярного резервного копирования</li>
                        <li>Обучения сотрудников</li>
                    </ul>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">9. Передача персональных данных третьим лицам</h2>
                    <p>9.1. Оператор может передавать персональные данные:</p>
                    <ul>
                        <li>Курьерским службам (для доставки заказов)</li>
                        <li>Платежным системам (для обработки платежей)</li>
                        <li>Государственным органам (по требованию закона)</li>
                        <li>Партнерам (для выполнения обязательств)</li>
                    </ul>
                    <p>9.2. При передаче данных обеспечивается их конфиденциальность и безопасность.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">10. Заключительные положения</h2>
                    <p>10.1. Настоящая Политика является общедоступной и подлежит размещению на официальном сайте Оператора.</p>
                    <p>10.2. Оператор вправе вносить изменения в настоящую Политику. Новая редакция вступает в силу с момента ее размещения на сайте.</p>
                    <p>10.3. Все предложения или вопросы по настоящей Политике следует направлять по адресу: info@lal-auto.ru</p>
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
</body>
</html>