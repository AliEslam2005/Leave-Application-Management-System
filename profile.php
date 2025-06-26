<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['log']) && $_GET['log'] === 'back_to_menu') {
    $action = "Back to menu from profile";
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
    header("Location: menu.php");
    exit();
}

$profileUpdated = false;
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $department = $_POST['department'];
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $profile_picture = '';
    $remove_picture = isset($_POST['remove_picture']) ? true : false;

    $profile_result = $conn->query("SELECT profile_picture FROM user_profiles WHERE user_id = $user_id");
    $profile_row = $profile_result->fetch_assoc();
    $current_picture = $profile_row['profile_picture'];

    if ($remove_picture) {
        if (!empty($current_picture) && file_exists($current_picture) && strpos($current_picture, './assets/') !== 0) {
            unlink($current_picture);
        }
        $profile_picture = './assets/test.jpg';
    } else {
        if (!empty($_FILES['profile_picture']['name'])) {
            $allowed_types = ['image/jpeg', 'image/jpg'];
            if (in_array($_FILES['profile_picture']['type'], $allowed_types)) {
                if ($_FILES['profile_picture']['size'] <= 2 * 1024 * 1024) { // 2MB max
                    $target_dir = "uploads/";
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $file_name = basename($_FILES['profile_picture']['name']);
                    $target_file = $target_dir . time() . "_" . $file_name;
                    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                        $profile_picture = $target_file;
                        if (!empty($current_picture) && file_exists($current_picture) && strpos($current_picture, './assets/') !== 0) {
                            unlink($current_picture);
                        }
                    } else {
                        $errorMsg = 'Failed to upload image.';
                    }
                } else {
                    $errorMsg = 'File size must be 2MB or less.';
                }
            } else {
                $errorMsg = 'Only JPG/JPEG files are allowed.';
            }
        } else {
            $profile_picture = $current_picture;
        }
    }

    if (empty($errorMsg)) {
        if (!empty($_POST['password'])) {
            $password = md5($_POST['password']);
            $conn->query("UPDATE users SET name='$name', email='$email', password='$password' WHERE id=$user_id");
        } else {
            $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$user_id");
        }

        $checkProfile = $conn->query("SELECT * FROM user_profiles WHERE user_id = $user_id");
        if ($checkProfile->num_rows > 0) {
            $update_sql = "UPDATE user_profiles SET phone='$phone', address='$address', department='$department', gender='$gender', date_of_birth='$dob', profile_picture='$profile_picture' WHERE user_id = $user_id";
            $conn->query($update_sql);
        } else {
            $conn->query("INSERT INTO user_profiles (user_id, phone, address, department, gender, date_of_birth, profile_picture) VALUES ($user_id, '$phone', '$address', '$department', '$gender', '$dob', '$profile_picture')");
        }

        $action = "Updated profile";
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $action);
        $stmt->execute();

        $profileUpdated = true;
    }
}

$user_result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_result->fetch_assoc();

$profile_result = $conn->query("SELECT * FROM user_profiles WHERE user_id = $user_id");
$profile = $profile_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profileContainer {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            background-color: rgb(179, 255, 230);
            border-radius: 25px;
            border: 5px outset;
            border-top-color: rgb(90, 189, 156);
            border-left-color: rgb(90, 189, 156);
            border-right-color: rgb(90, 189, 156);
            border-bottom-color: rgb(90, 189, 156);
            font-family: Arial, sans-serif;
        }

        .profileContainer h2 {
            text-align: center;
            color: #00448d;
            font-weight: 900;
        }

        form label {
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form input[type="date"],
        form select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        form input[type="file"] {
            margin-bottom: 15px;
        }

        button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 25px;
        }

        button:active {
            background: #00448d;
        }

        a {
            margin-left: 10px;
            font-weight: bold;
            text-decoration: none;
            color: #035daf;
        }

        a:hover {
            text-decoration: underline;
        }

        .success-msg {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .error-msg {
            text-align: center;
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .profile-pic {
            text-align: center;
            margin-bottom: 15px;
        }

        .profile-pic img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #00448d;
        }
    </style>
</head>
<body>

<div class="profileContainer">
    <h2>My Profile</h2>

    <?php
    if ($profileUpdated) {
        echo '<div class="success-msg">Profile updated!</div>';
    }

    if (!empty($errorMsg)) {
        echo '<div class="error-msg">' . $errorMsg . '</div>';
    }
    ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="profile-pic">
            <?php
            if (!empty($profile['profile_picture']) && file_exists($profile['profile_picture'])) {
                echo '<img src="' . $profile['profile_picture'] . '" alt="Profile Picture">';
            } else {
                echo '<img src="./assets/test.jpg" alt="Default Picture">';
            }
            ?>
        </div>

        <label>Upload Profile Picture (JPG/JPEG, max 2MB):</label>
        <input type="file" name="profile_picture">

        <label><input type="checkbox" name="remove_picture" value="1"> Remove current profile picture</label>

        <label>Name:</label>
        <input type="text" name="name" value="<?php echo $user['name']; ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

        <label>New Password (leave blank to keep current):</label>
        <input type="password" name="password">

        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo $profile['phone']; ?>">

        <label>Address:</label>
        <input type="text" name="address" value="<?php echo $profile['address']; ?>">

        <label>Department:</label>
        <input type="text" name="department" value="<?php echo $profile['department']; ?>">

        <label>Gender:</label>
        <select name="gender">
            <option value="">Select</option>
            <option value="male" <?php if ($profile['gender'] === 'male') { echo 'selected'; } ?>>Male</option>
            <option value="female" <?php if ($profile['gender'] === 'female') { echo 'selected'; } ?>>Female</option>
        </select>

        <label>Date of Birth:</label>
        <input type="date" name="date_of_birth" value="<?php echo $profile['date_of_birth']; ?>">

        <button type="submit">Update Profile</button>
        <a href="profile.php?log=back_to_menu">Back to Menu</a>
    </form>
</div>

<div class="footer">
    <span class="copyright">© 2025. All rights reserved.</span>
</div>

</body>
</html>
