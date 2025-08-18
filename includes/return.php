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
    <title>Возврат и обмен - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/return-styles.css">
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
            <h1 class="mb-3" style="padding-top: 60px;">Возврат и обмен товара</h1>
            <p class="lead">Условия возврата и обмена товаров в магазинах "Лал-Авто"</p>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="return-card">
                <h3 class="mb-4"><i class="bi bi-arrow-left-circle"></i> Условия возврата</h3>
                <p>Согласно Закону "О защите прав потребителей", вы можете вернуть товар надлежащего качества в течение 14 дней с момента покупки, если:</p>
                <ul>
                    <li>Товар не был в употреблении</li>
                    <li>Сохранены товарный вид и потребительские свойства</li>
                    <li>Сохранились фабричные ярлыки и упаковка</li>
                    <li>Имеется товарный или кассовый чек</li>
                </ul>
                <p><strong>Товары, не подлежащие возврату:</strong></p>
                <ul>
                    <li>Автомобильные химические товары (масла, жидкости, очистители)</li>
                    <li>Фильтры и расходные материалы</li>
                    <li>Товары, изготовленные по индивидуальному заказу</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <div class="return-card">
                <h3 class="mb-4"><i class="bi bi-arrow-repeat"></i> Условия обмена</h3>
                <p>Вы можете обменить товар надлежащего качества на аналогичный в течение 14 дней, если:</p>
                <ul>
                    <li>Товар не подошел по размеру, форме, габаритам</li>
                    <li>Товар не подошел по цвету или комплектации</li>
                    <li>Товар не был в употреблении</li>
                    <li>Сохранены все ярлыки и упаковка</li>
                </ul>
                <p><strong>Процедура обмена:</strong></p>
                <ol>
                    <li>Обратитесь в магазин с чеком и товаром</li>
                    <li>Заполните заявление на обмен</li>
                    <li>При наличии нужного товара обмен производится сразу</li>
                    <li>При отсутствии товара мы сообщим вам о его поступлении</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="return-process">
                <h3 class="mb-4 text-center"><i class="bi bi-clipboard-check"></i> Процедура возврата</h3>
                <div class="process-steps">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h5>Обращение в магазин</h5>
                            <p>Придите в любой магазин нашей сети с товаром и документом, подтверждающим покупку (чек, гарантийный талон).</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h5>Проверка товара</h5>
                            <p>Наш сотрудник проверит соответствие товара условиям возврата.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h5>Заполнение заявления</h5>
                            <p>Вы заполните заявление на возврат с указанием причины.</p>
                        </div>
                    </div>
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h5>Возврат денежных средств</h5>
                            <p>При положительном решении деньги будут возвращены тем же способом, которым была произведена оплата.</p>
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
</body>
</html>