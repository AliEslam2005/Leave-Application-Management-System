<?php
session_start();
require_once('config.php');

// Check login and role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's leave applications
$sql = "SELECT la.id, lt.type_name, la.start_date, la.end_date, la.reason, la.status, la.manager_comment
        FROM leave_applications la
        JOIN leave_types lt ON la.leave_type_id = lt.id
        WHERE la.staff_id = ?
        ORDER BY la.start_date DESC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Status</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-pending { color: orange; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
    </style>
</head>
<body>
    <h2>My Leave Applications</h2>
    
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Leave Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Manager Comment</th>
            </tr>
            
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['type_name']) ?></td>
                    <td><?= htmlspecialchars($row['start_date']) ?></td>
                    <td><?= htmlspecialchars($row['end_date']) ?></td>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td class="status-<?= $row['status'] ?>">
                        <?= ucfirst(htmlspecialchars($row['status'])) ?>
                    </td>
                    <td><?= $row['manager_comment'] ? htmlspecialchars($row['manager_comment']) : 'N/A' ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No leave applications found.</p>
    <?php endif; ?>
    
    <br>
    <a href="apply_leave.php">Apply for Leave</a> | 
    <a href="menu.php">Back to Menu</a>
</body>
</html>