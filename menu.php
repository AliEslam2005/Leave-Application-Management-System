<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['log']) && isset($_GET['page'])) {
    $user_id = $_SESSION['user_id'];
    $action = $_GET['log'];
    $destination = $_GET['page'];

    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();

    header("Location: " . $destination);
    exit();
}
?>
<?php
$today = date('Y-m-d');
$announcement_sql = "SELECT * FROM announcements WHERE expires_at IS NULL OR expires_at >= '$today' ORDER BY created_at DESC LIMIT 5";
$announcements = $conn->query($announcement_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
if ($announcements->num_rows > 0) {
    echo '<div class="announcement-section" style="background: #ffffcc; padding: 15px; margin: 20px auto; border: 2px dashed #e1b500; width: 90%; max-width: 800px;">';
    echo '<h3 style="color: #b06f00;">📢 Announcements</h3>';
    echo '<ul>';
    while ($row = $announcements->fetch_assoc()) {
        echo '<li style="margin-bottom: 10px;">';
        echo '<strong>' . $row['title'] . '</strong><br>';
        echo '<p>' . $row['message'] . '</p>';
        echo '<small>Posted on: ' . $row['created_at'] . '</small>';
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
}
?>

<?php
echo "<h1 id='menuIntro'>Welcome " . $_SESSION['username'] . " | Current status: " . $_SESSION['role'] . "</h1>";
echo '<div class="menuContainer">';

if ($_SESSION['role'] == "admin") {
    echo "<a href='menu.php?log=Accessed Manage Users&page=admin_user.php'><img src='./assets/manage.png' class='menuLogo'><br>Manage users</a>";
    echo "<a href='menu.php?log=Accessed Announcements Manager&page=post_announcement.php'><img src='./assets/announcement.png' class='menuLogo'><br>Manage Announcements</a>";

}

if ($_SESSION['role'] == "manager") {
    echo "<a href='menu.php?log=Viewed Leave Applications&page=leave_requests.php'><img src='./assets/application.png' class='menuLogo'><br>View Leave Applications</a>";
    echo "<a href='menu.php?log=Viewed Reports&page=report.php'><img src='./assets/report.png' class='menuLogo' style='margin-left: 50px'><br>Leave reports</a>";
}

if ($_SESSION['role'] == "staff") {
    echo "<a href='menu.php?log=Opened Apply Leave&page=apply_leave.php'><img src='./assets/applyLeave.png' class='menuLogo'><br>Apply for leave</a>";
    echo "<a href='menu.php?log=Checked Leave Status&page=leave_status.php'><img src='./assets/checking.png' class='menuLogo'><br>Check leave status</a>";
}

echo "<a href='menu.php?log=Viewed Profile&page=profile.php'><img src='./assets/profile.png' class='menuLogo'><br>Profile</a>";
echo "<a href='menu.php?log=Logged Out&page=logout.php'>Logout</a>";

echo '</div>';
?>

<div class="footer">
    <span class="copyright">© 2025. All rights reserved.</span>
</div>

</body>
</html>
