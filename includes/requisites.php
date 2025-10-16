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

        let copyButtons = document.querySelectorAll('.copy-btn');
        copyButtons.forEach(button => {
            button.addEventListener('click', function() 
            {
                let textToCopy = this.getAttribute('data-copy');
                navigator.clipboard.writeText(textToCopy).then(() => {
                    let originalText = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check"></i> Скопировано';
                    this.classList.add('btn-success');

                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.classList.remove('btn-success');
                    }, 2000);
                });
            });
        });

        let downloadButtons = document.querySelectorAll('.download-btn');

        downloadButtons.forEach(button => {
            button.addEventListener('click', function(e) 
            {
                e.preventDefault();
                let fileName = this.getAttribute('data-filename');
                let originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-download"></i> Скачивание...';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                    alert(`Файл "${fileName}" будет скачан`);
                }, 1500);
            });
        });
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5 pt-4">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold text-primary mb-3" style="padding-top: 60px;">Реквизиты компании</h1>
            <p class="lead fs-5 text-muted">Официальная информация о компании ООО "Лал-Авто"</p>
        </div>
    </div>
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="bi bi-info-circle-fill fs-4 me-3"></i>
        <div>
            <strong>Актуальная информация:</strong> Реквизиты действительны на <?php echo date('d.m.Y'); ?> года. 
            Для получения печатной версии обратитесь в бухгалтерию.
        </div>
    </div>
    <div class="row g-4 requisites-row">
        <div class="col-lg-12">
            <div class="requisites-card w-100">
                <div class="card-header-custom">
                    <i class="bi bi-building fs-2 me-3"></i>
                    <h3 class="mb-0">Общая информация</h3>
                </div>
                <div class="requisites-list">
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Полное наименование:</span>
                            <span class="requisite-value">Общество с ограниченной ответственностью "Лал-Авто"</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy='Общество с ограниченной ответственностью "Лал-Авто"'>
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Сокращенное наименование:</span>
                            <span class="requisite-value">ООО "Лал-Авто"</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy='ООО "Лал-Авто"'>
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">ИНН:</span>
                            <span class="requisite-value">3900000000</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="3900000000">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">КПП:</span>
                            <span class="requisite-value">390001001</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="390001001">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">ОГРН:</span>
                            <span class="requisite-value">1023900000000</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="1023900000000">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">ОКПО:</span>
                            <span class="requisite-value">12345678</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="12345678">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">ОКВЭД:</span>
                            <span class="requisite-value">45.32.1 Торговля автомобильными деталями, узлами и принадлежностями</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="45.32.1 Торговля автомобильными деталями, узлами и принадлежностями">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="requisites-card w-100">
                <div class="card-header-custom">
                    <i class="bi bi-bank fs-2 me-3"></i>
                    <h3 class="mb-0">Банковские реквизиты</h3>
                </div>
                <div class="requisites-list">
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Расчетный счет:</span>
                            <span class="requisite-value">40702810500000000001</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="40702810500000000001">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Банк:</span>
                            <span class="requisite-value">ПАО "Сбербанк"</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy='ПАО "Сбербанк"'>
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">БИК:</span>
                            <span class="requisite-value">044525225</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="044525225">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Корреспондентский счет:</span>
                            <span class="requisite-value">30101810400000000225</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="30101810400000000225">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Юридический адрес банка:</span>
                            <span class="requisite-value">117997, г. Москва, ул. Вавилова, д. 19</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="117997, г. Москва, ул. Вавилова, д. 19">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="requisites-card w-100">
                <div class="card-header-custom">
                    <i class="bi bi-geo-alt fs-2 me-3"></i>
                    <h3 class="mb-0">Адреса и контакты</h3>
                </div>
                <div class="requisites-list">
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Юридический адрес:</span>
                            <span class="requisite-value">236000, г. Калининград, ул. Автомобильная, д. 12</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="236000, г. Калининград, ул. Автомобильная, д. 12">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Фактический адрес:</span>
                            <span class="requisite-value">236000, г. Калининград, ул. Автомобильная, д. 12</span>
                        </div>
                    <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="236000, г. Калининград, ул. Автомобильная, д. 12">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Телефон:</span>
                            <span class="requisite-value">+7 (4012) 65-65-65</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="+74012656565">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Email:</span>
                            <span class="requisite-value">info@lal-auto.ru</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="info@lal-auto.ru">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Сайт:</span>
                            <span class="requisite-value">www.lal-auto.ru</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="www.lal-auto.ru">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="requisites-card w-100">
                <div class="card-header-custom">
                    <i class="bi bi-person fs-2 me-3"></i>
                    <h3 class="mb-0">Руководство</h3>
                </div>
                <div class="requisites-list">
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Генеральный директор:</span>
                            <span class="requisite-value">Иванов Петр Сергеевич</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="Иванов Петр Сергеевич">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Главный бухгалтер:</span>
                            <span class="requisite-value">Смирнова Ольга Владимировна</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="Смирнова Ольга Владимировна">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <div class="requisite-item">
                        <div class="requisite-content">
                            <span class="requisite-label">Действует на основании:</span>
                            <span class="requisite-value">Устава</span>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary copy-btn" data-copy="Устава">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="documents-section">
                <div class="section-header text-center mb-5">
                    <h2 class="display-5 fw-bold text-primary"><i class="bi bi-file-earmark-text me-3"></i>Документы компании</h2>
                    <p class="lead text-muted">Официальные документы для скачивания</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </div>
                            <h5>Устав компании</h5>
                            <p class="document-meta">PDF, 2.3 MB</p>
                            <p class="document-desc">Учредительный документ ООО "Лал-Авто"</p>
                            <a href="#" class="btn btn-primary download-btn" data-filename="Устав_ООО_Лал-Авто.pdf">
                                <i class="bi bi-download me-2"></i>Скачать
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </div>
                            <h5>Свидетельство ОГРН</h5>
                            <p class="document-meta">PDF, 1.8 MB</p>
                            <p class="document-desc">Свидетельство о государственной регистрации</p>
                            <a href="#" class="btn btn-primary download-btn" data-filename="Свидетельство_ОГРН.pdf">
                                <i class="bi bi-download me-2"></i>Скачать
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="document-card">
                            <div class="document-icon">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </div>
                            <h5>Свидетельство ИНН</h5>
                            <p class="document-meta">PDF, 1.5 MB</p>
                            <p class="document-desc">Свидетельство о постановке на налоговый учет</p>
                            <a href="#" class="btn btn-primary download-btn" data-filename="Свидетельство_ИНН.pdf">
                                <i class="bi bi-download me-2"></i>Скачать
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <div class="support-section bg-primary text-white rounded-3 p-5 text-center position-relative overflow-hidden">
                <div class="position-relative z-1">
                    <h3 class="mb-3 fw-bold">Нужна помощь с реквизитами?</h3>
                    <p class="lead mb-4 opacity-90">Наши специалисты готовы помочь с оформлением документов и ответить на все вопросы</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="tel:+74012656565" class="btn btn-light btn-lg fw-bold px-4 py-2">
                            <i class="bi bi-telephone me-2"></i>+7 (4012) 65-65-65
                        </a>
                        <a href="mailto:info@lal-auto.ru" class="btn btn-outline-light btn-lg fw-bold px-4 py-2">
                            <i class="bi bi-envelope me-2"></i>Написать на почту
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
<script src="../js/script.js"></script>
</body>
</html>