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
    <title>Реквизиты - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/requisites-styles.css">
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
            <h1 class="mb-3" style="padding-top: 60px;">Реквизиты компании</h1>
            <p class="lead">Юридическая информация и банковские реквизиты</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-6">
            <div class="requisites-card">
                <h3 class="mb-4"><i class="bi bi-building"></i> Общая информация</h3>
                <div class="requisites-item">
                    <div class="requisites-label">Полное наименование:</div>
                    <div class="requisites-value">Общество с ограниченной ответственностью "Лал-Авто"</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">Юридический адрес:</div>
                    <div class="requisites-value">236022, г. Калининград, ул. Автомобильная, д. 12</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">Фактический адрес:</div>
                    <div class="requisites-value">236022, г. Калининград, ул. Автомобильная, д. 12</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">ИНН:</div>
                    <div class="requisites-value">3905123456</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">КПП:</div>
                    <div class="requisites-value">390501001</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">ОГРН:</div>
                    <div class="requisites-value">1123905001234</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">ОКПО:</div>
                    <div class="requisites-value">12345678</div>
                </div>
            </div>
        </div>    
        <div class="col-md-6">
            <div class="requisites-card">
                <h3 class="mb-4"><i class="bi bi-bank"></i> Банковские реквизиты</h3>
                <div class="requisites-item">
                    <div class="requisites-label">Банк:</div>
                    <div class="requisites-value">ПАО "Сбербанк"</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">БИК:</div>
                    <div class="requisites-value">042202612</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">Корр. счет:</div>
                    <div class="requisites-value">30101810000000000612</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">Расчетный счет:</div>
                    <div class="requisites-value">40702810512340123456</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">Генеральный директор:</div>
                    <div class="requisites-value">Иванов Иван Иванович</div>
                </div>
                <div class="requisites-item">
                    <div class="requisites-label">Действует на основании:</div>
                    <div class="requisites-value">Устава</div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <div class="documents-card">
                <h3 class="mb-4"><i class="bi bi-file-earmark-text"></i> Документы</h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="docs/ustav.pdf" class="document-item" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <span>Устав компании</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="docs/certificate.pdf" class="document-item" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <span>Свидетельство о регистрации</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="docs/license.pdf" class="document-item" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <span>Лицензии</span>
                        </a>
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