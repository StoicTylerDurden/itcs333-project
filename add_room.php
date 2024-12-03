<?php
session_start();
// Include the database connection using PDO
include 'db_connect.php';
include 'navbar_admin.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    header("Location: login.php");
    exit("Error: You are not a logged in admin.");
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $room_name = $_POST['room_name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equipment'];
    $location = $_POST['location'];

    // Sanitize and validate the input to prevent SQL Injection
    $room_name = htmlspecialchars($room_name);
    $capacity = htmlspecialchars($capacity);
    $equipment = htmlspecialchars($equipment);
    $location = htmlspecialchars($location);

    try {
        // Check if the room name already exists using a prepared statement
        $query = "SELECT * FROM rooms WHERE ROOM_NAME = :room_name";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
        $stmt->execute();

        // Check if the room name already exists in the database
        if ($stmt->rowCount() > 0) {
            echo "<br/><br/><div class='alert alert-danger'>Error: Room name already exists. Please choose a different name.</div>";
        } else {
            // If the room name is unique, proceed to insert the new room
            $insert_query = "INSERT INTO rooms (ROOM_NAME, CAPACITY, EQUIPMENT, LOCATION, CREATED_AT) VALUES (:room_name, :capacity, :equipment, :location, NOW() )";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
            $insert_stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
            $insert_stmt->bindParam(':equipment', $equipment, PDO::PARAM_STR);
            $insert_stmt->bindParam(':location', $location, PDO::PARAM_STR);

            // Execute the insert query
            if ($insert_stmt->execute()) {
                // Room added successfully
                echo "<br/><br/><div class='alert alert-success'>Room added successfully! Redirecting in 3 seconds...</div>";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'admin_panel.php';
                    }, 3000); // 3000 milliseconds = 3 seconds
                </script>";
            }                       
             else {
                // Error inserting the room
                echo "<div class='alert alert-danger'>Error adding room: " . $insert_stmt->errorInfo()[2] . "</div>";
            }
        }
    } catch (PDOException $e) {
        // Catch any PDO errors
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>

<body>

    <div class="container mt-5">
        <h1 class="mb-4 text-center">Add New Room</h1>
        <form method="POST" action="add_room.php">
            <div class="form-group">
                <label for="room_name">Room Name</label>
                <input type="text" id="room_name" name="room_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="capacity">Capacity</label>
                <input type="number" id="capacity" name="capacity" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="equipment">Equipment</label>
                <textarea id="equipment" name="equipment" class="form-control" required></textarea>
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-50 mx-auto d-block">Add Room</button>
        </form>
    </div>
    
</body>

</html>