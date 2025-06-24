<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['leave_id'])) {
    $action = $_POST['action'];
    $leave_id = intval($_POST['leave_id']);
    $comment = $_POST['comment'] ?? '';

    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $conn->prepare("UPDATE leave_applications SET status=?, manager_comment=? WHERE id=?");
        $stmt->bind_param("ssi", $action, $comment, $leave_id);
        $stmt->execute();
    }
}

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
</head>
<body>
    <h2>Leave Requests</h2>
    <a href="menu.php">Back to Menu</a>
    <table border="1" cellpadding="8">
        <tr>
            <th>Staff</th>
            <th>Leave Type</th>
            <th>Date Range</th>
            <th>Reason</th>
            <th>Status</th>
            <th>Manager Comment</th>
            <th>Action</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['type_name']) ?></td>
                <td><?= $row['from_date'] ?> to <?= $row['to_date'] ?></td>
                <td><?= nl2br(htmlspecialchars($row['reason'])) ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= nl2br(htmlspecialchars($row['manager_comment'])) ?></td>
                <td>
                    <?php if ($row['status'] == 'pending'): ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="leave_id" value="<?= $row['id'] ?>">
                        <textarea name="comment" placeholder="Comment"></textarea><br>
                        <button name="action" value="approved">Approve</button>
                        <button name="action" value="rejected">Reject</button>
                    </form>
                    <?php else: ?>
                        (Finalized)
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
<?php
