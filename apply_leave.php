<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leave_type = $_POST['leave_type'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $reason = $_POST['reason'] ?? '';
    
    if (strtotime($start_date) && strtotime($end_date) && strtotime($start_date) <= strtotime($end_date)) {
  
        $stmt = $conn->prepare("INSERT INTO leave_applications (staff_id, leave_type_id, from_date, to_date, reason, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iisss", $user_id, $leave_type, $start_date, $end_date, $reason);
        
        if ($stmt->execute()) {
            $message = "Leave application submitted successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Invalid dates! Please check your dates.";
    }
}


$leave_types = $conn->query("SELECT * FROM leave_types");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Leave</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        .message { color: green; margin: 10px 0; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Apply for Leave</h2>
        
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="leave_type">Leave Type:</label>
                <select name="leave_type" id="leave_type" required>
                    <option value="">Select Leave Type</option>
                    <?php while ($type = $leave_types->fetch_assoc()): ?>
                        <option value="<?= $type['id'] ?>"><?= $type['type_name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" name="start_date" id="start_date" required>
            </div>
            
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="date" name="end_date" id="end_date" required>
            </div>
            
            <div class="form-group">
                <label for="reason">Reason:</label>
                <textarea name="reason" id="reason" rows="4" required></textarea>
            </div>
            
            <button type="submit">Submit Application</button>
        </form>
        
        <br>
        <a href="menu.php">Back to Menu</a>
    </div>

    <script>
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('start_date').min = today;
        document.getElementById('end_date').min = today;
        
        document.getElementById('start_date').addEventListener('change', function() {
            document.getElementById('end_date').min = this.value;
        });
    </script>
</body>
</html>