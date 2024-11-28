<?php
include "db_connect.php"; // Include the connection object
session_start();

// Ensure user is logged in
if (!isset($_SESSION['USER_ID'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Viewing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="View_room_fazil_style.css">
</head>

<body>
    <?php
    // Fetch room details from the database
    $sql = "SELECT ROOM_NAME, CAPACITY, EQUIPMENT, LOCATION FROM rooms";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<div class="container">';
    foreach ($result as $room) {
        $room_name = htmlspecialchars($room['ROOM_NAME']);
        $room_capacity = htmlspecialchars($room["CAPACITY"]);
        $room_equipment = htmlspecialchars($room["EQUIPMENT"]);
        $room_location = htmlspecialchars($room["LOCATION"]);

        echo '
            <div class="card" style="width: 18rem; margin: 10px; display: inline-block;">
                <img src="https://placehold.co/600x400" class="card-img-top" alt="Room image">
                <div class="card-body">
                    <h5 class="card-title">Room: ' . $room_name . '</h5>
                    <p class="card-text">Capacity: ' . $room_capacity . '</p>
                    <p class="card-text">Equipment: ' . $room_equipment . '</p>
                    <p class="card-text">Location: ' . $room_location . '</p>
                    <a href="booking_system_fazil.php?room_name=' . urlencode($room_name) . '&room_capacity=' . urlencode($room_capacity) . '&room_equipment=' . urlencode($room_equipment) . '&room_location=' . urlencode($room_location) . '" class="btn btn-primary">View</a>
                </div>
            </div>';
    }
    echo '</div>';
    ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>

</html>