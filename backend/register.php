<?php
include 'db.php';

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

$stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $fullname, $email, $password, $role);

if ($stmt->execute()) {
    header("Location: /Car-Bike-Sharing-Platform/frontend/login.html");
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
