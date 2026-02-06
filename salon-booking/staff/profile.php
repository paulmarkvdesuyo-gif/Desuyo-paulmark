<?php
require_once __DIR__ . '/../admin/config.php';
session_start();

// Ensure user is logged in
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
<title>My Profile</title>
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
    text-align:center;
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
.profile-img { width:150px; height:150px; border-radius:50%; object-fit:cover; margin-bottom:15px; background:#f0f0f0; display:inline-flex; align-items:center; justify-content:center; font-size:14px; color:#aaa; }
.info { margin-bottom:10px; font-size:16px; }
button { background:#28a745; color:#fff; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; font-size:14px; }
button:hover { background:#218838; }
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
    <h2>👤 My Profile</h2>
    <img src="<?= htmlspecialchars($user['profile_image'] ?? '../uploads/default.png') ?>" class="profile-img" alt="Profile Image">

    <div class="info"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></div>
    <div class="info"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></div>
    <div class="info"><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></div>

    <div style="margin-top:20px; display:flex; justify-content:center; gap:10px;">
        <form action="profile_edit.php" method="get">
            <button type="submit">Edit Profile</button>
        </form>
        <form action="../auth/logout_admin.php" method="post">
            <button type="submit" style="background:#007bff;">Logout</button>
        </form>
    </div>
</div>

</body>
</html>
