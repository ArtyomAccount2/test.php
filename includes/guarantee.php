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
    <title>Гарантия - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/guarantee-styles.css">
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

        <section class="guarantee-hero" style="margin-top: 100px;">
            <div class="container">
                <h1 class="guarantee-title">Гарантийные обязательства</h1>
                <p class="guarantee-subtitle">Мы гарантируем качество каждой детали</p>
            </div>
        </section>

        <section class="guarantee-info">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <h2>Условия гарантии</h2>
                        <div class="guarantee-term">
                            <h3>Гарантийный срок</h3>
                            <p>На все автозапчасти, приобретенные в компании "Лал-Авто", предоставляется гарантия от 6 месяцев до 3 лет в зависимости от типа товара и производителя.</p>
                        </div>
                        <div class="guarantee-term">
                            <h3>Что покрывается гарантией?</h3>
                            <ul>
                                <li>Заводские дефекты и брак</li>
                                <li>Преждевременный износ деталей</li>
                                <li>Несоответствие заявленным характеристикам</li>
                            </ul>
                        </div>
                        <div class="guarantee-term">
                            <h3>Условия для гарантийного обслуживания</h3>
                            <ul>
                                <li>Наличие чека или другого подтверждения покупки</li>
                                <li>Соблюдение правил эксплуатации</li>
                                <li>Установка квалифицированными специалистами</li>
                                <li>Своевременное техническое обслуживание</li>
                            </ul>
                        </div>
                        <div class="guarantee-term">
                            <h3>Гарантия не распространяется на:</h3>
                            <ul>
                                <li>Естественный износ деталей</li>
                                <li>Повреждения в результате ДТП</li>
                                <li>Неправильную установку или эксплуатацию</li>
                                <li>Повреждения от внешних воздействий (коррозия, химические вещества)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="guarantee-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4>Нужна помощь?</h4>
                                    <p>Наши специалисты готовы ответить на все вопросы по гарантии</p>
                                    <div class="contact-info">
                                        <p><i class="bi bi-telephone"></i> +7 (4012) 65-65-65</p>
                                        <p><i class="bi bi-envelope"></i> support@lal-auto.ru</p>
                                        <p><i class="bi bi-clock"></i> Пн-Пт: 9:00-18:00</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="documents-section">
                            <h4>Документы</h4>
                            <div class="document-links">
                                <a href="#" class="document-link">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Гарантийный талон
                                </a>
                                <a href="#" class="document-link">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Правила гарантийного обслуживания
                                </a>
                                <a href="#" class="document-link">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Бланк рекламации
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="warranty-process">
            <div class="container">
                <h2 class="text-center">Процесс гарантийного обслуживания</h2>
                <div class="process-steps">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <h4>Обращение</h4>
                        <p>Свяжитесь с нами по телефону или через форму на сайте</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <h4>Диагностика</h4>
                        <p>Наш специалист проведет диагностику неисправности</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <h4>Решение</h4>
                        <p>При подтверждении гарантийного случая - замена или ремонт</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">4</div>
                        <h4>Завершение</h4>
                        <p>Вы получаете отремонтированное или новое изделие</p>
                    </div>
                </div>
            </div>
        </section>
    
<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>