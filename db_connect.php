<?php
$dsn = "mysql: host=localhost;dbname=room_booking";
$username = "root";
$password = "";
try {
    $db = new PDO($dsn, $username, $password);
    echo " You have connected to room_booking database";
}catch(PDOException $e) {
    $error_message = $e->getMessage();
    echo $error_message;
    exit();
} 
?>