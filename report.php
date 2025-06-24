<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Basic filtering
$search = $_GET['search'] ?? '';
$search_sql = '';
$param = '';

if (!empty($search)) {
    $search_sql = "AND u.name LIKE ?";
    $param = "%$search%";
}

$sql = "SELECT u.name, lt.type_name, la.from_date, la.to_date, la.status
        FROM leave_applications la
        JOIN users u ON la.staff_id = u.id
        JOIN leave_types lt ON la.leave_type_id = lt.id
        WHERE 1 ";

if ($role == 'staff') {
    $sql .= " AND la.staff_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} elseif ($role == 'manager' && $search_sql) {
    $sql .= " $search_sql";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $param);
} else {
    $stmt = $conn->prepare($sql);
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
    <a href="menu.php">Back to Menu</a>

    <?php if ($role === 'manager'): ?>
    <form method="GET">
        <input type="text" name="search" placeholder="Search staff..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>
    <?php endif; ?>

    <table border="1" cellpadding="8">
        <tr>
            <th>Staff</th>
            <th>Leave Type</th>
            <th>Date Range</th>
            <th>Status</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['type_name']) ?></td>
            <td><?= $row['from_date'] ?> to <?= $row['to_date'] ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
