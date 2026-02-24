<?php
session_start();
require_once("../config/link.php");

$message = '';
$error = '';
$showForm = false;
$user_email = '';

if (isset($_GET['email']) && !empty($_GET['email'])) 
{
    $user_email = trim($_GET['email']);
    $_SESSION['reset_email'] = $user_email;
} 
else if (isset($_SESSION['reset_email']) && !empty($_SESSION['reset_email'])) 
{
    $user_email = $_SESSION['reset_email'];
}
else if (isset($_POST['email']) && !empty($_POST['email'])) 
{
    $user_email = trim($_POST['email']);
    $_SESSION['reset_email'] = $user_email;
}

if (empty($user_email) && isset($_SESSION['forgot_email'])) 
{
    $user_email = $_SESSION['forgot_email'];
    $_SESSION['reset_email'] = $user_email;
    unset($_SESSION['forgot_email']);
}

if (isset($_GET['new_code']) && !empty($user_email)) 
{
    $email = $user_email;
    $stmt = $conn->prepare("SELECT id_users FROM users WHERE email_users = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) 
    {
        $user = $result->fetch_assoc();
        $user_id = $user['id_users'];

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

        $updateOldStmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE user_id = ?");
        $updateOldStmt->bind_param("i", $user_id);
        $updateOldStmt->execute();
        $insertStmt = $conn->prepare("INSERT INTO password_resets (user_id, token, short_token, expires_at, used) VALUES (?, ?, ?, ?, 0)");
        $insertStmt->bind_param("isss", $user_id, $token, $short_token, $expires);
        
        if ($insertStmt->execute()) 
        {
            $_SESSION['show_new_code_modal'] = true;
            $_SESSION['new_code_value'] = $short_token;
            $_SESSION['new_code_email'] = $email;
            $_SESSION['new_code_expires'] = $expires;
            $_SESSION['reset_email'] = $email;
            
            header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email) . "&modal=1");
            exit();
        } 
        else 
        {
            $_SESSION['reset_error'] = "Ошибка при генерации нового кода. Попробуйте еще раз.";
            header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email));

            exit();
        }
    } 
    else 
    {
        $_SESSION['reset_error'] = "Пользователь с email " . htmlspecialchars($email) . " не найден.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email));

        exit();
    }
}

$showNewCodeModal = isset($_SESSION['show_new_code_modal']) ? $_SESSION['show_new_code_modal'] : false;
$newCodeValue = isset($_SESSION['new_code_value']) ? $_SESSION['new_code_value'] : '';
$newCodeEmail = isset($_SESSION['new_code_email']) ? $_SESSION['new_code_email'] : '';
$newCodeExpires = isset($_SESSION['new_code_expires']) ? $_SESSION['new_code_expires'] : '';

if (isset($_GET['modal']) && $_GET['modal'] == '1' && !$showNewCodeModal && !empty($newCodeValue)) 
{
    $showNewCodeModal = true;
}

$error = $_SESSION['reset_error'] ?? '';
$message = $_SESSION['reset_message'] ?? '';
$showForm = $_SESSION['reset_showform'] ?? false;
$code = $_SESSION['reset_code'] ?? '';
$user_id = $_SESSION['reset_user_id'] ?? '';

unset($_SESSION['reset_error'], $_SESSION['reset_message'], $_SESSION['reset_showform'], $_SESSION['reset_code'], $_SESSION['reset_user_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) 
{
    if ($_POST['action'] == 'verify_code') 
    {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $email = trim($_POST['email'] ?? '');
        
        if (empty($code) || strlen($code) != 6) 
        {
            $_SESSION['reset_error'] = "Пожалуйста, введите 6-значный код.";
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_email'] = $email;
            
            header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email));
            exit();
        } 
        else 
        {
            $stmt = $conn->prepare("SELECT pr.*, u.login_users, u.email_users FROM password_resets pr JOIN users u ON pr.user_id = u.id_users WHERE pr.short_token = ? AND u.email_users = ? AND pr.expires_at > NOW() AND pr.used = 0");
            $stmt->bind_param("ss", $code, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) 
            {
                $_SESSION['reset_error'] = "Неверный или устаревший код. Пожалуйста, проверьте код или запросите новый.";
                $_SESSION['reset_code'] = $code;
                $_SESSION['reset_email'] = $email;

                header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email));
                exit();
            } 
            else 
            {
                $resetData = $result->fetch_assoc();
                $_SESSION['reset_showform'] = true;
                $_SESSION['reset_code'] = $code;
                $_SESSION['reset_user_id'] = $resetData['user_id'];
                $_SESSION['user_email'] = $resetData['email_users'];
                $_SESSION['reset_email'] = $resetData['email_users'];

                header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($resetData['email_users']));
                exit();
            }
        }
    } 
    else if ($_POST['action'] == 'reset_password' && isset($_POST['code'])) 
    {
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        if (empty($password)) 
        {
            $_SESSION['reset_error'] = "Пожалуйста, введите новый пароль.";
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_showform'] = true;
            $_SESSION['reset_email'] = $email;

            header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email));
            exit();
        } 
        else if (strlen($password) < 6) 
        {
            $_SESSION['reset_error'] = "Пароль должен содержать минимум 6 символов.";
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_showform'] = true;
            $_SESSION['reset_email'] = $email;

            header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email));
            exit();
        } 
        else if ($password !== $confirm_password) 
        {
            $_SESSION['reset_error'] = "Пароли не совпадают.";
            $_SESSION['reset_code'] = $code;
            $_SESSION['reset_showform'] = true;
            $_SESSION['reset_email'] = $email;

            header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email));
            exit();
        } 
        else 
        {
            $stmt = $conn->prepare("SELECT pr.* FROM password_resets pr JOIN users u ON pr.user_id = u.id_users WHERE pr.short_token = ? AND u.email_users = ? AND pr.expires_at > NOW() AND pr.used = 0");
            $stmt->bind_param("ss", $code, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 0) 
            {
                $_SESSION['reset_error'] = "Срок действия кода истек. Пожалуйста, запросите новый код.";
                $_SESSION['reset_email'] = $email;

                header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email));
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
                    $_SESSION['reset_email'] = $email;

                    header("Location: " . $_SERVER['PHP_SELF'] . "?email=" . urlencode($email));
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
    $email = $_GET['email'] ?? '';

    header("Location: " . $_SERVER['PHP_SELF'] . "?auto_code=1&email=" . urlencode($email));
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
        ?>
    });
    </script>
