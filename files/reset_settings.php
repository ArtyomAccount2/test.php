<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

$tab = $_GET['tab'] ?? '';

try 
{
    $default_settings = get_default_settings($tab);
    
    foreach ($default_settings as $key => $setting) 
    {
        $check_stmt = $conn->prepare("SELECT id FROM settings WHERE setting_key = ?");
        $check_stmt->bind_param("s", $key);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) 
        {
            $stmt = $conn->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
            $stmt->bind_param("ss", $setting['value'], $key);
        } 
        else 
        {
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?)");
            $group = get_setting_group($key, $tab);
            $stmt->bind_param("sss", $key, $setting['value'], $group);
        }
        
        $stmt->execute();
    }
    
    echo json_encode(['success' => true, 'message' => 'Настройки успешно сброшены']);
    
} 
catch (Exception $e) 
{
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function get_default_settings($tab = '') 
{
    $defaults = [];

    $all_defaults = [
        'site_name' => ['value' => 'Лал-Авто', 'group' => 'general'],
        'admin_email' => ['value' => 'admin@lal-auto.ru', 'group' => 'general'],
        'site_description' => ['value' => 'Автозапчасти и автосервис - качественное обслуживание вашего автомобиля', 'group' => 'general'],
        'support_phone' => ['value' => '+7 (999) 123-45-67', 'group' => 'general'],
        'working_hours' => ['value' => 'Пн-Пт: 9:00-18:00, Сб: 10:00-16:00', 'group' => 'general'],
        'default_language' => ['value' => 'Русский', 'group' => 'general'],
        'currency' => ['value' => 'RUB', 'group' => 'general'],
        'min_order_amount' => ['value' => '1000', 'group' => 'store'],
        'vat_rate' => ['value' => '20', 'group' => 'store'],
        'usd_rate' => ['value' => '90.5', 'group' => 'store'],
        'eur_rate' => ['value' => '99.8', 'group' => 'store'],
        'low_stock_alert' => ['value' => '1', 'group' => 'store'],
        'allow_backorder' => ['value' => '0', 'group' => 'store'],
        'return_policy' => ['value' => 'Возврат товара возможен в течение 14 дней с момента покупки при сохранении товарного вида и упаковки.', 'group' => 'store'],
        'email_new_orders' => ['value' => '1', 'group' => 'notifications'],
        'email_payments' => ['value' => '1', 'group' => 'notifications'],
        'email_reviews' => ['value' => '0', 'group' => 'notifications'],
        'email_low_stock' => ['value' => '1', 'group' => 'notifications'],
        'email_newsletter' => ['value' => '0', 'group' => 'notifications'],
        'sms_order_status' => ['value' => '0', 'group' => 'notifications'],
        'sms_delivery' => ['value' => '0', 'group' => 'notifications'],
        'sms_promo' => ['value' => '1', 'group' => 'notifications'],
        'smtp_server' => ['value' => 'smtp.gmail.com', 'group' => 'notifications'],
        'smtp_port' => ['value' => '587', 'group' => 'notifications'],
        'min_password_length' => ['value' => '8', 'group' => 'security'],
        'password_expiry_days' => ['value' => '90', 'group' => 'security'],
        'require_special_char' => ['value' => '1', 'group' => 'security'],
        'require_numbers' => ['value' => '1', 'group' => 'security'],
        'require_upper_lower' => ['value' => '0', 'group' => 'security'],
        'prevent_reuse' => ['value' => '1', 'group' => 'security'],
        'enable_2fa_admin' => ['value' => '0', 'group' => 'security'],
        'enable_2fa_users' => ['value' => '0', 'group' => 'security'],
        'max_login_attempts' => ['value' => '5', 'group' => 'security'],
        'lockout_minutes' => ['value' => '30', 'group' => 'security'],
        'bank_cards_enabled' => ['value' => '1', 'group' => 'payment'],
        'yoomoney_enabled' => ['value' => '1', 'group' => 'payment'],
        'sberbank_enabled' => ['value' => '0', 'group' => 'payment'],
        'cash_on_delivery' => ['value' => '1', 'group' => 'payment'],
        'processing_fee' => ['value' => '2.5', 'group' => 'payment'],
        'min_fee' => ['value' => '10', 'group' => 'payment'],
        'courier_enabled' => ['value' => '1', 'group' => 'shipping'],
        'courier_cost' => ['value' => '300', 'group' => 'shipping'],
        'pickup_enabled' => ['value' => '1', 'group' => 'shipping'],
        'russian_post_enabled' => ['value' => '0', 'group' => 'shipping'],
        'russian_post_cost' => ['value' => '500', 'group' => 'shipping'],
        'cdek_enabled' => ['value' => '1', 'group' => 'shipping'],
        'cdek_cost' => ['value' => '450', 'group' => 'shipping'],
        'free_shipping_min' => ['value' => '5000', 'group' => 'shipping'],
        'delivery_days' => ['value' => '3', 'group' => 'shipping'],
        'meta_title' => ['value' => 'Лал-Авто - Автозапчасти и автосервис', 'group' => 'seo'],
        'meta_description' => ['value' => 'Качественные автозапчасти и профессиональный автосервис. Широкий ассортимент, доступные цены, гарантия качества.', 'group' => 'seo'],
        'meta_keywords' => ['value' => 'автозапчасти, автосервис, автомобильные запчасти, ремонт авто', 'group' => 'seo'],
        'og_title' => ['value' => 'Лал-Авто', 'group' => 'seo'],
        'seo_friendly_urls' => ['value' => '1', 'group' => 'seo'],
        'generate_sitemap' => ['value' => '1', 'group' => 'seo'],
        'robots_txt' => ['value' => "User-agent: *\nDisallow: /admin/\nDisallow: /cart/\nAllow: /public/\nSitemap: https://lal-auto.ru/sitemap.xml", 'group' => 'seo'],
        'api_enabled' => ['value' => '1', 'group' => 'api'],
        'graphql_enabled' => ['value' => '0', 'group' => 'api'],
        'request_limit' => ['value' => '100', 'group' => 'api'],
        'maintenance_mode' => ['value' => '0', 'group' => 'maintenance'],
        'maintenance_message' => ['value' => 'Сайт временно недоступен. Ведутся технические работы. Приносим извинения за неудобства.', 'group' => 'maintenance']
    ];
    
    if (empty($tab)) 
    {
        return $all_defaults;
    } 
    else 
    {
        foreach ($all_defaults as $key => $setting) 
        {
            if ($setting['group'] === $tab) 
            {
                $defaults[$key] = $setting;
            }
        }

        return $defaults;
    }
}

function get_setting_group($key, $default_tab) 
{
    $groups = [
        'general' => ['site_name', 'admin_email', 'site_description', 'support_phone', 'working_hours', 'default_language', 'currency'],
        'store' => ['min_order_amount', 'vat_rate', 'usd_rate', 'eur_rate', 'low_stock_alert', 'allow_backorder', 'return_policy'],
        'notifications' => ['email_new_orders', 'email_payments', 'email_reviews', 'email_low_stock', 'email_newsletter', 'sms_order_status', 'sms_delivery', 'sms_promo', 'smtp_server', 'smtp_port'],
        'security' => ['min_password_length', 'password_expiry_days', 'require_special_char', 'require_numbers', 'require_upper_lower', 'prevent_reuse', 'enable_2fa_admin', 'enable_2fa_users', 'max_login_attempts', 'lockout_minutes'],
        'payment' => ['bank_cards_enabled', 'yoomoney_enabled', 'sberbank_enabled', 'cash_on_delivery', 'processing_fee', 'min_fee'],
        'shipping' => ['courier_enabled', 'courier_cost', 'pickup_enabled', 'russian_post_enabled', 'russian_post_cost', 'cdek_enabled', 'cdek_cost', 'free_shipping_min', 'delivery_days'],
        'seo' => ['meta_title', 'meta_description', 'meta_keywords', 'og_title', 'seo_friendly_urls', 'generate_sitemap', 'robots_txt'],
        'api' => ['api_enabled', 'graphql_enabled', 'request_limit'],
        'maintenance' => ['maintenance_mode', 'maintenance_message']
    ];
    
    foreach ($groups as $group => $keys) 
    {
        if (in_array($key, $keys)) 
        {
            return $group;
        }
    }
    
    return $default_tab;
}
?>