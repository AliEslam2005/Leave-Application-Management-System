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
    <link rel="stylesheet" href="style.css">
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

                $user_id = $user['id'];
                $action = 'Logged in';
                $log_stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
                $log_stmt->bind_param("is", $user_id, $action);
                $log_stmt->execute();

                header("Location: menu.php");
                exit();
            } else {

                echo "Invalid username or password";
            }
        }
    
    ?>

    <div class="container">

        <div class="innerContainer">

            <h2>Login</h2>
            <form method="POST" action="">

                <input type="text" name="username" placeholder="Username" required><br><br>
                <input type="password" name="password" placeholder="Password" required><br><br>
                <button type="submit" class="clickable" id="logbtn">Login</button>
            </form>
        
        </div>


    </div>

    <div class="footer">
        <span class="copyright">© 2025. All rights reserved.</span>
    </div>
    
    
    

</body>
</html>