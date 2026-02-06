<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}

$pdo = db_connect();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("DELETE FROM users WHERE user_id=? AND role='staff'");
$stmt->execute([$id]);

header("Location: staff_list.php");
exit;
