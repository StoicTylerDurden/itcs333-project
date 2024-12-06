<?php
session_start();
// Include the database connection
include 'db_connect.php';
include 'navbar_admin.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    header("Location: login.php");
    exit("Error: You are not a logged in admin.");
}

// Get the ROOM_ID from the URL
if (isset($_GET['room_id'])) {
    $room_id = $_GET['room_id'];

    // Fetch the room details from the database
    try {
        $query = "SELECT * FROM rooms WHERE ROOM_ID = :room_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();

        // If room exists, fetch the details
        if ($stmt->rowCount() > 0) {
            $room = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "<div class='alert alert-danger mt-5'>Error: Room not found!</div>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger mt-5'>Error: " . $e->getMessage() . "</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger mt-5'>Room ID is missing!</div>";
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $room_name = $_POST['room_name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equipment'];
    $location = $_POST['location'];

    // Sanitize and validate input
    $room_name = htmlspecialchars($room_name);
    $capacity = htmlspecialchars($capacity);
    $equipment = htmlspecialchars($equipment);
    $location = htmlspecialchars($location);

    try {
        // Check if the room name is unique
        $query = "SELECT * FROM rooms WHERE ROOM_NAME = :room_name AND ROOM_ID != :room_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-danger mt-5'>Error: Room name already exists. Please choose a different name.</div>";
        } else {
            // Update the room details
            $update_query = "UPDATE rooms SET ROOM_NAME = :room_name, CAPACITY = :capacity, EQUIPMENT = :equipment, LOCATION = :location WHERE ROOM_ID = :room_id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
            $update_stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
            $update_stmt->bindParam(':equipment', $equipment, PDO::PARAM_STR);
            $update_stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $update_stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);

            // Execute the update query
            if ($update_stmt->execute()) {
                echo "<div class='alert alert-success mt-5'>Room details updated successfully! Redirecting in 3 seconds...</div>";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'admin_panel.php';
                    }, 3000); // 3000 milliseconds = 3 seconds
                </script>";
            } else {
                echo "<div class='alert alert-danger mt-5'>Error updating room details.</div>";
            }
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger mt-5'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>

<body>

    <div class="container mt-5">
        <!-- Use room name directly from the fetched details in $room array -->
         <br/>
        <h1 class="mb-4 text-center">Update Room: <?php echo htmlspecialchars($room['ROOM_NAME']); ?></h1>
        <form method="POST" action="edit_room.php?room_id=<?php echo $room_id; ?>" class="mt-5">
            <div class="form-group">
                <label for="room_name">Room Name</label>
                <input type="text" class="form-control" name="room_name" id="room_name"
                    value="<?php echo htmlspecialchars($room['ROOM_NAME']); ?>" required>
            </div>
            <div class="form-group">
                <label for="capacity">Capacity</label>
                <input type="number" class="form-control" name="capacity" id="capacity"
                    value="<?php echo htmlspecialchars($room['CAPACITY']); ?>" required>
            </div>
            <div class="form-group">
                <label for="equipment">Equipment</label>
                <input type="text" class="form-control" name="equipment" id="equipment"
                    value="<?php echo htmlspecialchars($room['EQUIPMENT']); ?>" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" name="location" id="location"
                    value="<?php echo htmlspecialchars($room['LOCATION']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary w-50 mx-auto d-block">Update Room</button>
        </form>
    </div>

</body>

</html>
