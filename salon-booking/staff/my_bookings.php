<?php
require_once __DIR__ . '/../admin/config.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'staff') {
    header('Location: ../auth/login_admin_staff.php');
    exit;
}

$pdo = db_connect();
$staff_id = $_SESSION['user']['user_id'];

// Correct query joining the customers table
$stmt = $pdo->prepare("
    SELECT b.*, s.name AS service_name, c.name AS customer_name
    FROM bookings b
    JOIN services s ON b.service_id = s.service_id
    JOIN customers c ON b.customer_id = c.customer_id
    WHERE b.staff_id = ?
    ORDER BY b.booking_date DESC, b.booking_time DESC
");
$stmt->execute([$staff_id]);
$bookings = $stmt->fetchAll();

$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Bookings</title>
<style>
body { margin:0; font-family:'Segoe UI',sans-serif; display:flex; min-height:100vh; }
.sidebar { background:#b84d7a; color:white; width:220px; padding:20px; box-sizing:border-box; transition:transform 0.3s ease; }
.sidebar h2 { font-size:20px; margin-bottom:20px; }
.sidebar a { display:block; padding:10px; margin:8px 0; color:white; text-decoration:none; border-radius:6px; transition:background 0.3s; }
.sidebar a:hover, .sidebar a.active { background:#9c3f65; }
.content { flex:1; padding:20px; background:#f9f9f9; }
.menu-toggle { display:none; position:absolute; top:15px; left:15px; background:#b84d7a; color:white; border:none; padding:10px; border-radius:6px; cursor:pointer; z-index:1000; }
@media(max-width:768px){ 
    .sidebar { position:fixed; top:0; left:0; height:100%; transform:translateX(-100%); } 
    .sidebar.active { transform:translateX(0); } 
    .menu-toggle { display:block; } 
}
table { width:100%; border-collapse: collapse; margin-top:20px; }
table, th, td { border:1px solid #ccc; }
th, td { padding:10px; text-align:left; }
th { background:#f0f0f0; }
button.logout-btn { background:#007bff; color:white; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; font-size:14px; margin-top:10px; width:100%; }
button.logout-btn:hover { background:#0056b3; }
</style>
</head>
<body>

<button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰ Menu</button>

<div class="sidebar">
    <h2>💼 Staff Panel</h2>
    <a href="profile.php">My Profile</a>
    <a href="my_bookings.php" class="active">My Bookings</a>   
</div>

<div class="content">
    <h2>My Assigned Bookings</h2>
    <?php if(empty($bookings)): ?>
        <p>No bookings assigned yet.</p>
    <?php else: ?>
    <table>
        <tr>
            <th>Customer</th>
            <th>Service</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
        <?php foreach($bookings as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['customer_name']) ?></td>
            <td><?= htmlspecialchars($b['service_name']) ?></td>
            <td><?= htmlspecialchars($b['booking_date']) ?></td>
            <td><?= htmlspecialchars($b['booking_time']) ?></td>
            <td><?= htmlspecialchars($b['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>

</body>
</html>
