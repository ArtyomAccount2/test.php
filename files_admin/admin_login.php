<?php
session_start();
require_once("../config/link.php");

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true && $_SESSION['user'] == 'admin')
{
    header("Location: ../admin.php?section=users_list");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
    $login = $_POST['login'];
    $password = $_POST['password'];

    if (strtolower($login) === 'admin' && strtolower($password) === 'admin') 
    {
        $_SESSION['loggedin'] = true;
        $_SESSION['user'] = 'admin';
        $_SESSION['auth_type'] = 'secret_form';
        unset($_SESSION['login_error']);
        unset($_SESSION['error_message']);
        
        header("Location: ../admin.php?section=users_list");
        exit();
    }
    else
    {
        $_SESSION['login_error'] = true;
        $_SESSION['error_message'] = "Неверный логин или пароль!";
        header("Location: admin_login.php");
        exit();
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
    <title>Секретный вход - Администратор | Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/admin_login-styles.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="container-fluid login-container">
    <div class="row justify-content-center align-items-center h-100">
        <div class="col-md-6 col-lg-8">
            <div class="card login-card">
                <div class="login-header">
                    <h3 class="mb-2"><i class="bi bi-shield-lock me-2"></i>Административная панель</h3>
                    <p class="mb-0 opacity-75">Секретный вход для персонала</p>
                </div>
                <div class="card-body login-body">
                    <div class="alert security-alert d-flex align-items-center mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                        <div>
                            <strong> Внимание!</strong> Эта форма предназначена исключительно для администраторов системы. Несанкционированный доступ преследуется по закону.
                        </div>
                    </div>
                    <form method="POST" action="admin_login.php">
                        <div class="mb-4">
                            <label for="login" class="form-label fw-semibold">
                                <i class="bi bi-person-badge me-2"></i>Логин администратора
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" name="login" class="form-control" id="login" placeholder="admin" required value="<?= htmlspecialchars($form_data['login'] ?? '') ?>" autocomplete="off">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">
                                <i class="bi bi-key me-2"></i>Пароль
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required autocomplete="off">
                                <button class="btn password-toggle" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <?php 
                        if (isset($_SESSION['error_message']))
                        {
                        ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            <?= htmlspecialchars($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php 
                            unset($_SESSION['error_message']);
                        } 
                        ?>

                        <button type="submit" class="btn btn-admin btn-lg w-100 text-white mb-3">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Войти в панель управления
                        </button>
                    </form>
                    <div class="text-center footer-links mt-2">
                        <a href="../index.php" class="text-decoration-none text-primary">
                            <i class="bi bi-arrow-left me-1"></i>Вернуться на главную
                        </a>
                    </div>
                    <div class="system-info">
                        <i class="bi bi-shield-check"></i>Защищённое соединение • v2.2.0
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() 
{
    let togglePassword = document.getElementById('togglePassword');
    let passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) 
    {
        togglePassword.addEventListener('click', function() 
        {
            let type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            let icon = this.querySelector('i');

            if (type === 'password') 
            {
                icon.className = 'bi bi-eye';
            } 
            else 
            {
                icon.className = 'bi bi-eye-slash';
            }
        });
    }

    let loginInput = document.getElementById('login');

    if (loginInput) 
    {
        loginInput.focus();
    }

    let alert = document.querySelector('.alert-danger');

    if (alert) {
        setTimeout(() => {
            let bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    }
    
    let formInputs = document.querySelectorAll('input[type="text"], input[type="password"]');

    formInputs.forEach(input => {
        input.addEventListener('copy', (e) => e.preventDefault());
        input.addEventListener('paste', (e) => e.preventDefault());
        input.addEventListener('cut', (e) => e.preventDefault());
    });
});
</script>
</body>
</html>