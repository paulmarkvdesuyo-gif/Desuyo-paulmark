<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}

$pdo = db_connect();
$id = (int)($_GET['id'] ?? 0);

// Fetch the service
$stmt = $pdo->prepare("SELECT * FROM services WHERE service_id=?");
$stmt->execute([$id]);
$service = $stmt->fetch();
if (!$service) die("Service not found!");

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $duration = (int)$_POST['duration'];
    $price = (float)$_POST['price'];

    // Validate duration <= 6 hours
    if ($name && $duration > 0 && $duration <= 6 && $price > 0) {
        $stmt = $pdo->prepare("UPDATE services SET name=?, description=?, duration=?, price=? WHERE service_id=?");
        $stmt->execute([$name, $description, $duration, $price, $id]);
        header("Location: services.php");
        exit;
    } else {
        $error = "Please fill in all fields correctly. Duration must be between 1 and 6 hours.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Service</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
}
.sidebar h2 { font-size: 20px; margin-bottom: 20px; }
.sidebar a { display: block; padding: 10px; margin: 8px 0; color: white; text-decoration: none; border-radius: 6px; transition: background 0.3s; }
.sidebar a:hover, .sidebar a.active { background: #9c3f65; }
.content { flex: 1; padding: 20px; background: #f9f9f9; }
.menu-toggle { display: none; position: absolute; top: 15px; left: 15px; background: #b84d7a; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; z-index: 1000; }
@media (max-width: 768px) {
    .sidebar { position: fixed; top: 0; left: 0; height: 100%; transform: translateX(-100%); }
    .sidebar.active { transform: translateX(0); }
    .menu-toggle { display: block; }
}
.form-buttons { display: flex; gap: 10px; margin-top: 10px; }
.btn { min-width: 120px; text-align: center; }
</style>
</head>
<body>
<button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰ Menu</button>

<div class="sidebar">
    <h2>💇 Admin Panel</h2>
    <a href="profile.php">Profile</a>
    <a href="index.php">Dashboard</a>
    <a href="services.php" class="active">Manage Services</a>
    <a href="bookings.php">Manage Bookings</a>
    <a href="staff_list.php">Manage Staff</a>
</div>

<div class="content">
    <h2>Edit Service</h2>

    <?php if($error) echo "<p class='text-danger'>$error</p>"; ?>

    <form method="post">
        <input name="name" value="<?= htmlspecialchars($service['name']) ?>" class="form-control mb-2" placeholder="Service Name" required>
        <textarea name="description" class="form-control mb-2" placeholder="Description"><?= htmlspecialchars($service['description']) ?></textarea>
        <input name="duration" type="number" value="<?= $service['duration'] ?>" class="form-control mb-2" min="1" max="6" placeholder="Duration (hours)" required>
        <input name="price" type="number" step="0.01" value="<?= $service['price'] ?>" class="form-control mb-2" min="1" placeholder="Price (₱)" required>

        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Update Service</button>
            <a href="services.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
