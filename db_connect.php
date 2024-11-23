<?php
$dsn = "mysql: host=localhost;dbname=room_booking";
$username = "root";
$password = "";
try {
    $db = new PDO($dsn, $username, $password);
    echo " You have connected to room_booking database";
}catch(PDOException $e) {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=localhost;dbname=room_booking","root","");
    // echo "Connected successfully to the database!"; 
    //use the above echo command to check if the connection is esatablished successfully or not
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    echo $error_message." Cannot connect to the database!!\nTry again";
    exit();
} 
?>