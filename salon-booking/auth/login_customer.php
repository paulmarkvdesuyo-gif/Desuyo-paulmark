<?php
require_once __DIR__ . '/../customer/config.php';
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = db_connect();
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ? AND role = 'customer'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']); // remove password from session
        $_SESSION['user'] = $user;
        header("Location: ../customer/index.php");
        exit;
    } else {
        $errors[] = "Invalid customer credentials!";
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Customer Login - Salon Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { background: #f8f5f0; font-family: 'Poppins', sans-serif; }
    .navbar { background-color: #d4a373 !important; }
    h2 { color: #6d4c41; }
    .btn-primary { background-color: #a47148; border: none; }
    .btn-primary:hover { background-color: #8b5e3c; }
    .card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">💇 Glamour Salon</a>
  </div>
</nav>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card p-4">
        <h2 class="text-center mb-4">Customer Login</h2>
        <?php foreach($errors as $e): ?>
          <div class="alert alert-danger"><?=htmlspecialchars($e)?></div>
        <?php endforeach; ?>
        <form method="post">
          <div class="mb-3">
            <label>Email</label>
            <input name="email" type="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input name="password" type="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3">
          New here? <a href="register_customer.php">Register as Customer</a>
        </p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
