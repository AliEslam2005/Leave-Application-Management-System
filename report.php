<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$search = '';
$search_sql = '';
$param = '';

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    if (!empty($search)) {
        $search_sql = "AND u.name LIKE ?";
        $param = "%" . $search . "%";
    }
}

$sql = "SELECT u.name, lt.type_name, la.from_date, la.to_date, la.status
        FROM leave_applications la
        JOIN users u ON la.staff_id = u.id
        JOIN leave_types lt ON la.leave_type_id = lt.id
        WHERE 1";

if ($role === 'staff') {
    $sql .= " AND la.staff_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} elseif ($role === 'manager' && $search_sql) {
    $sql .= " " . $search_sql;
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
    
    <style>
        body { font-family: Arial, sans-serif; background-color: aquamarine;}
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; background-color: rgb(255, 255, 255); }
        th { background-color: #f2f2f2; }
        input[type="text"] { padding: 5px; width: 200px; }
        button { padding: 6px 12px; cursor: pointer;}
        main {flex: 1; padding: 20px;}
    </style>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main>
<h2>Leave Report</h2>
<a href="menu.php">Back to Menu</a>
<br><br>

<?php
if ($role === 'manager') {
    echo '<form method="GET">';
    echo '<input type="text" name="search" placeholder="Search staff..." value="' . $search . '"> ';
    echo '<button type="submit">Search</button>';
    echo '</form><br>';
}
?>

<table>
    <tr>
        <th>Staff</th>
        <th>Leave Type</th>
        <th>Date Range</th>
        <th>Status</th>
    </tr>

    <?php
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['type_name'] . '</td>';
        echo '<td>' . $row['from_date'] . ' to ' . $row['to_date'] . '</td>';
        echo '<td>' . $row['status'] . '</td>';
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
