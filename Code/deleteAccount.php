<?php
    include("session.php");
    if(isset($_POST['c_id'])){
        if($_POST['debt']==0){
            include('connect.php');

            $payments_q="DELETE FROM winesdb.payments WHERE c_id_FK=".$_POST['c_id'].";";
            mysqli_query($conn,$payments_q);
        
            $returns_q="DELETE FROM winesdb.returns WHERE c_id_FK=".$_POST['c_id'].";";
            mysqli_query($conn,$returns_q);
        
            $clientsOrders="SELECT o_id FROM winesdb.orders WHERE c_id_FK=".$_POST['c_id'].";";
            $result=mysqli_query($conn,$clientsOrders);
            
            while($row=$result->fetch_assoc()){
                $delete_contains_q="DELETE FROM winesdb.contains WHERE ο_id_FK=".$row["o_id"].";";
                mysqli_query($conn,$delete_contains_q);
            }

            $delete_orders_q="DELETE FROM winesdb.orders WHERE c_id_FK=".$_POST['c_id'].";";
            mysqli_query($conn,$delete_orders_q);
            
            $client_q="DELETE FROM winesdb.clients WHERE c_id=".$_POST['c_id'].";";
            mysqli_query($conn,$client_q);
            if($_SESSION["isMerchant"]){
                $client_q="DELETE FROM winesdb.merchants WHERE c_id=".$_POST['c_id'].";";
                mysqli_query($conn,$client_q);
            }else{
                $client_q="DELETE FROM winesdb.users WHERE c_id=".$_POST['c_id'].";";
                mysqli_query($conn,$client_q);
            }
            $conn->close();
            echo "1";
        }else{
            echo "0";
        }
    }
?>