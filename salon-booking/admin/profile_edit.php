<?php
require_once __DIR__ . '/../admin/config.php';
session_start();

if (!is_admin()) { 
    header('Location: ../auth/login_admin_staff.php'); 
    exit; 
}

$user = $_SESSION['user'];
$pdo = db_connect();
$errors = [];
$success = '';

$password_raw = $_POST['password'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";

    // Phone validation
    if (!preg_match('/^\d{0,11}$/', $phone)) $errors[] = "Phone must be numeric and maximum 11 digits.";

    // Password validation
    if ($password_raw) {
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password_raw)) {
            $errors[] = "Password must be at least 8 characters, include uppercase, lowercase, number, and symbol.";
        } else {
            $password = password_hash($password_raw, PASSWORD_BCRYPT);
        }
    } else {
        
    }

    // Profile image
    if (!empty($_FILES['profile_image']['name'])) {
        $filename = uniqid() . "_admin"; // no extension
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $targetPath = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            $profile_image = '../uploads/' . $filename;
        } else {
            $errors[] = "Failed to upload image.";
            $profile_image = $user['profile_image'] ?? '../uploads/default.png';
        }
    } else {
        $profile_image = $user['profile_image'] ?? '../uploads/default.png';
    }

    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE users SET username=?, email=?, phone=?, password=?, profile_image=? WHERE user_id=?");
        if ($stmt->execute([$username, $email, $phone, $password, $profile_image, $user['user_id']])) {
            $user['username'] = $username;
            $user['email'] = $email;
            $user['phone'] = $phone;
            $user['profile_image'] = $profile_image;
            $_SESSION['user'] = $user;

            header("Location: profile.php");
            exit;
        } else {
            $errors[] = "Failed to update profile.";
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Profile</title>
<style>
body { font-family:'Segoe UI',sans-serif; margin:0; display:flex; min-height:100vh; }
.sidebar { background:#b84d7a; color:white; width:220px; padding:20px; box-sizing:border-box; transition:transform 0.3s ease; }
.sidebar h2 { font-size:20px; margin-bottom:20px; }
.sidebar a { display:block; padding:10px; margin:8px 0; color:white; text-decoration:none; border-radius:6px; transition:background 0.3s; }
.sidebar a:hover, .sidebar a.active { background:#9c3f65; }
.content { flex:1; padding:20px; background:#f9f9f9; max-width:500px; margin:0 auto; }
.menu-toggle { display:none; position:absolute; top:15px; left:15px; background:#b84d7a; color:white; border:none; padding:10px; border-radius:6px; cursor:pointer; z-index:1000; }
@media(max-width:768px){ .sidebar { position:fixed; top:0; left:0; height:100%; transform:translateX(-100%); } .sidebar.active { transform:translateX(0); } .menu-toggle { display:block; } }
input, button { width:100%; padding:10px; margin:8px 0; border-radius:6px; border:1px solid #ccc; font-size:14px; box-sizing:border-box; }
button { background:#28a745; color:#fff; border:none; cursor:pointer; }
button:hover { background:#218838; }
button.cancel { background:#6c757d; }
button.cancel:hover { background:#5a6268; }
.error { color:red; margin-bottom:10px; }
.success { color:green; margin-bottom:10px; }
.profile-img { width:150px; height:150px; border-radius:50%; object-fit:cover; margin-bottom:15px; cursor:pointer; }
.flex-buttons { display:flex; gap:10px; }
.flex-buttons button { flex:1; }
</style>
</head>
<body>

<button class="menu-toggle" onclick="document.querySelector('.sidebar').classList.toggle('active')">☰ Menu</button>

<div class="sidebar">
    <h2>💇 Admin Panel</h2>
    <a href="profile.php" class="active">Profile</a>
    <a href="index.php">Dashboard</a>
    <a href="services.php">Manage Services</a>
    <a href="bookings.php">Manage Bookings</a>
    <a href="staff_list.php">Manage Staff</a>
    <a href="../auth/logout_admin.php">Logout</a>
</div>

<div class="content">
    <h2>Edit Profile</h2>

    <?php foreach($errors as $e) echo "<div class='error'>".htmlspecialchars($e)."</div>"; ?>
    <?php if($success) echo "<div class='success'>".htmlspecialchars($success)."</div>"; ?>

    <form method="post" enctype="multipart/form-data">
        <img id="profilePreview" src="<?= htmlspecialchars($user['profile_image'] ?? '../uploads/default.png') ?>" class="profile-img" alt="Profile Image">
        <input type="file" name="profile_image" accept="image/*" onchange="previewImage(event)">
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" placeholder="Username" required>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" required>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="Phone" maxlength="11">
        <input type="password" name="password" placeholder="Leave blank to keep old password">
        <div class="flex-buttons">
            <button type="submit">Update Profile</button>
            <button type="button" class="cancel" onclick="window.location.href='profile.php'">Cancel</button>
        </div>
    </form>
</div>

<script>
// Preview uploaded image immediately
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('profilePreview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
