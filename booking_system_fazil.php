<?php
session_start();
unset($_SESSION['Array_booked']);
include "db_connect.php";
// Ensure 'Array_booked' is initialized properly
if (!isset($_SESSION['Array_booked'])) {
    $_SESSION['Array_booked'] = [];
}
$_SESSION['Array_booked_counter'] = 0;
// Check if the room data is passed via URL
if (isset($_GET['room_name']) && isset($_GET['room_capacity']) && isset($_GET['room_equipment']) && isset($_GET['room_location'])) {
    $_SESSION['room_name'] = $_GET['room_name'];
    $_SESSION['room_capacity'] = $_GET['room_capacity'];
    $_SESSION['room_equipment'] = $_GET['room_equipment'];
    $_SESSION['room_location'] = $_GET['room_location'];
}

// You can now use these session variables as needed, for example:
// echo "Room Name: " . $_SESSION['room_name'] . "<br>";
// echo "Capacity: " . $_SESSION['room_capacity'] . "<br>";
// echo "Equipment: " . $_SESSION['room_equipment'] . "<br>";
// echo "Location: " . $_SESSION['room_location'] . "<br>";

$user_id = $_SESSION['USER_ID']; //used for knowing who is the current user


$sql = "SELECT B.START_TIME,B.END_TIME,U.NAME,U.ROLE,R.ROOM_NAME FROM BOOKINGS B NATURAL JOIN USERS U NATURAL JOIN ROOMS R WHERE R.ROOM_NAME = :room_name AND B.STATUS = :book";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":room_name", $_SESSION['room_name']);
$stmt->bindValue(":book", "BOOKED");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
// var_dump($result);

foreach ($result as $user_who_booked) {
    if (!empty($result)) {
        foreach ($result as $user_who_booked) {
            $room_name_booked = $user_who_booked["ROOM_NAME"];
            $user_name_who_booked = $user_who_booked["NAME"];
            $user_start_time = $user_who_booked["START_TIME"];
            $user_end_time = $user_who_booked["END_TIME"];
    
            $part1 = explode(" ", $user_start_time);
            $date1 = $part1[0];
            $time1 = $part1[1];
    
            $part2 = explode(" ", $user_end_time);
            $date2 = $part2[0];
            $time2 = $part2[1];
    
            $length = $_SESSION["Array_booked_counter"];
            $_SESSION['Array_booked'][$length] = [
                "date" => $date1,
                "stime" => $time1,
                "etime" => $time2,
            ];
            $_SESSION["Array_booked_counter"]++;
        }
    }
    // echo "<br><br><br>ROOM NAME = ".$user_who_booked['ROOM_NAME']."<br>BOOKED BY: ".$user_who_booked["NAME"]."<br>ROLE = ".$user_who_booked["ROLE"]."<br>START TIME = ".$user_who_booked["START_TIME"]."<br>END_TIME = ".$user_who_booked["END_TIME"];
    
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
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
            max-width: 400px;
            width: 100%;
        }
        .booking-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #343a40;
        }
        .btn-primary {
            width: 100%;
        }
        .ending-time {
            font-weight: bold;
            color: #343a40;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container booking-container">
    <div class="booking-title">Book Room <?php echo htmlspecialchars($_SESSION['room_name']); ?></div>
    <form action="process_booking_fazil.php" method="post">
            <div class="form-group">
                <label for="date">Select Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label>Select Type</label>
                <div>
                    <input type="radio" id="test" name="type" value="test" onclick="updateTimes()" required>
                    <label for="test">Test (1 hour)</label>
                </div>
                <div>
                    <input type="radio" id="midterm" name="type" value="midterm" onclick="updateTimes()">
                    <label for="midterm">Midterm (1 hour 15 minutes)</label>
                </div>
                <div>
                    <input type="radio" id="final" name="type" value="final" onclick="updateTimes()">
                    <label for="final">Final (2 hours)</label>
                </div>
            </div>
            <div class="form-group">
                <label for="stime">Starting Time</label>
                <select id="stime" name="stime" class="form-control" onchange="updateEndingTime()" required>
                    <!-- Options will be dynamically added here -->
                </select>
            </div>
            <div class="form-group">
                <label>Ending Time</label>
                <div class="ending-time" id="etime-display">Please select type and starting time.</div>
                <input type="hidden" id="etime" name="etime">
            </div>
            <button type="submit" class="btn btn-primary">Book Now</button>
        </form>
    </div>

    <script>
        function updateTimes() {
            const stimeSelect = document.getElementById("stime");
            const etimeDisplay = document.getElementById("etime-display");
            const etimeInput = document.getElementById("etime");

            // Clear existing options
            stimeSelect.innerHTML = "";
            etimeDisplay.textContent = "Please select a starting time.";
            etimeInput.value = "";

            const startHour = 8; // Start at 8:00 AM
            const endHour = 18; // End at 6:00 PM

            for (let hour = startHour; hour < endHour; hour++) {
                const time = `${hour.toString().padStart(2, "0")}:00`;

                const stimeOption = document.createElement("option");
                stimeOption.value = time;
                stimeOption.textContent = time;

                stimeSelect.appendChild(stimeOption);
            }
        }

        function updateEndingTime() {
            const stimeSelect = document.getElementById("stime");
            const etimeDisplay = document.getElementById("etime-display");
            const etimeInput = document.getElementById("etime");
            const type = document.querySelector('input[name="type"]:checked').value;

            const duration = type === "test" ? 60 : type === "midterm" ? 75 : 120; // Duration in minutes
            const startTime = stimeSelect.value;

            if (startTime) {
                const [startHour, startMinute] = startTime.split(":").map(Number);

                let endHour = startHour + Math.floor(duration / 60);
                let endMinute = (startMinute + (duration % 60)) % 60;
                endHour += Math.floor((startMinute + (duration % 60)) / 60);

                if (endHour <= 18) { // Ensure the end time is within working hours
                    const endTime = `${endHour.toString().padStart(2, "0")}:${endMinute.toString().padStart(2, "0")}`;
                    etimeDisplay.textContent = endTime;
                    etimeInput.value = endTime; // Set the hidden input value
                } else {
                    etimeDisplay.textContent = "End time exceeds working hours.";
                    etimeInput.value = "";
                }
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCX9Rkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>

</html>

