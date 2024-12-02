<?php
session_start();
include "db_connect.php"; // Include database connection

// The navbar should be included in all pages
include "navbar.php"; 

$user_id = $_SESSION['USER_ID']; // Get user ID from session
$room_name = $_SESSION['room_name']; // Get room name from session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_selected = $_POST["date"];
    $stime_selected = $_POST["stime"];
    $etime_selected = $_POST["etime"];

    if (empty($date_selected) || empty($stime_selected) || empty($etime_selected)) {
        $error_message = "All fields are required.";
    } else {
        try {
            // Check for conflicts
            $sql = "SELECT * 
                    FROM bookings 
                    JOIN rooms ON bookings.ROOM_ID = rooms.ROOM_ID 
                    WHERE rooms.ROOM_NAME = :room_name 
                      AND bookings.STATUS = 'BOOKED'
                      AND DATE(START_TIME) = :date
                      AND (TIME(:stime) < TIME(END_TIME) AND TIME(:etime) > TIME(START_TIME))";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":room_name", $room_name);
            $stmt->bindValue(":date", $date_selected);
            $stmt->bindValue(":stime", $stime_selected);
            $stmt->bindValue(":etime", $etime_selected);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error_message = "The selected time slot overlaps with an existing booking.";
            } else {
                // Insert new booking
                $sql = "INSERT INTO bookings (USER_ID, ROOM_ID, START_TIME, END_TIME, STATUS) 
                        VALUES (:user_id, 
                                (SELECT ROOM_ID FROM rooms WHERE ROOM_NAME = :room_name), 
                                :start_time, :end_time, 'BOOKED')";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":user_id", $user_id);
                $stmt->bindValue(":room_name", $room_name);
                $stmt->bindValue(":start_time", "$date_selected $stime_selected");
                $stmt->bindValue(":end_time", "$date_selected $etime_selected");
                $stmt->execute();

                $success_message = "Booking successful!";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .booking-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .booking-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #343a40;
        }

        .alert {
            font-size: 1.1rem;
            font-weight: bold;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container booking-container">
        <div class="booking-title">Room Booking Confirmation</div>
        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (isset($success_message)) : ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <a href="booking_system_fazil.php" class="btn btn-secondary">Back to Booking</a>
    </div>
</body>

</html>