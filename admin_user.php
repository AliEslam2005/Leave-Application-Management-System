<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$edit_mode = false;
$edit_id = '';
$username = '';
$name = '';
$email = '';
$role = 'staff';

$phone = '';
$address = '';
$department = '';

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $delete_id");
    $conn->query("DELETE FROM user_profiles WHERE user_id = $delete_id");

    $action = "Deleted user ID $delete_id";
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();

    header("Location: admin_user.php");
    exit();
}

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM users WHERE id = $edit_id");

    if ($result->num_rows > 0) {
        $edit_mode = true;
        $user = $result->fetch_assoc();
        $username = $user['username'];
        $name = $user['name'];
        $email = $user['email'];
        $role = $user['role'];

        $profile_result = $conn->query("SELECT * FROM user_profiles WHERE user_id = $edit_id");
        if ($profile_result->num_rows > 0) {
            $profile = $profile_result->fetch_assoc();
            $phone = $profile['phone'];
            $address = $profile['address'];
            $department = $profile['department'];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $department = $_POST['department'];

    if (!empty($_POST['edit_id'])) {
        $edit_id = $_POST['edit_id'];
        $conn->query("UPDATE users SET username='$username', name='$name', email='$email', role='$role' WHERE id=$edit_id");

        $check_profile = $conn->query("SELECT * FROM user_profiles WHERE user_id = $edit_id");
        if ($check_profile->num_rows > 0) {
            $conn->query("UPDATE user_profiles SET phone='$phone', address='$address', department='$department' WHERE user_id = $edit_id");
        } else {
            $conn->query("INSERT INTO user_profiles (user_id, phone, address, department) VALUES ($edit_id, '$phone', '$address', '$department')");
        }

        $action = "Edited user ID $edit_id";
    } else {
        $check = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
        if ($check->num_rows > 0) {
            header("Location: admin_user.php");
            exit();
        }

        $password = md5($_POST['password']);
        $stmt = $conn->prepare("INSERT INTO users (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $name, $email, $role);
        $stmt->execute();
        $new_id = $conn->insert_id;

        $conn->query("INSERT INTO user_profiles (user_id, phone, address, department) VALUES ($new_id, '$phone', '$address', '$department')");

        $action = "Added new user ID $new_id";
    }

    $log = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $log->bind_param("is", $user_id, $action);
    $log->execute();

    header("Location: admin_user.php");
    exit();
}

if (isset($_GET['log']) && $_GET['log'] === 'back_to_menu') {
    $action = "Back to menu from admin_user";
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
    header("Location: menu.php");
    exit();
}
?>

<?php
if ($edit_mode) {
    $heading = "Edit User";
} else {
    $heading = "Add User";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .adminContainer {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background-color: rgb(179, 255, 230);

            border-radius: 25px;
            border: 5px outset;
            border-top-color: rgb(90, 189, 156);
            border-left-color: rgb(90, 189, 156);
            border-right-color: rgb(90, 189, 156);
            border-bottom-color: rgb(90, 189, 156);
        }

        .adminContainer h2,
        .adminContainer h3 {
            color: #00448d;
            font-weight: 900;
            text-align: center;
        }

        form label {
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="email"],
        form input[type="password"],
        form select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 10px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 25px;
        }

        button:active {
            background: #00448d;
        }

        a {
            color: #035daf;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .cancel-link {
            display: inline-block;
            margin-left: 10px;
            color: red;
        }

        .back-link {
            margin-top: 15px;
            display: block;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            background-color: rgb(255, 255, 255);
        }

        th {
            background-color: #f2f2f2;
        }

        .actions a {
            margin-right: 10px;
        }
    </style>
</head>

<body>

<div class="adminContainer">
    <h2><?php echo $heading; ?></h2>

    <form method="POST">
        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

        <label>Username:</label>
        <input type="text" name="username" value="<?php echo $username; ?>" required>

        <label>Name:</label>
        <input type="text" name="name" value="<?php echo $name; ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $email; ?>" required>

        <?php if (!$edit_mode): ?>
            <label>Password:</label>
            <input type="password" name="password" required>
        <?php endif; ?>

        <label>Role:</label>
        <select name="role">
            <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="manager" <?php if ($role == 'manager') echo 'selected'; ?>>Manager</option>
            <option value="staff" <?php if ($role == 'staff') echo 'selected'; ?>>Staff</option>
        </select>

        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo $phone; ?>">

        <label>Address:</label>
        <input type="text" name="address" value="<?php echo $address; ?>">

        <label>Department:</label>
        <input type="text" name="department" value="<?php echo $department; ?>">

        <button type="submit"><?php echo $edit_mode ? "Update User" : "Add User"; ?></button>

        <?php if ($edit_mode): ?>
            <a class="cancel-link" href="admin_user.php">Cancel</a>
        <?php endif; ?>
    </form>

    <a class="back-link" href="admin_user.php?log=back_to_menu">Back to Menu</a>

    <h3>All Users</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>

        <?php
        $result = $conn->query("SELECT * FROM users");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . ucfirst($row['role']) . "</td>";
            echo "<td class='actions'>";
            echo "<a href='?edit=" . $row['id'] . "'>Edit</a> | ";
            echo "<a href='?delete=" . $row['id'] . "' onclick=\"return confirm('Are you sure?');\">Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>
<div class="footer">
        <span class="copyright">© 2025. All rights reserved.</span>
    </div>
</body>
</html>
