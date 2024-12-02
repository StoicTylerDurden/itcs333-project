<?php
$dsn = "mysql: host=localhost;dbname=room_booking";
$username = "root";
$password = "";
try {
    
    // Create a new PDO instance
    $pdo = new PDO($dsn, $username, $password);
    // echo " You have connected to room_booking database";
    

} catch (PDOException $e) {
    $error_message = $e->getMessage();
    echo $error_message." Cannot connect to the database!!\nTry again";
    exit();
} 
?>