<?php
session_start();

// Check if the room data is passed via URL
if (isset($_GET['room_name']) && isset($_GET['room_capacity']) && isset($_GET['room_equipment']) && isset($_GET['room_location'])) {
    $_SESSION['room_name'] = $_GET['room_name'];
    $_SESSION['room_capacity'] = $_GET['room_capacity'];
    $_SESSION['room_equipment'] = $_GET['room_equipment'];
    $_SESSION['room_location'] = $_GET['room_location'];
}

// You can now use these session variables as needed, for example:
echo "Room Name: " . $_SESSION['room_name']."<br>";
echo "Capacity: " . $_SESSION['room_capacity']."<br>";
echo "Equipment: " . $_SESSION['room_equipment']."<br>";
echo "Location: " . $_SESSION['room_location']."<br>";

// header('Location:view_room_fazil.php');
?>