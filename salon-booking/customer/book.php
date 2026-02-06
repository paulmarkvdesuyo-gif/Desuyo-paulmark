<?php
require_once __DIR__ . '/../customer/config.php';
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'customer') {
    header('Location: ../auth/login_customer.php');
    exit;
}

$user = $_SESSION['user'];
$pdo = db_connect();
$errors = [];
$success = "";

// Fetch services
$services = $pdo->query("SELECT * FROM services ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = (int)$_POST['service_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];

    if (!$service_id || !$booking_date || !$booking_time) {
        $errors[] = "All fields are required!";
    } else {
        // Prevent past date
        if (strtotime($booking_date) < strtotime(date('Y-m-d'))) {
            $errors[] = "You cannot book for a past date.";
        }

        // Get service duration
        $stmt = $pdo->prepare("SELECT duration FROM services WHERE service_id = ?");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch();
        $duration = $service ? $service['duration'] : 1;

        // Check overlapping bookings
        $stmt = $pdo->prepare("SELECT b.booking_time, s.duration
                               FROM bookings b
                               JOIN services s ON b.service_id = s.service_id
                               WHERE b.booking_date = ?");
        $stmt->execute([$booking_date]);
        $existingBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $overlap = false;
        $bookingStart = strtotime("$booking_date $booking_time");
        $bookingEnd = strtotime("+$duration hours", $bookingStart);

        foreach ($existingBookings as $b) {
            $bStart = strtotime("$booking_date " . $b['booking_time']);
            $bEnd = strtotime("+" . $b['duration'] . " hours", $bStart);

            if ($bookingStart < $bEnd && $bookingEnd > $bStart) {
                $overlap = true;
                break;
            }
        }

        if ($overlap) {
            $errors[] = "Selected time overlaps with an existing booking. Choose another time.";
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO bookings (customer_id, service_id, booking_date, booking_time) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user['customer_id'], $service_id, $booking_date, $booking_time])) {
            $success = "Booking successfully created!";
        } else {
            $errors[] = "Failed to create booking. Try again.";
        }
    }
}

// Generate unavailable time slots
$unavailableTimes = [];
if (!empty($_POST['booking_date'])) {
    $date = $_POST['booking_date'];
    $stmt = $pdo->prepare("SELECT b.booking_time, s.duration
                           FROM bookings b
                           JOIN services s ON b.service_id = s.service_id
                           WHERE b.booking_date = ?");
    $stmt->execute([$date]);
    $existingBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($existingBookings as $b) {
        $start = strtotime($b['booking_time']);
        $end = strtotime("+" . $b['duration'] . " hours", $start);
        while ($start < $end) {
            $unavailableTimes[] = date('H:i', $start);
            $start = strtotime('+30 minutes', $start); // 30-min intervals
        }
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Book Service - Salon Booking</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background: #f8f5f0; }
.sidebar { background-color: #d4a373; min-height: 100vh; padding-top: 20px; }
.sidebar a { display: block; color: #fff; padding: 12px 20px; text-decoration: none; font-weight: 500; }
.sidebar a:hover { background-color: #a47148; color: #fff; text-decoration: none; }
.content { padding: 20px; }
h2, h4 { color: #6d4c41; }
.card { border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.btn-primary { background-color: #a47148; border: none; }
.btn-primary:hover { background-color: #8b5e3c; }
option.unavailable { background-color: #f8d7da; color: #721c24; }
</style>
</head>
<body>
<div class="container-fluid">
<div class="row">
<div class="col-md-3 sidebar">
<h4 class="text-center text-white mb-4">💇 Trix Salon</h4>
<a href="profile.php">Profile</a>
<a href="index.php">Home</a>
<a href="book.php">Book Service</a>
<a href="bookings.php">My Bookings</a>
</div>

<div class="col-md-9 content">
<h2>Book a Service</h2>

<?php foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
<?php endforeach; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card p-4 mt-3">
<form method="post">
<div class="mb-3">
<label for="service_id" class="form-label">Select Service</label>
<select name="service_id" id="service_id" class="form-control" required>
<option value="">-- Choose a service --</option>
<?php foreach ($services as $service): ?>
<option value="<?= $service['service_id'] ?>"><?= htmlspecialchars($service['name']) ?> (<?= htmlspecialchars($service['duration']) ?>h, ₱<?= number_format($service['price'],2) ?>)</option>
<?php endforeach; ?>
</select>
</div>

<div class="mb-3">
<label for="booking_time" class="form-label">Booking Time</label>
<select name="booking_time" id="booking_time" class="form-control" required>
<option value="">-- Select Time --</option>
<?php
// Example: working hours 09:00 - 18:00, interval 30 min
$start = strtotime('09:00');
$end = strtotime('18:00');
while ($start < $end):
$timeStr = date('H:i', $start);
$disabled = in_array($timeStr, $unavailableTimes) ? 'class="unavailable" disabled' : '';
echo "<option value=\"$timeStr\" $disabled>$timeStr</option>";
$start = strtotime('+30 minutes', $start);
endwhile;
?>
</select>
</div>

<div class="mb-3">
<label for="booking_date" class="form-label">Booking Date</label>
<input type="date" name="booking_date" id="booking_date" class="form-control" required min="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
</div>


<button type="submit" class="btn btn-primary">Book Now</button>
</form>
</div>
</div>
</div>
</div>
</body>
</html>
