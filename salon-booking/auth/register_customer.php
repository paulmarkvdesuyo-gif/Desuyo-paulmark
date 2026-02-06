<?php 
require_once __DIR__ . '/../customer/config.php';
session_start();
$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';

    // --- SERVER-SIDE VALIDATION ---
    if (strlen($name) < 3 || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors[] = "Full name must be at least 3 characters and contain only letters and spaces.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!empty($phone) && !preg_match("/^[0-9]{10,13}$/", $phone)) {
        $errors[] = "Phone must contain only digits (10–13 characters).";
    }

    if (strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password) ||
        !preg_match("/[\W]/", $password)) {
        $errors[] = "Password must be at least 8 characters with uppercase, lowercase, number, and special character.";
    }

    if ($password !== $cpassword) {
        $errors[] = "Passwords do not match!";
    }

    if (empty($errors)) {
        $pdo = db_connect();

        // Check if email already exists
        $check = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = "Email already registered!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO customers (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hash, $phone]);

            $success = "Registration successful! You can now log in.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Customer Registration - Salon Booking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { background: #f8f5f0; font-family: 'Poppins', sans-serif; }
    .navbar { background-color: #d4a373 !important; }
    h2 { color: #6d4c41; }
    .btn-primary { background-color: #a47148; border: none; }
    .btn-primary:hover { background-color: #8b5e3c; }
    .card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .error-msg { color: red; font-size: 0.9em; }
    .success-msg { color: green; font-size: 0.9em; }
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
    <div class="col-md-6">
      <div class="card p-4">
        <h2 class="text-center mb-4">Customer Registration</h2>

        <?php foreach($errors as $e): ?>
          <div class="alert alert-danger"><?=htmlspecialchars($e)?></div>
        <?php endforeach; ?>

        <?php if ($success): ?>
          <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
          <div class="text-center">
            <a href="login_customer.php" class="btn btn-primary mt-2">Go to Login</a>
          </div>
        <?php else: ?>
        <form method="post" id="registerForm" novalidate>
          <div class="mb-3">
            <label>Full Name</label>
            <input name="name" id="name" type="text" class="form-control" value="<?=htmlspecialchars($_POST['name'] ?? '')?>" required>
            <div id="nameError" class="error-msg"></div>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input name="email" id="email" type="email" class="form-control" value="<?=htmlspecialchars($_POST['email'] ?? '')?>" required>
            <div id="emailError" class="error-msg"></div>
          </div>
          <div class="mb-3">
            <label>Phone Number</label>
            <input name="phone" id="phone" type="text" class="form-control" value="<?=htmlspecialchars($_POST['phone'] ?? '')?>">
            <div id="phoneError" class="error-msg"></div>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input name="password" id="password" type="password" class="form-control" required>
            <div id="passwordError" class="error-msg"></div>
          </div>
          <div class="mb-3">
            <label>Confirm Password</label>
            <input name="cpassword" id="cpassword" type="password" class="form-control" required>
            <div id="cpasswordError" class="error-msg"></div>
          </div>
          <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const name = document.getElementById("name");
  const email = document.getElementById("email");
  const phone = document.getElementById("phone");
  const password = document.getElementById("password");
  const cpassword = document.getElementById("cpassword");

  name.addEventListener("input", () => {
    document.getElementById("nameError").textContent = 
      name.value.length < 3 || !/^[a-zA-Z\s]+$/.test(name.value) 
      ? "Full name must be at least 3 letters and contain only alphabets." 
      : "";
  });

  email.addEventListener("input", () => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    document.getElementById("emailError").textContent = re.test(email.value) ? "" : "Invalid email format.";
  });

  phone.addEventListener("input", () => {
    document.getElementById("phoneError").textContent = /^[0-9]{10,13}$/.test(phone.value) || phone.value === "" 
      ? "" : "Phone must be 10–13 digits";
  });

  password.addEventListener("input", () => {
    document.getElementById("passwordError").textContent = 
      (password.value.length < 8 || !/[A-Z]/.test(password.value) || !/[a-z]/.test(password.value) || !/[0-9]/.test(password.value) || !/[\W]/.test(password.value))
      ? "Password must have 8+ chars, uppercase, lowercase, number & special char." 
      : "";
  });

  cpassword.addEventListener("input", () => {
    document.getElementById("cpasswordError").textContent = cpassword.value === password.value ? "" : "Passwords do not match.";
  });
});
</script>
</body>
</html>
