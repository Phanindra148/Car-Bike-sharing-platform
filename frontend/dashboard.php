<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Car & Bike Sharing</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="#">Dashboard</a></li>
            <li><a href="#" id="logoutBtn">Logout</a></li>
        </ul>
    </nav>

    <!-- Main Container -->
    <div class="container">
        <h1>Welcome to Car & Bike Sharing Dashboard</h1>

        <!-- Add Vehicle Section -->
        <h2>Add Vehicle</h2>
        <form id="addVehicleForm">
            <select id="vehicleType" required>
                <option value="">Select Vehicle Type</option>
                <option value="Car">Car</option>
                <option value="Bike">Bike</option>
            </select>
            <input type="text" id="model" placeholder="Vehicle Model" required>
            <input type="number" id="price" placeholder="Price per Hour (₹)" required>
            <input type="number" id="hours" placeholder="Available Hours" required>
            <button type="submit" class="btn">Add Vehicle</button>
        </form>

        <!-- Book Vehicle Section -->
        <h2>Available Vehicles</h2>
        <div id="vehicles" class="section"></div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2025 Car & Bike Sharing Platform. All rights reserved.
    </footer>

    <script>
        // Store PHP session user in localStorage
        localStorage.setItem("user", JSON.stringify(<?php echo json_encode($user); ?>));
    </script>
    <script src="../js/dashboard.js"></script>
</body>
</html>
