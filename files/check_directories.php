<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user'] !== 'admin') 
{
    echo "Доступ запрещен";
    exit();
}

$directories = [
    '../backups/',
    '../backups/uploads/',
    '../backups/temp/',
    '../cache/',
    '../logs/',
    '../updates/',
    '../uploads/',
    '../uploads/avatars/',
    '../uploads/products/'
];

echo "<h3>Проверка директорий</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Директория</th><th>Статус</th><th>Права</th></tr>";

foreach ($directories as $dir) 
{
    if (!is_dir($dir)) 
    {
        if (mkdir($dir, 0755, true)) 
        {
            $status = "Создана ✓";
        } 
        else 
        {
            $status = "Ошибка создания";
        }
    } 
    else 
    {
        $status = "Существует ✓";
    }
    
    $perms = substr(sprintf('%o', fileperms($dir)), -4);
    
    echo "<tr>";
    echo "<td>$dir</td>";
    echo "<td>$status</td>";
    echo "<td>$perms</td>";
    echo "</tr>";
}

echo "</table>";
echo "<h3>Проверка записи</h3>";

$test_file = '../backups/test_write.txt';

if (file_put_contents($test_file, 'test')) 
{
    echo "Запись в backups/ возможна ✓<br>";
    unlink($test_file);
} 
else 
{
    echo "Ошибка записи в backups/<br>";
}

echo "<h3>Проверка PHP настроек</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";
echo "<h3>Проверка расширений PHP</h3>";

$extensions = ['mysqli', 'zip', 'zlib', 'json'];

foreach ($extensions as $ext) 
{
    echo "$ext: " . (extension_loaded($ext) ? "✓" : "✗") . "<br>";
}
?>