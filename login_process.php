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
        $_SESSION['user_id'] = $user['USER_ID'];
        $_SESSION['name'] = $user['NAME'];
        $_SESSION['role'] = $user['ROLE'];

        header("Location: View_room_fazil.php"); 
        exit();
    } else {
        $_SESSION['login_error'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
    }
}
?>
