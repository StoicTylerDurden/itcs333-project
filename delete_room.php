<?php
// Start session and include the database connection
session_start();
include 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    header("Location: login.php");
    exit("Error: You are not a logged in admin.");
}

// Check if the room_id is passed via the URL
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    try {
        // Delete the room from the database
        $delete_query = "DELETE FROM rooms WHERE ROOM_ID = :room_id";
        $stmt = $pdo->prepare($delete_query);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);

        // Execute the deletion query
        if ($stmt->execute()) {
            // Redirect back to the admin panel after successful deletion
            header("Location: admin_panel.php");
            exit();
        } else {
            echo "<div class='alert alert-danger mt-5'>Error: Unable to delete the room.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger mt-5'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='alert alert-danger mt-5'>Error: Room ID is missing!</div>";
    exit();
}
?>
