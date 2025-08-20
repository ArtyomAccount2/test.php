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
        <div class="col-12 text-center">
            <h1 class="mb-3" style="padding-top: 60px;">Реквизиты компании</h1>
            <p class="lead">Официальная информация о компании Лал-Авто</p>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="requisites-card">
                <h3 class="mb-4"><i class="bi bi-building"></i> Общая информация</h3>
                <div class="requisites-list">
                    <div class="requisite-item">
                        <span class="requisite-label">Полное наименование:</span>
                        <span class="requisite-value">Общество с ограниченной ответственностью "Лал-Авто"</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Сокращенное наименование:</span>
                        <span class="requisite-value">ООО "Лал-Авто"</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">ИНН:</span>
                        <span class="requisite-value">3900000000</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">КПП:</span>
                        <span class="requisite-value">390001001</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">ОГРН:</span>
                        <span class="requisite-value">1023900000000</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">ОКПО:</span>
                        <span class="requisite-value">12345678</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">ОКВЭД:</span>
                        <span class="requisite-value">45.32.1 Торговля автомобильными деталями, узлами и принадлежностями</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="requisites-card">
                <h3 class="mb-4"><i class="bi bi-bank"></i> Банковские реквизиты</h3>
                <div class="requisites-list">
                    <div class="requisite-item">
                        <span class="requisite-label">Расчетный счет:</span>
                        <span class="requisite-value">40702810500000000001</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Банк:</span>
                        <span class="requisite-value">ПАО "Сбербанк"</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">БИК:</span>
                        <span class="requisite-value">044525225</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Корреспондентский счет:</span>
                        <span class="requisite-value">30101810400000000225</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Юридический адрес банка:</span>
                        <span class="requisite-value">117997, г. Москва, ул. Вавилова, д. 19</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="requisites-card">
                <h3 class="mb-4"><i class="bi bi-geo-alt"></i> Адреса и контакты</h3>
                <div class="requisites-list">
                    <div class="requisite-item">
                        <span class="requisite-label">Юридический адрес:</span>
                        <span class="requisite-value">236000, г. Калининград, ул. Автомобильная, д. 12</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Фактический адрес:</span>
                        <span class="requisite-value">236000, г. Калининград, ул. Автомобильная, д. 12</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Телефон:</span>
                        <span class="requisite-value">+7 (4012) 65-65-65</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Email:</span>
                        <span class="requisite-value">info@lal-auto.ru</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Сайт:</span>
                        <span class="requisite-value">www.lal-auto.ru</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="requisites-card">
                <h3 class="mb-4"><i class="bi bi-person"></i> Руководство</h3>
                <div class="requisites-list">
                    <div class="requisite-item">
                        <span class="requisite-label">Генеральный директор:</span>
                        <span class="requisite-value">Иванов Петр Сергеевич</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Главный бухгалтер:</span>
                        <span class="requisite-value">Смирнова Ольга Владимировна</span>
                    </div>
                    <div class="requisite-item">
                        <span class="requisite-label">Действует на основании:</span>
                        <span class="requisite-value">Устава</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <div class="documents-section">
                <h3 class="mb-4 text-center"><i class="bi bi-file-earmark-text"></i> Документы</h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="bi bi-file-pdf"></i>
                            </div>
                            <h5>Устав компании</h5>
                            <p>PDF, 2.3 MB</p>
                            <a href="#" class="btn btn-outline-primary">Скачать</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="bi bi-file-pdf"></i>
                            </div>
                            <h5>Свидетельство ОГРН</h5>
                            <p>PDF, 1.8 MB</p>
                            <a href="#" class="btn btn-outline-primary">Скачать</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="bi bi-file-pdf"></i>
                            </div>
                            <h5>Свидетельство ИНН</h5>
                            <p>PDF, 1.5 MB</p>
                            <a href="#" class="btn btn-outline-primary">Скачать</a>
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