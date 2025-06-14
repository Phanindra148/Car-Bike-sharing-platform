<?php
// Database connection parameters
$host = '127.0.0.1';
$port = 3306; // Add the correct port
$username = 'root';
$password = ''; // Empty string for no password
$database = 'car_bike_sharing';

// Create connection
try {
    $conn = new mysqli($host, $username, $password, $database, $port); // Note the port parameter
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper encoding
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}
?>