<?php
require_once __DIR__ . '/../customer/config.php';
session_start(); 

// Check if user is logged in and is a customer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header('Location: ../auth/login_customer.php');
    exit;
}

$user = $_SESSION['user']; // get user info
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Customer Dashboard - Salon Booking</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
h2, h4 { color: #6d4c41; }
.card { 
    border-radius: 12px; 
    box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
    padding: 20px;
}
.btn-primary { 
    background-color: #a47148; 
    border: none; 
    margin-right: 10px;
}
.btn-primary:hover { 
    background-color: #8b5e3c; 
}
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
      <h2>Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
      <div class="card mt-3 text-center">
        <h4>Your Dashboard</h4>
        <p>Manage your bookings, view your profile, and book your favorite salon services.</p>
        <a href="book.php" class="btn btn-primary">Book a Service</a>
        <a href="bookings.php" class="btn btn-primary">View My Bookings</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
