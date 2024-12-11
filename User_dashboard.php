
<?php
session_start();
require 'db_connect.php'; // Include your existing db connection file

$user_id = $_SESSION['USER_ID']; // Assuming user ID is stored in session

// Fetch upcoming bookings
$stmt = $pdo->prepare("SELECT b.BOOK_ID, r.ROOM_NAME, b.START_TIME, b.END_TIME, b.STATUS
                        FROM bookings b
                        JOIN rooms r ON b.ROOM_ID = r.ROOM_ID
                        WHERE b.USER_ID = :user_id AND b.START_TIME > NOW()
                        ORDER BY b.START_TIME");
$stmt->execute(['user_id' => $user_id]);
$upcoming_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch past bookings
$stmt = $pdo->prepare("SELECT b.BOOK_ID, r.ROOM_NAME, b.START_TIME, b.END_TIME, b.STATUS
                        FROM bookings b
                        JOIN rooms r ON b.ROOM_ID = r.ROOM_ID
                        WHERE b.USER_ID = :user_id AND b.END_TIME < NOW()
                        ORDER BY b.END_TIME DESC");
$stmt->execute(['user_id' => $user_id]);
$past_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="global.css">
    <title>User Dashboard</title>
</head>
<body>
    <div class="container mt-5">
        <h2>Your Upcoming Bookings</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcoming_bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['ROOM_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($booking['START_TIME']); ?></td>
                        <td><?php echo htmlspecialchars($booking['END_TIME']); ?></td>
                        <td><?php echo htmlspecialchars($booking['STATUS']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Your Past Bookings</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($past_bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['ROOM_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($booking['START_TIME']); ?></td>
                        <td><?php echo htmlspecialchars($booking['END_TIME']); ?></td>
                        <td><?php echo htmlspecialchars($booking['STATUS']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>