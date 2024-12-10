<?php
session_start();
include "db_connect.php"; // Include the database connection

// Initialize session variables for booked times if not already set
if (!isset($_SESSION['Array_booked'])) {
    $_SESSION['Array_booked'] = [];
}
// The navbar should be included in all pages
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] == 'ADMIN') {
    include "navbar_admin.php";
} else {
    include "navbar.php";
}

// Retrieve room data from URL parameters and store it in session variables
if (isset($_GET['room_name'], $_GET['room_capacity'], $_GET['room_equipment'], $_GET['room_location'])) {
    $_SESSION = array_merge($_SESSION, [
        'room_name' => $_GET['room_name'],
        'room_capacity' => $_GET['room_capacity'],
        'room_equipment' => $_GET['room_equipment'],
        'room_location' => $_GET['room_location']
    ]);
}

// Fetch bookings for the selected room from the database
$sql = "SELECT DATE(B.START_TIME) AS booking_date, TIME(B.START_TIME) AS start_time, TIME(B.END_TIME) AS end_time 
        FROM BOOKINGS B 
        NATURAL JOIN ROOMS R 
        WHERE R.ROOM_NAME = :room_name AND B.STATUS = 'BOOKED'";
$stmt = $pdo->prepare($sql);
$stmt->execute(['room_name' => $_SESSION['room_name']]);
$_SESSION['Array_booked'] = $stmt->fetchAll(PDO::FETCH_ASSOC); // Store booked times in session
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* General styling for the page */
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
        <!-- Display the room being booked -->
        <div class="booking-title">Book Room <?php echo htmlspecialchars($_SESSION['room_name']); ?></div>
        <form action="process_booking_fazil.php" method="post">
            <!-- Date selection field -->
            <div class="form-group">
                <label for="date">Select Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <!-- Booking type options -->
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
            <!-- Starting time selection -->
            <div class="form-group">
                <label for="stime">Starting Time</label>
                <select id="stime" name="stime" class="form-control" onchange="updateEndingTime()" required>
                    <option value="" hidden>Select Starting Time</option>
                </select>
            </div>
            <!-- Display ending time based on the selected start time -->
            <div class="form-group">
                <label>Ending Time</label>
                <div class="ending-time" id="etime-display">Please select a starting time.</div>
                <input type="hidden" id="etime" name="etime">
            </div>
            <button type="submit" class="btn btn-primary">Book Now</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const dateInput = document.getElementById("date");
            const today = new Date().toISOString().split("T")[0];
            dateInput.setAttribute("min", today); // Ensure past dates cannot be selected

            // Open the date picker when clicking anywhere on the input field
            dateInput.addEventListener("click", () => dateInput.showPicker());

            dateInput.addEventListener("change", updateTimes); // Update available times on date change
            document.querySelectorAll('input[name="type"]').forEach(radio => {
                radio.addEventListener("change", updateTimes); // Update times when type changes
            });
        });

        // Dynamically update available starting times
        function updateTimes() {
            const stimeSelect = document.getElementById("stime");
            const etimeDisplay = document.getElementById("etime-display");
            const etimeInput = document.getElementById("etime");
            const selectedDate = document.getElementById("date").value;
            const today = new Date();
            const startHour = 8; // Booking start time: 8:00 AM
            const endHour = 18; // Booking end time: 6:00 PM
            const currentHour = today.getHours();

            stimeSelect.innerHTML = "<option value='' hidden>Select Starting Time</option>";
            etimeDisplay.textContent = "Please select a starting time.";
            etimeInput.value = "";

            const isToday = selectedDate === today.toISOString().split("T")[0];

            // Add valid time options
            for (let hour = startHour; hour < endHour; hour++) {
                if (isToday && hour <= currentHour) continue; // Skip past hours if booking is for today
                const time = `${hour.toString().padStart(2, "0")}:00`;
                stimeSelect.add(new Option(time, time));
            }
        }

        // Update the ending time based on the selected start time and type
        function updateEndingTime() {
            const stimeSelect = document.getElementById("stime");
            const etimeDisplay = document.getElementById("etime-display");
            const etimeInput = document.getElementById("etime");
            const type = document.querySelector('input[name="type"]:checked').value;

            const duration = type === "test" ? 60 : type === "midterm" ? 75 : 120; // Booking durations
            const startTime = stimeSelect.value;

            if (startTime) {
                const [startHour, startMinute] = startTime.split(":").map(Number);

                let endHour = startHour + Math.floor(duration / 60);
                let endMinute = (startMinute + (duration % 60)) % 60;
                endHour += Math.floor((startMinute + (duration % 60)) / 60);

                if (endHour <= 18) {
                    const endTime = `${endHour.toString().padStart(2, "0")}:${endMinute.toString().padStart(2, "0")}`;
                    etimeDisplay.textContent = endTime;
                    etimeInput.value = endTime;
                } else {
                    etimeDisplay.textContent = "End time exceeds working hours.";
                    etimeInput.value = "";
                }
            }
        }
    </script>
</body>

</html>
