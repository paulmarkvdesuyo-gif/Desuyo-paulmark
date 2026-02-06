<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}
$pdo = db_connect();

// Update booking status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int) $_POST['booking_id'];
    $status = $_POST['status'];
    $staff_id = !empty($_POST['staff_id']) ? (int) $_POST['staff_id'] : null;
    $stmt = $pdo->prepare("UPDATE bookings SET status=?, staff_id=? WHERE booking_id=?");
    $stmt->execute([$status, $staff_id, $id]);
    header("Location: bookings.php"); exit;
}

// Fetch bookings
$stmt = $pdo->query("
    SELECT b.*, c.name AS customer_name, s.name AS service_name, st.username AS staff_name
    FROM bookings b
    JOIN customers c ON b.customer_id = c.customer_id
    JOIN services s ON b.service_id = s.service_id
    LEFT JOIN users st ON b.staff_id = st.user_id
    ORDER BY b.booking_date DESC, b.booking_time DESC
");
$bookings = $stmt->fetchAll();

// Fetch staff list
$staffs = $pdo->query("SELECT user_id, username FROM users WHERE role='staff'")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Bookings - Admin Panel</title>
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
.sidebar h2 { font-size: 20px; margin-bottom: 20px; }
.sidebar a {
  display: block;
  padding: 10px;
  margin: 8px 0;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  transition: background 0.3s;
}
.sidebar a:hover, .sidebar a.active { background: #9c3f65; }

.content { flex: 1; padding: 20px; background: #f9f9f9; }

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
  .sidebar { position: fixed; top:0; left:0; height:100%; transform: translateX(-100%); }
  .sidebar.active { transform: translateX(0); }
  .menu-toggle { display: block; }
}

.btn { display: inline-block; padding: 8px 12px; border-radius: 4px; text-decoration: none; color: white; font-size: 14px; margin-right: 5px; }
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
.btn-row { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; }
.form-select-sm { margin-bottom: 5px; }
</style>
</head>
<body>

<button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰ Menu</button>

<div class="sidebar">
  <h2>💇 Admin Panel</h2>
    <a href="profile.php">Profile</a>
    <a href="index.php">Dashboard</a>
    <a href="services.php">Manage Services</a>
    <a href="bookings.php" class="active">Manage Bookings</a>
    <a href="staff_list.php">Manage Staff</a>
  
</div>

<div class="content">
  <h2>💇 Manage Bookings</h2>
  <table>
    <tr>
      <th>Customer</th>
      <th>Service</th>
      <th>Date</th>
      <th>Time</th>
      <th>Status</th>
      <th>Staff</th>
      <th class="text-center">Action</th>
    </tr>
    <?php foreach($bookings as $b): ?>
    <tr>
      <td><?=htmlspecialchars($b['customer_name'])?></td>
      <td><?=htmlspecialchars($b['service_name'])?></td>
      <td><?=$b['booking_date']?></td>
      <td><?=$b['booking_time']?></td>
      <td><?=$b['status']?></td>
      <td><?=htmlspecialchars($b['staff_name'] ?? 'Unassigned')?></td>
      <td class="text-center">
        <form method="post" class="btn-row">
          <input type="hidden" name="booking_id" value="<?=$b['booking_id']?>">
          <select name="status" class="form-select form-select-sm">
            <option <?=($b['status']=='pending'?'selected':'')?> value="pending">Pending</option>
            <option <?=($b['status']=='confirmed'?'selected':'')?> value="confirmed">Confirmed</option>
            <option <?=($b['status']=='completed'?'selected':'')?> value="completed">Completed</option>
            <option <?=($b['status']=='cancelled'?'selected':'')?> value="cancelled">Cancelled</option>
          </select>
          <select name="staff_id" class="form-select form-select-sm">
            <option value="">--Assign Staff--</option>
            <?php foreach($staffs as $st): ?>
              <option value="<?=$st['user_id']?>" <?=($b['staff_id']==$st['user_id']?'selected':'')?>>
                <?=htmlspecialchars($st['username'])?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="update_status" class="btn btn-primary">Update</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

</body>
</html>
