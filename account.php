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

        if ($fileSize <= 64 * 1024) { // Ensure file size is <= 64KB
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($fileType, $allowedTypes)) {
                $fileData = file_get_contents($fileTmpPath);
                $updateQuery .= ", PROFILE_PICTURE = :profile_picture";
                $params[':profile_picture'] = $fileData;
            } else {
                echo "<div class='alert alert-danger'>Invalid file type. Please upload a JPEG, PNG, or GIF image.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>File size exceeds the limit of 64 KB.</div>";
        }
    }

    $updateQuery .= " WHERE USER_ID = :user_id";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute($params);
    // After update, refresh the page to show updated info
    header('Location: account.php');
    exit();
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
    <title>My Account</title>
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
        .profile-pic-container {
            text-align: center;
            margin-bottom: 20px;
            cursor: pointer;
        }
        #profile-pic-preview {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }
        .form-group label {
            font-weight: bold;
        }
        .action-buttons {
            margin-top: 20px;
        }
        .action-buttons button {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4 text-center">My Account</h1>

    <div class="profile-pic-container" id="profile-pic-container">
        <img id="profile-pic-preview"
             src="<?php echo $user['PROFILE_PICTURE'] ? 'data:image/jpeg;base64,' . base64_encode($user['PROFILE_PICTURE']) : 'https://via.placeholder.com/150'; ?>"
             alt="Profile Picture">
    </div>

    <form method="post" enctype="multipart/form-data" id="profile-form" class="mb-4">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($user['NAME']); ?>" disabled>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['EMAIL']); ?>" disabled>
        </div>

        <!-- Hidden file input for profile picture -->
        <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg, image/png, image/gif" style="display: none;" disabled>

        <div class="action-buttons">
            <button type="button" id="edit-button" class="btn btn-primary">Edit Profile</button>
            <button type="submit" id="save-button" class="btn btn-success" style="display: none;">Save</button>
            <button type="button" id="cancel-button" class="btn btn-secondary" style="display: none;">Cancel</button>
        </div>
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
    const editButton = document.getElementById('edit-button');
    const saveButton = document.getElementById('save-button');
    const cancelButton = document.getElementById('cancel-button');

    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const profilePictureInput = document.getElementById('profile_picture');

    let originalName = nameInput.value;
    let originalEmail = emailInput.value;

    editButton.addEventListener('click', () => {
        // Switch to edit mode
        nameInput.removeAttribute('disabled');
        emailInput.removeAttribute('disabled');
        profilePictureInput.removeAttribute('disabled');

        editButton.style.display = 'none';
        saveButton.style.display = 'inline-block';
        cancelButton.style.display = 'inline-block';
    });

    cancelButton.addEventListener('click', () => {
        // Revert to original values and read-only mode
        nameInput.value = originalName;
        emailInput.value = originalEmail;

        nameInput.setAttribute('disabled', 'disabled');
        emailInput.setAttribute('disabled', 'disabled');
        profilePictureInput.setAttribute('disabled', 'disabled');

        editButton.style.display = 'inline-block';
        saveButton.style.display = 'none';
        cancelButton.style.display = 'none';
    });

    document.getElementById('profile-pic-container').addEventListener('click', function () {
        if (!profilePictureInput.disabled) {
            profilePictureInput.click();
        }
    });

    profilePictureInput.addEventListener('change', function (event) {
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
