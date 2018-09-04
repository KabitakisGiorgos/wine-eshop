<?php 
    if(isset($_POST["newOrder"])){
        
        $today=date("Y/m/d");
        $today=date_create($today);
        include("connect.php");
       
        $newOrder_q="INSERT INTO winesdb.orders(c_id_FK,date,state_credited,state_paid,amount) VALUES (".$_POST["c_id"].",'".$today->format('Y-m-d')."','1','0',".$_POST["amount"].")";
        $result=mysqli_query($conn,$newOrder_q);
        echo  mysqli_insert_id($conn);//we need order id for contains
            
        $updateDebt_q="UPDATE winesdb.clients SET debt=debt+".$_POST['amount']." WHERE c_id=".$_POST["c_id"].";";
        $result=mysqli_query($conn,$updateDebt_q);
        
        $conn->close();
    }

    if(isset($_POST["canOrder"])){
        include("connect.php");
        $canOrder=true;
        $today=date("Y/m/d");
        $today=date_create($today);
        $checkOrders_q="SELECT date FROM winesdb.orders WHERE state_paid=false AND  c_id_FK=".$_POST["c_id"].";";
        $result=mysqli_query($conn,$checkOrders_q);
         //query to take all his unpaid orders and check their date
        while($row=$result->fetch_assoc()){
            $orderdate=date_create($row["date"]);
            $interval=date_diff($orderdate,$today);
            $diff=$interval->format('%a');
            $diff=intval($diff);
            if($diff>=10){
                $canOrder=false;
                break;
            }
        }
        if($canOrder){
            echo "1";
        }else {
            echo "0";
        }
        $conn->close();
    }

    if(isset($_POST["minimumOrder"])){
        include("session.php");
        $amount=0;
        $counter=0;
        for($i=0;$i<count($_SESSION['p_id']);$i++)
        {
            if($_SESSION["amount"][$i]>=6){
                $counter++;
            }
        }
        if($counter<3){
            echo '0';
        }else{
            echo '1';
        }
    }

    if(isset($_POST["containsAdd"])){
        include("connect.php");
        $newContains_q="INSERT INTO winesdb.contains (p_id_FK,ο_id_FK,amount) VALUES(".$_POST['p_id'].",".$_POST['o_id'].",".$_POST['amount'].");";
       

        // Perform a query, check for error
        if (! mysqli_query($conn,$newContains_q))
        {
        echo("Error description: " . mysqli_error($conn));
        }
        //echo "all good";
        $conn->close();
    }
?>