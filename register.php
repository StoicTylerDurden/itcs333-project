<?php
session_start();

// Simple in-memory storage (replace with database in real-world scenario)
if (!isset($_SESSION["users"])) {
    $_SESSION["users"] = [];
}

$users = $_SESSION["users"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $users;
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // In a real application, you'd save this to a database
    $users[$username] = $hashed_password;
    
    $_SESSION['registration_success'] = "Registration successful. You can now log in.";
    header("Location: login.php");
    exit();
}
echo '<h1>Whatup </h1>'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>