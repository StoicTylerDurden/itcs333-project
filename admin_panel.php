<?php
include "db_connect.php";
session_start();
include "navbar_admin.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    header("Location: login.php");
    exit("Error: You are not a logged in admin.");
}

// Fetch all room details from the database, including ROOM_ID for edit/delete links
$sql = "SELECT ROOM_ID, ROOM_NAME, CAPACITY, EQUIPMENT, LOCATION, ROOM_PICTURE FROM rooms";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Manage Available Rooms</h1>

        <!-- Search Bar -->
        <div class="mb-4">
            <input type="text" id="search-bar" class="form-control" placeholder="Search by room name or number">
        </div>

        <!-- Room List -->
        <div id="room-list" class="row">
            <?php foreach ($result as $room): ?>
                <div class="col-md-4 mb-4 room-card" data-room-name="<?php echo htmlspecialchars($room['ROOM_NAME']); ?>">
                    <div class="card h-100 shadow-sm">
                        <img src="<?php echo htmlspecialchars($room['ROOM_PICTURE'] ?: 'https://placehold.co/600x400'); ?>"
                            class="card-img-top" alt="Room image">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($room['ROOM_NAME']); ?></h5>
                            <p class="card-text"><strong>Capacity:</strong>
                                <?php echo htmlspecialchars($room['CAPACITY']); ?></p>
                            <p class="card-text"><strong>Equipment:</strong>
                                <?php echo htmlspecialchars($room['EQUIPMENT']); ?></p>
                            <p class="card-text"><strong>Location:</strong>
                                <?php echo htmlspecialchars($room['LOCATION']); ?></p>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex">
                                <a href="edit_room.php?room_id=<?php echo urlencode($room['ROOM_ID']); ?>"
                                    class="btn btn-warning mr-2" style="flex: 1;">Edit</a>

                                <a href="delete_room.php?room_id=<?php echo urlencode($room['ROOM_ID']); ?>"
                                    class="btn btn-danger" style="flex: 1;"
                                    onclick="return confirm('Are you sure you want to delete this room?');">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Add Room Button at the End -->
        <a href="add_room.php" class="btn btn-primary w-50 mx-auto d-block">Add New Room</a>
    </div>

    <script>
        // filter room cards based on search query
        document.getElementById('search-bar').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const roomCards = document.querySelectorAll('.room-card');

            roomCards.forEach(function (card) {
                const roomName = card.getAttribute('data-room-name').toLowerCase();
                if (roomName.includes(query)) {
                    card.style.display = 'block'; // Show matching cards
                } else {
                    card.style.display = 'none'; // Hide non-matching cards
                }
            });
        });
    </script>
</body>

</html>