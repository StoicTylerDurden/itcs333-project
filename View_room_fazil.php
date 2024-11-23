<?php

include "db_connect.php"; //including the connection object that is connected to the database in db_connect.php i.e $pdo
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room viewing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="View_room_fazil_style.css">
</head>

<body>
    <?php
    $sql = "SELECT ROOM_NAME,CAPACTIY,EQUIPMENT,LOCATION FROM ROOMS";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<div class="container">';
    foreach ($result as $room) {
        $room_name = $room['ROOM_NAME'];
        $room_capacity = $room["CAPACTIY"];
        $room_equipment = $room["EQUIPMENT"];
        $room_location = $room["LOCATION"];
        echo '
        <div class="card" style="width: 18rem;">
            <img src="https://placehold.co/600x400" class="card-img-top" alt="Room image">
                <div class="card-body">
                    <h5 class="card-title"><h6>Room: ' . $room_name . '</h5>
                    <p class="card-text"> <h6>Capacity: ' . $room_capacity . '</h6></p>
                    <p class="card-text"> <h6>Equipment: ' . $room_equipment . '</h6></p>
                    <p class="card-text"> <h6>Location: ' . $room_location . '</p>
                    <a href="booking_system_fazil.php?room_name=' . urlencode($room_name) . '&room_capacity=' . urlencode($room_capacity) . '&room_equipment=' . urlencode($room_equipment) . '&room_location=' . urlencode($room_location) . '" class="btn btn-primary">Book</a>
                </div>
        </div>
        ';
    }
    echo '</div>';
    ?>
    

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>

</html>