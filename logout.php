<?php
session_start();

$_SESSION = [];
session_unset();
session_destroy();

if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, "/");
}

header("Location: home.php");
exit();
