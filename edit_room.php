<?php
session_start();
require 'aws.phar';
include 'db_connect.php';
include 'navbar_admin.php';
require 'config.php'; // Contains AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Check if user is logged in and is an admin
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    header("Location: login.php");
    exit("Error: You are not a logged in admin.");
}

// Initialize AWS S3 client
$s3 = new S3Client([
    'region' => 'us-east-1',
    'version' => 'latest',
    'credentials' => [
        'key' => AWS_ACCESS_KEY_ID,
        'secret' => AWS_SECRET_ACCESS_KEY,
    ],
]);

// Check if ROOM_ID is set
if (!isset($_GET['room_id'])) {
    echo "<div class='alert alert-danger mt-5'>Room ID is missing!</div>";
    exit();
}

$room_id = $_GET['room_id'];

// Fetch the room details
try {
    $query = "SELECT * FROM rooms WHERE ROOM_ID = :room_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        echo "<div class='alert alert-danger mt-5'>Error: Room not found!</div>";
        exit();
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger mt-5'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equipment'];
    $location = $_POST['location'];

    // Keep current picture unless a new one is uploaded
    $room_picture_url = $room['ROOM_PICTURE'];

    $room_picture = $_FILES['room_picture'];
    if ($room_picture['error'] === UPLOAD_ERR_OK) {
        // New image uploaded
        $fileName = time() . '_' . basename($room_picture['name']);
        $fileTmpPath = $room_picture['tmp_name'];

        try {
            $result = $s3->putObject([
                'Bucket' => 'testingmarketplace', // Replace with your bucket name
                'Key' => 'room_pictures/' . $fileName,
                'SourceFile' => $fileTmpPath,
            ]);
            $room_picture_url = $result['ObjectURL'];
        } catch (AwsException $e) {
            echo "<div class='alert alert-danger mt-5'>Error uploading to S3: " . htmlspecialchars($e->getMessage()) . "</div>";
            exit();
        }
    }

    try {
        // Check if room name is unique (except for current room)
        $query = "SELECT * FROM rooms WHERE ROOM_NAME = :room_name AND ROOM_ID != :room_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error_message = "Error: Room name already exists. Choose another name.";
            echo "<div class='container'>
                <div class='alert alert-danger'>
                    $error_message
                </div>
            </div>";
        } else {
            // Update room details
            $update_query = "UPDATE rooms SET ROOM_NAME = :room_name, CAPACITY = :capacity, EQUIPMENT = :equipment, LOCATION = :location, ROOM_PICTURE = :room_picture WHERE ROOM_ID = :room_id";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
            $update_stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
            $update_stmt->bindParam(':equipment', $equipment, PDO::PARAM_STR);
            $update_stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $update_stmt->bindParam(':room_picture', $room_picture_url, PDO::PARAM_STR);
            $update_stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);

            if ($update_stmt->execute()) {
                $success_message = "Room details updated successfully! Redirecting in 3 seconds...";
                echo "<div class='container'>
            <div class='alert alert-success'>
                $success_message
            </div>
        </div>";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'admin_panel.php';
                    }, 3000);
                </script>";
            } else {
                $error_message = "Error updating room details.";
                echo "<div class='container booking-container'>
            <div class='alert alert-danger'>
                $error_message
            </div>
        </div>";
            }
        }
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger mt-5'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
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
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>
    <div class="container mt-5">
        <br />
        <h1 class="mb-4 text-center">Update Room: <?php echo htmlspecialchars($room['ROOM_NAME']); ?></h1>
        <form method="POST" action="edit_room.php?room_id=<?php echo urlencode($room_id); ?>"
            enctype="multipart/form-data" class="mt-5">
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

            <?php if (!empty($room['ROOM_PICTURE'])): ?>
                <div class="form-group">
                    <label>Current Picture:</label><br>
                    <img src="<?php echo htmlspecialchars($room['ROOM_PICTURE']); ?>" alt="Room Image"
                        style="max-width:200px;">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="room_picture">New Room Picture (Optional)</label>
                <input type="file" id="room_picture" name="room_picture" class="form-control">
                <small class="text-muted">Leave blank if you do not want to change the picture.</small>
            </div>

            <button type="submit" class="btn btn-primary w-50 mx-auto d-block">Update Room</button>
        </form>
    </div>
</body>

</html>