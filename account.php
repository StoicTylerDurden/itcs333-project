<?php
session_start();
// The navbar should be included in all pages
include "navbar.php"; include('db_connect.php');

if (!isset($_SESSION['USER_ID'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['USER_ID'];

$query = "SELECT * FROM users WHERE USER_ID = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    
    $updateQuery = "UPDATE users SET NAME = :name, EMAIL = :email";
    $params = [':name' => $name, ':email' => $email, ':user_id' => $user_id];

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileSize = $_FILES['profile_picture']['size'];

        if ($fileSize > 64 * 1024) {
            echo "File size exceeds the limit of 64 KB.";
        } else {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($fileType, $allowedTypes)) {
                $fileData = file_get_contents($fileTmpPath);
                $updateQuery .= ", PROFILE_PICTURE = :profile_picture";
                $params[':profile_picture'] = $fileData;
            } else {
                echo "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
            }
        }
    }

    $updateQuery .= " WHERE USER_ID = :user_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute($params);
    header('Location: account.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Management</title>
    <link rel="stylesheet" href="account.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

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
    </div>

    <script>
        document.getElementById('profile-pic-container').addEventListener('click', function() {
            document.getElementById('profile_picture').click();
        });

        document.getElementById('profile_picture').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 64 * 1024) {
                    alert("File size exceeds the 64 KB limit.");
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-pic-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
