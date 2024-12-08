<?php
include "db_connect.php";
session_start();

// The navbar should be included in all pages
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] == 'ADMIN') {
    include "navbar_admin.php";
} else {
    include "navbar.php";
}

// Ensure user is logged in
if (!isset($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

// Fetch all room details from the database, including ROOM_PICTURE
$sql = "SELECT ROOM_NAME, CAPACITY, EQUIPMENT, LOCATION, ROOM_PICTURE FROM rooms";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Viewing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .search-bar {
            border-radius: 25px;
            border: 2px solid #007bff;
            padding: 10px 20px;
        }

        .search-bar:focus {
            border-color: #0056b3;
            outline: none;
            box-shadow: 0px 0px 5px rgba(0, 123, 255, 0.5);
        }

        .card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .card-footer a {
            background-color: #007bff;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            color: #ffffff;
            font-size: 1rem;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .card-footer a:hover {
            background-color: #0056b3;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center text-primary">Browse Rooms</h1>

        <!-- Search Bar -->
        <div class="mb-4">
            <input type="text" id="search-bar" class="form-control search-bar" placeholder="Search by room name or number">
        </div>

        <!-- Room List -->
        <div id="room-list" class="row">
            <?php foreach ($result as $room): ?>
                <div class="col-md-4 mb-4 room-card" data-room-name="<?php echo htmlspecialchars($room['ROOM_NAME']); ?>">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo htmlspecialchars($room['ROOM_PICTURE'] ?: 'https://placehold.co/600x400'); ?>" class="card-img-top" alt="Room image">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($room['ROOM_NAME']); ?></h5>
                            <p class="card-text"><strong>Capacity:</strong> <?php echo htmlspecialchars($room['CAPACITY']); ?></p>
                            <p class="card-text"><strong>Equipment:</strong> <?php echo htmlspecialchars($room['EQUIPMENT']); ?></p>
                            <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($room['LOCATION']); ?></p>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="booking_system_fazil.php?room_name=<?php echo urlencode($room['ROOM_NAME']); ?>&room_capacity=<?php echo urlencode($room['CAPACITY']); ?>&room_equipment=<?php echo urlencode($room['EQUIPMENT']); ?>&room_location=<?php echo urlencode($room['LOCATION']); ?>" class="btn btn-primary w-100">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // filter room cards based on search query
        document.getElementById('search-bar').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const roomCards = document.querySelectorAll('.room-card');

            roomCards.forEach(function(card) {
                const roomName = card.getAttribute('data-room-name').toLowerCase();
                if (roomName.includes(query)) {
                    card.style.display = 'block'; // Show matching cards
                } else {
                    card.style.display = 'none'; // Hide non-matching cards
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfZ2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy63B9q64WdZWQUiUq4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>
</html>
