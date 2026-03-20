<?php
error_reporting(E_ALL);
session_start();
require_once("config/link.php");
require_once("config/check_auth.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)
{
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
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

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Лал-Авто</title>
    <link rel="icon" href="img/iconAuto.png" type="image/png" height="32">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/register-styles.css">
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
    require_once("files/header.php");
?>

<div class="container my-5" style="padding-top: 85px;">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="shadow-lg border-0 rounded-3 overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-7 p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">Регистрация для физических лиц</h2>
                            <p class="text-muted">Создайте личный кабинет для доступа ко всем возможностям</p>
                        </div>
                        <form action="files/registerForm.php" method="POST" id="registrationForm">
                            <div class="mb-4">
                                <label class="form-label fw-semibold" for="fullName">ФИО<span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="surname" class="form-control" id="lastName" placeholder="Фамилия" required>
                                            <label for="lastName">Фамилия</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="username" class="form-control" id="firstName" placeholder="Имя" required>
                                            <label for="firstName">Имя</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="text" name="patronymic" class="form-control" id="middleName" placeholder="Отчество" required>
                                            <label for="middleName">Отчество</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Введите E-mail" required>
                                        <label for="email">E-mail<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="tel" name="phone" class="form-control" id="phone" placeholder="+7 911 123 45 67" required>
                                        <label for="phone">Мобильный телефон<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="login" class="form-control" id="login" placeholder="Введите логин" required>
                                        <label for="login">Логин<span class="text-danger">*</span></label>
                                        <div class="form-text">Минимум 4 символа</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Введите пароль" required>
                                        <label for="password">Пароль<span class="text-danger">*</span></label>
                                        <div class="form-text">Минимум 6 символов</div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="Повторите пароль" required>
                                <label for="confirmPassword">Повтор пароля<span class="text-danger">*</span></label>
                                <div class="invalid-feedback" id="passwordError">Пароли не совпадают</div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="discountCardCheck" name="hasDiscountCard">
                                    <label class="form-check-label fw-semibold" for="discountCardCheck">У меня есть карта скидок</label>
                                </div>
                            </div>
                            <div class="mb-3" id="discountCardNumberGroup" style="display: none;">
                                <div class="form-floating">
                                    <input type="text" name="discountCardNumber" class="form-control" id="discountCardNumber" placeholder="Введите номер карты" maxlength="6" pattern="[A-Za-z0-9]{6}">
                                    <label for="discountCardNumber">Номер карты скидок (6 символов)</label>
                                    <div class="form-text">Только буквы и цифры</div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="region" class="form-control" id="region" value="Калининградская область" readonly>
                                        <label for="region">Регион<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="city" class="form-control" id="city" placeholder="Введите город" required>
                                        <label for="city">Город<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="address" class="form-control" id="address" placeholder="Введите адрес" required>
                                <label for="address">Адрес<span class="text-danger">*</span></label>
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="agreement" required>
                                    <label class="form-check-label" for="agreement">
                                        Я согласен с <a href="#" class="text-decoration-none">Пользовательским соглашением</a> и 
                                        <a href="#" class="text-decoration-none">Политикой конфиденциальности</a><span class="text-danger">*</span>
                                    </label>
                                </div>
                            </div>
                            <input type="hidden" name="user_type" value="physical">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-semibold py-3">
                                    <i class="bi bi-person-plus me-2"></i> Зарегистрироваться
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-5 bg-light p-4 p-lg-5">
                        <div style="top: 100px;">
                            <h4 class="fw-bold mb-4">Информация для новых клиентов</h4>
                            <div class="d-flex align-items-start mb-4">
                                <div class="bg-primary text-white rounded-circle p-2 me-3 mt-1">
                                    <i class="bi bi-question-circle"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold">Вы первый раз на сайте?</h6>
                                    <p class="small text-muted mb-0">Ознакомьтесь с инструкцией ниже</p>
                                </div>
                            </div>
                            <div class="mb-4">
                                <ol class="list-steps">
                                    <li class="mb-3">
                                        <span class="fw-semibold">Нет карты скидок?</span>
                                        <p class="small text-muted mb-0">Просто заполните форму слева для регистрации</p>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-semibold">Есть карта скидок, но не зарегистрированы?</span>
                                        <p class="small text-muted mb-0">Заполните форму и укажите номер карты</p>
                                    </li>
                                    <li class="mb-3">
                                        <span class="fw-semibold">Уже зарегистрированы, но не видите скидку?</span>
                                        <p class="small text-muted mb-0">Добавьте номер карты в настройках профиля</p>
                                    </li>
                                </ol>
                            </div>
                            <div class="alert alert-warning">
                                <h6 class="alert-heading fw-bold mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Внимание!</h6>
                                <p class="small mb-2">При регистрации с указанием карты скидок вы сможете видеть цены на сайте согласно вашей скидке.</p>
                                <p class="small mb-0">Скидка появится на сайте примерно через сутки. Если этого не произошло, 
                                    <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#contactModal">напишите менеджеру</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="contactModalLabel">
                    <i class="bi bi-chat-dots me-2"></i> Связаться с менеджером
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-person-badge fs-1 text-primary"></i>
                    </div>
                    <h6 class="fw-bold">Напишите нам любым удобным способом:</h6>
                    <p class="small text-muted">Мы ответим в течение 15-30 минут в рабочее время</p>
                </div>
                <div class="contact-list">
                    <a href="https://vk.com/lalauto" class="text-decoration-none" target="_blank">
                        <div class="d-flex align-items-center p-3 mb-2 bg-light rounded-3 hover-effect">
                            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #4C75A3; width: 40px; height: 40px;">
                                <i class="bi bi-people-fill text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">ВКонтакте</div>
                                <div class="small text-muted">vk.com/lalauto</div>
                            </div>
                            <i class="bi bi-arrow-right-short fs-4" style="color: #4C75A3;"></i>
                        </div>
                    </a>
                    <a href="https://ok.ru/lalauto" class="text-decoration-none" target="_blank">
                        <div class="d-flex align-items-center p-3 mb-2 bg-light rounded-3 hover-effect">
                            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #EE8208; width: 40px; height: 40px;">
                                <i class="bi bi-person-standing text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Одноклассники</div>
                                <div class="small text-muted">ok.ru/lalauto</div>
                            </div>
                            <i class="bi bi-arrow-right-short fs-4" style="color: #EE8208;"></i>
                        </div>
                    </a>
                    <a href="https://vk.me/lalauto" class="text-decoration-none" target="_blank">
                        <div class="d-flex align-items-center p-3 mb-2 bg-light rounded-3 hover-effect">
                            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #0077FF; width: 40px; height: 40px;">
                                <i class="bi bi-chat-dots text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">VK Мессенджер</div>
                                <div class="small text-muted">Написать в чат VK</div>
                            </div>
                            <i class="bi bi-arrow-right-short fs-4" style="color: #0077FF;"></i>
                        </div>
                    </a>
                    <a href="mailto:info@lal-auto.ru" class="text-decoration-none">
                        <div class="d-flex align-items-center p-3 mb-2 bg-light rounded-3 hover-effect">
                            <div class="bg-danger text-white rounded-circle p-2 me-3">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Email</div>
                                <div class="small text-muted">info@lal-auto.ru</div>
                            </div>
                            <i class="bi bi-arrow-right-short fs-4 text-danger"></i>
                        </div>
                    </a>
                    <a href="https://yandex.ru/chat/#/connect/lalauto" class="text-decoration-none" target="_blank">
                        <div class="d-flex align-items-center p-3 mb-2 bg-light rounded-3 hover-effect">
                            <div class="rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="background-color: #FC3F1D; width: 40px; height: 40px;">
                                <i class="bi bi-chat-square-text text-white"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Яндекс Мессенджер</div>
                                <div class="small text-muted">Чат в Яндекс</div>
                            </div>
                            <i class="bi bi-arrow-right-short fs-4" style="color: #FC3F1D;"></i>
                        </div>
                    </a>
                    <a href="tel:+74012656565" class="text-decoration-none">
                        <div class="d-flex align-items-center p-3 mb-2 bg-light rounded-3 hover-effect">
                            <div class="bg-info text-white rounded-circle p-2 me-3">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">Телефон</div>
                                <div class="small text-muted">+7 (4012) 65-65-65</div>
                            </div>
                            <i class="bi bi-arrow-right-short fs-4 text-info"></i>
                        </div>
                    </a>
                </div>
                <div class="alert alert-info mt-3 mb-0 small">
                    <i class="bi bi-info-circle me-2"></i>
                    Если у вас возникли проблемы с регистрацией или отображением скидки, напишите нам — мы поможем!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<?php
    require_once("files/footer.php");
?>

<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>