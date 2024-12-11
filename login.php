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
            <div class="header-container">
                <img id="logo" src="logo.png" alt="Logo">
                <div class="text-container">
                    <h1 class="heading">WELCOME TO ROOM BOOKING</h1>
                    <p class="heading-p">Book rooms for IT department in simple steps</p>
                </div>
            </div>
            <div class="login-wrapper">
                <h2>Login</h2>
                <form action="login_process.php" method="post">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>

                    <button type="submit">Login</button>
                    <?php
                    if (isset($_SESSION['login_error'])) {
                        echo "<p id='login-error'>" . $_SESSION['login_error'] . "</p>";
                        unset($_SESSION['login_error']);
                    }
                    if (isset($_SESSION['registration_success'])) {
                        echo "<p style='color: green;'>" . $_SESSION['registration_success'] . "</p>";
                        unset($_SESSION['registration_success']);
                    }
                    ?>
                </form>
                <p>Don't have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </main>
</body>

</html>