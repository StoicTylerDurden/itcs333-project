<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connect.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE USER_ID = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $image_data = file_get_contents($_FILES['profile_picture']['tmp_name']);
        $sql = "UPDATE users SET PROFILE_PICTURE = ? WHERE USER_ID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $image_data, PDO::PARAM_LOB);
        $stmt->bindParam(2, $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    $sql = "UPDATE users SET NAME = ?, EMAIL = ? WHERE USER_ID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $user_id]);

    if ($stmt->rowCount()) {
        $user['NAME'] = $name;
        $user['EMAIL'] = $email;
        $message = "Profile updated successfully!";
    } else {
        $error = "Error updating profile!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>User Profile</h2>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="card">
            <div class="card-header">Profile Details</div>
            <div class="card-body">
                <form action="profile_page.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="profilePicture" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profilePicture" name="profile_picture">
                        <?php if (!empty($user['PROFILE_PICTURE'])): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($user['PROFILE_PICTURE']); ?>" alt="Profile Picture" class="img-thumbnail mt-2" style="max-width: 150px;">
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="userName" name="name" value="<?php echo htmlspecialchars($user['NAME']); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="userEmail" name="email" value="<?php echo htmlspecialchars($user['EMAIL']); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
