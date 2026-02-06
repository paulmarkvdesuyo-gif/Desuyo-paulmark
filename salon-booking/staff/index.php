<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    header('Location: ../auth/login_admin_staff.php');
    exit;
}

$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Staff Dashboard</title>
<style>
body {
    margin:0;
    font-family:'Segoe UI',sans-serif;
    display:flex;
    min-height:100vh;
}
.sidebar {
    background:#b84d7a;
    color:white;
    width:220px;
    padding:20px;
    box-sizing:border-box;
    transition:transform 0.3s ease;
}
.sidebar h2 { font-size:20px; margin-bottom:20px; }
.sidebar a {
    display:block;
    padding:10px;
    margin:8px 0;
    color:white;
    text-decoration:none;
    border-radius:6px;
    transition:background 0.3s;
}
.sidebar a:hover, .sidebar a.active { background:#9c3f65; }
.content {
    flex:1;
    padding:20px;
    background:#f9f9f9;
}
.menu-toggle {
    display:none;
    position:absolute;
    top:15px;
    left:15px;
    background:#b84d7a;
    color:white;
    border:none;
    padding:10px;
    border-radius:6px;
    cursor:pointer;
    z-index:1000;
}
@media(max-width:768px){
    .sidebar {
        position:fixed;
        top:0;
        left:0;
        height:100%;
        transform:translateX(-100%);
    }
    .sidebar.active { transform:translateX(0); }
    .menu-toggle { display:block; }
}
button.logout-btn {
    background:#007bff;
    color:white;
    border:none;
    padding:10px 20px;
    border-radius:6px;
    cursor:pointer;
    font-size:14px;
}
button.logout-btn:hover { background:#0056b3; }
</style>
</head>
<body>

<button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
    ☰ Menu
</button>

<div class="sidebar">
    <h2>💼 Staff Panel</h2>
    <a href="profile.php" class="active">My Profile</a>
    <a href="my_bookings.php" >My Bookings</a>
</div>

<div class="content">
    <h2>Welcome, <?= htmlspecialchars($user['username']) ?></h2>
    <p>This is your staff dashboard. Use the sidebar to navigate.</p>
</div>

</body>
</html>
