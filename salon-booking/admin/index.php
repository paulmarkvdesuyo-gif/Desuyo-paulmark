<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
   body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  display: flex;
  min-height: 100vh;
}
.sidebar {
  background: #b84d7a;
  color: white;
  width: 220px;
  padding: 20px;
  box-sizing: border-box;
  transition: transform 0.3s ease;
}
.sidebar h2 {
  font-size: 20px;
  margin-bottom: 20px;
}
.sidebar a {
  display: block;
  padding: 10px;
  margin: 8px 0;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  transition: background 0.3s;
}
.sidebar a:hover, .sidebar a.active {
  background: #9c3f65;
}
.content {
  flex: 1;
  padding: 20px;
  background: #f9f9f9;
}
.menu-toggle {
  display: none;
  position: absolute;
  top: 15px;
  left: 15px;
  background: #b84d7a;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 6px;
  cursor: pointer;
  z-index: 1000;
}
@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    transform: translateX(-100%);
  }
  .sidebar.active {
    transform: translateX(0);
  }
  .menu-toggle {
    display: block;
  }
}
  </style>
</head>
<body>
  <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
    ☰ Menu
  </button>

  <div class="sidebar">
    <h2>💇 Admin Panel</h2>
    <a href="profile.php">Profile</a>
    <a href="index.php"  class="active">Dashboard</a>
    <a href="services.php">Manage Services</a>
    <a href="bookings.php">Manage Bookings</a>
    <a href="staff_list.php">Manage Staff</a>
  </div>

  <div class="content">
    <h2>Welcome, Admin</h2>
    <p>Select an option from the sidebar.</p>
  </div>
</body>
</html>
