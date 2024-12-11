<?php
session_start();
include 'db_connect.php';
include 'navbar_admin.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    header("Location: login.php");
    exit("Error: You are not a logged-in admin.");
}

// Initialize variables for form
$sid = null;
$room_id = '';
$date = '';
$start_time = '';
$end_time = '';
$status = '';
$book_id = null; // Added for booking editing

// Handle adding or editing schedules
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'edit_booking') {
        // Handle booking edit
        $book_id = $_POST['book_id'];
        $room_id = $_POST['room_id'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $status = $_POST['status'];

        // Update booking
        $sql = "UPDATE bookings SET ROOM_ID = :room_id, START_TIME = :start_time, END_TIME = :end_time, STATUS = :status WHERE BOOK_ID = :book_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':book_id', $book_id);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':end_time', $end_time);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            $success_message = "Booking updated successfully!";
        } else {
            $error_message = "Error: " . $stmt->errorInfo()[2];
        }
    }
}



// Fetch rooms for dropdown
$rooms_query = "SELECT ROOM_ID, ROOM_NAME FROM rooms";
$rooms_stmt = $pdo->query($rooms_query);
$rooms_result = $rooms_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch current booking data if editing
if (isset($_GET['edit_booking'])) {
    $book_id = $_GET['edit_booking'];
    $sql = "SELECT ROOM_ID, START_TIME, END_TIME, STATUS FROM bookings WHERE BOOK_ID = :book_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':book_id', $book_id);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($booking) {
        $room_id = $booking['ROOM_ID'];
        $start_time = $booking['START_TIME'];
        $end_time = $booking['END_TIME'];
        $status = $booking['STATUS'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules and Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Edit Booking</h1>
        <?php if (isset($success_message)) {
            echo "<div class='alert alert-success'>$success_message  Redirecting in 3 seconds...</div>";
        echo "<script>
                    setTimeout(function() {
                        window.location.href = 'schedule_managment.php';
                    }, 3000);
                </script>"; }?>
        <?php if (isset($error_message))
            echo "<div class='alert alert-danger'>$error_message</div>"; ?>

        <form method="POST">
            <input type="hidden" name="action" value="edit_booking">
            <input type="hidden" name="book_id" value="<?= htmlspecialchars($book_id) ?>">
            <div class="form-group">
                <label for="room_id">Room:</label>
                <select name="room_id" id="room_id" class="form-control" required>
                    <option value="">Select a room</option>
                    <?php foreach ($rooms_result as $room) { ?>
                        <option value="<?= $room['ROOM_ID'] ?>" <?= $room['ROOM_ID'] == $room_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($room['ROOM_NAME']) ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="start_time">Start Time:</label>
                <input type="datetime-local" name="start_time" id="start_time" class="form-control"
                    value="<?= htmlspecialchars($start_time) ?>" required>
            </div>
            <div class="form-group">
                <label for="end_time">End Time:</label>
                <input type="datetime-local" name="end_time" id="end_time" class="form-control"
                    value="<?= htmlspecialchars($end_time) ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="BOOKED" <?= $status == 'BOOKED' ? 'selected' : '' ?>>Booked</option>
                    <option value="CANCELLED" <?= $status == 'CANCELLED' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</body>

</html>