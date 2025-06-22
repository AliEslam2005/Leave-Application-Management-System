<?php
session_start();
require_once('config.php');

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Manager sees all, staff sees own
if ($role == 'manager') {
    $sql = "SELECT la.*, u.username FROM leave_applications la JOIN users u ON la.staff_id = u.id ORDER BY u.username ASC";
    $stmt = $conn->prepare($sql);
} else if ($role == 'staff') {
    $sql = "SELECT la.*, u.username FROM leave_applications la JOIN users u ON la.staff_id = u.id WHERE la.staff_id = ? ORDER BY la.start_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Report</title>
</head>
<body>

<h2>Leave Report</h2>

<table border="1">
    <tr>
        <th>Staff</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Reason</th>
        <th>Status</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) : ?>
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['start_date']) ?></td>
            <td><?= htmlspecialchars($row['end_date']) ?></td>
            <td><?= htmlspecialchars($row['reason']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
        </tr>
    <?php endwhile; ?>

</table>

<br>
<a href="menu.php">Back to Menu</a>

</body>
</html>