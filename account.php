<?php
session_start();
include('db_connect.php');

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
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
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
    <link rel="stylesheet" href="account.css">
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>
        <div class="profile-pic">
            <img id="profile-pic-preview" 
                src="<?php echo $user['PROFILE_PICTURE'] ? 'data:image/jpeg;base64,' . base64_encode($user['PROFILE_PICTURE']) : 'https://via.placeholder.com/150'; ?>" 
                alt="Profile Picture">
        </div>
        <form method="post" enctype="multipart/form-data" id="profile-form">
            <label for="profile_picture">Upload New Profile Picture:</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg, image/png, image/gif">

            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['NAME']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['EMAIL']); ?>" required>

            <button type="submit">Update Profile</button>
        </form>
    </div>

    <script>
        document.getElementById('profile_picture').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-pic-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('profile-form').addEventListener('submit', function(event) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            let valid = true;

            if (!name) {
                alert("Name cannot be empty");
                valid = false;
                document.getElementById('name').focus();
            } else if (!/^[\w\s]+$/.test(name)) {
                alert("Name contains invalid characters");
                valid = false;
                document.getElementById('name').focus();
            }

            if (!email) {
                alert("Email cannot be empty");
                valid = false;
                document.getElementById('email').focus();
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                alert("Invalid email format");
                valid = false;
                document.getElementById('email').focus();
            }

            if (!valid) event.preventDefault();
        });
    </script>
</body>
</html>
