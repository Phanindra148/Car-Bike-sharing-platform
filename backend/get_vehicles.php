<?php
include 'db.php';

// Mark expired vehicles unavailable
$conn->query("
  UPDATE vehicles
  SET available = 0
  WHERE NOW() > expiry
    AND available = 1
");

// Fetch available vehicles with ISO expiry
$sql = "
  SELECT
    id,
    vehicleType,
    model,
    price,
    hours,
    location,
    contact,
    DATE_FORMAT(expiry, '%Y-%m-%dT%H:%i:%s') AS expiry_iso
  FROM vehicles
  WHERE available = 1
";
$result = $conn->query($sql);

$vehicles = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
}

echo json_encode(["vehicles" => $vehicles]);
$conn->close();
