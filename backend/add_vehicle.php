<?php
session_start();
include 'db.php';

// Only owners can add
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'owner') {
    echo json_encode(["error" => "❌ Please log in as owner first."]);
    exit();
}

// Gather inputs
$ownerId     = $_SESSION['user']['id'];
$vehicleType = $_POST['vehicleType'];
$model       = $_POST['model'];
$price       = $_POST['price'];
$hours       = $_POST['hours'];
$location    = $_POST['location'];
$contact     = $_POST['contact'];

// Validate numeric fields
if (!is_numeric($price) || !is_numeric($hours)) {
    echo json_encode(["error" => "❌ Price and Hours must be numeric values."]);
    exit();
}

// Insert with expiry = NOW() + hours
$sql = "
  INSERT INTO vehicles
    (ownerId, vehicleType, model, price, hours, location, contact,
     available, added_at, expiry)
  VALUES
    (?, ?, ?, ?, ?, ?, ?, 1, NOW(), DATE_ADD(NOW(), INTERVAL ? HOUR))
";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "issdissi",
    $ownerId,
    $vehicleType,
    $model,
    $price,
    $hours,
    $location,
    $contact,
    $hours
);

if (!$stmt->execute()) {
    echo json_encode(["error" => "❌ Error adding vehicle: " . $stmt->error]);
} else {
    echo json_encode(["message" => "✅ Vehicle added successfully!"]);
}

$stmt->close();
$conn->close();
