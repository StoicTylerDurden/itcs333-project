<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <main>
        <div class="con">
            <h1>Login</h1>
            <div class="login-wrapper">
                <?php
                if (isset($_SESSION['login_error'])) {
                    echo "<p style='color: red;'>" . $_SESSION['login_error'] . "</p>";
                    unset($_SESSION['login_error']);
                }
                if (isset($_SESSION['registration_success'])) {
                    echo "<p style='color: green;'>" . $_SESSION['registration_success'] . "</p>";
                    unset($_SESSION['registration_success']);
                }
                ?>
                <form action="login_process.php" method="post">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>

                    <button type="submit">Login</button>
                </form>
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </main>
</body>
</html>
