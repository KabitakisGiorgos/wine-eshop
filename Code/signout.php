<?php
    session_start();
    
    if(session_destroy()){
        session_unset();
        // $_SESSION = [];
        // echo $_SESSION['login_user'].$_SESSION['isMerchant'];
        header("Location: index.php");
    }
?>