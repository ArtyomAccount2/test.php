<?php
session_start();
require_once("../config/link.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo json_encode(['success' => false, 'error' => 'Доступ запрещен']);
    exit();
}

$current_version = '2.1.0';

try 
{
    $update_data = check_for_updates_via_api();
    
    if ($update_data === false) 
    {
        $latest_version = get_latest_version_from_file();
        $update_available = version_compare($latest_version, $current_version, '>');
        
        echo json_encode([
            'success' => true,
            'current_version' => $current_version,
            'latest_version' => $latest_version,
            'update_available' => $update_available,
            'release_notes' => $update_available ? get_release_notes($latest_version) : '',
            'update_type' => $update_available ? get_update_type($current_version, $latest_version) : 'none'
        ]);
    } 
    else 
    {
        echo json_encode([
            'success' => true,
            'current_version' => $current_version,
            'latest_version' => $update_data['version'],
            'update_available' => $update_data['update_available'],
            'release_notes' => $update_data['release_notes'] ?? '',
            'update_type' => $update_data['update_type'] ?? 'patch',
            'download_url' => $update_data['download_url'] ?? '',
            'changelog' => $update_data['changelog'] ?? []
        ]);
    }
    
} 
catch (Exception $e) 
{
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function check_for_updates_via_api() 
{
    // $api_url = "https://updates.autoshop-system.com/check.php";
    // $params = [
    //     'version' => $current_version,
    //     'license_key' => get_license_key(),
    //     'domain' => $_SERVER['HTTP_HOST']
    // ];

    return false;
}

function get_latest_version_from_file() 
{
    $version_file = '../version.json';
    
    if (file_exists($version_file)) 
    {
        $version_data = json_decode(file_get_contents($version_file), true);

        if (isset($version_data['latest_version'])) 
        {
            return $version_data['latest_version'];
        }
    }

    global $current_version;
    return $current_version;
}

function get_release_notes($version) 
{
    $notes = [
        '2.2.0' => 'Новый функционал:\n- Добавлена система скидок\n- Улучшен интерфейс админ-панели\n- Оптимизирована производительность',
        '2.1.1' => 'Исправления ошибок:\n- Исправлена проблема с загрузкой изображений\n- Устранена уязвимость безопасности\n- Улучшена совместимость с PHP 8.2',
        '2.1.0' => 'Текущая стабильная версия'
    ];
    
    return $notes[$version] ?? 'Обновление до версии ' . $version;
}

function get_update_type($current, $latest) 
{
    $current_parts = explode('.', $current);
    $latest_parts = explode('.', $latest);
    
    if ($current_parts[0] != $latest_parts[0]) 
    {
        return 'major';
    } 
    else if ($current_parts[1] != $latest_parts[1]) 
    {
        return 'minor';
    } 
    else 
    {
        return 'patch';
    }
}

function get_license_key() 
{
    $settings_file = '../config/settings.json';

    if (file_exists($settings_file)) 
    {
        $settings = json_decode(file_get_contents($settings_file), true);

        return $settings['license_key'] ?? '';
    }
    
    return '';
}
?>