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
    
    // Optional: update password if provided
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $conn->query("UPDATE users SET name='$name', email='$email', password='$password' WHERE id=$user_id");
    } else {
        $conn->query("UPDATE users SET name='$name', email='$email' WHERE id=$user_id");
    }

    echo "<p>Profile updated!</p>";
}

$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();
?>

<h2>My Profile</h2>
<form method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo $user['name'] ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo $user['email'] ?>" required><br><br>

    <label>New Password (leave blank to keep current):</label><br>
    <input type="password" name="password"><br><br>

    <button type="submit">Update Profile</button>
</form>
