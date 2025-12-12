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
    <title>Поставщикам - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/suppliers-styles.css">
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

        let supplierForm = document.getElementById('supplierForm');
        supplierForm.addEventListener('submit', function(e) 
        {
            e.preventDefault();

            if (this.checkValidity()) 
            {
                let submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Заявка отправлена!';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-success');
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    this.reset();
                    submitBtn.innerHTML = '<i class="bi bi-send me-2"></i>Отправить заявку';
                    submitBtn.classList.remove('btn-success');
                    submitBtn.classList.add('btn-primary');
                    submitBtn.disabled = false;
                }, 3000);
            }
        });

        function equalizeCardsHeight() 
        {
            let cards = document.querySelectorAll('.cooperation-card');
            let maxHeight = 0;
            
            cards.forEach(card => {
                card.style.height = 'auto';
                let height = card.offsetHeight;

                if (height > maxHeight) 
                {
                    maxHeight = height;
                }
            });
            
            cards.forEach(card => {
                card.style.height = maxHeight + 'px';
            });
        }

        window.addEventListener('load', equalizeCardsHeight);
        window.addEventListener('resize', equalizeCardsHeight);
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-5">
    <div class="hero-section text-center mb-5" style="padding-top: 85px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Сотрудничество с поставщиками</h1>
        <p class="lead text-muted mb-4">Станьте частью нашей сети и развивайте бизнес вместе с Лал-Авто</p>
    </div>
    <div class="benefits-section mb-5">
        <h2 class="text-center mb-4">Преимущества работы с нами</h2>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="cooperation-card h-100">
                    <div class="cooperation-icon mb-3">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h5 class="text-center mb-2">Стабильные заказы</h5>
                    <p class="text-center text-muted mb-0">Регулярные поставки и предсказуемый объем закупок</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="cooperation-card h-100">
                    <div class="cooperation-icon mb-3">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h5 class="text-center mb-2">Своевременная оплата</h5>
                    <p class="text-center text-muted mb-0">Четкие сроки оплаты и прозрачные условия расчетов</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="cooperation-card h-100">
                    <div class="cooperation-icon mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="text-center mb-2">Долгосрочное партнерство</h5>
                    <p class="text-center text-muted mb-0">Работаем с проверенными поставщиками годами</p>
                </div>
            </div>
        </div>
    </div>
    <div class="main-content-section mb-5">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-6">
                <div class="requirements-section h-100">
                    <div class="section-header mb-4">
                        <div class="d-flex align-items-center">
                            <div class="header-icon me-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <h3 class="mb-0">Требования к поставщикам</h3>
                        </div>
                    </div>
                    <div class="requirements-list">
                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="bi bi-1-circle"></i>
                            </div>
                            <div class="requirement-content">
                                <h6>Качество продукции</h6>
                                <p class="mb-0">Соответствие ГОСТ, ТУ и международным стандартам качества</p>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="bi bi-2-circle"></i>
                            </div>
                            <div class="requirement-content">
                                <h6>Сертификация</h6>
                                <p class="mb-0">Наличие всех необходимых сертификатов и разрешительной документации</p>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="bi bi-3-circle"></i>
                            </div>
                            <div class="requirement-content">
                                <h6>Стабильность поставок</h6>
                                <p class="mb-0">Соблюдение согласованных сроков и объемов поставок</p>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="bi bi-4-circle"></i>
                            </div>
                            <div class="requirement-content">
                                <h6>Конкурентные цены</h6>
                                <p class="mb-0">Предложение рыночных цен и гибкие условия сотрудничества</p>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="bi bi-5-circle"></i>
                            </div>
                            <div class="requirement-content">
                                <h6>Гарантийные обязательства</h6>
                                <p class="mb-0">Предоставление гарантии на поставляемую продукцию</p>
                            </div>
                        </div>
                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="bi bi-6-circle"></i>
                            </div>
                            <div class="requirement-content">
                                <h6>Логистические возможности</h6>
                                <p class="mb-0">Обеспечение своевременной доставки по всей территории России</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="cooperation-form-section h-100">
                    <div class="section-header mb-4">
                        <div class="d-flex align-items-center">
                            <div class="header-icon me-3">
                                <i class="bi bi-envelope-fill"></i>
                            </div>
                            <h3 class="mb-0">Стать поставщиком</h3>
                        </div>
                    </div>
                    <div class="special-offer mb-4">
                        <div class="alert alert-info mb-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-lightning-fill me-3"></i>
                                <div>
                                    <strong>Рассмотрение заявки за 2 дня!</strong><br>
                                    <small>Быстрый старт сотрудничества</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <form id="supplierForm" class="needs-validation" novalidate>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="companyName" class="form-label fw-semibold">
                                    <i class="bi bi-building me-1"></i>Название компании<span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="companyName" required placeholder="ООО «Лал-Авто»">
                                <div class="invalid-feedback">Пожалуйста, укажите название компании</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contactPerson" class="form-label fw-semibold">
                                    <i class="bi bi-person me-1"></i>Контактное лицо<span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="contactPerson" required placeholder="Иванов А.П.">
                                <div class="invalid-feedback">Пожалуйста, укажите контактное лицо</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-semibold">
                                    <i class="bi bi-phone me-1"></i>Телефон<span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="phone" required placeholder="+7 (495) 123-45-67">
                                <div class="invalid-feedback">Пожалуйста, укажите телефон</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope me-1"></i>Email<span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" required placeholder="info@lal-avto.ru">
                                <div class="invalid-feedback">Пожалуйста, укажите email</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="productCategory" class="form-label fw-semibold">
                                <i class="bi bi-tags me-1"></i>Категория товаров<span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="productCategory" required>
                                <option value="" selected disabled>Выберите категорию</option>
                                <option>Автозапчасти</option>
                                <option>Масла и жидкости</option>
                                <option>Аксессуары</option>
                                <option>Автохимия</option>
                                <option>Шины и диски</option>
                                <option>Инструменты</option>
                                <option>Другое</option>
                            </select>
                            <div class="invalid-feedback">Пожалуйста, выберите категорию</div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label fw-semibold">
                                <i class="bi bi-chat-text me-1"></i>О компании и продукции
                            </label>
                            <textarea class="form-control" id="message" rows="3" placeholder="Расскажите о вашей компании и продукции..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">
                                <i class="bi bi-file-earmark-text me-1"></i>Прайс-лист (опционально)
                            </label>
                            <input type="file" class="form-control" id="file">
                            <div class="form-text">PDF, DOC, XLS до 10MB</div>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agree" required>
                            <label class="form-check-label small" for="agree">
                                Согласен на обработку персональных данных
                            </label>
                            <div class="invalid-feedback">Необходимо ваше согласие</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="bi bi-send me-2"></i>Отправить заявку
                        </button>
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Мы свяжемся с вами в течение 2 рабочих дней
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="partners-section">
        <div class="section-header text-center mb-4">
            <div class="d-flex align-items-center justify-content-center">
                <div class="header-icon me-3">
                    <i class="bi bi-people"></i>
                </div>
                <h3 class="mb-0">Наши партнеры</h3>
            </div>
        </div>
        <div class="partners-grid">
            <div class="partner-item">
                <img src="../img/Manufacturers/BOGE.jpg" alt="Boge" class="partner-logo">
                <span class="partner-name">BOGE</span>
            </div>
            <div class="partner-item">
                <img src="../img/Manufacturers/fag.jpg" alt="Fag" class="partner-logo">
                <span class="partner-name">FAG</span>
            </div>
            <div class="partner-item">
                <img src="../img/Manufacturers/GKN.png" alt="Gkn" class="partner-logo">
                <span class="partner-name">GKN</span>
            </div>
            <div class="partner-item">
                <img src="../img/Manufacturers/Jurid-logo.png" alt="Jurid" class="partner-logo">
                <span class="partner-name">JURID</span>
            </div>
            <div class="partner-item">
                <img src="../img/Manufacturers/KNF.png" alt="Knf" class="partner-logo">
                <span class="partner-name">KNF</span>
            </div>
            <div class="partner-item">
                <img src="../img/Manufacturers/sasic.jpg" alt="Sasic" class="partner-logo">
                <span class="partner-name">SASIC</span>
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