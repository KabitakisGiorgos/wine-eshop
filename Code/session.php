<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "winesdb";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    mysqli_set_charset($conn, "utf8");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    session_start();
    
    // $check=$_SESSION['login_user'];

    if(!isset($_SESSION['login_user'])){
        header("location:index.php");
     }
     else{
        //  echo $_SESSION['login_user'].$_SESSION['isMerchant'];
     }
  
?>