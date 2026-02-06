<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}

$pdo = db_connect();
$services = $pdo->query("SELECT * FROM services ORDER BY name ASC")->fetchAll();

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Services - Admin Panel</title>
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

table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  margin-top: 15px;
}
th, td { padding: 10px; border: 1px solid #ddd; }
th { background: #ffe4f2; }
tr:nth-child(even) { background: #f9f9f9; }
.text-center { text-align: center; }
.btn-row { display: flex; justify-content: center; gap: 8px; }
.disabled-btn {
    background: #6c757d !important;
    cursor: not-allowed !important;
    pointer-events: none !important; /* makes it unclickable */
    color: #fff !important;
    text-decoration: none !important;
}
.disabled-btn:hover {
    background: #6c757d !important; /* prevents hover color change */
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
    <a href="index.php">Dashboard</a>
    <a href="services.php" class="active">Manage Services</a>
    <a href="bookings.php">Manage Bookings</a>
    <a href="staff_list.php">Manage Staff</a>
</div>

<div class="content">
  <h2>💇 Manage Services</h2>

<?php 
$service_count = count($services);
$add_disabled = $service_count >= 10;
?>
<a href="<?= $add_disabled ? '#' : 'service_add.php' ?>" 
   class="btn btn-success mb-3 <?= $add_disabled ? 'disabled-btn' : '' ?>">
   + Add Service
</a>
  <table>
    <tr>
      <th>Service Name</th>
      <th>Description</th>
      <th>Duration (hours)</th>
      <th>Price (₱)</th>
      <th class="text-center">Actions</th>
    </tr>
    <?php foreach($services as $s): ?>
    <tr>
      <td><?= htmlspecialchars($s['name']) ?></td>
      <td><?= htmlspecialchars($s['description']) ?></td>
      <td><?= $s['duration'] ?></td>
      <td><?= number_format($s['price'],2) ?></td>
      <td class="text-center">
        <div class="btn-row">
          <a href="service_edit.php?id=<?= $s['service_id'] ?>" class="btn btn-primary">Edit</a>
          <a href="service_delete.php?id=<?= $s['service_id'] ?>" class="btn btn-danger"
             onclick="return confirm('Delete this service?')">Delete</a>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>


</body>
</html>
