<?php
session_start();
require_once('config.php');

// Check login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager') {
    header("Location: login.php");
    exit();
}

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leave_id = $_POST['leave_id'];
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE leave_applications SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $leave_id);
        $stmt->execute();
    }
}

// Get all leave requests
$sql = "SELECT la.id, la.staff_id, la.start_date, la.end_date, la.reason, la.status, u.username 
        FROM leave_applications la 
        JOIN users u ON la.staff_id = u.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Requests</title>
</head>
<body>

<h2>Leave Requests</h2>

<table border="1">
    <tr>
        <th>Staff</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['start_date']) ?></td>
            <td><?= htmlspecialchars($row['end_date']) ?></td>
            <td><?= htmlspecialchars($row['reason']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?php if ($row['status'] == 'pending'): ?>
                    <form method="POST">
                        <input type="hidden" name="leave_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="action" value="approved">Approve</button>
                        <button type="submit" name="action" value="rejected">Reject</button>
                    </form>
                <?php else: ?>
                    <?= ucfirst($row['status']) ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>

</table>

<br>
<a href="menu.php">Back to Menu</a>

</body>
</html>
