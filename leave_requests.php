<?php
session_start();
require_once('config.php');

// Only managers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

// Handle approve/reject action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['leave_id'])) {
    $action = $_POST['action'];
    $leave_id = intval($_POST['leave_id']);
    $comment = $_POST['comment'] ?? '';

    if ($action === 'approved' || $action === 'rejected') {
        $stmt = $conn->prepare("UPDATE leave_applications SET status=?, manager_comment=? WHERE id=?");
        $stmt->bind_param("ssi", $action, $comment, $leave_id);
        $stmt->execute();
    }
}

// Fetch leave requests
$sql = "SELECT la.id, u.name, lt.type_name, la.from_date, la.to_date, la.reason, la.status, la.manager_comment
        FROM leave_applications la
        JOIN users u ON la.staff_id = u.id
        JOIN leave_types lt ON la.leave_type_id = lt.id
        ORDER BY la.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Requests</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {font-family: Arial, sans-serif; background-color: aquamarine;}
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; background-color: rgb(255, 255, 255);}
        th { background-color: #f2f2f2; }
        textarea { width: 100%; height: 60px; }
        main {flex: 1; padding: 20px;}
    </style>

    
</head>
<body>

<main>
<h2>Leave Requests</h2>
<a href="menu.php">Back to Menu</a>

<table>
    <tr>
        <th>Staff</th>
        <th>Leave Type</th>
        <th>Date Range</th>
        <th>Reason</th>
        <th>Status</th>
        <th>Manager Comment</th>
        <th>Action</th>
    </tr>

    <?php
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['type_name'] . '</td>';
        echo '<td>' . $row['from_date'] . ' to ' . $row['to_date'] . '</td>';
        echo '<td>' . nl2br($row['reason']) . '</td>';
        echo '<td>' . $row['status'] . '</td>';
        echo '<td>' . nl2br($row['manager_comment']) . '</td>';

        echo '<td>';
        if ($row['status'] === 'pending') {
            echo '<form method="POST">';
            echo '<input type="hidden" name="leave_id" value="' . $row['id'] . '">';
            echo '<textarea name="comment" placeholder="Comment"></textarea><br>';
            echo '<button name="action" value="approved">Approve</button> ';
            echo '<button name="action" value="rejected">Reject</button>';
            echo '</form>';
        } else {
            echo '(Finalized)';
        }
        echo '</td>';

        echo '</tr>';
    }
    ?>

</table>
</main>
<div class="footer">
        <span class="copyright">© 2025. All rights reserved.</span>
</div>
</body>
</html>
