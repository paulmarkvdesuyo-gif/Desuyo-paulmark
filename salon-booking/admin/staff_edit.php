<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}

$pdo = db_connect();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id=? AND role='staff'");
$stmt->execute([$id]);
$staff = $stmt->fetch();

if (!$staff) {
    die("Staff not found!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : $staff['password'];

    $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, phone=?, password=? WHERE user_id=?");
    $stmt->execute([$username, $email, $phone, $password, $id]);

    header("Location: staff_list.php");
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Staff</title>
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
    form {
      max-width: 500px;
      background: #fff;
      padding: 20px;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    input, button {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border-radius: 4px;
      border: 1px solid #ccc;
      font-size: 14px;
      box-sizing: border-box;
    }
    button {
      background: #b84d7a;
      color: #fff;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover { background: #9c3f65; }
    .btn-secondary {
      background: #6c757d;
      margin-top: 5px;
    }
    .btn-secondary:hover { background: #5a6268; }
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
  <h2>Edit Staff</h2>
<form method="post" id="editStaffForm">
  <input name="username" value="<?= htmlspecialchars($staff['username']) ?>" placeholder="Username" required class="form-control mb-2">
  <input name="email" type="email" value="<?= htmlspecialchars($staff['email']) ?>" placeholder="Email" required class="form-control mb-2">
  <input name="phone" value="<?= htmlspecialchars($staff['phone']) ?>" placeholder="Phone" class="form-control mb-2" maxlength="11">
  <input name="password" type="password" placeholder="Leave blank to keep old password" class="form-control mb-3">

  <div style="display: flex; gap: 10px;">
    <button type="submit" class="btn btn-primary flex-fill">Update</button>
    <button type="button" class="btn btn-secondary flex-fill" onclick="window.location.href='staff_list.php'">Cancel</button>
  </div>
</form>

</div>
<script>
document.getElementById('editStaffForm').addEventListener('submit', function(e) {
    let phone = this.phone.value.trim();
    let password = this.password.value;

    // Phone validation: numbers only, max 11 digits
    if (phone && !/^\d{11}$/.test(phone)) {
        alert('Phone number must be numeric and maximum 11 digits.');
        e.preventDefault();
        return false;
    }

    // Password validation: only if user entered a new password
    if (password && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(password)) {
        alert('Password must be at least 8 characters and include uppercase, lowercase, number, and a symbol.');
        e.preventDefault();
        return false;
    }
});
</script>
</body>
</html>
