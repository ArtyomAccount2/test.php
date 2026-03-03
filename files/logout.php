<?php
error_reporting(E_ALL);
session_start();

require_once("../config/link.php");

if (isset($_SESSION['user_id'])) 
{
    $userId = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("DELETE FROM remember_tokens WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
}

if (isset($_COOKIE['remember_token'])) 
{
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

$_SESSION = array();

if (ini_get("session.use_cookies")) 
{
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

header("Location: ../index.php");
exit();
?>