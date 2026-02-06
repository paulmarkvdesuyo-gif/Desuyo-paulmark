<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}

$pdo = db_connect();
$staffs = $pdo->query("SELECT * FROM users WHERE role='staff' ORDER BY created_at DESC")->fetchAll();
$staffCount = count($staffs);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Manage Staff - Admin Panel</title>
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
    .btn {
      display: inline-block;
      padding: 8px 12px;
      border-radius: 4px;
      text-decoration: none;
      color: white;
      font-size: 14px;
      margin-right: 5px;
    }
    .btn-success { background: #28a745; }
    .btn-success:hover { background: #218838; }
    .btn-primary { background: #007bff; }
    .btn-primary:hover { background: #0069d9; }
    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #c82333; }
    table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
    th { background: #ffe4f2; }
    tr:nth-child(even) { background: #f9f9f9; }
  </style>
</head>
<body>

<button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
  ☰ Menu
</button>

<div class="sidebar">
  <h2>💇 Admin Panel</h2>
    <a href="profile.php">Profile</a>
    <a href="index.php">Dashboard</a>
    <a href="services.php">Manage Services</a>
    <a href="bookings.php">Manage Bookings</a>
    <a href="staff_list.php" class="active">Manage Staff</a>
</div>

<div class="content">
  <h2>Manage Staff</h2>

  <?php if($staffCount < 3): ?>
    <a href="staff_add.php" class="btn btn-success mb-3">+ Add Staff</a>
  <?php else: ?>
    <p style="color:red; font-weight:bold;">Maximum of 3 staff allowed. Please delete an existing staff to add a new one.</p>
  <?php endif; ?>

  <table>
    <tr>
      <th>Username</th>
      <th>Email</th>
      <th>Phone</th>
      <th>Actions</th>
    </tr>
    <?php foreach($staffs as $s): ?>
    <tr>
      <td><?= htmlspecialchars($s['username']) ?></td>
      <td><?= htmlspecialchars($s['email']) ?></td>
      <td><?= htmlspecialchars($s['phone']) ?></td>
      <td>
        <a href="staff_edit.php?id=<?= $s['user_id'] ?>" class="btn btn-primary">Edit</a>
        <a href="staff_delete.php?id=<?= $s['user_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this staff?')">Delete</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

</body>
</html>
