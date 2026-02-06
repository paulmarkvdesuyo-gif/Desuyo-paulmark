<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'salon_booking');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/staff/');

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
    if (empty($_SESSION['staff'])) {
        header('Location: ' . BASE_URL . 'login_admin_staff.php');
        exit;
    }
}
?>
