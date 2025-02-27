<?php
error_reporting(E_ALL);
session_start();

require_once("config/link.php");

if(empty($_POST))
{
?>
    <form action="/" method="POST">
        <label for="">Логин: <input type="text" name="login" required></label><br><br>
        <label for="">Пароль: <input type="password" name="password" required></label><br><br>
        <input type="submit" value="Войти">
    </form>
<?php
}
?>