<?php

include 'db_connect.php'; // Include your existing db connection file
include "navbar.php";
include "User_dashboard.php";

if ($_SESSION['USER_ROLE'] !== 'USER') {
    header("Location: login.php");
    exit();
}


// Fetch total bookings per room
$stmt = $pdo->query("SELECT r.ROOM_NAME, COUNT(b.BOOK_ID) AS TOTAL_BOOKINGS
                     FROM rooms r
                     LEFT JOIN bookings b ON r.ROOM_ID = b.ROOM_ID
                     GROUP BY r.ROOM_ID
                     ORDER BY TOTAL_BOOKINGS DESC");
$total_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total usage hours for each room
$stmt = $pdo->query("SELECT r.ROOM_NAME, SUM(TIMESTAMPDIFF(HOUR, b.START_TIME, b.END_TIME)) AS TOTAL_USAGE_HOURS
                     FROM bookings b
                     JOIN rooms r ON b.ROOM_ID = r.ROOM_ID
                     GROUP BY r.ROOM_ID");
$total_usage_hours = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Room Booking Statistics</h2>
        
        <h3>Total Bookings per Room</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Total Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($total_bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['ROOM_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($booking['TOTAL_BOOKINGS']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total Usage Hours per Room</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Total Usage Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($total_usage_hours as $usage): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usage['ROOM_NAME']); ?></td>
                        <td><?php echo htmlspecialchars($usage['TOTAL_USAGE_HOURS']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <canvas id="bookingChart" width="200" height="100"></canvas>
        <script>
            const ctx = document.getElementById('bookingChart').getContext('2d');
            const labels = <?php echo json_encode(array_column($total_bookings, 'ROOM_NAME')); ?>;
            const data = {
                labels: labels,
                datasets: [{
                    label: 'Total Bookings',
                    data: <?php echo json_encode(array_column($total_bookings, 'TOTAL_BOOKINGS')); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };

            const bookingChart = new Chart(ctx, {
                type: 'bar',
                data: data,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>
    </div>
</body>
</html>