</head>
<body>
    <div class="reset-container">
        <?php 
        if (!$message) 
        {
            if ($showForm && isset($_SESSION['user_email'])) 
            {
            ?>
                <div class="text-end mb-3">
                    <a href="reset_password.php?email=<?= urlencode($_SESSION['user_email']) ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-left"></i> Назад
                    </a>
                </div>
            <?php
            } 
            else 
            {
            ?>
                <div class="text-end mb-3">
                    <a href="forgot_password.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-left"></i> Назад
                    </a>
                </div>
            <?php
            }
        }
        ?> 
        <div class="reset-header">
            <h2><i class="bi bi-key"></i> Сброс пароля</h2>
            <p class="text-muted">Введите код для установки нового пароля</p>
        </div>
        <?php 
        if (!$message) 
        {
        ?>
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
        }

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
                <div class="mt-3 d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-primary w-50" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="bi bi-box-arrow-in-right"></i> Войти в аккаунт
                    </button>
                    <a href="../index.php" class="btn btn-outline-secondary w-50">
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
                <input type="hidden" name="email" value="<?= htmlspecialchars($user_email) ?>">
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
        ?>
            <form method="POST" action="" id="codeForm">
                <input type="hidden" name="action" value="verify_code">
                <?php 
                if (!empty($user_email))
                {
                ?>
                    <input type="hidden" name="email" value="<?= htmlspecialchars($user_email) ?>">
                <?php 
                }
                ?>
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
                    if (!empty($user_email))
                    {
                    ?>
                        <a href="?new_code=1&email=<?= urlencode($user_email) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-repeat"></i> Получить новый код
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
                        <div class="d-flex gap-2 align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Войти
                            </button>
                            <a href="includes/forgot_password.php" class="btn btn-outline-secondary">
                                <i class="bi bi-question-circle"></i> Забыли пароль?
                            </a>
                        </div>
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
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title w-100 text-center" id="newCodeModalLabel">
                        <i class="bi bi-key-fill"></i> Новый код сгенерирован
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-check-circle-fill text-primary display-1"></i>
                        <h4 class="mt-2">Новый код успешно создан!</h4>
                        <p class="text-muted">Для email: <strong><?= htmlspecialchars($newCodeEmail) ?></strong></p>
                        <p class="text-muted">Старый код больше недействителен</p>
                    </div>
                    <div class="new-code-box p-4 bg-light rounded border">
                        <p class="mb-2"><strong>Ваш новый код для сброса пароля:</strong></p>
                        <div class="new-code display-2 text-primary font-monospace fw-bold mb-2">
                            <?= htmlspecialchars($newCodeValue) ?>
                        </div>
                        <small class="text-muted d-block mb-2">
                            <i class="bi bi-clock"></i> Действителен до: <?= date('d.m.Y H:i', strtotime($newCodeExpires)) ?>
                        </small>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control text-center font-monospace" id="newCodeInput" value="<?= htmlspecialchars($newCodeValue) ?>" readonly>
                            <button class="btn btn-outline-primary" type="button" onclick="copyNewCode()">
                                <i class="bi bi-clipboard"></i> Копировать
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="?code=<?= htmlspecialchars($newCodeValue) ?>&email=<?= urlencode($newCodeEmail) ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-right"></i> Использовать этот код
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="clearNewCodeSession()">
                        <i class="bi bi-x-circle"></i> Закрыть
                    </button>
                </div>
            </div>
        </div>
    </div>
    
<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
function copyNewCode() 
{
    let newCodeInput = document.getElementById('newCodeInput');
    
    if (newCodeInput) 
    {
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
}

function clearNewCodeSession() 
{
    fetch('clear_new_code_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    });
}

document.addEventListener('DOMContentLoaded', function() 
{
    <?php 
    if ($showNewCodeModal && !empty($newCodeValue))
    {
    ?>
        setTimeout(function() {
            var newCodeModal = new bootstrap.Modal(document.getElementById('newCodeModal'));
            newCodeModal.show();
            
            fetch('clear_new_code_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
        }, 500);
    <?php 
    }
    ?>

    let codeInput = document.getElementById('code');

    if (codeInput) 
    {
        codeInput.focus();

        codeInput.addEventListener('input', function() 
        {
            this.value = this.value.toUpperCase();
        });
    }
});
</script>
</body>
</html>