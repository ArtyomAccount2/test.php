<?php
error_reporting(E_ALL);
session_start();
require_once("../config/link.php");
require_once("../config/check_auth.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../files/logout.php");
}

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

    if (isset($_POST['support_submit'])) 
    {
        $form_token = md5(serialize($_POST) . serialize($_FILES));
        
        if (isset($_SESSION['last_support_form_token']) && $_SESSION['last_support_form_token'] === $form_token) 
        {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }

        $problem_type = $_POST['problem_type'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $consent = isset($_POST['consent']) ? 1 : 0;
        
        $errors = [];
        
        if (empty($problem_type)) 
        {
            $errors[] = "Пожалуйста, выберите тип проблемы";
        }
        
        if (empty($email)) 
        {
            $errors[] = "Пожалуйста, укажите email для обратной связи";
        } 
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $errors[] = "Пожалуйста, введите корректный email";
        }
        
        if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) 
        {
            $errors[] = "Пожалуйста, введите корректный URL";
        }
        
        if (empty($description)) 
        {
            $errors[] = "Пожалуйста, опишите проблему";
        } 
        else if (strlen($description) > 1000) 
        {
            $errors[] = "Описание не должно превышать 1000 символов";
        }
        
        if (!$consent) 
        {
            $errors[] = "Необходимо ваше согласие на обработку персональных данных";
        }

        $screenshot_file = '';

        if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] == 0) 
        {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $max_size = 5 * 1024 * 1024;
            
            if (!in_array($_FILES['screenshot']['type'], $allowed_types)) 
            {
                $errors[] = "Разрешены только изображения JPG, PNG, GIF";
            } 
            else if ($_FILES['screenshot']['size'] > $max_size) 
            {
                $errors[] = "Размер файла не должен превышать 5MB";
            } 
            else 
            {
                $upload_dir = "../uploads/support/";

                if (!file_exists($upload_dir)) 
                {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION);
                $screenshot_file = time() . '_' . uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $screenshot_file;
                
                if (!move_uploaded_file($_FILES['screenshot']['tmp_name'], $upload_path)) 
                {
                    $errors[] = "Ошибка при загрузке файла";
                }
            }
        }

        if (empty($errors)) 
        {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            $insert_query = "INSERT INTO support_requests (problem_type, email, url, description, screenshot, status, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, 'new', ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sssssss", $problem_type, $email, $url, $description, $screenshot_file, $ip_address, $user_agent);
            
            if ($stmt->execute()) 
            {
                $_SESSION['last_support_form_token'] = $form_token;
                $_SESSION['show_support_success_modal'] = true;

                $admin_email = "support@lal-auto.ru";
                $email_subject = "Новое обращение в поддержку: $problem_type";
                
                $problem_types = [
                    'technical' => 'Техническая ошибка',
                    'function' => 'Не работает функция',
                    'display' => 'Некорректное отображение',
                    'performance' => 'Медленная работа',
                    'security' => 'Проблема безопасности',
                    'other' => 'Другое'
                ];
                
                $problem_type_rus = $problem_types[$problem_type] ?? $problem_type;
                
                $email_message = "Тип проблемы: $problem_type_rus\n";
                $email_message .= "Email: $email\n";
                $email_message .= "URL: " . ($url ?: 'Не указан') . "\n";
                $email_message .= "Описание:\n$description\n";
                $email_message .= "Скриншот: " . ($screenshot_file ? "Приложен" : "Не приложен") . "\n";
                $email_message .= "IP: $ip_address\n";
                
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } 
            else 
            {
                $error_message = "Произошла ошибка при отправке сообщения: " . $conn->error;
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

if (isset($_SESSION['show_support_success_modal']) && $_SESSION['show_support_success_modal'] === true) 
{
    $show_success_modal = true;
    unset($_SESSION['show_support_success_modal']);
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поддержка сайта - Лал-Авто</title>
    <link rel="icon" href="../img/iconAuto.png" type="image/png" height="32">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/support-styles.css">
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

        let problemForm = document.getElementById('problemForm');

        if (problemForm) 
        {
            problemForm.addEventListener('submit', function(e) 
            {
                if (!this.checkValidity()) 
                {
                    e.preventDefault();
                    e.stopPropagation();
                }

                this.classList.add('was-validated');
            });
        }

        let descriptionField = document.getElementById('description');
        let charCount = document.createElement('div');
        charCount.className = 'form-text text-end';
        charCount.textContent = '0/1000 символов';
        
        if (descriptionField) 
        {
            descriptionField.parentNode.appendChild(charCount);

            descriptionField.addEventListener('input', function()
            {
                let count = this.value.length;
                charCount.textContent = `${count}/1000 символов`;

                if (count > 1000) 
                {
                    charCount.classList.add('text-danger');
                    this.value = this.value.substring(0, 1000);
                } 
                else 
                {
                    charCount.classList.remove('text-danger');
                }
            });
        }

        function equalizeCardHeights() 
        {
            let cards = document.querySelectorAll('.support-card');
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

        window.addEventListener('load', equalizeCardHeights);
        window.addEventListener('resize', equalizeCardHeights);

        let urlInput = document.getElementById('url');

        if (urlInput) 
        {
            urlInput.addEventListener('input', function() 
            {
                if (this.value && !this.value.startsWith('http')) 
                {
                    this.value = 'https://' + this.value;
                }
            });
        }

        let loginModal = document.getElementById('loginModal');

        if (loginModal) 
        {
            loginModal.addEventListener('show.bs.modal', function() 
            {
                let loginForm = document.querySelector('#loginModal form');

                if (loginForm) 
                {
                    loginForm.reset();
                }
            });
        }
    });
    </script>
</head>
<body>

<?php 
    require_once("header.php"); 
?>

<div class="container my-4">
    <div class="hero-section text-center mb-5" style="padding-top: 105px;">
        <h1 class="display-5 fw-bold text-primary mb-3">Служба поддержки</h1>
        <p class="lead text-muted mb-4">Помощь в работе с сайтом и техническая поддержка</p>
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
    <div class="row g-3 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="support-card">
                <div class="card-body-custom text-center p-4">
                    <div class="support-icon mb-3">
                        <i class="bi bi-question-circle"></i>
                    </div>
                    <h4 class="mb-3">Частые вопросы</h4>
                    <p class="text-muted mb-3">Ответы на самые популярные вопросы по работе с сайтом</p>
                    <a href="faq.php" class="btn btn-outline-primary w-100">Смотреть FAQ</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="support-card">
                <div class="card-body-custom text-center p-4">
                    <div class="support-icon mb-3">
                        <i class="bi bi-book"></i>
                    </div>
                    <h4 class="mb-3">Инструкции</h4>
                    <p class="text-muted mb-3">Пошаговые руководства по использованию функционала сайта</p>
                    <a href="#instructions" class="btn btn-outline-primary w-100">Читать инструкции</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="support-card">
                <div class="card-body-custom text-center p-4">
                    <div class="support-icon mb-3">
                        <i class="bi bi-bug"></i>
                    </div>
                    <h4 class="mb-3">Сообщить о проблеме</h4>
                    <p class="text-muted mb-3">Нашли ошибку или неработающую функцию? Сообщите нам</p>
                    <a href="#report" class="btn btn-outline-primary w-100">Сообщить о проблеме</a>
                </div>
            </div>
        </div>
    </div>
    <div class="support-content-wrapper">
        <div class="support-content">
            <section id="instructions" class="mb-5">
                <div class="section-header mb-4">
                    <i class="bi bi-book section-icon"></i>
                    <h2 class="section-title">Инструкции по работе с сайтом</h2>
                </div>
                <div class="accordion" id="instructionsAccordion">
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#instruction1" aria-expanded="true" aria-controls="instruction1">
                                Как зарегистрироваться на сайте
                            </button>
                        </h3>
                        <div id="instruction1" class="accordion-collapse collapse show" data-bs-parent="#instructionsAccordion">
                            <div class="accordion-body">
                                <ol class="mb-0">
                                    <li>Нажмите кнопку "Зарегистрироваться" в правом верхнем углу</li>
                                    <li>Выберите тип регистрации: физическое или юридическое лицо</li>
                                    <li>Заполните все обязательные поля формы</li>
                                    <li>Подтвердите email, перейдя по ссылке в письме</li>
                                    <li>Войдите в систему используя свои учетные данные</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#instruction2" aria-expanded="false" aria-controls="instruction2">
                                Как сделать заказ
                            </button>
                        </h3>
                        <div id="instruction2" class="accordion-collapse collapse" data-bs-parent="#instructionsAccordion">
                            <div class="accordion-body">
                                <ol class="mb-0">
                                    <li>Найдите нужный товар через поиск или каталог</li>
                                    <li>Добавьте товар в корзину, указав количество</li>
                                    <li>Перейдите в корзину и проверьте состав заказа</li>
                                    <li>Выберите способ доставки и оплаты</li>
                                    <li>Подтвердите заказ и дождитесь звонка менеджера</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#instruction3" aria-expanded="false" aria-controls="instruction3">
                                Как отследить статус заказа
                            </button>
                        </h3>
                        <div id="instruction3" class="accordion-collapse collapse" data-bs-parent="#instructionsAccordion">
                            <div class="accordion-body">
                                <ol class="mb-0">
                                    <li>Войдите в свой аккаунт</li>
                                    <li>Перейдите в раздел "Мои заказы"</li>
                                    <li>Выберите нужный заказ из списка</li>
                                    <li>Просмотрите текущий статус в карточке заказа</li>
                                    <li>При необходимости свяжитесь с менеджером</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section id="report" class="mb-5">
                <div class="section-header mb-4">
                    <i class="bi bi-bug section-icon"></i>
                    <h2 class="section-title">Сообщить о проблеме</h2>
                </div>
                <div class="report-form-container">
                    <form id="problemForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" class="needs-validation" novalidate>
                        <input type="hidden" name="support_submit" value="1">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="problem_type" class="form-label fw-semibold">
                                    <i class="bi bi-tag me-1"></i>Тип проблемы<span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="problem_type" name="problem_type" required>
                                    <option value="" <?php echo empty($_POST['problem_type']) ? 'selected' : ''; ?> disabled>Выберите тип проблемы</option>
                                    <option value="technical" <?php echo (isset($_POST['problem_type']) && $_POST['problem_type'] == 'technical') ? 'selected' : ''; ?>>Техническая ошибка</option>
                                    <option value="function" <?php echo (isset($_POST['problem_type']) && $_POST['problem_type'] == 'function') ? 'selected' : ''; ?>>Не работает функция</option>
                                    <option value="display" <?php echo (isset($_POST['problem_type']) && $_POST['problem_type'] == 'display') ? 'selected' : ''; ?>>Некорректное отображение</option>
                                    <option value="performance" <?php echo (isset($_POST['problem_type']) && $_POST['problem_type'] == 'performance') ? 'selected' : ''; ?>>Медленная работа</option>
                                    <option value="security" <?php echo (isset($_POST['problem_type']) && $_POST['problem_type'] == 'security') ? 'selected' : ''; ?>>Проблема безопасности</option>
                                    <option value="other" <?php echo (isset($_POST['problem_type']) && $_POST['problem_type'] == 'other') ? 'selected' : ''; ?>>Другое</option>
                                </select>
                                <div class="invalid-feedback">Пожалуйста, выберите тип проблемы</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="bi bi-envelope me-1"></i>Email для обратной связи<span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="your@email.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                <div class="invalid-feedback">Пожалуйста, введите корректный email</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label fw-semibold">
                                <i class="bi bi-link me-1"></i>URL страницы (если применимо)
                            </label>
                            <input type="url" class="form-control" id="url" name="url" placeholder="https://..." value="<?php echo htmlspecialchars($_POST['url'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">
                                <i class="bi bi-chat-text me-1"></i>Описание проблемы<span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Подробно опишите возникшую проблему, шаги для её воспроизведения и ожидаемый результат..." 
                                      required maxlength="1000"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                            <div class="invalid-feedback">Пожалуйста, опишите проблему</div>
                        </div>
                        <div class="mb-4">
                            <label for="screenshot" class="form-label fw-semibold">
                                <i class="bi bi-image me-1"></i>Скриншот (опционально)
                            </label>
                            <input type="file" class="form-control" id="screenshot" name="screenshot" accept="image/jpeg,image/png,image/gif">
                            <div class="form-text">Поддерживаемые форматы: JPG, PNG, GIF. Максимальный размер: 5MB</div>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="consent" name="consent" required <?php echo (isset($_POST['consent']) || empty($_POST)) ? 'checked' : ''; ?>>
                            <label class="form-check-label small" for="consent">
                                Я согласен на обработку персональных данных
                            </label>
                            <div class="invalid-feedback">Необходимо ваше согласие</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="bi bi-send me-2"></i>Отправить сообщение
                        </button>
                    </form>
                </div>
            </section>
            <section class="contact-support">
                <div class="section-header mb-4">
                    <i class="bi bi-headset section-icon"></i>
                    <h2 class="section-title">Служба поддержки</h2>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="contact-method">
                            <div class="contact-icon">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div class="contact-info">
                                <h5>Телефон</h5>
                                <p class="contact-detail">+7 (4012) 65-65-65</p>
                                <p class="contact-hours">Пн-Пт: 9:00-18:00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="contact-method">
                            <div class="contact-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="contact-info">
                                <h5>Email</h5>
                                <p class="contact-detail">support@lal-auto.ru</p>
                                <p class="contact-hours">Ответ в течение 24 часов</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="contact-method">
                            <div class="contact-icon">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <div class="contact-info">
                                <h5>Онлайн-чат</h5>
                                <p class="contact-detail">Доступен на сайте</p>
                                <p class="contact-hours">Круглосуточно</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="contact-method">
                            <div class="contact-icon">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="contact-info">
                                <h5>Время ответа</h5>
                                <p class="contact-detail">15-30 минут</p>
                                <p class="contact-hours">В рабочее время</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle-fill me-2"></i>Сообщение отправлено
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Ваше сообщение успешно отправлено в службу поддержки! Мы ответим вам в ближайшее время.</p>
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
</body>
</html>