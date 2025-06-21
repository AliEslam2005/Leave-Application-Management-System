<?php

    session_start();
    require_once('config.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    

    <?php 
    
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {


            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $sql = "SELECT * FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);

            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            
            if ($user && $user['password'] === md5($password)) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: menu.php");
                exit();
            } else {

                echo "Invalid username or password";
            }
        }
    
    ?>
    <h2>Login</h2>
    
    <form method="POST" action="">

        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>

</body>
</html>