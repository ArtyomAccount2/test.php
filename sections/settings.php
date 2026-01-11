<?php
$active_tab = $_GET['tab'] ?? 'general';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_settings'])) 
{
    $group = $_POST['setting_group'] ?? 'general';
    
    foreach ($_POST as $key => $value) 
    {
        if (strpos($key, 'setting_') === 0) 
        {
            $setting_key = substr($key, 8);
            $check_stmt = $conn->prepare("SELECT id FROM settings WHERE setting_key = ?");
            $check_stmt->bind_param("s", $setting_key);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows > 0) 
            {
                $stmt = $conn->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
                $stmt->bind_param("ss", $value, $setting_key);
            } 
            else 
            {
                $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $setting_key, $value, $group);
            }
            
            if ($stmt->execute()) 
            {
                // Успешно сохранено
            } 
            else 
            {
                $error = "Ошибка при сохранении настройки: $setting_key";
            }
        }
    }
    
    $_SESSION['success_message'] = 'Настройки успешно сохранены';
    echo '<script>window.location.href = "admin.php?section=settings&tab=' . $active_tab . '";</script>';
    exit();
}

function get_setting($key, $default = '') 
{
    global $conn;
    
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
        return $row['setting_value'];
    }

    $defaults = get_default_settings();
    return isset($defaults[$key]) ? $defaults[$key]['value'] : $default;
}

function get_default_settings() 
{
    $defaults = [
        'site_name' => ['value' => 'Лал-Авто', 'type' => 'text'],
        'admin_email' => ['value' => 'admin@lal-auto.ru', 'type' => 'email'],
        'site_description' => ['value' => 'Автозапчасти и автосервис - качественное обслуживание вашего автомобиля', 'type' => 'textarea'],
        'support_phone' => ['value' => '+7 (999) 123-45-67', 'type' => 'tel'],
        'working_hours' => ['value' => 'Пн-Пт: 9:00-18:00, Сб: 10:00-16:00', 'type' => 'text'],
        'default_language' => ['value' => 'Русский', 'type' => 'select', 'options' => 'Русский,English,Deutsch'],
        'currency' => ['value' => 'RUB', 'type' => 'select', 'options' => 'RUB - Российский рубль,USD - Доллар США,EUR - Евро'],
        'min_order_amount' => ['value' => '1000', 'type' => 'number'],
        'vat_rate' => ['value' => '20', 'type' => 'select', 'options' => '20,10,0'],
        'usd_rate' => ['value' => '90.5', 'type' => 'number'],
        'eur_rate' => ['value' => '99.8', 'type' => 'number'],
        'low_stock_alert' => ['value' => '1', 'type' => 'checkbox'],
        'allow_backorder' => ['value' => '0', 'type' => 'checkbox'],
        'return_policy' => ['value' => 'Возврат товара возможен в течение 14 дней с момента покупки при сохранении товарного вида и упаковки.', 'type' => 'textarea'],
        'email_new_orders' => ['value' => '1', 'type' => 'checkbox'],
        'email_payments' => ['value' => '1', 'type' => 'checkbox'],
        'email_reviews' => ['value' => '0', 'type' => 'checkbox'],
        'email_low_stock' => ['value' => '1', 'type' => 'checkbox'],
        'email_newsletter' => ['value' => '0', 'type' => 'checkbox'],
        'sms_order_status' => ['value' => '0', 'type' => 'checkbox'],
        'sms_delivery' => ['value' => '0', 'type' => 'checkbox'],
        'sms_promo' => ['value' => '1', 'type' => 'checkbox'],
        'smtp_server' => ['value' => 'smtp.gmail.com', 'type' => 'text'],
        'smtp_port' => ['value' => '587', 'type' => 'number'],
        'min_password_length' => ['value' => '8', 'type' => 'number'],
        'password_expiry_days' => ['value' => '90', 'type' => 'number'],
        'require_special_char' => ['value' => '1', 'type' => 'checkbox'],
        'require_numbers' => ['value' => '1', 'type' => 'checkbox'],
        'require_upper_lower' => ['value' => '0', 'type' => 'checkbox'],
        'prevent_reuse' => ['value' => '1', 'type' => 'checkbox'],
        'enable_2fa_admin' => ['value' => '0', 'type' => 'checkbox'],
        'enable_2fa_users' => ['value' => '0', 'type' => 'checkbox'],
        'max_login_attempts' => ['value' => '5', 'type' => 'number'],
        'lockout_minutes' => ['value' => '30', 'type' => 'number'],
        'bank_cards_enabled' => ['value' => '1', 'type' => 'checkbox'],
        'yoomoney_enabled' => ['value' => '1', 'type' => 'checkbox'],
        'sberbank_enabled' => ['value' => '0', 'type' => 'checkbox'],
        'cash_on_delivery' => ['value' => '1', 'type' => 'checkbox'],
        'processing_fee' => ['value' => '2.5', 'type' => 'number'],
        'min_fee' => ['value' => '10', 'type' => 'number'],
        'courier_enabled' => ['value' => '1', 'type' => 'checkbox'],
        'courier_cost' => ['value' => '300', 'type' => 'number'],
        'pickup_enabled' => ['value' => '1', 'type' => 'checkbox'],
        'russian_post_enabled' => ['value' => '0', 'type' => 'checkbox'],
        'russian_post_cost' => ['value' => '500', 'type' => 'number'],
        'cdek_enabled' => ['value' => '1', 'type' => 'checkbox'],
        'cdek_cost' => ['value' => '450', 'type' => 'number'],
        'free_shipping_min' => ['value' => '5000', 'type' => 'number'],
        'delivery_days' => ['value' => '3', 'type' => 'number'],
        'meta_title' => ['value' => 'Лал-Авто - Автозапчасти и автосервис', 'type' => 'text'],
        'meta_description' => ['value' => 'Качественные автозапчасти и профессиональный автосервис. Широкий ассортимент, доступные цены, гарантия качества.', 'type' => 'textarea'],
        'meta_keywords' => ['value' => 'автозапчасти, автосервис, автомобильные запчасти, ремонт авто', 'type' => 'text'],
        'og_title' => ['value' => 'Лал-Авто', 'type' => 'text'],
        'seo_friendly_urls' => ['value' => '1', 'type' => 'checkbox'],
        'generate_sitemap' => ['value' => '1', 'type' => 'checkbox'],
        'robots_txt' => ['value' => "User-agent: *\nDisallow: /admin/\nDisallow: /cart/\nAllow: /public/\nSitemap: https://lal-auto.ru/sitemap.xml", 'type' => 'textarea'],
        'api_enabled' => ['value' => '1', 'type' => 'checkbox'],
        'graphql_enabled' => ['value' => '0', 'type' => 'checkbox'],
        'request_limit' => ['value' => '100', 'type' => 'number'],
        'maintenance_mode' => ['value' => '0', 'type' => 'checkbox'],
        'maintenance_message' => ['value' => 'Сайт временно недоступен. Ведутся технические работы. Приносим извинения за неудобства.', 'type' => 'textarea']
    ];
    
    return $defaults;
}

