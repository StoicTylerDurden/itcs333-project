<?php

// Make sure you create a config file I've shared it via whatsapp
// Then create a file named ".gitignore" in this file include "config.php"

// include the config file which has the credentials

try {
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
