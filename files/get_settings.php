<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

$group = $_GET['group'] ?? 'general';
$settings = [];

$stmt = $conn->prepare("SELECT setting_key, setting_value, setting_type, options FROM settings WHERE setting_group = ?");
$stmt->bind_param("s", $group);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) 
{
    $settings[$row['setting_key']] = [
        'value' => $row['setting_value'],
        'type' => $row['setting_type'],
        'options' => $row['options']
    ];
}

$default_settings = get_default_settings($group);

foreach ($default_settings as $key => $default) 
{
    if (!isset($settings[$key])) 
    {
        $settings[$key] = $default;
    }
}

echo json_encode(['success' => true, 'settings' => $settings]);

function get_default_settings($group) 
{
    $defaults = [];
    
    switch ($group) 
    {
        case 'general':
            $defaults = [
                'site_name' => ['value' => 'Лал-Авто', 'type' => 'text'],
                'admin_email' => ['value' => 'admin@lal-auto.ru', 'type' => 'email'],
                'site_description' => ['value' => 'Автозапчасти и автосервис - качественное обслуживание вашего автомобиля', 'type' => 'textarea'],
                'support_phone' => ['value' => '+7 (999) 123-45-67', 'type' => 'tel'],
                'working_hours' => ['value' => 'Пн-Пт: 9:00-18:00, Сб: 10:00-16:00', 'type' => 'text'],
                'default_language' => ['value' => 'Русский', 'type' => 'select', 'options' => 'Русский,English,Deutsch'],
                'currency' => ['value' => 'RUB', 'type' => 'select', 'options' => 'RUB - Российский рубль,USD - Доллар США,EUR - Евро']
            ];
            break;
        case 'store':
            $defaults = [
                'min_order_amount' => ['value' => '1000', 'type' => 'number'],
                'vat_rate' => ['value' => '20', 'type' => 'select', 'options' => '20% (стандартный),10% (льготный),0% (без НДС)'],
                'usd_rate' => ['value' => '90.5', 'type' => 'number'],
                'eur_rate' => ['value' => '99.8', 'type' => 'number'],
                'low_stock_alert' => ['value' => '1', 'type' => 'checkbox'],
                'allow_backorder' => ['value' => '0', 'type' => 'checkbox'],
                'return_policy' => ['value' => 'Возврат товара возможен в течение 14 дней с момента покупки при сохранении товарного вида и упаковки.', 'type' => 'textarea']
            ];
            break; 
        case 'notifications':
            $defaults = [
                'email_new_orders' => ['value' => '1', 'type' => 'checkbox'],
                'email_payments' => ['value' => '1', 'type' => 'checkbox'],
                'email_reviews' => ['value' => '0', 'type' => 'checkbox'],
                'email_low_stock' => ['value' => '1', 'type' => 'checkbox'],
                'email_newsletter' => ['value' => '0', 'type' => 'checkbox'],
                'sms_order_status' => ['value' => '0', 'type' => 'checkbox'],
                'sms_delivery' => ['value' => '0', 'type' => 'checkbox'],
                'sms_promo' => ['value' => '1', 'type' => 'checkbox'],
                'smtp_server' => ['value' => 'smtp.gmail.com', 'type' => 'text'],
                'smtp_port' => ['value' => '587', 'type' => 'number']
            ];
            break;
        case 'security':
            $defaults = [
                'min_password_length' => ['value' => '8', 'type' => 'number'],
                'password_expiry_days' => ['value' => '90', 'type' => 'number'],
                'require_special_char' => ['value' => '1', 'type' => 'checkbox'],
                'require_numbers' => ['value' => '1', 'type' => 'checkbox'],
                'require_upper_lower' => ['value' => '0', 'type' => 'checkbox'],
                'prevent_reuse' => ['value' => '1', 'type' => 'checkbox'],
                'enable_2fa_admin' => ['value' => '0', 'type' => 'checkbox'],
                'enable_2fa_users' => ['value' => '0', 'type' => 'checkbox'],
                'max_login_attempts' => ['value' => '5', 'type' => 'number'],
                'lockout_minutes' => ['value' => '30', 'type' => 'number']
            ];
            break;
        case 'payment':
            $defaults = [
                'bank_cards_enabled' => ['value' => '1', 'type' => 'checkbox'],
                'yoomoney_enabled' => ['value' => '1', 'type' => 'checkbox'],
                'sberbank_enabled' => ['value' => '0', 'type' => 'checkbox'],
                'cash_on_delivery' => ['value' => '1', 'type' => 'checkbox'],
                'processing_fee' => ['value' => '2.5', 'type' => 'number'],
                'min_fee' => ['value' => '10', 'type' => 'number']
            ];
            break;
        case 'shipping':
            $defaults = [
                'courier_enabled' => ['value' => '1', 'type' => 'checkbox'],
                'courier_cost' => ['value' => '300', 'type' => 'number'],
                'pickup_enabled' => ['value' => '1', 'type' => 'checkbox'],
                'russian_post_enabled' => ['value' => '0', 'type' => 'checkbox'],
                'russian_post_cost' => ['value' => '500', 'type' => 'number'],
                'cdek_enabled' => ['value' => '1', 'type' => 'checkbox'],
                'cdek_cost' => ['value' => '450', 'type' => 'number'],
                'free_shipping_min' => ['value' => '5000', 'type' => 'number'],
                'delivery_days' => ['value' => '3', 'type' => 'number']
            ];
            break;
        case 'seo':
            $defaults = [
                'meta_title' => ['value' => 'Лал-Авто - Автозапчасти и автосервис', 'type' => 'text'],
                'meta_description' => ['value' => 'Качественные автозапчасти и профессиональный автосервис. Широкий ассортимент, доступные цены, гарантия качества.', 'type' => 'textarea'],
                'meta_keywords' => ['value' => 'автозапчасти, автосервис, автомобильные запчасти, ремонт авто', 'type' => 'text'],
                'og_title' => ['value' => 'Лал-Авто', 'type' => 'text'],
                'seo_friendly_urls' => ['value' => '1', 'type' => 'checkbox'],
                'generate_sitemap' => ['value' => '1', 'type' => 'checkbox'],
                'robots_txt' => ['value' => "User-agent: *\nDisallow: /admin/\nDisallow: /cart/\nAllow: /public/\nSitemap: https://lal-auto.ru/sitemap.xml", 'type' => 'textarea']
            ];
            break;
        case 'api':
            $defaults = [
                'api_enabled' => ['value' => '1', 'type' => 'checkbox'],
                'graphql_enabled' => ['value' => '0', 'type' => 'checkbox'],
                'request_limit' => ['value' => '100', 'type' => 'number']
            ];
            break;
        case 'maintenance':
            $defaults = [
                'maintenance_mode' => ['value' => '0', 'type' => 'checkbox'],
                'maintenance_message' => ['value' => 'Сайт временно недоступен. Ведутся технические работы. Приносим извинения за неудобства.', 'type' => 'textarea']
            ];
            break;
    }
    
    return $defaults;
}
?>