<?php 
    if(isset($_POST['newPayment'])){
        $today=date("Y/m/d");
        $today=date_create($today);

        include("connect.php");
        include("session.php");
        $amount=0;//to sinoliko poso poy exei plirosei mexri tora
        $check_payments_q="SELECT amount FROM winesdb.payments WHERE ο_id_FK=".$_POST["o_id"].";";
        $result=mysqli_query($conn,$check_payments_q);

        while($row=$result->fetch_assoc()){
            $amount=$amount+$row["amount"];
        }

        if($_POST["amount"]+$amount>$_POST["order_price"]){
            echo "-1";
        }else{
            if($_POST["amount"]+$amount==$_POST["order_price"]){
                $order_q="UPDATE winesdb.orders SET state_paid='1' WHERE o_id=".$_POST["o_id"].";";
                mysqli_query($conn,$order_q);
                $p_id_q="SELECT p_id_FK,amount FROM winesdb.contains WHERE ο_id_FK=".$_POST["o_id"].";";
                $result=mysqli_query($conn,$p_id_q);
                // if (!)
                // {
                //     echo("Error description: " . mysqli_error($conn));
                // }
                while($row=$result->fetch_assoc()){
                    $update_wines_saleNO_q="UPDATE winesdb.wines SET saleNo=saleNo+".$row["amount"]." WHERE p_id=".$row["p_id_FK"].";";
                    mysqli_query($conn,$update_wines_saleNO_q);
                }
            }
            $new_payment_q="INSERT INTO winesdb.payments(c_id_FK,ο_id_FK,amount,date) VALUES(".$_SESSION["login_user"].",".$_POST["o_id"].",".$_POST["amount"].",'".$today->format('Y-m-d')."');";
            if (!mysqli_query($conn,$new_payment_q))
            {
                echo("Error description: " . mysqli_error($conn));
            }
            $updateDebt_q="UPDATE winesdb.clients SET debt=debt-".$_POST['amount']." WHERE c_id=".$_SESSION["login_user"].";";
            mysqli_query($conn,$updateDebt_q);
        }

        
        $conn->close();
    }  
?>