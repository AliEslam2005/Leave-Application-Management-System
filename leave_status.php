<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT la.*, lt.type_name, 
        CASE la.status 
            WHEN 'pending' THEN 'Pending' 
            WHEN 'approved' THEN 'Approved' 
            WHEN 'rejected' THEN 'Rejected' 
        END AS status_text
        FROM leave_applications la
        JOIN leave_types lt ON la.leave_type_id = lt.id
        WHERE la.staff_id = ?
        ORDER BY la.created_at DESC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Status</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-pending { color: #ff9800; font-weight: bold; }
        .status-approved { color: #4caf50; font-weight: bold; }
        .status-rejected { color: #f44336; font-weight: bold; }
        .no-records { text-align: center; padding: 20px; font-style: italic; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Leave Applications</h2>
        
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Applied On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['type_name']) ?></td>
                            <td><?= htmlspecialchars($row['from_date']) ?></td>
                            <td><?= htmlspecialchars($row['to_date']) ?></td>
                            <td><?= htmlspecialchars($row['reason']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                            <td class="status-<?= $row['status'] ?>"><?= $row['status_text'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-records">You have no leave applications yet.</div>
        <?php endif; ?>
        
        <br>
        <a href="apply_leave.php">Apply for Leave</a> | 
        <a href="menu.php">Back to Menu</a>
    </div>
</body>
</html>