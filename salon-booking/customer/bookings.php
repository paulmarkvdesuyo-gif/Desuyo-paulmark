<?php
require_once __DIR__ . '/../customer/config.php';
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header('Location: ../auth/login_customer.php');
    exit;
}

$user = $_SESSION['user'];
$pdo = db_connect();

// Fetch customer bookings with service and staff info
$stmt = $pdo->prepare("
    SELECT b.booking_id, b.booking_date, b.booking_time, b.status,
           s.name AS service_name, s.duration, s.price,
           u.username AS staff_name
    FROM bookings b
    JOIN services s ON b.service_id = s.service_id
    LEFT JOIN users u ON b.staff_id = u.user_id
    WHERE b.customer_id = ?
    ORDER BY b.booking_date DESC, b.booking_time DESC
");
$stmt->execute([$user['customer_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Bookings - Salon Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f8f5f0; }
    .sidebar {
        background-color: #d4a373;
        min-height: 100vh;
        padding-top: 20px;
    }
    .sidebar a {
        display: block;
        color: #fff;
        padding: 12px 20px;
        text-decoration: none;
        font-weight: 500;
    }
    .sidebar a:hover {
        background-color: #a47148;
        color: #fff;
        text-decoration: none;
    }
    .content { padding: 20px; }
    h2 { color: #6d4c41; }
    .table thead { background-color: #a47148; color: #fff; }
    .status-pending { color: orange; font-weight: bold; }
    .status-confirmed { color: blue; font-weight: bold; }
    .status-completed { color: green; font-weight: bold; }
    .status-cancelled { color: red; font-weight: bold; }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 sidebar">
      <h4 class="text-center text-white mb-4">💇 Trix Salon</h4>
      <a href="profile.php">Profile</a>
      <a href="index.php">Home</a>
      <a href="book.php">Book Service</a>
      <a href="bookings.php">My Bookings</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 content">
      <h2>My Bookings</h2>

      <?php if (!$bookings): ?>
        <div class="alert alert-info mt-3">You have no bookings yet.</div>
      <?php else: ?>
        <div class="card mt-3 p-3">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Service</th>
                <th>Staff</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration (hours)</th>
                <th>Price (₱)</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($bookings as $b): ?>
              <tr>
                <td><?= htmlspecialchars($b['service_name']) ?></td>
                <td><?= htmlspecialchars($b['staff_name'] ?? 'TBD') ?></td>
                <td><?= htmlspecialchars($b['booking_date']) ?></td>
                <td><?= htmlspecialchars($b['booking_time']) ?></td>
                <td><?= htmlspecialchars($b['duration']) ?></td>
                <td><?= number_format($b['price'], 2) ?></td>
                <td class="status-<?= strtolower($b['status']) ?>"><?= ucfirst($b['status']) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

    </div>
  </div>
</div>
</body>
</html>
