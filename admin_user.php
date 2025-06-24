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

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $delete_id");
    header("Location: admin_user.php");
    exit();
}

if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM users WHERE id = $edit_id");

    if ($result->num_rows > 1) {
        $edit_mode = true;
        $user = $result->fetch_assoc();
        $username = $user['username'];
        $name = $user['name'];
        $email = $user['email'];
        $role = $user['role'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if (!empty($_POST['edit_id'])) {

        $edit_id = $_POST['edit_id'];
        $conn->query("UPDATE users SET username='$username', name='$name', email='$email', role='$role' WHERE id=$edit_id");
    } else {

        $password = md5($_POST['password']);
        $stmt = $conn->prepare("INSERT INTO users (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $password, $name, $email, $role);
        $stmt->execute();
    }

    header("Location: admin_user.php");
    exit();
}
?>

<?php
echo '<h2>';
    
    if ($edit_mode) {
        echo "Edit User";
    } else {
        echo "Add User";
    }
    
echo '</h2>';
?>

<form method="POST">
    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

    <label>Username:</label><br>
    <input type="text" name="username" placeholder="Username" value="<?php echo $username; ?>" required><br><br>

    <label>Name:</label><br>
    <input type="text" name="name" placeholder="Name" value="<?php echo $name; ?>" required><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" placeholder="Email" value="<?php echo $email; ?>" required><br><br>

    <?php
    if (!$edit_mode) {
        echo '<label>Password:</label><br>';
        echo '<input type="password" name="password" placeholder="Password" required><br><br>';
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

    <button type="submit">
        <?php
        if ($edit_mode) {
            echo "Update User";
        } else {
            echo "Add User";
        }
        ?>
    </button>

    <?php
    if ($edit_mode) {
        echo '<a href="admin_user.php">Cancel</a>';
    }
    ?>
</form>

<h3>All Users</h3>
<table border="1" cellpadding="10" style = "border-collapse: collapse">
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
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>";
        echo "<a href='?edit=" . $row['id'] . "'>Edit</a> | ";
        echo "<a href='?delete=" . $row['id'] . "' onclick=\"return confirm('Are you sure?');\">Delete</a>";
        echo "</td>";
        echo "</tr>";
    }
    ?>
</table>
