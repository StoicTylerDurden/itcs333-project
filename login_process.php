<?php
require 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['HASHED_PASSWORD'])) {
        $_SESSION['USER_ID'] = $user['USER_ID']; // Set user ID
        $_SESSION['USER_ROLE'] = $user['ROLE']; // Optional: Store user role
        $_SESSION['USER_NAME'] = $user['NAME']; // Optional: Store user name
        header("Location: View_room_fazil.php"); // Redirect to room viewing page
        exit();
    } else {
        $_SESSION['login_error'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
    }
}
?>
