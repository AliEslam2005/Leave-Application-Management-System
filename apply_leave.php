<?php
session_start();
require_once('config.php');

// Check login and role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle leave application submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];

    // Validate dates
    if (strtotime($start_date) > strtotime($end_date)) {
        $message = "End date must be after start date!";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO leave_applications 
                                (staff_id, leave_type_id, start_date, end_date, reason, status) 
                                VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iisss", $user_id, $leave_type, $start_date, $end_date, $reason);
        
        if ($stmt->execute()) {
            $message = "Leave application submitted successfully!";
        } else {
            $message = "Error submitting application: " . $conn->error;
        }
    }
}

// Get leave types for dropdown
$leave_types = $conn->query("SELECT * FROM leave_types");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Leave</title>
    <style>
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Apply for Leave</h2>
    
    <?php if ($message): ?>
        <p class="<?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>
    
    <form method="POST">
        <label>Leave Type:</label><br>
        <select name="leave_type" required>
            <option value="">Select Leave Type</option>
            <?php while ($type = $leave_types->fetch_assoc()): ?>
                <option value="<?= $type['id'] ?>">
                    <?= htmlspecialchars($type['type_name']) ?>
                </option>
            <?php endwhile; ?>
        </select><br><br>
        
        <label>Start Date:</label><br>
        <input type="date" name="start_date" required><br><br>
        
        <label>End Date:</label><br>
        <input type="date" name="end_date" required><br><br>
        
        <label>Reason:</label><br>
        <textarea name="reason" rows="4" required></textarea><br><br>
        
        <button type="submit">Submit Application</button>
    </form>
    
    <br>
    <a href="menu.php">Back to Menu</a>
</body>
</html>