<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_announcement']) && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $success = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && isset($_POST['message'])) {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $expires_at = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;

    if (!empty($title) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO announcements (title, message, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $message, $expires_at);
        $stmt->execute();
        $success = true;
    } else {
        $error = "Title and message are required.";
    }
}

if (isset($_POST['dismiss_announcement']) && isset($_POST['dismiss_id'])) {
    $dismiss_id = intval($_POST['dismiss_id']);
    $dismissed = array();

    if (isset($_COOKIE['dismissed_announcements'])) {
        $dismissed = json_decode($_COOKIE['dismissed_announcements'], true);
    }

    $dismissed[] = $dismiss_id;
    setcookie("dismissed_announcements", json_encode(array_unique($dismissed)), time() + (86400 * 30), "/"); // 30 days
    header("Location: post_announcement.php");
    exit();
}

$announcement_list = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
$dismissed_ids = isset($_COOKIE['dismissed_announcements']) ? json_decode($_COOKIE['dismissed_announcements'], true) : array();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Post Announcement</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-box { 
            max-width: 600px; 
            margin: 40px auto; 
            background: rgb(179, 255, 230);
            padding: 50px; 
            border-radius: 10px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
            border-style: outset;
            border-width: 4px;
            border-radius: 25px;
            border-top-color: rgb(90, 189, 156);
            border-left-color: rgb(90, 189, 156);
            border-right-color: rgb(90, 189, 156);
            border-bottom-color: rgb(90, 189, 156);
        }
        input[type="text"], textarea, input[type="date"] {
            width: 100%; padding: 10px; margin: 10px 0; border-radius: 6px; border: 1px solid #ccc;
        }
        button { 
            background-color: #007bff; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 25px; font-weight: bold; 
            cursor: pointer; 
        }
        button:hover { background-color: #0056b3; }
        .msg { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .announcement-block {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fffbe6;
            border: 1px dashed #e1b500;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Post Announcement</h2>

        <?php if ($success) echo '<div class="msg">Action completed successfully!</div>'; ?>
        <?php if (!empty($error)) echo '<div class="error">' . $error . '</div>'; ?>

        <form method="POST">
            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Message:</label>
            <textarea name="message" rows="5" required></textarea>

            <label>Expiry Date:</label>
            <input type="date" name="expires_at">

            <button type="submit">Post</button>
            <a href="menu.php">Back to Menu</a>
        </form>

        <hr>
        <h3>Existing Announcements</h3>
        <?php
        if ($announcement_list->num_rows > 0) {
            while ($row = $announcement_list->fetch_assoc()) {

                
                if (in_array($row['id'], $dismissed_ids)) {
                    continue;
                }

                echo "<div class='announcement-block'>";
                echo "<strong>" . $row['title'] . "</strong><br>";
                echo "<p>" . $row['message'] . "</p>";
                echo "<small>Posted: " . $row['created_at'] . "</small>";
                if (!empty($row['expires_at'])) {
                    echo "<br><small>Expires: " . $row['expires_at'] . "</small>";
                }

                echo "<form method='POST' style='margin-top: 10px; display: inline-block;'>";
                echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
                echo "<button type='submit' name='delete_announcement' onclick='return confirm(\"Delete this announcement?\")'>Delete</button>";
                echo "</form>";

                echo "<form method='POST' style='margin-top: 10px; display: inline-block; margin-left: 10px;'>";
                echo "<input type='hidden' name='dismiss_id' value='" . $row['id'] . "'>";
                echo "<button type='submit' name='dismiss_announcement'>Dismiss (cookie)</button>";
                echo "</form>";

                echo "</div>";
            }
        } else {
            echo "<p>No announcements yet.</p>";
        }
        ?>
    </div>
</body>
</html>
