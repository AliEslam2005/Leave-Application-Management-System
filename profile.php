<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once('config.php');

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $department = $_POST['department'];

    
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $conn->query("UPDATE users SET name='$name', email='$email', password='$password' WHERE id=$user_id");
    } else {
        $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$user_id");
    }

  
    $checkProfile = $conn->query("SELECT * FROM user_profiles WHERE user_id = $user_id");
    if ($checkProfile->num_rows > 0) {
       
        $conn->query("UPDATE user_profiles SET phone='$phone', address='$address', department='$department' WHERE user_id = $user_id");
    } else {
       
        $conn->query("INSERT INTO user_profiles (user_id, phone, address, department) VALUES ($user_id, '$phone', '$address', '$department')");
    }

    echo "<p>Profile updated!</p>";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
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
    </style>
</head>
<body>
    
</body>
</html>
<body>

<div class="profileContainer">
    <h2>My Profile</h2>

    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <div class="success-msg">Profile updated!</div>
    <?php endif; ?>

    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" value="<?php echo $user['name'] ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $user['email'] ?>" required>

        <label>New Password (leave blank to keep current):</label>
        <input type="password" name="password">

        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo $profile['phone'] ?? '' ?>">

        <label>Address:</label>
        <input type="text" name="address" value="<?php echo $profile['address'] ?? '' ?>">

        <label>Department:</label>
        <input type="text" name="department" value="<?php echo $profile['department'] ?? '' ?>">

        <button type="submit">Update Profile</button>
        <a href="menu.php">Back to Menu</a>
    </form>
</div>
<div class="footer">
        <span class="copyright">© 2025. All rights reserved.</span>
    </div>
</body>
</html>
