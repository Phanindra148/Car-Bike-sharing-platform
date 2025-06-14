<?php
session_start();
include 'db.php';

// 1. Ensure a logged-in user
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    echo "Unauthorized access.";
    exit();
}

// 2. Validate POST inputs
if (!isset($_POST['vehicleId'], $_POST['amount'])) {
    echo "Invalid request.";
    exit();
}

$userId    = $_SESSION['user']['id'];
$vehicleId = intval($_POST['vehicleId']);
// Round amount to 2 decimal places
$amount    = round(floatval($_POST['amount']), 2);

// 3. Insert booking
$stmt = $conn->prepare("
    INSERT INTO bookings (userId, vehicleId, amountPaid, booking_time)
    VALUES (?, ?, ?, NOW())
");
$stmt->bind_param("iid", $userId, $vehicleId, $amount);

if (!$stmt->execute()) {
    echo "<h2>❌ Booking failed.</h2><p>Please try again.</p>";
    exit();
}

// 4. Mark the vehicle unavailable
$conn->query("UPDATE vehicles SET available = 0 WHERE id = $vehicleId");

// 5. Success message
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Confirmed</title>
  <link rel="stylesheet" href="../frontend/style.css">
  <style>
    body {
      background: linear-gradient(to right, #2c5364, #203a43, #0f2027);
      color: white; font-family: Arial, sans-serif;
      display: flex; align-items: center; justify-content: center;
      height: 100vh; margin: 0;
    }
    .confirm-card {
      background: rgba(255,255,255,0.1);
      padding: 30px; border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      text-align: center;
    }
    .confirm-card h2 { color: #D4AF37; margin-bottom: 15px; }
    .confirm-card p { margin: 10px 0; }
    .confirm-card a {
      display: inline-block; margin-top: 20px;
      padding: 10px 20px; background: #D4AF37;
      color: white; text-decoration: none; border-radius: 6px;
    }
    .confirm-card a:hover { background: #caa731; }
  </style>
</head>
<body>
  <div class="confirm-card">
    <h2>✅ Booking Confirmed!</h2>
    <p>Your booking (ID: <?= $stmt->insert_id ?>) has been placed.</p>
    <p><strong>Amount Paid:</strong> ₹<?= number_format($amount, 2) ?></p>
    <a href="../frontend/user_dashboard.html">Back to Dashboard</a>
  </div>
</body>
</html>
