<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE USER_ID = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes)) {
            $fileData = file_get_contents($fileTmpPath);
            $updateQuery = "UPDATE users SET PROFILE_PICTURE = :profile_picture WHERE USER_ID = :user_id";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->bindParam(':profile_picture', $fileData, PDO::PARAM_LOB);
            $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $updateStmt->execute();
            header('Location: profile_page.php');
            exit();
        } else {
            echo "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
        }
    }

    if (isset($_POST['name']) && isset($_POST['email'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $updateQuery = "UPDATE users SET NAME = :name, EMAIL = :email WHERE USER_ID = :user_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindParam(':name', $name, PDO::PARAM_STR);
        $updateStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $updateStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $updateStmt->execute();
        header('Location: profile_page.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['NAME']); ?></h1>
    <p>Email: <?php echo htmlspecialchars($user['EMAIL']); ?></p>

    <p>
        <?php if ($user['PROFILE_PICTURE']): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($user['PROFILE_PICTURE']); ?>" alt="Profile Picture" width="150" height="150">
        <?php else: ?>
            <p>No profile picture uploaded.</p>
        <?php endif; ?>
    </p>

    <form method="post" enctype="multipart/form-data">
        <label for="profile_picture">Upload New Profile Picture:</label>
        <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg, image/png, image/gif">
        <br><br>
        
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['NAME']); ?>" required>
        <br><br>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['EMAIL']); ?>" required>
        <br><br>

        <button type="submit">Update Profile</button>
    </form>
</body>
</html>
