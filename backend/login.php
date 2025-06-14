<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user; // Store full user details

            // Redirect based on role
            if ($user['role'] === 'owner') {
                header("Location: ../frontend/owner_dashboard.html");
            } else {
                header("Location: ../frontend/user_dashboard.html");
            }
            exit();
        } else {
            header("Location: ../frontend/login.html?error=wrongpass");
            exit();
        }
    } else {
        header("Location: ../frontend/login.html?error=nouser");
        exit();
    }
}
?>
