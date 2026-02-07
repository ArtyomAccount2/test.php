<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) 
{
    header("Location: ../index.php");
    exit();
}

$userId = null;
$userData = [];

if (isset($_SESSION['user'])) 
{
    $username = $_SESSION['user'];
    $sql = "SELECT id_users FROM users WHERE CONCAT(surname_users, ' ', name_users, ' ', patronymic_users) = ? OR person_users = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) 
    {
        $userData = $result->fetch_assoc();
        $userId = $userData['id_users'];
    }
    $stmt->close();
}

$apiKeys = [];

if ($userId) 
{
    $apiSql = "SELECT * FROM api_keys WHERE name LIKE ? OR (permissions LIKE ? AND status = 'active') ORDER BY created_at DESC";
    $apiStmt = $conn->prepare($apiSql);
    $userPattern = "%user{$userId}%";
    $permPattern = "%all%";
    $apiStmt->bind_param("ss", $userPattern, $permPattern);
    $apiStmt->execute();
    $apiResult = $apiStmt->get_result();
    
    while ($key = $apiResult->fetch_assoc()) 
    {
        $apiKeys[] = $key;
    }

    $apiStmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_api_key'])) 
{
    $appName = $_POST['app_name'] ?? '';
    $permissions = isset($_POST['permissions']) ? implode(',', $_POST['permissions']) : 'read';
    $expiryDays = intval($_POST['expiry_days'] ?? 90);
    
    if (!empty($appName) && $userId) 
    {
        $apiKey = 'sk_live_' . bin2hex(random_bytes(16));
        $secretKey = 'sk_' . bin2hex(random_bytes(24));

        $expiresAt = null;

        if ($expiryDays > 0) 
        {
            $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiryDays} days"));
        }
        
        $insertSql = "INSERT INTO api_keys (name, api_key, secret_key, status, permissions, expires_at, created_at) VALUES (?, ?, ?, 'active', ?, ?, NOW())";
        $insertStmt = $conn->prepare($insertSql);

        $appNameWithUser = $appName . " (user{$userId})";
        
        $insertStmt->bind_param("sssss", $appNameWithUser, $apiKey, $secretKey, $permissions, $expiresAt);
        
        if ($insertStmt->execute()) 
        {
            $_SESSION['new_api_key'] = [
                'api_key' => $apiKey,
                'secret_key' => $secretKey,
                'app_name' => $appName
            ];
            $_SESSION['success_message'] = "API ключ успешно создан! Сохраните ключи в безопасном месте.";
        } 
        else 
        {
            $_SESSION['error_message'] = "Ошибка при создании API ключа";
        }
        
        $insertStmt->close();
        header("Location: developer.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['revoke_api_key'])) 
{
    $keyId = $_POST['key_id'] ?? 0;
    
    if ($keyId && $userId) 
    {
        $updateSql = "UPDATE api_keys SET status = 'revoked', revoked_at = NOW() WHERE id = ? AND (name LIKE ? OR name = ?)";
        $updateStmt = $conn->prepare($updateSql);
        $userPattern = "%user{$userId}%";
        $updateStmt->bind_param("iss", $keyId, $userPattern, $userPattern);
        
        if ($updateStmt->execute()) 
        {
            $_SESSION['success_message'] = "API ключ успешно отозван";
        }
        
        $updateStmt->close();
        header("Location: developer.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_api_key'])) 
{
    $keyId = $_POST['key_id'] ?? 0;
    
    if ($keyId && $userId) 
    {
        $deleteSql = "DELETE FROM api_keys WHERE id = ? AND (name LIKE ? OR name = ?)";
        $deleteStmt = $conn->prepare($deleteSql);
        $userPattern = "%user{$userId}%";
        $deleteStmt->bind_param("iss", $keyId, $userPattern, $userPattern);
        
        if ($deleteStmt->execute()) 
        {
            $_SESSION['success_message'] = "API ключ удален";
        }
        
        $deleteStmt->close();
        header("Location: developer.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Настройки разработчика - Лал-Авто</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/developer-styles.css">
</head>
<body>

<nav class="navbar navbar-expand-xl navbar-light bg-light shadow-sm fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php"><img src="../img/Auto.png" alt="Лал-Авто" height="75"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../index.php">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="assortment.php">Каталог</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="orders.php">Мои заказы</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="support.php">Поддержка</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['user']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../profile.php"><i class="bi bi-person me-2"></i>Профиль</a></li>
                        <li><a class="dropdown-item" href="orders.php"><i class="bi bi-list-check me-2"></i>Заказы</a></li>
                        <li><a class="dropdown-item" href="cart.php"><i class="bi bi-cart3 me-2"></i>Корзина</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../files/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Выйти</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<div class="developer-container">
    <div class="container py-5">
        <?php 
        if (isset($_SESSION['success_message']))
        {
        ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= $_SESSION['success_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php 
        }

        if (isset($_SESSION['error_message']))
        {
        ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?= $_SESSION['error_message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php 
        }
        ?>
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-6 fw-bold text-primary">
                    <i class="bi bi-code-slash me-2"></i>Настройки разработчика
                </h1>
                <p class="text-muted">Управляйте вашими API ключами для интеграции с системой Лал-Авто</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-key me-2"></i>Мои API ключи</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        if (!empty($apiKeys))
                        {
                        ?>
                            <div class="row">
                                <?php 
                                foreach ($apiKeys as $key)
                                {
                                ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="card api-key-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title mb-0"><?= htmlspecialchars($key['name']) ?></h6>
                                                    <span class="badge bg-<?= $key['status'] == 'active' ? 'success' : 'danger' ?>">
                                                        <?= $key['status'] == 'active' ? 'Активен' : 'Отозван' ?>
                                                    </span>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">API ключ:</small>
                                                    <div class="d-flex align-items-center">
                                                        <code class="api-key-preview flex-grow-1">
                                                            <?= substr($key['api_key'], 0, 15) ?>...
                                                        </code>
                                                        <button class="btn btn-sm btn-outline-secondary copy-btn ms-2" 
                                                                data-key="<?= htmlspecialchars($key['api_key']) ?>"
                                                                title="Копировать API ключ">
                                                            <i class="bi bi-clipboard"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted">Создан:</small>
                                                    <div><?= date('d.m.Y H:i', strtotime($key['created_at'])) ?></div>
                                                </div>
                                                <?php 
                                                if ($key['expires_at'])
                                                {
                                                ?>
                                                    <div class="mb-3">
                                                        <small class="text-muted">Истекает:</small>
                                                        <div><?= date('d.m.Y', strtotime($key['expires_at'])) ?></div>
                                                    </div>
                                                <?php 
                                                }
                                                ?>
                                                <div class="mb-3">
                                                    <small class="text-muted">Разрешения:</small>
                                                    <div>
                                                        <?php 
                                                        $perms = explode(',', $key['permissions']);

                                                        foreach ($perms as $perm)
                                                        {
                                                        ?>
                                                            <span class="badge bg-info permission-badge me-1"><?= $perm ?></span>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between mt-3">
                                                    <?php 
                                                    if ($key['status'] == 'active')
                                                    {
                                                    ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="key_id" value="<?= $key['id'] ?>">
                                                            <button type="submit" name="revoke_api_key" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('Отозвать этот API ключ?')">
                                                                <i class="bi bi-x-circle me-1"></i>Отозвать
                                                            </button>
                                                        </form>
                                                    <?php
                                                    }
                                                    ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="key_id" value="<?= $key['id'] ?>">
                                                        <button type="submit" name="delete_api_key" class="btn btn-sm btn-outline-dark"
                                                                onclick="return confirm('Удалить этот API ключ навсегда?')">
                                                            <i class="bi bi-trash me-1"></i>Удалить
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                }
                                ?>
                            </div>
                        <?php 
                        }
                        else
                        {
                        ?>
                            <div class="text-center py-5">
                                <i class="bi bi-key text-muted display-1 mb-3"></i>
                                <h5>Нет API ключей</h5>
                                <p class="text-muted">Создайте ваш первый API ключ для доступа к API</p>
                            </div>
                        <?php 
                        }
                        ?>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Создать новый API ключ</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Название приложения<span class="text-danger">*</span></label>
                                    <input type="text" name="app_name" class="form-control" placeholder="Мое приложение" required>
                                    <small class="text-muted">Например: "Мобильное приложение" или "Интеграция CRM"</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Срок действия</label>
                                    <select name="expiry_days" class="form-select">
                                        <option value="30">30 дней</option>
                                        <option value="90" selected>90 дней</option>
                                        <option value="180">180 дней</option>
                                        <option value="365">1 год</option>
                                        <option value="0">Бессрочно</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Разрешения</label>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                       value="read" id="perm_read" checked>
                                                <label class="form-check-label" for="perm_read">
                                                    Чтение (read)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                       value="write" id="perm_write">
                                                <label class="form-check-label" for="perm_write">
                                                    Запись (write)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                       value="products" id="perm_products">
                                                <label class="form-check-label" for="perm_products">
                                                    Товары (products)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                       value="orders" id="perm_orders">
                                                <label class="form-check-label" for="perm_orders">
                                                    Заказы (orders)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        <strong>Внимание:</strong> После создания ключа вы увидите его только один раз. 
                                        Сохраните ключи в безопасном месте.
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="create_api_key" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-key me-2"></i>Создать API ключ
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-book me-2"></i>Документация API</h5>
                    </div>
                    <div class="card-body">
                        <h6>Как использовать API</h6>
                        <ol class="small mb-4">
                            <li class="mb-2">Создайте API ключ (выше)</li>
                            <li class="mb-2">Используйте его в заголовках запросов:</li>
                            <pre class="bg-light p-2 rounded mb-3"><code>Authorization: Bearer YOUR_API_KEY</code></pre>
                            <li class="mb-2">Базовый URL API:</li>
                            <code class="d-block bg-light p-2 rounded mb-3">https://api.lal-auto.ru/v1/</code>
                        </ol>
                        <h6>Примеры запросов</h6>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Получить список товаров:</small>
                            <code class="d-block bg-light p-2 rounded small">
                                GET /products?limit=10&category=Запчасти
                            </code>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Создать заказ:</small>
                            <code class="d-block bg-light p-2 rounded small">
                                POST /orders
                            </code>
                        </div>
                        <h6 class="mt-4">Полезные ссылки</h6>
                        <div class="d-grid gap-2">
                            <a href="api.php" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-file-text me-1"></i>Полная документация
                            </a>
                            <a href="support.php" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-question-circle me-1"></i>Поддержка API
                            </a>
                        </div>
                        <div class="alert alert-danger small mt-4">
                            <i class="bi bi-shield-exclamation me-2"></i>
                            <strong>Безопасность:</strong> Никогда не раскрывайте ваш API ключ публично. 
                            Храните его в безопасном месте.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($_SESSION['new_api_key']))
{
?>
<div class="modal fade" id="newApiKeyModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-key me-2"></i>Новый API ключ создан
                </h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Сохраните эти ключи сейчас!</strong> Они больше не будут отображаться полностью.
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">API ключ:</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" id="apiKeyDisplay" 
                               value="<?= htmlspecialchars($_SESSION['new_api_key']['api_key']) ?>" readonly>
                        <button class="btn btn-outline-secondary copy-btn-modal" type="button" data-target="apiKeyDisplay">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Секретный ключ:</label>
                    <div class="input-group">
                        <input type="text" class="form-control font-monospace" id="secretKeyDisplay" 
                               value="<?= htmlspecialchars($_SESSION['new_api_key']['secret_key']) ?>" readonly>
                        <button class="btn btn-outline-secondary copy-btn-modal" type="button" data-target="secretKeyDisplay">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Приложение:</strong> <?= htmlspecialchars($_SESSION['new_api_key']['app_name']) ?>
                    <br>
                    <strong>Использование:</strong> Добавьте ключ в заголовок Authorization: Bearer YOUR_API_KEY
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="markAsSaved()">
                    <i class="bi bi-check-circle me-1"></i>Я сохранил ключи
                </button>
            </div>
        </div>
    </div>
</div>
<?php 
unset($_SESSION['new_api_key']); 
}
?>

<?php 
    require_once("footer.php"); 
?>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/script.js"></script>
<script>
async function copyText(text, button) 
{
    try 
    {
        await navigator.clipboard.writeText(text);
        showCopySuccess(button);

        return true;
    } 
    catch (err) 
    {
        console.error('Clipboard API не доступен:', err);
        return copyTextFallback(text, button);
    }
}

function copyTextFallback(text, button) 
{
    try 
    {
        let textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-999999px';
        textarea.style.top = '-999999px';
        document.body.appendChild(textarea);
        
        textarea.select();
        textarea.setSelectionRange(0, 99999);
        
        let successful = document.execCommand('copy');
        document.body.removeChild(textarea);
        
        if (successful) 
        {
            showCopySuccess(button);
            return true;
        } 
        else 
        {
            showCopyManual(text, button);
            return false;
        }
    } 
    catch (err) 
    {
        console.error('Fallback копирование не удалось:', err);
        showCopyManual(text, button);

        return false;
    }
}

function showCopySuccess(button) 
{
    if (!button)
    {
        return;
    }
    
    let originalHTML = button.innerHTML;
    let originalClass = button.className;

    button.innerHTML = '<i class="bi bi-check"></i>';
    button.className = originalClass.replace('btn-outline-secondary', 'btn-success');
    button.classList.add('copy-success');

    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.className = originalClass;
        button.classList.remove('copy-success');
    }, 2000);
}

function showCopyManual(text, button) 
{
    let modalHTML = `
        <div class="modal fade" id="manualCopyModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Скопируйте вручную</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Выделите текст ниже и скопируйте его (Ctrl+C):</p>
                        <textarea class="form-control" rows="4" style="font-family: monospace;" readonly>${text}</textarea>
                        <div class="mt-2 text-muted small">
                            <i class="bi bi-info-circle"></i> Выделите весь текст и нажмите Ctrl+C
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Готово</button>
                    </div>
                </div>
            </div>
        </div>
    `;

    let modalDiv = document.createElement('div');
    modalDiv.innerHTML = modalHTML;
    document.body.appendChild(modalDiv);

    let manualModal = new bootstrap.Modal(document.getElementById('manualCopyModal'));
    manualModal.show();

    setTimeout(() => {
        let textarea = document.querySelector('#manualCopyModal textarea');

        if (textarea) 
        {
            textarea.select();
        }
    }, 300);

    document.getElementById('manualCopyModal').addEventListener('hidden.bs.modal', function() 
    {
        document.body.removeChild(modalDiv);

        if (button) 
        {
            showCopySuccess(button);
        }
    });
}

document.addEventListener('DOMContentLoaded', function() 
{
    <?php 
    if (isset($_SESSION['new_api_key']))
    {
    ?>
        let newKeyModal = new bootstrap.Modal(document.getElementById('newApiKeyModal'));
        newKeyModal.show();
    <?php 
    } 
    ?>

    document.querySelectorAll('.copy-btn[data-key]').forEach(button => {
        button.addEventListener('click', async function(e) 
        {
            e.preventDefault();

            let text = this.getAttribute('data-key');
            await copyText(text, this);
        });
    });

    document.querySelectorAll('.copy-btn-modal').forEach(button => {
        button.addEventListener('click', async function(e) 
        {
            e.preventDefault();

            let targetId = this.getAttribute('data-target');
            let targetElement = document.getElementById(targetId);
            
            if (targetElement) 
            {
                let text = targetElement.value;
                await copyText(text, this);
            }
        });
    });
});

function copyToClipboard(elementId) 
{
    let element = document.getElementById(elementId);

    if (element) 
    {
        copyText(element.value);
    }
}

function markAsSaved() 
{
    console.log('Ключи сохранены пользователем');
}
</script>
</body>
</html>