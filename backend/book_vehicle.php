<?php
session_start();
header('Content-Type: application/json');

include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehicleId']) && isset($_POST['hours'])) {
    $vehicleId = (int) $_POST['vehicleId'];
    $hours = (int) $_POST['hours'];  // Assuming the hours are passed as part of the request

    // Get the user ID from the session
    $userId = $_SESSION['user']['id'];

    // Fetch vehicle details to get location and contact
    $sql = "SELECT * FROM vehicles WHERE id = $vehicleId";
    $result = $conn->query($sql);
    $vehicle = $result->fetch_assoc();

    // Extract location and contact details from the fetched vehicle record
    $location = $vehicle['location'];
    $contact = $vehicle['contact'];

    // Insert booking details into the database
    $sql = "INSERT INTO bookings (userid, vehicleid, hours, location, contact) 
            VALUES ('$userId', '$vehicleId', '$hours', '$location', '$contact')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Booking successful! Redirecting to payment page..."]);
    } else {
        echo json_encode(["error" => "Booking failed. Please try again."]);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
