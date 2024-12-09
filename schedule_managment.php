<?php
session_start();
include 'db_connect.php';
include 'navbar_admin.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    header("Location: login.php");
    exit("Error: You are not a logged in admin.");
}

// Handle form submission to add a schedule
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $status = $_POST['status'];

    // Insert the new schedule into the database using PDO
    $sql = "INSERT INTO schedules (ROOM_ID, DATE, START_TIME, END_TIME, STATUS) 
            VALUES (:room_id, :date, :start_time, :end_time, :status)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':room_id', $room_id);
    $stmt->bindParam(':date', $date);
    $stmt->bindParam(':start_time', $start_time);
    $stmt->bindParam(':end_time', $end_time);
    $stmt->bindParam(':status', $status);

    if ($stmt->execute()) {
        $success_message = "Schedule added successfully!";
                echo "<div class='container'>
            <div class='alert alert-success'>
                $success_message
            </div>
        </div>";
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'schedule_managment.php';
                    }, 3000);
                </script>";
    } else {
        echo "<div class='container'>
            <div class='alert alert-danger'>
                'Error adding schedule: " . $stmt->errorInfo()[2] . "'
            </div>
        </div>";
    }
}

// Fetch all schedules from the database using PDO
$schedules_query = "
    (SELECT s.SID, r.ROOM_NAME, s.DATE, s.START_TIME, s.END_TIME, s.STATUS 
    FROM schedules s
    JOIN rooms r ON s.ROOM_ID = r.ROOM_ID)
    UNION
    (SELECT b.BOOK_ID AS SID, r.ROOM_NAME, DATE(b.START_TIME) AS DATE, 
    TIME(b.START_TIME) AS START_TIME, TIME(b.END_TIME) AS END_TIME, b.STATUS
    FROM bookings b
    JOIN rooms r ON b.ROOM_ID = r.ROOM_ID
    WHERE b.STATUS = 'BOOKED')
    ORDER BY DATE, START_TIME
";
$schedules_stmt = $pdo->query($schedules_query);
$schedules_result = $schedules_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all rooms for the dropdown using PDO
$rooms_query = "SELECT ROOM_ID, ROOM_NAME FROM rooms";
$rooms_stmt = $pdo->query($rooms_query);
$rooms_result = $rooms_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Schedule Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>
    <div class="container mt-5">
        <h1>Room Schedule Management</h1>

        <!-- Display Existing Schedules -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Room#</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules_result as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['ROOM_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($row['DATE']); ?></td>
                        <td><?php echo htmlspecialchars($row['START_TIME']); ?></td>
                        <td><?php echo htmlspecialchars($row['END_TIME']); ?></td>
                        <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Add New Schedule -->
        <h2>Add Schedule</h2>
        <form method="POST">
            <div class="form-group">
                <label for="room_id">Room Number:</label>
                <select name="room_id" id="room_id" required>
                    <option value="">Select a room</option>
                    <?php foreach ($rooms_result as $room) { ?>
                        <option value="<?php echo $room['ROOM_ID']; ?>">
                            <?php echo htmlspecialchars($room['ROOM_NAME']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" name="date" id="date" required>
            </div>
            <div class="form-group">
                <label for="start_time">Start Time:</label>
                <input type="time" name="start_time" id="start_time" required>
            </div>
            <div class="form-group">
                <label for="end_time">End Time:</label>
                <input type="time" name="end_time" id="end_time" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" id="status" required>
                    <option value="AVAILABLE">Available</option>
                    <option value="BOOKED">Booked</option>
                    <option value="MAINTENANCE">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary w-100px mx-left d-block">Add Schedule</button>
            </div>
        </form>
    </div>
</body>

</html>