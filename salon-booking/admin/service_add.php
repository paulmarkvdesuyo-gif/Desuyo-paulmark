<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}

$pdo = db_connect();
$error = '';

// Server-side validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $duration = (int) $_POST['duration'];
    $price = (float) $_POST['price'];

    if ($name && $duration > 0 && $duration <= 6 && $price > 0) {
        $stmt = $pdo->prepare("INSERT INTO services (name, description, duration, price) VALUES (?,?,?,?)");
        $stmt->execute([$name,$description,$duration,$price]);
        header("Location: services.php"); exit;
    } else {
        $error = "Please fill in all fields with valid values. Duration must be between 1 and 6 hours.";
    }
}

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Service - Admin Panel</title>
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
  display: inline-block;   /* Allows setting width */
  min-width: 120px;        /* Minimum width, adjust as needed */
  padding: 10px 15px;      /* Padding inside the button */
  text-align: center;      /* Center text */
  border-radius: 4px;
  text-decoration: none;
  color: white;
  font-size: 14px;
}

.btn-success { background: #28a745; }
.btn-success:hover { background: #218838; }

.btn-secondary { background: #6c757d; }
.btn-secondary:hover { background: #5a6268; }

form input, form textarea, form button {
  width: 100%;
  padding: 10px;
  margin: 8px 0;
  border-radius: 4px;
  border: 1px solid #ccc;
  box-sizing: border-box;
}
button { cursor: pointer; border: none; }
.button-row {
  display: flex;
  justify-content: flex-start;
  gap: 10px;
  margin-top: 10px;
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
  <h2>Add New Service</h2>
  <?php if($error): ?>
    <p class="text-danger"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="post">
      <input name="name" placeholder="Service Name" required>
      <textarea name="description" placeholder="Description (optional)"></textarea>
      <input name="duration" type="number" placeholder="Duration (hours)" min="1" max="6" required>
      <input name="price" type="number" step="0.01" placeholder="Price (₱)" min="1" required>

     <div class="button-row">
  <button class="btn btn-success">Add Service</button>
  <a href="services.php" class="btn btn-secondary">Cancel</a>
</div>

  </form>
</div>

</body>
</html>