$backup_stats = ['total' => 0, 'successful' => 0, 'failed' => 0, 'total_size' => 0];
$backup_history = [];

$check_table = $conn->query("SHOW TABLES LIKE 'backup_logs'");

if ($check_table->num_rows > 0) 
{
    $backup_stats_stmt = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful, SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed, COALESCE(SUM(file_size), 0) as total_size FROM backup_logs");

    if ($backup_stats_stmt) 
    {
        $backup_stats = $backup_stats_stmt->fetch_assoc();
    }

    $history_stmt = $conn->query("SELECT * FROM backup_logs ORDER BY created_at DESC LIMIT 10");

    if ($history_stmt) 
    {
        while ($row = $history_stmt->fetch_assoc()) 
        {
            $backup_history[] = $row;
        }
    }
}

$api_keys = [];
$api_keys_stmt = $conn->query("SELECT * FROM api_keys ORDER BY created_at DESC");

if ($api_keys_stmt) 
{
    while ($row = $api_keys_stmt->fetch_assoc()) 
    {
        $api_keys[] = $row;
    }
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
    <h2 class="mb-3 mb-md-0">
        <i class="bi bi-gear me-2"></i>Настройки системы
    </h2>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" onclick="saveAllSettings()">
            <i class="bi bi-save me-1"></i>
            <span class="d-none d-sm-inline">Сохранить все</span>
        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="resetSettings()">
            <i class="bi bi-arrow-clockwise me-1"></i>
            <span class="d-none d-sm-inline">Сбросить</span>
        </button>
    </div>
</div>

<?php 
if (isset($_SESSION['success_message'])) 
{
?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success_message'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
unset($_SESSION['success_message']); 
}

if (isset($_SESSION['error_message'])) 
{
?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $_SESSION['error_message'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php 
unset($_SESSION['error_message']); 
}
?>

<div class="row">
    <div class="col-md-3">
        <div class="list-group" id="settingsTabs" role="tablist">
            <a class="list-group-item list-group-item-action <?= $active_tab == 'general' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#general" role="tab" onclick="setActiveTab('general')">
                <i class="bi bi-house me-2"></i>Основные настройки
            </a>
            <a class="list-group-item list-group-item-action <?= $active_tab == 'store' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#store" role="tab" onclick="setActiveTab('store')">
                <i class="bi bi-shop me-2"></i>Настройки магазина
            </a>
            <a class="list-group-item list-group-item-action <?= $active_tab == 'notifications' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#notifications" role="tab" onclick="setActiveTab('notifications')">
                <i class="bi bi-bell me-2"></i>Уведомления
            </a>
            <a class="list-group-item list-group-item-action <?= $active_tab == 'security' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#security" role="tab" onclick="setActiveTab('security')">
                <i class="bi bi-shield me-2"></i>Безопасность
            </a>
            <a class="list-group-item list-group-item-action <?= $active_tab == 'backup' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#backup" role="tab" onclick="setActiveTab('backup')">
                <i class="bi bi-database me-2"></i>Резервные копии
            </a>
            <a class="list-group-item list-group-item-action <?= $active_tab == 'payment' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#payment" role="tab" onclick="setActiveTab('payment')">
                <i class="bi bi-credit-card me-2"></i>Платежи
            </a>
            <a class="list-group-item list-group-item-action <?= $active_tab == 'shipping' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#shipping" role="tab" onclick="setActiveTab('shipping')">
                <i class="bi bi-truck me-2"></i>Доставка
            </a>
            <a class="list-group-item list-group-item-action <?= $active_tab == 'seo' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#seo" role="tab" onclick="setActiveTab('seo')">
                <i class="bi bi-search me-2"></i>SEO
            </a>
            <a class="list-group-item list-group-item-action <?= $active_tab == 'api' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#api" role="tab" onclick="setActiveTab('api')">
                <i class="bi bi-code-slash me-2"></i>API
            </a>
            <a class="list-group-item list-group-item-action <?= $active_tab == 'maintenance' ? 'active' : '' ?>" 
               data-bs-toggle="list" href="#maintenance" role="tab" onclick="setActiveTab('maintenance')">
                <i class="bi bi-tools me-2"></i>Тех. обслуживание
            </a>
        </div>
    </div>
    <div class="col-md-9">
        <div class="tab-content">
            <div class="tab-pane fade <?= $active_tab == 'general' ? 'show active' : '' ?>" id="general" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-house me-2"></i>Основные настройки</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="generalForm">
                            <input type="hidden" name="save_settings" value="1">
                            <input type="hidden" name="setting_group" value="general">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Название сайта<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="setting_site_name" value="<?= htmlspecialchars(get_setting('site_name', 'Лал-Авто')) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email администратора<span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="setting_admin_email" value="<?= htmlspecialchars(get_setting('admin_email', 'admin@lal-auto.ru')) ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Описание сайта</label>
                                <textarea class="form-control" name="setting_site_description" rows="3"><?= htmlspecialchars(get_setting('site_description', 'Автозапчасти и автосервис - качественное обслуживание вашего автомобиля')) ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Телефон поддержки<span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="setting_support_phone" value="<?= htmlspecialchars(get_setting('support_phone', '+7 (999) 123-45-67')) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Время работы</label>
                                        <input type="text" class="form-control" name="setting_working_hours" value="<?= htmlspecialchars(get_setting('working_hours', 'Пн-Пт: 9:00-18:00, Сб: 10:00-16:00')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Язык по умолчанию</label>
                                        <select class="form-select" name="setting_default_language">
                                            <?php
                                            $languages = ['Русский', 'English', 'Deutsch'];
                                            $current_lang = get_setting('default_language', 'Русский');

                                            foreach ($languages as $lang) 
                                            {
                                                $selected = $current_lang == $lang ? 'selected' : '';
                                                echo "<option value=\"$lang\" $selected>$lang</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Валюта</label>
                                        <select class="form-select" name="setting_currency">
                                            <?php
                                            $currencies = [
                                                'RUB' => 'RUB - Российский рубль',
                                                'USD' => 'USD - Доллар США',
                                                'EUR' => 'EUR - Евро'
                                            ];
                                            $current_currency = get_setting('currency', 'RUB');

                                            foreach ($currencies as $code => $name) 
                                            {
                                                $selected = $current_currency == $code ? 'selected' : '';
                                                echo "<option value=\"$code\" $selected>$name</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="resetTab('general')">Сбросить</button>
                                <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= $active_tab == 'store' ? 'show active' : '' ?>" id="store" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Настройки магазина</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="storeForm">
                            <input type="hidden" name="save_settings" value="1">
                            <input type="hidden" name="setting_group" value="store">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Минимальная сумма заказа</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="setting_min_order_amount" value="<?= htmlspecialchars(get_setting('min_order_amount', '1000')) ?>">
                                            <span class="input-group-text">₽</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">НДС (%)</label>
                                        <select class="form-select" name="setting_vat_rate">
                                            <?php
                                            $vat_rates = ['20', '10', '0'];
                                            $current_vat = get_setting('vat_rate', '20');

                                            foreach ($vat_rates as $rate) 
                                            {
                                                $selected = $current_vat == $rate ? 'selected' : '';
                                                $label = $rate == '0' ? "$rate% (без НДС)" : "$rate% (" . ($rate == '20' ? 'стандартный' : 'льготный') . ")";
                                                echo "<option value=\"$rate\" $selected>$label</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Валютный курс (к рублю)</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text">USD</span>
                                            <input type="number" class="form-control" name="setting_usd_rate" value="<?= htmlspecialchars(get_setting('usd_rate', '90.5')) ?>" step="0.01">
                                        </div>
                                        <div class="input-group">
                                            <span class="input-group-text">EUR</span>
                                            <input type="number" class="form-control" name="setting_eur_rate" value="<?= htmlspecialchars(get_setting('eur_rate', '99.8')) ?>" step="0.01">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Настройки инвентаря</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="setting_low_stock_alert" value="1" id="lowStockAlert" <?= get_setting('low_stock_alert', '1') == '1' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="lowStockAlert">Уведомлять о низком запасе</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="setting_allow_backorder" value="1" id="allowBackorder" <?= get_setting('allow_backorder', '0') == '1' ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="allowBackorder">Разрешить предзаказ</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Политика возвратов</label>
                                <textarea class="form-control" name="setting_return_policy" rows="4"><?= htmlspecialchars(get_setting('return_policy', 'Возврат товара возможен в течение 14 дней с момента покупки при сохранении товарного вида и упаковки.')) ?></textarea>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="resetTab('store')">Сбросить</button>
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= $active_tab == 'notifications' ? 'show active' : '' ?>" id="notifications" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bell me-2"></i>Настройки уведомлений</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="notificationsForm">
                            <input type="hidden" name="save_settings" value="1">
                            <input type="hidden" name="setting_group" value="notifications">
                            <h6 class="mb-3">Email уведомления</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_email_new_orders" value="1" id="emailOrders" <?= get_setting('email_new_orders', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="emailOrders">Новые заказы</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_email_payments" value="1" id="emailPayments" <?= get_setting('email_payments', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="emailPayments">Оплаты</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_email_reviews" value="1" id="emailReviews" <?= get_setting('email_reviews', '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="emailReviews">Новые отзывы</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_email_low_stock" value="1" id="emailStock" <?= get_setting('email_low_stock', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="emailStock">Низкий запас</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_email_newsletter" value="1" id="emailNewsletter" <?= get_setting('email_newsletter', '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="emailNewsletter">Новостная рассылка</label>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">SMS уведомления</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_sms_order_status" value="1" id="smsOrders" <?= get_setting('sms_order_status', '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="smsOrders">Статус заказа</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_sms_delivery" value="1" id="smsDelivery" <?= get_setting('sms_delivery', '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="smsDelivery">Доставка</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_sms_promo" value="1" id="smsPromo" <?= get_setting('sms_promo', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="smsPromo">Акции и скидки</label>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Настройки SMTP</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">SMTP Сервер</label>
                                        <input type="text" class="form-control" name="setting_smtp_server" value="<?= htmlspecialchars(get_setting('smtp_server', 'smtp.gmail.com')) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Порт</label>
                                        <input type="number" class="form-control" name="setting_smtp_port" value="<?= htmlspecialchars(get_setting('smtp_port', '587')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="resetTab('notifications')">Сбросить</button>
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= $active_tab == 'security' ? 'show active' : '' ?>" id="security" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-shield me-2"></i>Настройки безопасности</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="securityForm">
                            <input type="hidden" name="save_settings" value="1">
                            <input type="hidden" name="setting_group" value="security">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Последнее обновление настроек безопасности: <?= date('d.m.Y') ?>
                            </div>
                            <h6 class="mb-3">Парольная политика</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Минимальная длина пароля</label>
                                        <input type="number" class="form-control" name="setting_min_password_length" value="<?= htmlspecialchars(get_setting('min_password_length', '8')) ?>" min="6">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Срок действия пароля (дней)</label>
                                        <input type="number" class="form-control" name="setting_password_expiry_days" value="<?= htmlspecialchars(get_setting('password_expiry_days', '90')) ?>" min="30">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_require_special_char" value="1" id="requireSpecialChar" <?= get_setting('require_special_char', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="requireSpecialChar">Требовать спец. символы</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_require_numbers" value="1" id="requireNumbers" <?= get_setting('require_numbers', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="requireNumbers">Требовать цифры</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_require_upper_lower" value="1" id="requireUpperLower" <?= get_setting('require_upper_lower', '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="requireUpperLower">Верхний/нижний регистр</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_prevent_reuse" value="1" id="preventReuse" <?= get_setting('prevent_reuse', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="preventReuse">Запретить повторное использование</label>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Двухфакторная аутентификация</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_enable_2fa_admin" value="1" id="enable2FA" <?= get_setting('enable_2fa_admin', '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enable2FA">Включить 2FA для администраторов</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_enable_2fa_users" value="1" id="enable2FAUsers" <?= get_setting('enable_2fa_users', '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="enable2FAUsers">2FA для пользователей</label>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Защита от brute-force</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Максимум попыток входа</label>
                                        <input type="number" class="form-control" name="setting_max_login_attempts" value="<?= htmlspecialchars(get_setting('max_login_attempts', '5')) ?>" min="3">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Блокировка на (минут)</label>
                                        <input type="number" class="form-control" name="setting_lockout_minutes" value="<?= htmlspecialchars(get_setting('lockout_minutes', '30')) ?>" min="5">
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="resetTab('security')">Сбросить</button>
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= $active_tab == 'backup' ? 'show active' : '' ?>" id="backup" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-database me-2"></i>Управление резервными копиями</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>Последнее резервное копирование: 
                            <?php 
                            if (!empty($backup_history)) 
                            {
                                echo date('d.m.Y H:i', strtotime($backup_history[0]['created_at']));
                            } 
                            else 
                            {
                                echo 'Никогда';
                            }
                            ?>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-database display-4 text-primary mb-3"></i>
                                        <h5>Всего копий</h5>
                                        <h3 class="text-primary"><?= $backup_stats['total'] ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-hdd display-4 text-success mb-3"></i>
                                        <h5>Общий размер</h5>
                                        <h3 class="text-success"><?= format_size($backup_stats['total_size']) ?></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">Расписание резервного копирования</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Частота</label>
                                    <select class="form-select" id="backupFrequency">
                                        <option>Ежедневно</option>
                                        <option selected>Еженедельно</option>
                                        <option>Ежемесячно</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Время выполнения</label>
                                    <input type="time" class="form-control" id="backupTime" value="02:00">
                                </div>
                            </div>
                        </div>
                        <h6 class="mb-3">История резервных копий</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Файл</th>
                                        <th>Размер</th>
                                        <th>Статус</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (!empty($backup_history))
                                    {
                                    ?>
                                        <?php 
                                        foreach ($backup_history as $backup)
                                        {
                                        ?>
                                        <tr>
                                            <td><?= date('d.m.Y H:i', strtotime($backup['created_at'])) ?></td>
                                            <td><?= htmlspecialchars($backup['filename']) ?></td>
                                            <td><?= $backup['file_size'] ? format_size($backup['file_size']) : '—' ?></td>
                                            <td>
                                                <?php 
                                                if ($backup['status'] == 'success')
                                                {
                                                ?>
                                                    <span class="badge bg-success">Успешно</span>
                                                <?php 
                                                }
                                                else if ($backup['status'] == 'failed')
                                                {
                                                ?>
                                                    <span class="badge bg-danger">Ошибка</span>
                                                <?php 
                                                }
                                                else
                                                {
                                                ?>
                                                    <span class="badge bg-warning">В процессе</span>
                                                <?php 
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if ($backup['status'] == 'success' && file_exists('../backups/' . $backup['filename']))
                                                {
                                                ?>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="downloadBackup('<?= $backup['filename'] ?>')">
                                                        Скачать
                                                    </button>
                                                <?php 
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php 
                                        }
                                        ?>
                                    <?php 
                                    }
                                    else
                                    {
                                    ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">
                                                Нет данных о резервных копиях
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-primary" onclick="createBackup()">
                                <i class="bi bi-plus-circle me-1"></i>Создать резервную копию
                            </button>
                            <button class="btn btn-outline-secondary" onclick="showRestoreModal()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Восстановить
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= $active_tab == 'payment' ? 'show active' : '' ?>" id="payment" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Настройки платежей</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="paymentForm">
                            <input type="hidden" name="save_settings" value="1">
                            <input type="hidden" name="setting_group" value="payment">
                            <h6 class="mb-3">Платежные системы</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Банковские карты</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="setting_bank_cards_enabled" value="1" <?= get_setting('bank_cards_enabled', '1') == '1' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <small class="text-muted">Visa, Mastercard, Mir</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">ЮMoney</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="setting_yoomoney_enabled" value="1" <?= get_setting('yoomoney_enabled', '1') == '1' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <small class="text-muted">Быстрые онлайн-платежи</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Сбербанк Онлайн</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="setting_sberbank_enabled" value="1" <?= get_setting('sberbank_enabled', '0') == '1' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <small class="text-muted">Оплата через Сбербанк</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Наличные при получении</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="setting_cash_on_delivery" value="1" <?= get_setting('cash_on_delivery', '1') == '1' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <small class="text-muted">Оплата курьеру</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Настройки комиссий</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Комиссия за обработку (%)</label>
                                        <input type="number" class="form-control" name="setting_processing_fee" value="<?= htmlspecialchars(get_setting('processing_fee', '2.5')) ?>" step="0.1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Минимальная комиссия (₽)</label>
                                        <input type="number" class="form-control" name="setting_min_fee" value="<?= htmlspecialchars(get_setting('min_fee', '10')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="resetTab('payment')">Сбросить</button>
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= $active_tab == 'shipping' ? 'show active' : '' ?>" id="shipping" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Настройки доставки</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="shippingForm">
                            <input type="hidden" name="save_settings" value="1">
                            <input type="hidden" name="setting_group" value="shipping">
                            <h6 class="mb-3">Способы доставки</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Курьерская доставка</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="setting_courier_enabled" value="1" <?= get_setting('courier_enabled', '1') == '1' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Стоимость:</label>
                                                <input type="number" class="form-control" name="setting_courier_cost" value="<?= htmlspecialchars(get_setting('courier_cost', '300')) ?>">
                                            </div>
                                            <small class="text-muted">Доставка по городу</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Самовывоз</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="setting_pickup_enabled" value="1" <?= get_setting('pickup_enabled', '1') == '1' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Стоимость:</label>
                                                <input type="number" class="form-control" value="0" disabled>
                                            </div>
                                            <small class="text-muted">Из пунктов выдачи</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Почта России</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="setting_russian_post_enabled" value="1" <?= get_setting('russian_post_enabled', '0') == '1' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Стоимость:</label>
                                                <input type="number" class="form-control" name="setting_russian_post_cost" value="<?= htmlspecialchars(get_setting('russian_post_cost', '500')) ?>">
                                            </div>
                                            <small class="text-muted">По всей России</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">СДЭК</h6>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="setting_cdek_enabled" value="1" <?= get_setting('cdek_enabled', '1') == '1' ? 'checked' : '' ?>>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Стоимость:</label>
                                                <input type="number" class="form-control" name="setting_cdek_cost" value="<?= htmlspecialchars(get_setting('cdek_cost', '450')) ?>">
                                            </div>
                                            <small class="text-muted">Курьерская служба</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Бесплатная доставка</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Минимальная сумма для бесплатной доставки</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" name="setting_free_shipping_min" value="<?= htmlspecialchars(get_setting('free_shipping_min', '5000')) ?>">
                                            <span class="input-group-text">₽</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Срок доставки (дней)</label>
                                        <input type="number" class="form-control" name="setting_delivery_days" value="<?= htmlspecialchars(get_setting('delivery_days', '3')) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="resetTab('shipping')">Сбросить</button>
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= $active_tab == 'seo' ? 'show active' : '' ?>" id="seo" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-search me-2"></i>SEO настройки</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="seoForm">
                            <input type="hidden" name="save_settings" value="1">
                            <input type="hidden" name="setting_group" value="seo">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Эти настройки влияют на поисковую оптимизацию вашего сайта.
                            </div>
                            <h6 class="mb-3">Мета-теги</h6>
                            <div class="mb-3">
                                <label class="form-label">Meta Title</label>
                                <input type="text" class="form-control" name="setting_meta_title" value="<?= htmlspecialchars(get_setting('meta_title', 'Лал-Авто - Автозапчасти и автосервис')) ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meta Description</label>
                                <textarea class="form-control" name="setting_meta_description" rows="3"><?= htmlspecialchars(get_setting('meta_description', 'Качественные автозапчасти и профессиональный автосервис. Широкий ассортимент, доступные цены, гарантия качества.')) ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keywords</label>
                                <input type="text" class="form-control" name="setting_meta_keywords" value="<?= htmlspecialchars(get_setting('meta_keywords', 'автозапчасти, автосервис, автомобильные запчасти, ремонт авто')) ?>">
                            </div>
                            <h6 class="mb-3">Open Graph</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">OG Title</label>
                                        <input type="text" class="form-control" name="setting_og_title" value="<?= htmlspecialchars(get_setting('og_title', 'Лал-Авто')) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">OG Image URL</label>
                                        <input type="url" class="form-control" name="setting_og_image" placeholder="https://example.com/image.jpg">
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Настройки URL</h6>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="setting_seo_friendly_urls" value="1" id="seoFriendlyUrls" <?= get_setting('seo_friendly_urls', '1') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="seoFriendlyUrls">ЧПУ (Человекопонятные URL)</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="setting_generate_sitemap" value="1" id="generateSitemap" <?= get_setting('generate_sitemap', '1') == '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="generateSitemap">Автоматически генерировать sitemap.xml</label>
                            </div>
                            <h6 class="mb-3">Robots.txt</h6>
                            <div class="mb-3">
                                <textarea class="form-control" name="setting_robots_txt" rows="4"><?= htmlspecialchars(get_setting('robots_txt', "User-agent: *\nDisallow: /admin/\nDisallow: /cart/\nAllow: /public/\nSitemap: https://lal-auto.ru/sitemap.xml")) ?></textarea>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="resetTab('seo')">Сбросить</button>
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= $active_tab == 'api' ? 'show active' : '' ?>" id="api" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-code-slash me-2"></i>API настройки</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="apiForm">
                            <input type="hidden" name="save_settings" value="1">
                            <input type="hidden" name="setting_group" value="api">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>Будьте осторожны при работе с API ключами. Не передавайте их третьим лицам.
                            </div>
                            <h6 class="mb-3">API Ключи</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Название</th>
                                            <th>Ключ</th>
                                            <th>Создан</th>
                                            <th>Статус</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($api_keys))
                                        {
                                        ?>
                                            <?php 
                                            foreach ($api_keys as $api_key)
                                            {
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($api_key['name']) ?></td>
                                                <td>
                                                    <code class="api-key"><?= substr($api_key['api_key'], 0, 10) ?>******</code>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" 
                                                            onclick="copyToClipboard('<?= $api_key['api_key'] ?>')">
                                                        <i class="bi bi-clipboard"></i>
                                                    </button>
                                                </td>
                                                <td><?= date('d.m.Y', strtotime($api_key['created_at'])) ?></td>
                                                <td>
                                                    <?php 
                                                    if ($api_key['status'] == 'active')
                                                    {
                                                    ?>
                                                        <span class="badge bg-success">Активен</span>
                                                    <?php 
                                                    }
                                                    else if ($api_key['status'] == 'test')
                                                    {
                                                    ?>
                                                        <span class="badge bg-warning">Тестовый</span>
                                                    <?php 
                                                    }
                                                    else
                                                    {
                                                    ?>
                                                        <span class="badge bg-secondary">Неактивен</span>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="revokeApiKey('<?= $api_key['api_key'] ?>')">
                                                        Отозвать
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php 
                                            }
                                            ?>
                                        <?php 
                                        }
                                        else
                                        {
                                        ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    API ключи не найдены
                                                </td>
                                            </tr>
                                        <?php 
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-primary mb-4" onclick="createApiKey()">
                                <i class="bi bi-plus-circle me-1"></i>Создать новый API ключ
                            </button>
                            <h6 class="mb-3">Настройки доступа</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_api_enabled" value="1" id="apiEnabled" <?= get_setting('api_enabled', '1') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="apiEnabled">Включить REST API</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_graphql_enabled" value="1" id="graphqlEnabled" <?= get_setting('graphql_enabled', '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="graphqlEnabled">Включить GraphQL API</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Лимит запросов в минуту</label>
                                        <input type="number" class="form-control" name="setting_request_limit" value="<?= htmlspecialchars(get_setting('request_limit', '100')) ?>">
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Webhooks</h6>
                            <div class="mb-3">
                                <label class="form-label">URL для webhook уведомлений</label>
                                <input type="url" class="form-control" name="setting_webhook_url" placeholder="https://example.com/webhook">
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary" onclick="resetTab('api')">Сбросить</button>
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?= $active_tab == 'maintenance' ? 'show active' : '' ?>" id="maintenance" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Техническое обслуживание</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="maintenanceForm">
                            <input type="hidden" name="save_settings" value="1">
                            <input type="hidden" name="setting_group" value="maintenance">
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-octagon me-2"></i>Внимание! Эти настройки влияют на доступность сайта для пользователей.
                            </div>
                            <h6 class="mb-3">Режим обслуживания</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="setting_maintenance_mode" value="1" id="maintenanceMode" <?= get_setting('maintenance_mode', '0') == '1' ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="maintenanceMode">Включить режим обслуживания</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Сообщение для пользователей</label>
                                        <textarea class="form-control" name="setting_maintenance_message" rows="3"><?= htmlspecialchars(get_setting('maintenance_message', 'Сайт временно недоступен. Ведутся технические работы. Приносим извинения за неудобства.')) ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Очистка кэша</h6>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-trash display-6 text-primary mb-3"></i>
                                            <h6>Очистка кэша</h6>
                                            <button class="btn btn-outline-danger w-100 mt-2" type="button" onclick="clearCache('cache')">
                                                <i class="bi bi-trash me-1"></i>Очистить кэш
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-file-text display-6 text-warning mb-3"></i>
                                            <h6>Очистка логов</h6>
                                            <button class="btn btn-outline-warning w-100 mt-2" type="button" onclick="clearCache('logs')">
                                                <i class="bi bi-trash me-1"></i>Очистить логи
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Системная информация</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Версия PHP</label>
                                        <input type="text" class="form-control" value="<?= phpversion() ?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Версия MySQL</label>
                                        <?php
                                        $mysql_version = $conn->server_version;
                                        $mysql_version_formatted = floor($mysql_version / 10000) . '.' . floor(($mysql_version % 10000) / 100) . '.' . ($mysql_version % 100);
                                        ?>
                                        <input type="text" class="form-control" value="<?= $mysql_version_formatted ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Версия системы</label>
                                        <input type="text" class="form-control" value="2.2.0" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Последнее обновление</label>
                                        <input type="text" class="form-control" value="<?= date('d.m.Y') ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <h6 class="mb-3">Проверка обновлений</h6>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>У вас установлена последняя версия системы.
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary" onclick="checkUpdates()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Проверить обновления
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="updateSystem()">
                                    <i class="bi bi-download me-1"></i>Обновить систему
                                </button>
                            </div>
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-secondary" onclick="resetTab('maintenance')">Сбросить</button>
                                <button type="submit" class="btn btn-primary">Сохранить настройки</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Восстановление из резервной копии</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Внимание!</strong> Восстановление из резервной копии перезапишет все текущие данные в базе данных.
                    <br><small>Рекомендуется создать новую резервную копию перед восстановлением.</small>
                </div>
                <div class="mb-4">
                    <h6>Выберите файл для восстановления:</h6>
                    <select class="form-select" id="restoreFileSelect">
                        <option value="">Выберите файл из списка...</option>
                        <?php 
                        if (!empty($backup_history))
                        {
                        ?>
                            <?php 
                            foreach ($backup_history as $backup)
                            {
                            ?>
                                <?php 
                                if ($backup['status'] == 'success' || $backup['status'] == 'uploaded')
                                {
                                ?>
                                    <?php 
                                    $filepath = '../backups/' . $backup['filename'];

                                    if (!file_exists($filepath)) 
                                    {
                                        $filepath = '../backups/uploads/' . $backup['filename'];
                                    }

                                    if (file_exists($filepath))
                                    { 
                                    ?>
                                        <option value="<?= $backup['filename'] ?>">
                                            <?= $backup['filename'] ?> 
                                            (<?= date('d.m.Y H:i', strtotime($backup['created_at'])) ?>, 
                                            <?= $backup['file_size'] ? format_size($backup['file_size']) : '?' ?>)
                                        </option>
                                    <?php 
                                    }
                                }
                            }
                        }
                        ?>
                    </select>
                    <small class="text-muted">Будут показаны только успешные резервные копии</small>
                </div>
                <div class="mb-4">
                    <h6>Или загрузите новый файл:</h6>
                    <div class="input-group">
                        <input type="file" class="form-control" id="restoreFileUpload" accept=".sql,.gz,.zip">
                        <label class="input-group-text" for="restoreFileUpload">
                            <i class="bi bi-upload"></i>
                        </label>
                    </div>
                    <small class="text-muted">Максимальный размер: 100MB. Разрешены форматы: .sql, .gz, .zip</small>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Процесс восстановления может занять несколько минут в зависимости от размера базы данных.
                    <br>Не закрывайте это окно до завершения операции.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger" onclick="restoreBackup()">
                    <i class="bi bi-arrow-clockwise me-1"></i>Восстановить
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function setActiveTab(tab) 
{
    let url = new URL(window.location);
    url.searchParams.set('tab', tab);
    window.history.pushState({}, '', url);
    localStorage.setItem('activeSettingsTab', tab);
}

function saveAllSettings() 
{
    let forms = document.querySelectorAll('.tab-content form');
    let promises = [];
    
    forms.forEach(form => {
        if (form.checkValidity()) 
        {
            let formData = new FormData(form);
            let submitBtn = form.querySelector('button[type="submit"]');
            let originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            submitBtn.disabled = true;
            
            promises.push(
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) 
                    {
                        throw new Error('Ошибка сохранения');
                    }

                    return response.text();
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                })
            );
        }
    });
    
    Promise.all(promises)
        .then(() => {
            alert('Все настройки успешно сохранены');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при сохранении настроек');
        });
}

function resetSettings() 
{
    if (!confirm('Вы уверены, что хотите сбросить все настройки к значениям по умолчанию?')) 
    {
        return;
    }
    
    fetch('../files/reset_settings.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                alert('Настройки успешно сброшены');
                location.reload();
            } 
            else 
            {
                alert('Ошибка: ' + (data.error || 'Не удалось сбросить настройки'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при сбросе настроек');
        });
}

function resetTab(tab) 
{
    if (!confirm(`Сбросить настройки вкладки "${getTabName(tab)}" к значениям по умолчанию?`)) 
    {
        return;
    }
    
    fetch(`../files/reset_settings.php?tab=${tab}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                alert('Настройки успешно сброшены');
                location.reload();
            } 
            else 
            {
                alert('Ошибка: ' + (data.error || 'Не удалось сбросить настройки'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при сбросе настроек');
        });
}

function getTabName(tab) {
    let tabNames = {
        'general': 'Основные настройки',
        'store': 'Настройки магазина',
        'notifications': 'Уведомления',
        'security': 'Безопасность',
        'backup': 'Резервные копии',
        'payment': 'Платежи',
        'shipping': 'Доставка',
        'seo': 'SEO',
        'api': 'API',
        'maintenance': 'Техническое обслуживание'
    };

    return tabNames[tab] || tab;
}

document.addEventListener('submit', function(e) 
{
    if (e.target.closest('form') && e.target.closest('form').id !== 'restoreForm') 
    {
        e.preventDefault();

        let form = e.target;
        let formData = new FormData(form);
        let submitBtn = form.querySelector('button[type="submit"]');
        let originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Сохранение...';
        submitBtn.disabled = true;
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) 
            {
                return response.text();
            }

            throw new Error('Ошибка сохранения');
        })
        .then(() => {
            alert('Настройки успешно сохранены');
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при сохранении настроек');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }
});

function createBackup() 
{
    if (!confirm('Создать резервную копию базы данных? Операция может занять несколько минут.')) 
    {
        return;
    }
    
    let btn = document.querySelector('#backup .btn-primary');
    let originalText = btn.innerHTML;
    
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Создание...';
    btn.disabled = true;
    
    fetch('../files/create_backup.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                alert(`Резервная копия успешно создана:\n\nФайл: ${data.filename}\nРазмер: ${data.filesize}\nДата: ${data.date}`);
                location.reload();
            } 
            else 
            {
                alert('Ошибка: ' + (data.error || 'Не удалось создать резервную копию'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при создании резервной копии');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

function downloadBackup(filename) 
{
    window.location.href = `../files/download_backup.php?file=${encodeURIComponent(filename)}`;
}

function showRestoreModal() 
{
    let modal = new bootstrap.Modal(document.getElementById('restoreModal'));
    modal.show();
}

function restoreBackup() 
{
    let fileSelect = document.getElementById('restoreFileSelect');
    let fileUpload = document.getElementById('restoreFileUpload');
    let filename = '';
    let fileData = null;
    
    if (fileSelect.value) 
    {
        filename = fileSelect.value;
    } 
    else if (fileUpload.files.length > 0) 
    {
        let file = fileUpload.files[0];
        let formData = new FormData();
        formData.append('backup_file', file);
        
        showLoading('Загрузка файла...');
        
        fetch('../files/upload_backup.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                performRestore(data.filename);
            } 
            else 
            {
                hideLoading();
                alert('Ошибка: ' + (data.error || 'Не удалось загрузить файл'));
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('Ошибка при загрузке файла');
        });
        
        return;
    } 
    else 
    {
        alert('Выберите файл для восстановления');
        return;
    }
    
    performRestore(filename);
}

function performRestore(filename) 
{
    if (!confirm(`Вы уверены, что хотите восстановить базу данных из файла "${filename}"?\n\nВНИМАНИЕ: Все текущие данные будут перезаписаны!`)) 
    {
        return;
    }
    
    showLoading('Восстановление базы данных... Это может занять несколько минут.');

    let formData = new FormData();
    formData.append('filename', filename);
    
    fetch('../files/restore_backup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) 
        {
            throw new Error('Ошибка сервера: ' + response.status);
        }

        return response.json();
    })
    .then(data => {
        hideLoading();
        
        if (data.success) 
        {
            let message = data.message + '\n\n';
            
            if (data.details) 
            {
                message += 'Детали:\n';
                message += `- Файл: ${data.details.filename}\n`;
                message += `- Размер: ${data.details.file_size}\n`;
                message += `- Выполнено запросов: ${data.details.queries_executed}/${data.details.total_queries}\n`;
                message += `- Успешность: ${data.details.success_rate}\n`;
                
                if (data.details.errors_count > 0) 
                {
                    message += `- Ошибок: ${data.details.errors_count}\n`;
                }
            }
            
            if (data.warnings && data.warnings.length > 0) 
            {
                message += '\nПредупреждения:\n';
                data.warnings.slice(0, 3).forEach(warning => {
                    message += `- Запрос #${warning.query_num}: ${warning.error}\n`;
                });

                if (data.warnings.length > 3) 
                {
                    message += `- ... и еще ${data.warnings.length - 3} предупреждений\n`;
                }
            }
            
            if (confirm(message + '\nПерезагрузить страницу для применения изменений?')) 
            {
                location.reload();
            }
        } 
        else 
        {
            alert('Ошибка: ' + (data.error || 'Не удалось восстановить базу данных'));
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        alert('Ошибка при восстановлении базы данных: ' + error.message);
    });
}

function clearCache(type) 
{
    let typeName = type === 'cache' ? 'кэш' : 'логи';
    
    if (!confirm(`Вы уверены, что хотите очистить ${typeName}?`)) 
    {
        return;
    }
    
    fetch(`../files/clear_cache.php?type=${type}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                alert(data.message || `${typeName === 'кэш' ? 'Кэш' : 'Логи'} успешно очищены`);
                location.reload();
            } 
            else 
            {
                alert('Ошибка: ' + (data.error || `Не удалось очистить ${typeName}`));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Ошибка при очистке ${typeName}`);
        });
}

function checkUpdates() 
{
    let btn = event.target;
    let originalText = btn.innerHTML;
    
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    btn.disabled = true;
    
    fetch('../files/check_updates.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                if (data.update_available) 
                {
                    if (confirm(`Доступно обновление до версии ${data.latest_version}\nТекущая версия: ${data.current_version}\n\n${data.release_notes || ''}\n\nОбновить сейчас?`)) 
                    {
                        updateSystem();
                    }
                }
                else 
                {
                    alert('У вас установлена последняя версия системы.');
                }
            } 
            else 
            {
                alert('Ошибка при проверке обновлений: ' + (data.error || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при проверке обновлений');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

function updateSystem() 
{
    if (!confirm('Вы уверены, что хотите обновить систему? Во время обновления сайт может быть недоступен.')) 
    {
        return;
    }
    
    let btn = event.target;
    let originalText = btn.innerHTML;
    
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    btn.disabled = true;
    
    fetch('../files/update_system.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                alert('Система успешно обновлена. Страница будет перезагружена.');
                location.reload();
            } 
            else 
            {
                alert('Ошибка: ' + (data.error || 'Не удалось обновить систему'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при обновлении системы');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

function createApiKey() 
{
    let name = prompt('Введите название для нового API ключа:');

    if (!name) 
    {
        return;
    }
    
    fetch('../files/create_api_key.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name: name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) 
        {
            let key = data.api_key;
            alert(`API ключ успешно создан:\n\nКлюч: ${key}\n\nСохраните этот ключ, он больше не будет показан!`);
            location.reload();
        } 
        else 
        {
            alert('Ошибка: ' + (data.error || 'Не удалось создать API ключ'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при создании API ключа');
    });
}

function revokeApiKey(key) 
{
    if (!confirm('Вы уверены, что хотите отозвать этот API ключ?')) 
    {
        return;
    }
    
    fetch('../files/revoke_api_key.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ key: key })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) 
        {
            alert('API ключ успешно отозван');
            location.reload();
        } 
        else 
        {
            alert('Ошибка: ' + (data.error || 'Не удалось отозвать API ключ'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ошибка при отзыве API ключа');
    });
}

function copyToClipboard(text) 
{
    navigator.clipboard.writeText(text)
        .then(() => {
            alert('Ключ скопирован в буфер обмена');
        })
        .catch(err => {
            console.error('Ошибка копирования: ', err);
            alert('Не удалось скопировать ключ');
        });
}

document.getElementById('maintenanceMode')?.addEventListener('change', function() 
{
    if (this.checked) 
    {
        if (!confirm('Включение режима обслуживания сделает сайт недоступным для пользователей. Продолжить?')) 
        {
            this.checked = false;
            return;
        }
    }

    let form = this.closest('form');

    if (form) 
    {
        form.dispatchEvent(new Event('submit'));
    }
});

function formatSize(bytes) 
{
    if (bytes === 0) 
    {
        return '0 B';
    }

    let k = 1024;
    let sizes = ['B', 'KB', 'MB', 'GB'];
    let i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

document.addEventListener('DOMContentLoaded', function() 
{
    let activeTab = localStorage.getItem('activeSettingsTab') || 'general';
    let tabLink = document.querySelector(`[href="#${activeTab}"]`);

    if (tabLink) 
    {
        let tab = new bootstrap.Tab(tabLink);
        tab.show();
        setActiveTab(activeTab);
    }

    document.querySelectorAll('#settingsTabs a').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) 
        {
            let tabId = e.target.getAttribute('href').substring(1);
            setActiveTab(tabId);
        });
    });
});

function showLoading(message = 'Пожалуйста, подождите...') 
{
    let overlay = document.getElementById('loadingOverlay');

    if (!overlay) 
    {
        overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        `;
        
        let spinner = document.createElement('div');

        spinner.style.cssText = `
            text-align: center;
            color: white;
        `;
        
        let spinnerIcon = document.createElement('div');

        spinnerIcon.className = 'spinner-border text-light';
        spinnerIcon.style.cssText = 'width: 3rem; height: 3rem;';
        
        let text = document.createElement('div');

        text.id = 'loadingText';
        text.style.cssText = 'margin-top: 1rem; font-size: 1.2rem;';
        text.textContent = message;
        
        spinner.appendChild(spinnerIcon);
        spinner.appendChild(text);
        overlay.appendChild(spinner);
        document.body.appendChild(overlay);
    } 
    else 
    {
        document.getElementById('loadingText').textContent = message;
        overlay.style.display = 'flex';
    }
}

function hideLoading() 
{
    let overlay = document.getElementById('loadingOverlay');

    if (overlay) 
    {
        overlay.style.display = 'none';
    }
}
</script>

<?php
function format_size($bytes) 
{
    if ($bytes == 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}
?>