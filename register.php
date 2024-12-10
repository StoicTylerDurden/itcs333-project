<?php
require 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'USER'; // Default role for new users
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Email validation patterns
    $studentEmailPattern = "/^\d{9}@stu\.uob\.edu\.bh$/";
    $adminEmailPattern = "/^[a-zA-Z0-9._%+-]+@uob\.edu\.bh$/";

    // Validate email format
    if (!preg_match($studentEmailPattern, $email) && !preg_match($adminEmailPattern, $email)) {
        $_SESSION['registration_error'] = "Invalid email format. Students must use 9-digit numbers followed by '@stu.uob.edu.bh', and admins must use '@uob.edu.bh'.";
        header("Location: register.php");
        exit();
    }

    // Determine role based on email
    if (preg_match($adminEmailPattern, $email)) {
        $role = 'ADMIN';
    }

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
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="header-container">
        <img class="logo" src="logo.png" alt="Logo">
        <div class="text-container">
            <h1 class="heading">WELCOME TO ROOM BOOKING</h1>
            <p class="heading-p">Book rooms for IT department in simple steps</p>
        </div>
    </div>

    <!-- <h1 class="heading">WELCOME TO ROOM BOOKING</h1>
    <p class="heading-p">Book rooms for IT department in simple steps</p> -->
    <main>
        <div class="con">
            <h1>Register</h1>
            <div class="login-wrapper">
                <?php
                if (isset($_SESSION['registration_error'])) {
                    echo "<p style='color: red;'>" . $_SESSION['registration_error'] . "</p>";
                    unset($_SESSION['registration_error']);
                }
                ?>
                <form action="register.php" method="post">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter name" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter UOB email" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter Password" required>

                    <button type="submit">Register</button>
                </form>
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </main>
</body>

</html>