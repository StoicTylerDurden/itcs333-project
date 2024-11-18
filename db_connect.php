<?php

// Make sure you create a config file I've shared it via whatsapp
// Then create a file named ".gitignore" in this file include "config.php"

// include the config file which has the credentials
require_once 'config.php';

$dsn = "mysql:host=" . DATABASE_HOSTNAME . ";dbname=" . DATABASE_NAME;
$username = DATABASE_USERNAME;
$password = DATABASE_PASSWORD;

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $username, $password);
    echo "Connected successfully to the database!";
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    echo $error_message;
    exit();
}
?>
