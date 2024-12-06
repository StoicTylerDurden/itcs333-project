<?php
include "db_connect.php";
session_start();
include "navbar_admin.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] !== 'ADMIN') {
    header("Location: login.php");
    exit("Error: You are not a logged in admin.");
}

//echo "<br/><br/><br/>You are an admin";

// Fetch all room details from the database
$sql = "SELECT ROOM_ID, ROOM_NAME, CAPACITY, EQUIPMENT, LOCATION FROM rooms";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title> <!-- 4.6.2 ver not 5.3.3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Admin Panel</h1>
        <h2 class="mb-4 text-center">Add New Room</h2>
        <a href="add_room.php" class="btn btn-primary w-50 mx-auto d-block">Add</a>
        <br />
        <h2 class="mb-4 text-center">Manage Available Rooms</h2>

        <!-- Search Bar -->
        <div class="mb-4">
            <input type="text" id="search-bar" class="form-control" placeholder="Search by room name or number">
        </div>

        <!-- Room List -->
        <div id="room-list" class="row">
            <?php foreach ($result as $room): ?>
                <div class="col-md-4 mb-4 room-card" data-room-name="<?php echo htmlspecialchars($room['ROOM_NAME']); ?>">
                    <div class="card h-100 shadow-sm">
                        <img src="https://placehold.co/600x400" class="card-img-top" alt="Room image">
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
                            <div class="d-flex justify-content-between">
                                <a href="edit_room.php?room_id=<?php echo urlencode($room['ROOM_ID']); ?>"
                                    class="btn btn-warning w-100">Edit</a>

                                <!--<a href="#"
                                    class="btn btn-danger w-100">Delete</a>-->

                                <a href="delete_room.php?room_id=<?php echo urlencode($room['ROOM_ID']); ?>"
                                    class="btn btn-danger w-100"
                                    onclick="return confirm('Are you sure you want to delete this room?');">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <style>
        </style>
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