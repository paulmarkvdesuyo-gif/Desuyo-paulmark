<?php
require_once __DIR__ . '/../customer/config.php';
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header('Location: ../auth/login_customer.php');
    exit;
}

$user = $_SESSION['user']; // current user info
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // --- Validation ---
    if (strlen($name) < 3 || !preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors[] = "Full name must be at least 3 letters and contain only letters and spaces.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (!empty($phone) && !preg_match("/^[0-9]{10,13}$/", $phone)) {
        $errors[] = "Phone must contain only digits (10–13 characters).";
    }

    // Handle profile image upload
    $imageFileName = $user['profile_image'] ?? null;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_image']['tmp_name'];
        $fileName = $_FILES['profile_image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (!in_array($fileExt, $allowed)) {
            $errors[] = "Only JPG, JPEG, PNG, GIF files are allowed.";
        } else {
            $newFileName = uniqid('profile_', true) . '.' . $fileExt;
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imageFileName = $newFileName;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // Update database if no errors
    if (empty($errors)) {
        $pdo = db_connect();
        $stmt = $pdo->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, profile_image = ? WHERE customer_id = ?");
        $stmt->execute([$name, $email, $phone, $imageFileName, $user['customer_id']]);

        // Update session info
        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['phone'] = $phone;
        $_SESSION['user']['profile_image'] = $imageFileName;

        // Redirect back to profile page
        header('Location: profile.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Profile - Salon Booking</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f8f5f0; }
.sidebar { background-color: #d4a373; min-height: 100vh; padding-top: 20px; }
.sidebar a { display: block; color: #fff; padding: 12px 20px; text-decoration: none; font-weight: 500; }
.sidebar a:hover { background-color: #a47148; color: #fff; }
.content { padding: 20px; }
.card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
h2, h4 { color: #6d4c41; }
.btn-primary { background-color: #a47148; border: none; }
.btn-primary:hover { background-color: #8b5e3c; }
.profile-img { width: 150px; height: 150px; object-fit: cover; border-radius: 50%; }
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
      <h2>Edit Profile</h2>
      <div class="card p-4 mt-3">
        <?php foreach($errors as $e): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>

        <form method="post" enctype="multipart/form-data">
          <div class="mb-3 text-center">
            <img id="profilePreview" 
                 src="<?= !empty($user['profile_image']) ? '../uploads/' . htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/150' ?>" 
                 alt="Profile Image" class="profile-img mb-3">
            <input type="file" name="profile_image" class="form-control mt-2" accept="image/*" onchange="previewImage(event)">
          </div>
          <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
          </div>
          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
          </div>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function previewImage(event) {
  const input = event.target;
  const preview = document.getElementById('profilePreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      preview.src = e.target.result;
    }
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
</body>
</html>
