<?php
     if(isset($_POST["returnWInes"])){
        include("connect.php");//
        
        $wines_amount_q="SELECT p_id_FK,amount FROM winesdb.contains WHERE ο_id_FK=".$_POST["o_id"].";";
        $result=mysqli_query($conn,$wines_amount_q);
        while($row=$result->fetch_assoc()){

            $wines_update_q="UPDATE winesdb.wines SET saleNo=saleNo-".$row["amount"]." WHERE p_id=".$row["p_id_FK"].";";
            if (!mysqli_query($conn,$wines_update_q))
            {
                echo("Error description: " . mysqli_error($conn));
            }
        }
        $conn->close();
    }
    
//     if(isset($_POST["deleteOrder"])){
//         include("connect.php");//edo kaleitai otan adeiasei entelos i paraggelia apo krasia kai diagrafete i paraggelia apo tin basi olokliri
//         $payments_q="DELETE FROM winesdb.payments WHERE ο_id_FK=".$_POST['o_id'].";";
//         mysqli_query($conn,$payments_q);//diagrafete entelos i kataxorisi contains kai ta payments
// //ara ayto mallon den tha s xreiastei
//         $delete_contains_q="DELETE FROM winesdb.contains WHERE ο_id_FK=".$_POST["o_id"].";";
//         mysqli_query($conn,$delete_contains_q);
        
//         $delete_order_q="DELETE FROM winesdb.orders WHERE o_id=".$_POST['o_id'].";";
//         mysqli_query($conn,$delete_order_q);

//         $conn->close();
//     }

    if(isset($_POST["updateOrder"])){
        include("connect.php");
        //echo $_POST["amount"]."-".$_POST["substracted"];
        if($_POST["amount"]==0){
            //delete the product from contains 
            $delete_contains_q="DELETE FROM winesdb.contains WHERE ο_id_FK=".$_POST["o_id"]." AND p_id_FK=".$_POST["p_id"].";";
            mysqli_query($conn,$delete_contains_q);
        }else{
            $update_contains_q="UPDATE winesdb.contains SET amount=amount-".$_POST["substracted"]." WHERE ο_id_FK=".$_POST["o_id"]." AND p_id_FK=".$_POST["p_id"].";";
            mysqli_query($conn,$update_contains_q);
            //update the product from contains
        } 
        $wines_update_q="UPDATE winesdb.wines SET saleNo=saleNo-".$_POST["substracted"]." WHERE p_id=".$_POST["p_id"].";";
        if (!mysqli_query($conn,$wines_update_q))
        {
            echo("Error description: " . mysqli_error($conn));
        }
        //update wines amount
       // echo "all good";
        $orderWinesAmount=0;
        $orderPrice=0;//edo ipologizo tin nea timi tis paraggelias kai to discount ean exei
        $wines_prices_q="";
        $contains_wines_q="SELECT p_id_FK,amount FROM winesdb.contains WHERE ο_id_FK=".$_POST["o_id"].";";
        $result=mysqli_query($conn,$contains_wines_q);
        while($row=$result->fetch_assoc()){
            $orderWinesAmount=$orderWinesAmount+$row["amount"];
            if($_POST["isMerchant"]==1){
                $wines_prices_q="SELECT wholesale FROM winesdb.wines WHERE p_id=".$row["p_id_FK"].";";
                $row2=mysqli_query($conn,$wines_prices_q)->fetch_assoc();
                $orderPrice=$orderPrice+$row2["wholesale"]*$row["amount"];
            }else{
                $wines_prices_q="SELECT retail FROM winesdb.wines WHERE p_id=".$row["p_id_FK"].";";
                $row2=mysqli_query($conn,$wines_prices_q)->fetch_assoc();
                $orderPrice=$orderPrice+$row2["retail"]*$row["amount"];
            }    
        }
        if($_POST["isMerchant"]==1){
            if($orderWinesAmount>22 && $orderWinesAmount<25 ){
                $orderPrice=$orderPrice-0.05*$orderPrice;
            }else if($orderWinesAmount>=25){
                $orderPrice=$orderPrice-0.1*$orderPrice;
            }else{
                echo $orderPrice;
            }
        }else{
            echo $orderPrice;
        }
        $update_order_price_q="UPDATE winesdb.orders SET amount=".$orderPrice." WHERE o_id=".$_POST["o_id"].";";
        mysqli_query($conn,$update_order_price_q);
       
        //calculate order new price see if merchant or user and make the sale
        $conn->close();
    }

    if(isset($_POST["createReturn"])){
         
        $today=date("Y/m/d");
        $today=date_create($today);
        include("connect.php");
        $insert_return_q="INSERT INTO winesdb.returns (c_id_FK,ο_id_FK,date) VALUES (".$_POST["c_id"].",".$_POST["o_id"].",'".$today->format('Y-m-d')."');";
        if (!mysqli_query($conn,$insert_return_q))
        {
            echo("Error description: " . mysqli_error($conn));
        }
        //header("Location: products.php");
        $conn->close();
    }
?>