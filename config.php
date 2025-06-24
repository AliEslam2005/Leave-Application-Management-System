<?php 

    $servername = "sql106.infinityfree.com";
    $username = "if0_39066130";
    $password = "6QGPORy759";
    $database = "if0_39066130_leave_system";

        
    $conn = mysqli_connect($servername, $username, $password, $database);

        
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>