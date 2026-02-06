<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}

$pdo = db_connect();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password, role) VALUES (?, ?, ?, ?, 'staff')");
        $stmt->execute([$username, $email, $phone, $password]);
        header("Location: staff_list.php");
        exit;
    } catch (Exception $e) {
        $errors[] = "Error: " . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Staff - Admin Panel</title>
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
    .form-card {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      max-width: 500px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .form-card input, .form-card button {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 4px;
      border: 1px solid #ccc;
    }
    .form-card button {
      background: #b84d7a;
      color: white;
      border: none;
      cursor: pointer;
    }
    .form-card button:hover {
      background: #9c3f65;
    }
    .alert {
      color: red;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">
    ☰ Menu
  </button>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="profile.php">Profile</a>
    <a href="index.php">Dashboard</a>
    <a href="services.php">Manage Services</a>
    <a href="bookings.php">Manage Bookings</a>
    <a href="staff_list.php" class="active">Manage Staff</a>
  </div>

  <!-- Main Content -->
  <div class="content">
    <h2>Add Staff</h2>

    <?php foreach($errors as $e): ?>
      <div class="alert"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
<form method="post" class="form-card" id="addStaffForm">
  <input name="username" placeholder="Username" required>
  <input name="email" type="email" placeholder="Email" required>
  <input name="phone" placeholder="Phone" maxlength="11" required>
  <input name="password" type="password" placeholder="Password" required>
  <div style="display: flex; gap: 10px;">
    <button type="submit">Save</button>
    <button type="button" class="btn" style="background-color: #6c757d; color: white; border: none; padding: 10px 15px; border-radius: 6px;" onclick="window.location.href='staff_list.php'">
      Cancel
    </button>
  </div>
</form>

  </div>
  <script>
document.getElementById('addStaffForm').addEventListener('submit', function(e) {
    let phone = this.phone.value.trim();
    let password = this.password.value;

    // Phone validation: numbers only, max 11 digits
    let phonePattern = /^\d{1,11}$/;
    if (!phonePattern.test(phone)) {
        alert('Phone number must be numeric and maximum 11 digits.');
        e.preventDefault();
        return false;
    }

    // Password validation: at least 8 chars, uppercase, lowercase, number, symbol
    let passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!passwordPattern.test(password)) {
        alert('Password must be at least 8 characters and include uppercase, lowercase, number, and a symbol.');
        e.preventDefault();
        return false;
    }
});
</script>
</body>
</html>
