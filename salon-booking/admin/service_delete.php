<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}

$id = (int)($_GET['id'] ?? 0);
if($id){
    $pdo = db_connect();
    $pdo->prepare("DELETE FROM services WHERE service_id=?")->execute([$id]);
}
header("Location: services.php");
exit;
