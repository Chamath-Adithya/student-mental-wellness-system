<?php
require_once __DIR__ . '/config/db.php';

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

session_start();
set_flash('success', 'ඔබ සාර්ථකව පද්ධතියෙන් ඉවත් විය.');
redirect(SITE_URL . '/index.php');
