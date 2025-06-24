<?php
session_start();

require_once('config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


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

    } else {
        
        $password = md5($_POST['password']);
        $stmt = $conn->prepare("INSERT INTO users (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $name, $email, $role);
        $stmt->execute();
        $new_id = $conn->insert_id;

        $conn->query("INSERT INTO user_profiles (user_id, phone, address, department) VALUES ($new_id, '$phone', '$address', '$department')");
    }

    header("Location: admin_user.php");
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
<h2><?php echo $heading; ?></h2>

<form method="POST">
    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

    <label>Username:</label><br>
    <input type="text" name="username" value="<?php echo $username; ?>" required><br><br>

    <label>Name:</label><br>
    <input type="text" name="name" value="<?php echo $name; ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" value="<?php echo $email; ?>" required><br><br>

    <?php
    if (!$edit_mode) {
        echo '<label>Password:</label><br>';
        echo '<input type="password" name="password" required><br><br>';
    }
    ?>

    <label>Role:</label><br>
    <select name="role">
        <?php
        if ($role == 'admin') {

            echo '<option value="admin" selected>Admin</option>';
            echo '<option value="manager">Manager</option>';
            echo '<option value="staff">Staff</option>';
        } elseif ($role == 'manager') {

            echo '<option value="admin">Admin</option>';
            echo '<option value="manager" selected>Manager</option>';
            echo '<option value="staff">Staff</option>';
        } else {

            echo '<option value="admin">Admin</option>';
            echo '<option value="manager">Manager</option>';
            echo '<option value="staff" selected>Staff</option>';
        }
    ?>
    </select><br><br>

    
    <label>Phone:</label><br>
    <input type="text" name="phone" value="<?php echo $phone; ?>"><br><br>

    <label>Address:</label><br>
    <input type="text" name="address" value="<?php echo $address; ?>"><br><br>

    <label>Department:</label><br>
    <input type="text" name="department" value="<?php echo $department; ?>"><br><br>

    <button type="submit"><?php echo $edit_mode ? "Update User" : "Add User"; ?></button>

    <?php if ($edit_mode): ?>
        <a href="admin_user.php">Cancel</a>
    <?php endif; ?>
</form>

<h3>All Users</h3>
<table border="1" cellpadding="10" style="border-collapse: collapse;">
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
        echo "<td>";
        echo "<a href='?edit=" . $row['id'] . "'>Edit</a> | ";
        echo "<a href='?delete=" . $row['id'] . "' onclick=\"return confirm('Are you sure?');\">Delete</a>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
</table>
