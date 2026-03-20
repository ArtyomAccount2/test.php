<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");
require_once("../config/check_auth.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    if (isset($_POST['login']) && isset($_POST['password'])) 
    {
        $login = $_POST['login'];
        $password = $_POST['password'];

        if (strtolower($login) === 'admin' && strtolower($password) === 'admin') 
        {
            $_SESSION['login_error'] = true;
            $_SESSION['error_message'] = "Неверный логин или пароль!";
            header("Location: " . $_SERVER['REQUEST_URI']);
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
                $_SESSION['user_id'] = $row['id_users'];
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

    if (isset($_POST['supplier_submit'])) 
    {
        $form_token = md5(serialize($_POST));
        
        if (isset($_SESSION['last_supplier_form_token']) && $_SESSION['last_supplier_form_token'] === $form_token) {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }

        $company_name = trim($_POST['company_name'] ?? '');
        $contact_person = trim($_POST['contact_person'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $product_category = $_POST['product_category'] ?? '';
        $message = trim($_POST['message'] ?? '');
        $agree = isset($_POST['agree']) ? 1 : 0;
        
        $errors = [];
        
        if (empty($company_name)) 
        {
            $errors[] = "Пожалуйста, укажите название компании";
        }
        
        if (empty($contact_person)) 
        {
            $errors[] = "Пожалуйста, укажите контактное лицо";
        }
        
        if (empty($phone)) 
        {
            $errors[] = "Пожалуйста, укажите телефон";
        } 
        else if (!preg_match('/^[\+\-\d\s\(\)]{10,}$/', $phone)) 
        {
            $errors[] = "Пожалуйста, введите корректный номер телефона";
        }
        
        if (empty($email)) 
        {
            $errors[] = "Пожалуйста, укажите email";
        } 
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $errors[] = "Пожалуйста, введите корректный email";
        }
        
        if (empty($product_category)) 
        {
            $errors[] = "Пожалуйста, выберите категорию товаров";
        }
        
        if (!$agree) 
        {
            $errors[] = "Необходимо ваше согласие на обработку персональных данных";
        }

        $file_name = '';

        if (isset($_FILES['price_file']) && $_FILES['price_file']['error'] == 0) 
        {
            $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            $max_size = 10 * 1024 * 1024;
            
            if (!in_array($_FILES['price_file']['type'], $allowed_types)) 
            {
                $errors[] = "Разрешены только файлы PDF, DOC, XLS";
            } 
            else if ($_FILES['price_file']['size'] > $max_size) 
            {
                $errors[] = "Размер файла не должен превышать 10MB";
            } 
            else 
            {
                $upload_dir = "../uploads/suppliers/";

                if (!file_exists($upload_dir)) 
                {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['price_file']['name'], PATHINFO_EXTENSION);
                $file_name = time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $file_name;
                
                if (!move_uploaded_file($_FILES['price_file']['tmp_name'], $upload_path)) 
                {
                    $errors[] = "Ошибка при загрузке файла";
                }
            }
        }

        if (empty($errors)) 
        {
            $insert_query = "INSERT INTO supplier_requests (company_name, contact_person, phone, email, product_category, message, price_file, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'new')";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sssssss", $company_name, $contact_person, $phone, $email, $product_category, $message, $file_name);
            
            if ($stmt->execute()) 
            {
                $_SESSION['last_supplier_form_token'] = $form_token;
                $_SESSION['show_supplier_success_modal'] = true;
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } 
            else 
            {
                $error_message = "Произошла ошибка при отправке заявки: " . $conn->error;
            }

            $stmt->close();
        } 
        else 
        {
            $error_message = implode("<br>", $errors);
        }
    }
}

$show_success_modal = false;

if (isset($_SESSION['show_supplier_success_modal']) && $_SESSION['show_supplier_success_modal'] === true) 
{
    $show_success_modal = true;
    unset($_SESSION['show_supplier_success_modal']);
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
    <link rel="icon" href="../img/iconAuto.png" type="image/png" height="32">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
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

        if (!empty($error_message))
        {
        ?>
            setTimeout(function() 
            {
                alert('<?= addslashes($error_message) ?>');
            }, 100);
        <?php 
        }

        if ($show_success_modal)
        {
        ?>
            setTimeout(function() 
            {
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            }, 100);
        <?php 
        }
        ?>

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
                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="bi bi-7-circle"></i>
                            </div>
                            <div class="requirement-content">
                                <h6>Техническая поддержка</h6>
                                <p class="mb-0">Наличие сервисной службы и консультационной поддержки по продукции</p>
                            </div>
                        </div>
                        <div class="requirement-item" style="background: #e7f1ff; border-left-color: #0d6efd;">
                            <div class="requirement-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="requirement-content">
                                <h6>Сроки рассмотрения</h6>
                                <p class="mb-0">Рассмотрение заявки - 2 рабочих дня. Полный процесс отбора - до 7 рабочих дней</p>
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
                    <?php 
                    if (!empty($error_message))
                    {
                    ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $error_message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php 
                    }
                    ?>
                    <form id="supplierForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <input type="hidden" name="supplier_submit" value="1">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label fw-semibold">
                                    <i class="bi bi-building me-1"></i>Название компании<span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="company_name" name="company_name" required placeholder="ООО «Лал-Авто»" value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>">
                                <div class="invalid-feedback">Пожалуйста, укажите название компании</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label fw-semibold">
                                    <i class="bi bi-person me-1"></i>Контактное лицо<span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" required placeholder="Иванов А.П." value="<?php echo htmlspecialchars($_POST['contact_person'] ?? ''); ?>">
                                <div class="invalid-feedback">Пожалуйста, укажите контактное лицо</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label fw-semibold">
                                    <i class="bi bi-phone me-1"></i>Телефон<span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" required placeholder="+7 (495) 123-45-67" pattern="[\+\-\d\s\(\)]{10,}" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                <div class="invalid-feedback">Пожалуйста, укажите телефон</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope me-1"></i>Email<span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" required placeholder="info@lal-avto.ru" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                <div class="invalid-feedback">Пожалуйста, укажите корректный email</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="product_category" class="form-label fw-semibold">
                                <i class="bi bi-tags me-1"></i>Категория товаров<span class="text-danger">*</span>
                            </label>
                            <select class="form-select" id="product_category" name="product_category" required>
                                <option value="" <?php echo empty($_POST['product_category']) ? 'selected' : ''; ?> disabled>Выберите категорию</option>
                                <option value="Автозапчасти" <?php echo (isset($_POST['product_category']) && $_POST['product_category'] == 'Автозапчасти') ? 'selected' : ''; ?>>Автозапчасти</option>
                                <option value="Масла и жидкости" <?php echo (isset($_POST['product_category']) && $_POST['product_category'] == 'Масла и жидкости') ? 'selected' : ''; ?>>Масла и жидкости</option>
                                <option value="Аксессуары" <?php echo (isset($_POST['product_category']) && $_POST['product_category'] == 'Аксессуары') ? 'selected' : ''; ?>>Аксессуары</option>
                                <option value="Автохимия" <?php echo (isset($_POST['product_category']) && $_POST['product_category'] == 'Автохимия') ? 'selected' : ''; ?>>Автохимия</option>
                                <option value="Шины и диски" <?php echo (isset($_POST['product_category']) && $_POST['product_category'] == 'Шины и диски') ? 'selected' : ''; ?>>Шины и диски</option>
                                <option value="Инструменты" <?php echo (isset($_POST['product_category']) && $_POST['product_category'] == 'Инструменты') ? 'selected' : ''; ?>>Инструменты</option>
                                <option value="Другое" <?php echo (isset($_POST['product_category']) && $_POST['product_category'] == 'Другое') ? 'selected' : ''; ?>>Другое</option>
                            </select>
                            <div class="invalid-feedback">Пожалуйста, выберите категорию</div>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label fw-semibold">
                                <i class="bi bi-chat-text me-1"></i>О компании и продукции
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="3" placeholder="Расскажите о вашей компании и продукции..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price_file" class="form-label fw-semibold">
                                <i class="bi bi-file-earmark-text me-1"></i>Прайс-лист (опционально)
                            </label>
                            <input type="file" class="form-control" id="price_file" name="price_file" accept=".pdf,.doc,.docx,.xls,.xlsx">
                            <div class="form-text">PDF, DOC, XLS до 10MB</div>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agree" name="agree" required <?php echo (isset($_POST['agree']) || empty($_POST)) ? 'checked' : ''; ?>>
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

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle-fill me-2"></i>Заявка отправлена
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Ваша заявка успешно отправлена! Мы свяжемся с вами в течение 2 рабочих дней.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
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
    var form = document.getElementById('supplierForm');
    
    if (form) 
    {
        form.addEventListener('submit', function(event) 
        {
            if (!form.checkValidity()) 
            {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    }
    
    var phoneInput = document.getElementById('phone');

    if (phoneInput) 
    {
        phoneInput.addEventListener('input', function() 
        {
            this.value = this.value.replace(/[^\d\s\(\)\+\-]/g, '');
        });
    }

    var loginModal = document.getElementById('loginModal');

    if (loginModal) 
    {
        loginModal.addEventListener('show.bs.modal', function() 
        {
            var loginForm = document.querySelector('#loginModal form');

            if (loginForm) 
            {
                loginForm.reset();
            }
        });
    }
});
</script>
</body>
</html>