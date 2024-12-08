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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_name = $_POST['room_name'];
    $capacity = $_POST['capacity'];
    $equipment = $_POST['equipment'];
    $location = $_POST['location'];
    $room_picture = $_FILES['room_picture'];

    // Handle file upload if a file is selected
    $room_picture_url = NULL; 
    if ($room_picture['error'] === UPLOAD_ERR_OK) {
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
            echo "<div class='alert alert-danger'>Error uploading to S3: " . htmlspecialchars($e->getMessage()) . "</div>";
            exit();
        }
    }

    // Insert data into the database
    $insert_query = "INSERT INTO rooms (ROOM_NAME, CAPACITY, EQUIPMENT, LOCATION, ROOM_PICTURE, CREATED_AT)
                     VALUES (:room_name, :capacity, :equipment, :location, :room_picture, NOW())";
    $insert_stmt = $pdo->prepare($insert_query);
    $insert_stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
    $insert_stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
    $insert_stmt->bindParam(':equipment', $equipment, PDO::PARAM_STR);
    $insert_stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $insert_stmt->bindParam(':room_picture', $room_picture_url, PDO::PARAM_STR);

    if ($insert_stmt->execute()) {
        echo "<br/><br/><div class='alert alert-success'>Room added successfully! Redirecting in 3 seconds...</div>";
        echo "<script>
            setTimeout(function() {
                window.location.href = 'admin_panel.php';
            }, 3000);
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Error adding room: " . $insert_stmt->errorInfo()[2] . "</div>";
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
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N"
          crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Add New Room</h1>
        <form method="POST" action="add_room.php" enctype="multipart/form-data">
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

            <div class="form-group">
                <label for="room_picture">Room Picture (Optional)</label>
                <input type="file" id="room_picture" name="room_picture" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-50 mx-auto d-block">Add Room</button>
        </form>
    </div>
</body>
</html>
