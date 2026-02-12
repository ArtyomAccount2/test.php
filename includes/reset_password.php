<?php
session_start();
require_once("../config/link.php");

$message = '';
$error = '';
$showForm = false;

if (isset($_GET['new_code']) && isset($_GET['email'])) 
{
    $email = trim($_GET['email']);
    
    $stmt = $conn->prepare("SELECT id_users FROM users WHERE email_users = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) 
    {
        $user = $result->fetch_assoc();

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
        
        $token = bin2hex(random_bytes(32));
        $short_token = generateShortToken(6);

        $checkStmt = $conn->prepare("SELECT id FROM password_resets WHERE short_token = ?");
        $checkStmt->bind_param("s", $short_token);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        while ($checkResult->num_rows > 0) 
        {
            $short_token = generateShortToken(6);
            $checkStmt->bind_param("s", $short_token);
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
            $_SESSION['new_code_modal'] = true;
            $_SESSION['new_code'] = $short_token;
            $_SESSION['new_code_email'] = $email;
            $_SESSION['new_code_expires'] = $expires;
            
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

$error = $_SESSION['reset_error'] ?? '';
$message = $_SESSION['reset_message'] ?? '';
$showForm = $_SESSION['reset_showform'] ?? false;
$code = $_SESSION['reset_code'] ?? '';
$user_id = $_SESSION['reset_user_id'] ?? '';
$newCodeModal = $_SESSION['new_code_modal'] ?? false;
$newCode = $_SESSION['new_code'] ?? '';
$newCodeEmail = $_SESSION['new_code_email'] ?? '';
$newCodeExpires = $_SESSION['new_code_expires'] ?? '';

unset($_SESSION['reset_error'], $_SESSION['reset_message'], $_SESSION['reset_showform'], $_SESSION['reset_code'], $_SESSION['reset_user_id'], $_SESSION['new_code_modal'], $_SESSION['new_code'], $_SESSION['new_code_email'], $_SESSION['new_code_expires']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) 
{
    if ($_POST['action'] == 'verify_code') 
    {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        
        if (empty($code) || strlen($code) != 6) 
        {
            $_SESSION['reset_error'] = "Пожалуйста, введите 6-значный код.";
            $_SESSION['reset_code'] = $code;
            
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } 
        else 
        {
            $stmt = $conn->prepare("SELECT pr.*, u.login_users, u.email_users FROM password_resets pr JOIN users u ON pr.user_id = u.id_users WHERE pr.short_token = ? AND pr.expires_at > NOW() AND pr.used = 0");
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) 
            {
                $_SESSION['reset_error'] = "Неверный или устаревший код. Пожалуйста, проверьте код или запросите новый.";
                $_SESSION['reset_code'] = $code;

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } 
            else 
            {
                $resetData = $result->fetch_assoc();
                $_SESSION['reset_showform'] = true;
                $_SESSION['reset_code'] = $code;
                $_SESSION['reset_user_id'] = $resetData['user_id'];
                $_SESSION['user_email'] = $resetData['email_users'];

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    }
    else if ($_POST['action'] == 'reset_password' && isset($_POST['code'])) 
    {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        
        if (empty($password)) 
        {
            $_SESSION['reset_error'] = "Пожалуйста, введите новый пароль.";
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_showform'] = true;

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } 
        else if (strlen($password) < 6) 
        {
            $_SESSION['reset_error'] = "Пароль должен содержать минимум 6 символов.";
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_showform'] = true;

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } 
        else if ($password !== $confirm_password) 
        {
            $_SESSION['reset_error'] = "Пароли не совпадают.";
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_showform'] = true;

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } 
        else 
        {
            $stmt = $conn->prepare("SELECT * FROM password_resets WHERE short_token = ? AND expires_at > NOW() AND used = 0");
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) 
            {
                $_SESSION['reset_error'] = "Срок действия кода истек. Пожалуйста, запросите новый код.";

                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } 
            else 
            {
                $resetData = $result->fetch_assoc();
                $updateStmt = $conn->prepare("UPDATE users SET password_users = ? WHERE id_users = ?");
                $updateStmt->bind_param("si", $password, $resetData['user_id']);
                
                if ($updateStmt->execute()) 
                {
                    $markStmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
                    $markStmt->bind_param("i", $resetData['id']);
                    $markStmt->execute();

                    $deleteStmt = $conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
                    $deleteStmt->bind_param("i", $resetData['user_id']);
                    $deleteStmt->execute();

                    $_SESSION['reset_message'] = "Пароль успешно изменен! Теперь вы можете войти с новым паролем.";

                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
                else 
                {
                    $_SESSION['reset_error'] = "Произошла ошибка при изменении пароля. Пожалуйста, попробуйте еще раз.";
                    $_SESSION['reset_code'] = $code;
                    $_SESSION['reset_showform'] = true;

                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            }
        }
    }
}

if (isset($_GET['code']) && !$showForm && !$message) 
{
    $code = strtoupper(trim($_GET['code']));
    $_SESSION['reset_code'] = $code;
    header("Location: " . $_SERVER['PHP_SELF'] . "?auto_code=1");
    exit();
}

if (isset($_GET['auto_code']) && isset($code) && !empty($code) && !$showForm && !$message) 
{
    $showForm = true;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля - Лал-Авто</title>
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

            <?php unset($_SESSION['login_error']); ?>
        <?php 
        } 
        
        if ($newCodeModal && $newCode) 
        {
        ?>
            var newCodeModal = new bootstrap.Modal(document.getElementById('newCodeModal'));
            newCodeModal.show();
        <?php 
        }
        ?>
    });
    </script>
</head>
<body>
    <div class="reset-container">
        <?php 
        if ($showForm && isset($_SESSION['user_email']))
        {
        ?>
            <div class="text-end mb-3">
                <a href="reset_password.php" class="btn btn-warning btn-sm">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
            </div>
        <?php
        }
        else 
        {
        ?>
            <div class="text-end mb-3">
                <a href="forgot_password.php" class="btn btn-warning btn-sm">
                    <i class="bi bi-arrow-left"></i> Назад
                </a>
            </div>
        <?php
        }
        ?>
        <div class="reset-header">
            <h2><i class="bi bi-key"></i> Сброс пароля</h2>
            <p class="text-muted">Введите код для установки нового пароля</p>
        </div>
        <div class="step-indicator">
            <div class="step <?= (!$showForm && !$message) ? 'active' : 'completed' ?>">
                <div class="step-number">1</div>
                <div class="step-title">Ввод кода</div>
                <div class="step-line"></div>
            </div>
            <div class="step <?= ($showForm && !$message) ? 'active' : '' ?>">
                <div class="step-number">2</div>
                <div class="step-title">Новый пароль</div>
            </div>
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
        
        if ($message)
        {
        ?>
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($message) ?>
                <div class="mt-3">
                    <button type="button" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="bi bi-box-arrow-in-right"></i> Войти в аккаунт
                    </button>
                    <a href="../index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-house-door"></i> Вернуться на главную
                    </a>
                </div>
            </div>
        <?php
        }
        else if ($showForm) 
        {
        ?>
            <form method="POST" action="" id="resetForm">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="code" value="<?= htmlspecialchars($code) ?>">
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i> Новый пароль
                    </label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Введите новый пароль" required minlength="6">
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="form-label">
                        <i class="bi bi-lock-fill"></i> Подтвердите пароль
                    </label>
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" 
                           placeholder="Повторите пароль" required minlength="6">
                    <div class="invalid-feedback" id="confirmError"></div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle"></i> Изменить пароль
                    </button>
                    <a href="../index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-house-door"></i> Вернуться на главную
                    </a>
                </div>
            </form>
        <?php 
        }
        else
        {
            $user_email = $_SESSION['user_email'] ?? '';
        ?>
            <form method="POST" action="" id="codeForm">
                <input type="hidden" name="action" value="verify_code">
                <div class="code-input-container">
                    <div class="mb-3">
                        <label for="code" class="form-label text-center d-block">
                            <i class="bi bi-shield-check"></i> 6-значный код
                        </label>
                        <div>
                            <input type="text" name="code" class="form-control form-control-lg text-center font-monospace" id="code" placeholder="XXXXXX" maxlength="6" style="letter-spacing: 3px;" required pattern="[A-Z0-9]{6}" title="Введите 6 символов (буквы и цифры)" value="<?= htmlspecialchars($code) ?>">
                        </div>
                        <div class="code-hint">
                            <i class="bi bi-info-circle"></i> Введите 6-значный код, полученный по email
                        </div>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-arrow-right-circle"></i> Продолжить
                    </button>

                    <?php 
                    if ($user_email)
                    {
                    ?>
                        <a href="?new_code=1&email=<?= urlencode($user_email) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-repeat"></i> Получить новый код
                        </a>
                    <?php 
                    }
                    else
                    { 
                    ?>
                        <a href="forgot_password.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-repeat"></i> Запросить новый код
                        </a>
                    <?php 
                    }
                    ?>
                    
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

    <div class="modal fade" id="newCodeModal" tabindex="-1" aria-labelledby="newCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title w-100 text-center" id="newCodeModalLabel">
                        <i class="bi bi-key-fill"></i> Новый код сгенерирован
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success display-1"></i>
                        <h4 class="mt-3">Новый код успешно создан!</h4>
                        <p class="text-muted">Старый код больше недействителен</p>
                    <div class="new-code-box p-4 bg-light rounded border">
                        <p class="mb-2"><strong>Ваш новый код для сброса пароля:</strong></p>
                        <div class="new-code display-2 text-primary font-monospace fw-bold mb-2">
                            <?= htmlspecialchars($newCode) ?>
                        </div>
                        <small class="text-muted d-block mb-3">
                            <i class="bi bi-clock"></i> Действителен до: <?= date('H:i', strtotime($newCodeExpires)) ?>
                        </small>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control text-center font-monospace" id="newCodeInput" value="<?= htmlspecialchars($newCode) ?>" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyNewCode()">
                                <i class="bi bi-clipboard"></i> Копировать
                            </button>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="?code=<?= htmlspecialchars($newCode) ?>" class="btn btn-success btn-lg">
                            <i class="bi bi-arrow-right"></i> Использовать этот код
                        </a>
                    </div>
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
function copyNewCode() 
{
    let newCodeInput = document.getElementById('newCodeInput');
    
    newCodeInput.select();
    newCodeInput.setSelectionRange(0, 99999);
            
    try 
    {
        navigator.clipboard.writeText(newCodeInput.value).then(() => {
            let copyBtn = document.querySelector('button[onclick="copyNewCode()"]');
            let originalHtml = copyBtn.innerHTML;

            copyBtn.innerHTML = '<i class="bi bi-check"></i> Скопировано';
            copyBtn.classList.remove('btn-outline-primary');
            copyBtn.classList.add('btn-success');
                    
            setTimeout(() => {
                copyBtn.innerHTML = originalHtml;
                copyBtn.classList.remove('btn-success');
                copyBtn.classList.add('btn-outline-primary');
            }, 2000);
        });
    } 
    catch (err) 
    {
        document.execCommand('copy');
        alert('Код скопирован в буфер обмена');
    }
}

document.addEventListener('DOMContentLoaded', function() 
{
    let passwordInput = document.getElementById('password');
    let confirmInput = document.getElementById('confirm_password');
    let strengthBar = document.getElementById('strengthBar');
    let strengthText = document.getElementById('strengthText');
    let confirmError = document.getElementById('confirmError');
    let codeInput = document.getElementById('code');
            
    if (codeInput) 
    {
        codeInput.addEventListener('input', function() 
        {
            this.value = this.value.toUpperCase();
        });

        codeInput.focus();
    }
            
    if (passwordInput) 
    {
        function checkPasswordStrength(password) 
        {
            let strength = 0;
                    
            if (password.length >= 6) 
            {
                strength++;
            }

            if (password.length >= 8) 
            {
                strength++;
            }

            if (/[A-Z]/.test(password)) 
            {
                strength++;
            }

            if (/[0-9]/.test(password)) 
            {
                strength++;
            }
                    
            if (/[^A-Za-z0-9]/.test(password)) 
            {
                strength++;
            }
                    
            return strength;
        }
                
        function updateStrengthBar(strength) 
        {
            let colors = ['#dc3545', '#ffc107', '#ffc107', '#28a745', '#28a745'];
            let texts = ['Очень слабый', 'Слабый', 'Средний', 'Хороший', 'Отличный'];
            let width = (strength / 5) * 100;

            strengthBar.style.width = width + '%';
            strengthBar.style.backgroundColor = colors[strength - 1] || colors[0];
            strengthText.textContent = texts[strength - 1] || texts[0];
            strengthText.style.color = colors[strength - 1] || colors[0];
        }
                
        function checkPasswordsMatch() 
        {
            let password = passwordInput.value;
            let confirm = confirmInput.value;
                    
            if (confirm && password !== confirm) 
            {
                confirmInput.classList.add('is-invalid');
                confirmError.textContent = 'Пароли не совпадают';

                return false;
                    
            } 
            else 
            {
                confirmInput.classList.remove('is-invalid');
                confirmError.textContent = '';

                return true;
            }
        }
                
        passwordInput.addEventListener('input', function() 
        {
            let strength = checkPasswordStrength(this.value);

            updateStrengthBar(strength);
            checkPasswordsMatch();
        });
                
        confirmInput.addEventListener('input', checkPasswordsMatch);
                
        document.getElementById('resetForm')?.addEventListener('submit', function(e) 
        {
            if (!checkPasswordsMatch()) 
            {
                e.preventDefault();
            }
        });
    }
            
    document.getElementById('codeForm')?.addEventListener('submit', function() 
    {
        let submitBtn = this.querySelector('button[type="submit"]');

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Проверка...';
    });
});
</script>
</body>
</html>