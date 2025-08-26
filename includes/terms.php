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
    <title>Условия использования - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/terms-styles.css">
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
            <h1 class="mb-3" style="padding-top: 60px;">Условия использования сайта</h1>
            <p class="text-muted">Последнее обновление: 15 мая 2025 года</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="terms-content">
                <section class="mb-5">
                    <h2 class="mb-3">1. Общие положения</h2>
                    <p>1.1. Настоящие Условия использования (далее — Условия) регулируют отношения между ООО "Лал-Авто" (далее — Компания) и пользователями официального сайта www.lal-auto.ru (далее — Сайт).</p>
                    <p>1.2. Используя Сайт, вы соглашаетесь с настоящими Условиями и обязуетесь их соблюдать.</p>
                    <p>1.3. Компания оставляет за собой право изменять Условия в любое время без предварительного уведомления.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">2. Интеллектуальная собственность</h2>
                    <p>2.1. Все материалы, размещенные на Сайте, включая тексты, изображения, логотипы, дизайн, являются собственностью Компании или используются с разрешения правообладателей.</p>
                    <p>2.2. Запрещается любое копирование, распространение, изменение материалов Сайта без письменного разрешения Компании.</p>
                    <p>2.3. Товарные знаки и логотипы, размещенные на Сайте, являются собственностью их владельцев.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">3. Использование сайта</h2>
                    <p>3.1. Пользователь обязуется использовать Сайт только в законных целях.</p>
                    <p>3.2. Запрещается:</p>
                    <ul>
                        <li>Размещать ложную или вводящую в заблуждение информацию</li>
                        <li>Нарушать работу Сайта или пытаться получить несанкционированный доступ</li>
                        <li>Размещать вредоносное программное обеспечение</li>
                        <li>Совершать действия, нарушающие законодательство РФ</li>
                        <li>Использовать автоматизированные системы для сбора информации</li>
                    </ul>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">4. Регистрация и учетная запись</h2>
                    <p>4.1. Для доступа к некоторым функциям Сайта требуется регистрация.</p>
                    <p>4.2. Пользователь обязан предоставлять достоверную информацию при регистрации.</p>
                    <p>4.3. Пользователь несет ответственность за сохранность своих учетных данных.</p>
                    <p>4.4. Компания вправе заблокировать учетную запись при нарушении Условий.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">5. Заказы и покупки</h2>
                    <p>5.1. Размещение заказа на Сайте является офертой к заключению договора купли-продажи.</p>
                    <p>5.2. Компания оставляет за собой право отказать в выполнении заказа без объяснения причин.</p>
                    <p>5.3. Цены на товары указаны в рублях и могут изменяться без предварительного уведомления.</p>
                    <p>5.4. Наличие товара и сроки доставки уточняются при оформлении заказа.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">6. Отзывы и комментарии</h2>
                    <p>6.1. Пользователи могут оставлять отзывы о товарах и услугах.</p>
                    <p>6.2. Компания оставляет за собой право модерировать и удалять отзывы, которые:</p>
                    <ul>
                        <li>Содержат ненормативную лексику</li>
                        <li>Нарушают законодательство</li>
                        <li>Содержат рекламу или спам</li>
                        <li>Являются оскорбительными или клеветническими</li>
                    </ul>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">7. Ограничение ответственности</h2>
                    <p>7.1. Компания не несет ответственности за:</p>
                    <ul>
                        <li>Неточности в описании товаров</li>
                        <li>Временную недоступность Сайта</li>
                        <li>Действия третьих лиц</li>
                        <li>Убытки, возникшие в результате использования Сайта</li>
                        <li>Несовместимость товаров с конкретными автомобилями</li>
                    </ul>
                    <p>7.2. Все рекомендации на Сайте носят информационный характер.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">8. Ссылки на другие сайты</h2>
                    <p>8.1. Сайт может содержать ссылки на сторонние ресурсы.</p>
                    <p>8.2. Компания не несет ответственности за содержание и политику конфиденциальности сторонних сайтов.</p>
                    <p>8.3. Размещение ссылок не означает одобрения или рекомендации.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">9. Применимое право</h2>
                    <p>9.1. Все споры и разногласия регулируются законодательством Российской Федерации.</p>
                    <p>9.2. Компания и пользователь будут стремиться урегулировать все споры путем переговоров.</p>
                    <p>9.3. При невозможности урегулирования спора мирным путем, он подлежит рассмотрению в суде по месту нахождения Компании.</p>
                </section>
                <section class="mb-5">
                    <h2 class="mb-3">10. Контактная информация</h2>
                    <p>10.1. По всем вопросам, связанным с настоящими Условиями, обращайтесь:</p>
                    <div class="contact-info">
                        <p><strong>ООО "Лал-Авто"</strong></p>
                        <p>Адрес: 236000, г. Калининград, ул. Автомобильная, д. 12</p>
                        <p>Телефон: +7 (4012) 65-65-65</p>
                        <p>Email: info@lal-auto.ru</p>
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
</body>
</html>