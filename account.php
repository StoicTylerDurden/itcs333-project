<?php
session_start();
include('db_connect.php');

// The navbar should be included in all pages
if (!isset($_SESSION['USER_ID']) || $_SESSION['USER_ROLE'] == 'ADMIN') {
    include "navbar_admin.php";
} else {
    include "navbar.php";
}
$user_id = $_SESSION['USER_ID'];

$query = "SELECT * FROM users WHERE USER_ID = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name'], $_POST['email'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $updateQuery = "UPDATE users SET NAME = :name, EMAIL = :email";
    $params = [':name' => $name, ':email' => $email, ':user_id' => $user_id];

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileSize = $_FILES['profile_picture']['size'];

        if ($fileSize <= 64 * 1024) { // Ensure file size is within the limit
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($fileType, $allowedTypes)) {
                $fileData = file_get_contents($fileTmpPath);
                $updateQuery .= ", PROFILE_PICTURE = :profile_picture";
                $params[':profile_picture'] = $fileData;
            } else {
                echo "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
            }
        } else {
            echo "File size exceeds the limit of 64 KB.";
        }
    }

    $updateQuery .= " WHERE USER_ID = :user_id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute($params);
}

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
    $book_id = $_POST['book_id'];
    $cancelQuery = "UPDATE bookings SET STATUS = 'CANCELLED' WHERE BOOK_ID = :book_id AND USER_ID = :user_id";
    $stmt = $pdo->prepare($cancelQuery);
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    header('Location: account.php');
    exit();
}

// Fetch upcoming and past bookings
$bookingsQuery = "SELECT b.BOOK_ID, r.ROOM_NAME, b.START_TIME, b.END_TIME, b.STATUS
                  FROM bookings b
                  JOIN rooms r ON b.ROOM_ID = r.ROOM_ID
                  WHERE b.USER_ID = :user_id
                  ORDER BY b.START_TIME DESC";
$stmt = $pdo->prepare($bookingsQuery);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$upcomingBookings = [];
$pastBookings = [];
$currentDateTime = date('Y-m-d H:i:s');

foreach ($bookings as $booking) {
    if ($booking['START_TIME'] > $currentDateTime && $booking['STATUS'] === 'BOOKED') {
        $upcomingBookings[] = $booking;
    } else {
        $pastBookings[] = $booking;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Management</title>
    <link rel="stylesheet" href="account.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .bookings-section {
            margin-top: 30px;
        }
        .booking-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .cancel-button {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .cancel-button:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <div class="profile-pic" id="profile-pic-container">
            <img id="profile-pic-preview" 
                src="<?php echo $user['PROFILE_PICTURE'] ? 'data:image/jpeg;base64,' . base64_encode($user['PROFILE_PICTURE']) : 'https://via.placeholder.com/150'; ?>" 
                alt="Profile Picture">
        </div>
        <form method="post" enctype="multipart/form-data" id="profile-form">
            <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg, image/png, image/gif" hidden>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['NAME']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['EMAIL']); ?>" required>

            <button type="submit">Save Changes</button>
        </form>

        <!-- Upcoming Bookings Section -->
        <div class="bookings-section">
            <h2>Upcoming Bookings</h2>
            <?php if (empty($upcomingBookings)): ?>
                <p>No upcoming bookings.</p>
            <?php else: ?>
                <?php foreach ($upcomingBookings as $booking): ?>
                    <div class="booking-card">
                        <p><strong>Room:</strong> <?php echo htmlspecialchars($booking['ROOM_NAME']); ?></p>
                        <p><strong>Start:</strong> <?php echo $booking['START_TIME']; ?></p>
                        <p><strong>End:</strong> <?php echo $booking['END_TIME']; ?></p>
                        <form method="post">
                            <input type="hidden" name="book_id" value="<?php echo $booking['BOOK_ID']; ?>">
                            <button type="submit" name="cancel_booking" class="cancel-button">Cancel Booking</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Past Bookings Section -->
        <div class="bookings-section">
            <h2>Past Bookings</h2>
            <?php if (empty($pastBookings)): ?>
                <p>No past bookings.</p>
            <?php else: ?>
                <?php foreach ($pastBookings as $booking): ?>
                    <div class="booking-card">
                        <p><strong>Room:</strong> <?php echo htmlspecialchars($booking['ROOM_NAME']); ?></p>
                        <p><strong>Start:</strong> <?php echo $booking['START_TIME']; ?></p>
                        <p><strong>End:</strong> <?php echo $booking['END_TIME']; ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['STATUS']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('profile-pic-container').addEventListener('click', function () {
            document.getElementById('profile_picture').click();
        });

        document.getElementById('profile_picture').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profile-pic-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
