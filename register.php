<?php
require 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'USER'; // Default role for new users
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check for duplicate email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['registration_error'] = "Email already exists!";
        header("Location: register.php");
        exit();
    }

    // Insert into users table
    $stmt = $pdo->prepare("INSERT INTO users (NAME, EMAIL, HASHED_PASSWORD, ROLE, CREATED_AT) 
                           VALUES (:name, :email, :password, :role, NOW())");
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password' => $hashed_password,
        'role' => $role
    ]);

    $_SESSION['registration_success'] = "Registration successful. You can now log in.";
    header("Location: login.php");
    exit();
}
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
    <?php
    if (isset($_SESSION['registration_error'])) {
        echo "<p style='color: red;'>" . $_SESSION['registration_error'] . "</p>";
        unset($_SESSION['registration_error']);
    }
    ?>
    <form action="register.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>
