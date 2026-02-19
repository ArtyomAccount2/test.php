<?php
session_start();
require_once("../config/link.php");

$message = '';
$error = '';
$success = '';

function generateShortToken($length = 6) 
{
    $characters = '23456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) 
    {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) 
    {
        $error = "Пожалуйста, введите email адрес.";
    } 
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
    {
        $error = "Пожалуйста, введите корректный email адрес.";
    } 
    else 
    {
        $stmt = $conn->prepare("SELECT id_users, login_users, surname_users, name_users FROM users WHERE email_users = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) 
        {
            $user = $result->fetch_assoc();
            $token = bin2hex(random_bytes(32));
            $short_token = generateShortToken(6);

            $checkStmt = $conn->prepare("SELECT id FROM password_resets WHERE short_token = ?");
            $checkStmt->bind_param("s", $short_token);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            while ($checkResult->num_rows > 0) 
            {
                $short_token = generateShortToken(6);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();
            }
            
            $expires = date('Y-m-d H:i:s', time() + 3600);

            $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $deleteStmt->bind_param("i", $user['id_users']);
            $deleteStmt->execute();

            $insertStmt = $conn->prepare("INSERT INTO password_resets (user_id, token, short_token, expires_at) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("isss", $user['id_users'], $token, $short_token, $expires);
            
            if ($insertStmt->execute()) 
            {
                $resetLink = "http://test.php:81/includes/reset_password.php?code=" . $short_token;

                $userName = !empty($user['surname_users']) ? $user['surname_users'] . " " . $user['name_users'] : $user['login_users'];
                
                $success = "
                <div class='success-message'>
                    <div class='alert alert-success' role='alert'>
                        <h5><i class='bi bi-check-circle-fill'></i> Ссылка для сброса пароля отправлена!</h5>
                        <p class='mb-2'>Здравствуйте, <strong>" . htmlspecialchars($userName) . "</strong>!</p>
                        <p>На ваш email <strong>" . htmlspecialchars($email) . "</strong> была отправлена ссылка для сброса пароля.</p>
                    </div>
                    <div class='reset-link-box bg-light p-3 rounded border'>
                        <h6 class='text-center mb-3'>Ссылка для сброса пароля:</h6>
                        <div class='input-group mb-2'>
                            <input type='text' class='form-control text-center font-monospace' id='resetLink' value='{$resetLink}' readonly>
                            <button class='btn btn-outline-secondary' type='button' onclick='copyResetLink()'>
                                <i class='bi bi-clipboard'></i> Копировать
                            </button>
                        </div>
                        <small class='text-muted d-block text-center'>Действительна в течение 1 часа</small>
                    </div>
                    <div class='mt-3 text-center'>
                        <p><strong>Ваш код для сброса:</strong></p>
                        <div class='reset-code display-4 text-primary font-monospace fw-bold'>
                            {$short_token}
                        </div>
                        <small class='text-muted'>Можно ввести этот код на странице сброса пароля</small>
                    </div>
                    <div class='mt-4'>
                        <a href='index.php' class='btn btn-primary'>
                            <i class='bi bi-house-door'></i> Вернуться на главную
                        </a>
                        <a href='reset_password.php' class='btn btn-outline-primary ms-2'>
                            <i class='bi bi-key'></i> Перейти к сбросу пароля
                        </a>
                    </div>
                </div>";  
            } 
            else 
            {
                $error = "Произошла ошибка при создании ссылки. Пожалуйста, попробуйте еще раз.";
            }
        } 
        else 
        {
            $error = "Пользователь с таким email не найден.";
        }
    }

    if ($error) 
    {
        $_SESSION['forgot_error'] = $error;
        $_SESSION['forgot_email'] = $email;

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } 
    else if ($success) 
    {
        $_SESSION['forgot_success'] = $success;
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$error = $_SESSION['forgot_error'] ?? '';
$success = $_SESSION['forgot_success'] ?? '';
$email = $_SESSION['forgot_email'] ?? $_GET['email'] ?? '';

unset($_SESSION['forgot_error'], $_SESSION['forgot_success'], $_SESSION['forgot_email']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Восстановление пароля - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/password-styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        <?php 
        if (isset($_SESSION['login_error'])) 
        { 
        ?>
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        <?php 
        } 
        ?>
    });
    </script>
</head>
<body>
    <div class="forgot-container">
        <div class="forgot-header">
            <h2>Восстановление пароля</h2>
            <p class="text-muted">Введите ваш email для получения ссылки на сброс пароля</p>
        </div>
        <?php 
        if ($error)
        {
        ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php 
        }
        
        if ($success)
        {
        ?>
            <div class="alert alert-success" role="alert">
                <?= $success ?>
            </div>
        <?php 
        }
        else
        { 
        ?>
            <div class="instructions mb-4">
                <h6 class="mb-3"><i class="bi bi-info-circle"></i> Как это работает:</h6>
                <div class="instruction-step">
                    <div class="step-number">1</div>
                    <span>Введите email, указанный при регистрации</span>
                </div>
                <div class="instruction-step">
                    <div class="step-number">2</div>
                    <span>Получите ссылку или 6-значный код для сброса</span>
                </div>
                <div class="instruction-step">
                    <div class="step-number">3</div>
                    <span>Перейдите по ссылке или введите код на странице сброса</span>
                </div>
                <div class="instruction-step">
                    <div class="step-number">4</div>
                    <span>Установите новый пароль для вашего аккаунта</span>
                </div>
            </div>
            <form method="POST" action="" id="forgotForm">
                <div class="mb-4">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i> Email адрес
                    </label>
                    <input type="email" name="email" class="form-control form-control-lg" id="email" 
                           placeholder="example@email.com" required value="<?= htmlspecialchars($email) ?>">
                    <div class="form-text">На этот email будет отправлена ссылка для сброса пароля</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="button-active btn btn-primary btn-lg w-50">
                        <i class="bi bi-send"></i> Получить код сброса
                    </button>
                    <a href="../index.php" class="button-active btn btn-outline-secondary w-50 align-self-center">
                        <i class="bi bi-house-door"></i> Вернуться на главную
                    </a>
                </div>
            </form>
            <div class="text-center mt-4">
                <p class="text-muted mb-2">Уже вспомнили пароль?</p>
                <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#loginModal">
                    <i class="bi bi-box-arrow-in-right"></i> Войти в аккаунт
                </button>
            </div>
        <?php 
        }
        ?>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title w-100 text-center" id="loginModalLabel">Авторизация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="/">
                        <div class="mb-3">
                            <label for="username" class="form-label">Логин</label>
                            <input type="text" name="login" class="form-control" id="username" placeholder="Введите логин" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Введите пароль" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="rememberMe" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Запомнить меня</label>
                        </div>
                        <?php 
                        if (isset($_SESSION['error_message'])) 
                        {
                        ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($_SESSION['error_message']); ?>
                            </div>
                        <?php 
                            unset($_SESSION['error_message']);
                        }
                        ?>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Войти
                        </button>
                        <a href="forgot_password.php" class="btn btn-link">Забыли пароль?</a>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
    
<script src="../js/bootstrap.bundle.min.js"></script>
<script>
function copyResetLink() 
{
    let resetLink = document.getElementById('resetLink');
    
    resetLink.select();
    resetLink.setSelectionRange(0, 99999);
            
    try 
    {
        navigator.clipboard.writeText(resetLink.value).then(() => {
            let copyBtn = document.querySelector('button[onclick="copyResetLink()"]');
            let originalHtml = copyBtn.innerHTML;

            copyBtn.innerHTML = '<i class="bi bi-check"></i> Скопировано';
            copyBtn.classList.remove('btn-outline-secondary');
            copyBtn.classList.add('btn-success');
                    
            setTimeout(() => {
                copyBtn.innerHTML = originalHtml;
                copyBtn.classList.remove('btn-success');
                copyBtn.classList.add('btn-outline-secondary');
            }, 2000);
        });
    } 
    catch (err) 
    {
        document.execCommand('copy');
        alert('Ссылка скопирована в буфер обмена');
    }
}
        
document.getElementById('forgotForm')?.addEventListener('submit', function() 
{
    let submitBtn = this.querySelector('button[type="submit"]');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Отправка...';
});
</script>
</body>
</html>