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
    <title>Поддержка сайта - Лал-Авто</title>
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
            <h1 class="mb-3" style="padding-top: 60px;">Поддержка сайта</h1>
            <p class="lead">Помощь в работе с сайтом и техническая поддержка</p>
        </div>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="support-card text-center p-4">
                <div class="support-icon mb-3">
                    <i class="bi bi-question-circle"></i>
                </div>
                <h4>Частые вопросы</h4>
                <p>Ответы на самые популярные вопросы по работе с сайтом</p>
                <a href="faq.php" class="btn btn-outline-primary mt-2">Смотреть FAQ</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="support-card text-center p-4">
                <div class="support-icon mb-3">
                    <i class="bi bi-book"></i>
                </div>
                <h4>Инструкции</h4>
                <p>Пошаговые руководства по использованию функционала сайта</p>
                <a href="#instructions" class="btn btn-outline-primary mt-2">Читать инструкции</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="support-card text-center p-4">
                <div class="support-icon mb-3">
                    <i class="bi bi-bug"></i>
                </div>
                <h4>Сообщить о проблеме</h4>
                <p>Нашли ошибку или неработающую функцию? Сообщите нам</p>
                <a href="#report" class="btn btn-outline-primary mt-2">Сообщить о проблеме</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 mx-auto">
            <div class="support-content">
                <section id="instructions" class="mb-5">
                    <h3 class="mb-4"><i class="bi bi-book"></i> Инструкции по работе с сайтом</h3>
                    <div class="accordion" id="instructionsAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#instruction1">
                                    Как зарегистрироваться на сайте
                                </button>
                            </h2>
                            <div id="instruction1" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <ol>
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
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#instruction2">
                                    Как сделать заказ
                                </button>
                            </h2>
                            <div id="instruction2" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Найдите нужный товар через поиск или каталог</li>
                                        <li>Добавьте товар в корзину, указав количество</li>
                                        <li>Перейдите в корзину и проверьте состав заказа</li>
                                        <li>Выберите способ доставки и оплаты</li>
                                        <li>Подтвердите заказ и дождитесь звонка менеджера</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section id="report" class="mb-5">
                    <h3 class="mb-4"><i class="bi bi-bug"></i> Сообщить о проблеме</h3>
                    <div class="report-form">

                        <form id="problemForm" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="problemType" class="form-label fw-semibold">
                                        <i class="bi bi-tag me-1"></i>Тип проблемы<span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" id="problemType" required>
                                        <option value="" selected disabled>Выберите тип проблемы</option>
                                        <option value="technical">Техническая ошибка</option>
                                        <option value="function">Не работает функция</option>
                                        <option value="display">Некорректное отображение</option>
                                        <option value="other">Другое</option>
                                    </select>
                                    <div class="invalid-feedback">Пожалуйста, выберите тип проблемы</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contactEmail" class="form-label fw-semibold">
                                        <i class="bi bi-envelope me-1"></i>Email для обратной связи<span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control form-control-lg" id="contactEmail" placeholder="your@email.com" required>
                                    <div class="invalid-feedback">Пожалуйста, введите корректный email</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="problemUrl" class="form-label fw-semibold">
                                    <i class="bi bi-link me-1"></i>URL страницы (если применимо)
                                </label>
                                <input type="url" class="form-control form-control-lg" id="problemUrl" placeholder="https://...">
                            </div>
                            <div class="mb-3">
                                <label for="problemDescription" class="form-label fw-semibold">
                                    <i class="bi bi-chat-text me-1"></i>Описание проблемы<span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control form-control-lg" id="problemDescription" rows="5" 
                                          placeholder="Подробно опишите возникшую проблему..." required></textarea>
                                <div class="invalid-feedback">Пожалуйста, опишите проблему</div>
                                <div class="form-text">Максимум 1000 символов</div>
                            </div>
                            <div class="mb-4">
                                <label for="screenshot" class="form-label fw-semibold">
                                    <i class="bi bi-image me-1"></i>Скриншот (опционально)
                                </label>
                                <input type="file" class="form-control form-control-lg" id="screenshot" accept="image/*">
                                <div class="form-text">Максимальный размер файла: 5MB</div>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="consent" required>
                                <label class="form-check-label small" for="consent">
                                    Я согласен на обработку персональных данных
                                </label>
                                <div class="invalid-feedback">Необходимо ваше согласие</div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                                <i class="bi bi-send me-2"></i>Отправить сообщение
                            </button>
                        </form>

                    </div>
                </section>
                <section class="contact-support">
                    <h3 class="mb-4"><i class="bi bi-headset"></i> Служба поддержки</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-method">
                                <h5><i class="bi bi-telephone"></i> Телефон</h5>
                                <p>+7 (4012) 65-65-65</p>
                                <p class="text-muted">Пн-Пт: 9:00-18:00</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-method">
                                <h5><i class="bi bi-envelope"></i> Email</h5>
                                <p>support@lal-auto.ru</p>
                                <p class="text-muted">Ответ в течение 24 часов</p>
                            </div>
                        </div>
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
<script src="../js/script.js"></script>
</body>
</html>