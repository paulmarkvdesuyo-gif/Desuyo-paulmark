<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'salon_booking');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/');

function db_connect() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}

function require_login() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user'])) {
        header('Location: ' . BASE_URL . 'auth/login_admin_staff.php');
        exit;
    }
}

function is_admin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function require_admin_login() {
    if (!is_admin()) {
        header('Location: ' . BASE_URL . 'auth/login_admin_staff.php');
        exit;
    }
}
?>
