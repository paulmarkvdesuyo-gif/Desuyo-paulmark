<?php
require_once __DIR__ . '/../admin/config.php';
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $pdo = db_connect();

    // Check in unified users table
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role IN ('admin','staff')");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']); // Don’t keep password in session
        $_SESSION['user'] = $user;

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header('Location: ../admin/index.php');
        } else {
            header('Location: ../staff/index.php');
        }
        exit;
    } else {
        $errors[] = 'Invalid admin/staff credentials';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Salon Login - Admin & Staff</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 350px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-align: center;
            animation: fadeIn 0.8s ease;
        }
        .login-card h2 {
            color: #b84d7a;
            margin-bottom: 20px;
        }
        .login-card input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .login-card button {
            width: 100%;
            padding: 12px;
            background: #b84d7a;
            border: none;
            color: white;
            font-size: 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-card button:hover {
            background: #9c3f65;
        }
        .error {
            color: red;
            margin: 10px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 15px;
            font-size: 13px;
            color: #777;
        }
        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>💇 Salon Admin & Staff Login</h2>

        <?php foreach($errors as $e): ?>
            <div class="error"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <form method="post">
            <input name="username" type="text" placeholder="Username" required>
            <input name="password" type="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <div class="footer">Salon Management System</div>
    </div>
</body>
</html>
