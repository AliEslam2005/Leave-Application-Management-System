<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'manager')) {
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

<h2>My Profile</h2>
<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo $user['name'] ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo $user['email'] ?>" required><br><br>

    <label>New Password (leave blank to keep current):</label><br>
    <input type="password" name="password"><br><br>

    <label>Phone:</label><br>
    <input type="text" name="phone" value="<?php echo $profile['phone'] ?? '' ?>"><br><br>

    <label>Address:</label><br>
    <input type="text" name="address" value="<?php echo $profile['address'] ?? '' ?>"><br><br>

    <label>Department:</label><br>
    <input type="text" name="department" value="<?php echo $profile['department'] ?? '' ?>"><br><br>

    <button type="submit">Update Profile</button> | 
    <a href="menu.php">Back to Menu</a>
</form>
