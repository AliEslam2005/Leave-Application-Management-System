<?php

    session_start();

    if(!isset($_SESSION['user_id'])){

        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
</head>
<body>


    <?php
    
        echo "<h1>Welcome " . $_SESSION['username'] . " - " . $_SESSION['role'] . "</h1>";

        if($_SESSION['role'] == "admin"){

            echo "<a href='admin_user.php'>Manage Users</a><br>";
        }

        if($_SESSION['role'] == "manager"){

            echo "<a href='leave_requests.php'>View Leave Applications</a><br>";
            echo "<a href='report.php'>Leave Reports</a><br>";
        }

        if($_SESSION['role'] == "staff"){

            echo "<a href='apply_leave.php'>Apply for Leave</a><br>";
            echo "<a href='leave_status.php'>Check Leave Status</a><br>";
        }

        echo "<br><a href='profile.php'>My Profile</a><br>";
        echo "<br><a href='logout.php'>Logout</a><br>";
        

    ?>
    
    
    

</body>
</html>