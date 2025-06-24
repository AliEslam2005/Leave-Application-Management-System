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
    <link rel="stylesheet" href="style.css">
</head>
<body>

    
    <?php
    
        echo "<h1 id='menuIntro'>Welcome " . $_SESSION['username'] . " | Current status: " . $_SESSION['role'] . "</h1>";
        echo '<div class="menuContainer">';
        if($_SESSION['role'] == "admin"){

            echo "<a href='admin_user.php'><img src='./assets/manage.png' class='menuLogo'><br>Manage users</a>";
        }

        if($_SESSION['role'] == "manager"){

            echo "<a href='leave_requests.php'><img src='./assets/application.png' class='menuLogo'><br>View Leave Applications</a>";
            echo "<a href='report.php'><img src='./assets/report.png' class='menuLogo' style='margin-left: 50px'><br>Leave reports</a>";
        }

        if($_SESSION['role'] == "staff"){

            echo "<a href='apply_leave.php'><img src='./assets/applyLeave.png' class='menuLogo'><br>Apply for leave</a>";
            echo "<a href='leave_status.php'><img src='./assets/checking.png' class='menuLogo'><br>Check leave status</a>";
        }

        echo "<a href='profile.php'><img src='./assets/profile.png' class='menuLogo'><br>Profile</a>";
        echo "<a href='logout.php'>Logout</a>";
        

    ?>
    
    </div>
    
    <div class="footer">
        <span class="copyright">© 2025. All rights reserved.</span>
    </div>



</body>
</html>