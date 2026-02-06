<?php
require_once __DIR__ . '/../customer/config.php';
session_start(); 

// Check if user is logged in and is a customer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header('Location: ../auth/login_customer.php');
    exit;
}

$user = $_SESSION['user']; // current user info
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Customer Profile - Salon Booking</title>
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
}
.content { padding: 20px; }
.card { 
    border-radius: 12px; 
    box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
    text-align: center; /* Center everything inside */
}
h2, h4 { color: #6d4c41; }
.btn-primary { background-color: #a47148; border: none; }
.btn-primary:hover { background-color: #8b5e3c; }
img.profile-img { 
    width: 150px; 
    height: 150px; 
    object-fit: cover; 
    border-radius: 50%; 
    margin: 0 auto 15px; 
    display: block; /* Ensures centering */
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
      <a href="../auth/logout_customer.php">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 content">
      <h2>My Profile</h2>
      <div class="card p-4 mt-3">
        <?php if (!empty($user['profile_image'])): ?>
          <img src="../uploads/<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="profile-img">
        <?php else: ?>
          <img src="https://via.placeholder.com/150" alt="Profile Image" class="profile-img">
        <?php endif; ?>
        <h4><?= htmlspecialchars($user['name']) ?></h4>
        <a href="profile_edit.php" class="btn btn-primary mt-3">Update Profile</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
