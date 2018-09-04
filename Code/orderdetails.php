<!DOCTYPE html>
<html lang="en">
<head>
  <title>Product Details Page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="./css/styles.css">    
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="#">Logo</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li class="active"><a href="products.php">Home</a></li>
        <li><a href="popular.php">Popular Products</a></li>
        <li><a href="customers.php">Customers</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="cart.php"><span class="glyphicon glyphicon-shopping-cart"></span> Cart </a></li>          
        <li><a href="account.php"><span class="glyphicon glyphicon-user"></span> Your Account</a></li>
        <li><a href="signout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<?php
  $counter=0;
  include("session.php");
  include("connect.php");
  $order_q="SELECT * FROM winesdb.orders WHERE o_id=".$_GET['o_id'].";";
  $result=mysqli_query($conn,$order_q);
  $conn->close();
  $row=$result->fetch_assoc();
  $orderPrice=$row["amount"];
?>
<script>
  function NewPayment(){
    var amount=document.getElementById("amount").value;
    if(amount>0){
      $.ajax({
        type:'post',
        url:'NewPayment.php',
        data:{
          newPayment:"1",
          o_id:<?php echo $_GET['o_id']; ?>,
          amount:amount,
          order_price:<?php echo $row["amount"]; ?>,
        },
        success:function(response){
          //console.log(response);
          if(response=="-1"){
            document.getElementById("errorMes").innerHTML="Someone is in spending MOOD -_-";
          }else{
            location.reload();
          }
        }
      });
    }else{
      // Nothing cant pay with wrong inputs
      document.getElementById("errorMes").innerHTML="Dont Troll plz!!";
    }
  }

  function SubstractItem(no){
     if(document.getElementById("amount"+no).innerHTML>0){
      document.getElementById("amount"+no).innerHTML=document.getElementById("amount"+no).innerHTML-1;
      document.getElementById("returnOrder").disabled=false;
      document.getElementById("substracted"+no).innerHTML=parseInt(document.getElementById("substracted"+no).innerHTML)+1;
     }
  }
</script>

<div id="body">
<div class="container">    
  <div class="row">
    <div class="col-sm-12 detailsdiv">
        <h3>Order Info</h3>
        <div class="pull-right" style="width:20%;" >
          <?php 
            if($row["state_paid"]){
              echo '<button id="returnOrder" class="btn btn-danger pull-right" style="display:block" onclick="ReturnItems();" disabled>Return Order</button>';
            }else{
              echo ' <input id="amount" class="form-control"type="number">
              <button onclick="NewPayment();"type="submit" class="btn btn-success pull-right" style="display:block">Pay Order</button> 
              <div style="color:red;" id="errorMes"></div>';
            }     
          ?>
         </div>
        <div>
            <h4>Id :</h4><?php echo $row["o_id"];?>                             
        </div>
        <div>
            <h4>Date :</h4><?php echo $row["date"];?>                  
        </div>
        <div>
            <h4>State :</h4><?php if($row["state_paid"]){
                        echo "Paid";
                    }else{
                        echo "Pending";
                    }?>                            
        </div>
        <div>
            <h4 style="display:inline-block;">Amount :</h4><?php echo $row["amount"]." ";?><p style="display:inline-block;color:red;" id="discMessage"></p>                                  
        </div>
        <h3>Products</h3>
        <ul class="list-group cartul" style="width:40%">
            <li id="header" class="list-group-item">
                <h5 id="name">Name</h5>
                <h5 id="amount">No Of Products</h5>
               <?php  if($row["state_paid"]){
                 echo '<div class="circle2 pull-right" onclick="location.reload();";>
                    <img style="width:20px; margin-left:5.5px; margin-top:6px;" src="https://png.icons8.com/material/100/333333/synchronize.png">
                </div>}';}
                $state=$row["state_paid"];
                ?>
                
            </li>
            <?php 
              include("connect.php");
              $products_q="SELECT p_id_FK,amount FROM winesdb.contains WHERE ο_id_FK=".$_GET['o_id'].";";
              $result=mysqli_query($conn,$products_q);
              
              while($row=$result->fetch_assoc()){
                //for each p_id bring the name
                $names_q="SELECT name FROM winesdb.wines WHERE p_id=".$row['p_id_FK'].";";
                $names=mysqli_query($conn,$names_q);
                $row2=$names->fetch_assoc();
                echo '<li class="list-group-item">
                        <div style="display:none;"id="p_id'.$counter.'">'.$row['p_id_FK'].'</div>
                        <div style="display:none;"id="substracted'.$counter.'">0</div>
                        <h5 id="name">'.$row2["name"].'</h5>
                        <h5 id="amount'.$counter.'">'.$row["amount"].'</h5>';
                        if($state){
                          echo '<div class="circle pull-right" onclick="SubstractItem('.$counter.')";>
                          <img style="width:20px; margin-left:5.5px; margin-top:6px;" src="https://png.icons8.com/material/100/ffffff/minus.png">
                        </div>';
                        }
                      echo '</li>';
                      $counter++;
              }
              $conn->close();
              if($_SESSION["isMerchant"]){
                echo '<script>
              var discountCheck=0;
              for(var i=0;i<'.$counter.';i++){
                discountCheck=discountCheck+parseInt(document.getElementById("amount"+i).innerHTML);
              }
              if(discountCheck<22){
                document.getElementById("discMessage").innerHTML="No discount was applied";
              }else if(discountCheck<25){
                document.getElementById("discMessage").innerHTML="5% discount was applied";
              }else{
                document.getElementById("discMessage").innerHTML="10% discount was applied";
              }
              
              
              </script>';
              }
              
            ?>
            
        </ul>
        <h3>Payments</h3>
        <?php
            include("connect.php");  
            $mindate=1901;
            $maxdate=1901;
            $validateform=true;
            $dateErr=""; 
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                if(isset($_GET["mindate"]) || isset($_GET["maxdate"]))
                {            
                  $o_id = test_input($_GET["o_id"]);
                  $mindate = test_input($_GET["mindate"]);
                  $maxdate = test_input($_GET["maxdate"]); 
                  
                  if(!empty($_GET["mindate"]) && !empty($_GET["maxdate"])){                  
                    if ($mindate>$maxdate) {
                      $dateErr = "Min Date needs to be smaller than Max"; 
                      $validateform = false;
                    }
                  }

                  if($validateform){
                    $pay_filter_q="SELECT * FROM winesdb.payments WHERE ο_id_FK=$o_id";
                    
                    if(!empty($_GET["mindate"])){
                      $pay_filter_q.=" AND date>='$mindate'";
                    }           
                    if(!empty($_GET["maxdate"])){
                      $pay_filter_q.=" AND date<='$maxdate'";
                    }  
                    echo '<script>';
                    echo 'console.log('. json_encode( $pay_filter_q ) .')';
                    echo '</script>';         
                    $pay_filtered=mysqli_query($conn,$pay_filter_q);                
                  }    
                }
              }
              function test_input($data) {
                $data = trim($data);
                $data = stripslashes($data);
                $data = htmlspecialchars($data);
                return $data;
              }
            
            
        ?>
        <form method="get" action='orderdetails.php'>
          <div class="filter-group form-group">
          <input type="hidden" value="<?php echo $_GET["o_id"] ?>" name="o_id" />
            <label style="display:block;">Date</label>
            <label>Min</label>
            <input name="mindate" class="form-control" type="date" style="width:15%; display:inline-block;">
            <label>Max</label>
            <input name="maxdate" class="form-control" type="date" style="width:15%; display:inline-block;">
            <div class="Error"> <?php echo $dateErr;?></div>
            <button id="filterSubmit" class="btn btn-info" style="margin-top:10px;">Get Payments</button>
          </div>
        </form>
        <ul class="list-group accountul">
            <!-- List Header -->
            <li id="header" class="list-group-item">
                <h5 id="id">Pay_Id</h5>
                <h5 id="date">Date</h5>
                <h5 id="amount">Amount</h5>           
            </li>
            <!-- To apo katw pare mono gia dunamiko create -->
            <!-- ANTEEEEEEEEE RE POY ME DOYLEVEIS KIOLAS KATALATHOS TA SBISA -_- -->
            <!-- HAHHAHAH Eklapsa x'D -->
            <?php 
              include("connect.php");
              $orders_payments_q="SELECT * FROM winesdb.payments WHERE ο_id_FK=".$_GET['o_id'].";";
              $orders_payments=mysqli_query($conn,$orders_payments_q);
              $conn->close();
              if(!isset($_GET["mindate"]) && !isset($_GET["maxdate"]))
              {                
                while($row=$orders_payments->fetch_assoc()){
                  echo '<li id="listitem" class="list-group-item">
                          <h5 id="id">'.$row["pay_id"].'</h5>
                          <h5 id="date">'.$row["date"].'</h5>
                          <h5 id="amount">'.$row["amount"].'</h5>             
                        </li>
                  ';
                }
              }
              else{
                if(!empty($_GET["mindate"]) || !empty($_GET["maxdate"])){
                  while($row=$pay_filtered->fetch_assoc()){
                    echo '<li id="listitem" class="list-group-item">
                            <h5 id="id">'.$row["pay_id"].'</h5>
                            <h5 id="date">'.$row["date"].'</h5>
                            <h5 id="amount">'.$row["amount"].'</h5>             
                          </li>
                    ';
                  }
                }
                else{
                  while($row=$orders_payments->fetch_assoc()){
                    echo '<li id="listitem" class="list-group-item">
                            <h5 id="id">'.$row["pay_id"].'</h5>
                            <h5 id="date">'.$row["date"].'</h5>
                            <h5 id="amount">'.$row["amount"].'</h5>             
                          </li>
                    ';
                  }
                }
              }
            ?>
        </ul>
    </div>
  </div>
</div><br><br>
</div>
<footer class="footer">
  <p>Online Store Copyright</p>  
  <p>George Mavridis, George Kampitakis, George Todoulakis</p>
</footer>
<script>

   function MerchantCantReturn(){
    var productsNo=<?php echo $counter?>;
    var counterW=0;
    for(var i=0;i<productsNo;i++){
      if(document.getElementById("amount"+i).innerHTML>=6){
        counterW++;
      }
    }
    if(counterW<3){
      alert("You dont meet merchants minimum order requirements");
      return 0;
    }else return 1;
   }

   function ReturnItems(){
    var productsNo=<?php echo $counter?>;
    var orderPrice=<?php echo $orderPrice;?>;
    var amount=0,SendAmount=0;
    for(var i=0;i<productsNo;i++){
      amount=amount+document.getElementById("amount"+i).innerHTML;
    }
    if(amount==0){//delete hole order 
      
      $.ajax({
        type:'post',
        url:'returnOrder.php',
        data:{
          returnWInes:'1',
          o_id:<?php echo $_GET['o_id'];?>,
        },
        success:function(response){
          console.log(response);
        }
        });
      // $.ajax({
      //   type:'post',
      //   url:'returnOrder.php',
      //   data:{
      //     deleteOrder:'1',
      //     o_id:<?//php echo $_GET['o_id'];?>,
      //   },
      //   success:function(response){
      //     alert("We deposited "+orderPrice+"€ to your account");
      //     window.location.replace("account.php");
      //   }
        // });
        <?php
        $userType=0;
        if($_SESSION["isMerchant"]){
          $userType=1;
        }
         for($i=0;$i<$counter;$i++){//Here fix the loop to send requests
            echo '
            $.ajax({
          type:"post",
          url:"returnOrder.php",
          data:{
            updateOrder:"1",
            o_id:'.$_GET['o_id'].',
            p_id:document.getElementById("p_id'.$i.'").innerHTML,
            substracted:document.getElementById("substracted'.$i.'").innerHTML,
            amount:document.getElementById("amount'.$i.'").innerHTML,
            orderPrice:orderPrice,
            isMerchant:'.$userType.'
          },
          success:function(response){
            console.log(response); 
          }
          });';
         
         }
      ?>
       $.ajax({
        type:'post',
        url:'returnOrder.php',
        data:{
          createReturn:'1',
          o_id:<?php echo $_GET['o_id'];?>,
          c_id:<?php echo $_SESSION["login_user"] ?>,
        },
        success:function(response){
          console.log(response);
        }
        });
        window.location = 'account.php';
    }else{//substract the amounts if a procuct zero delete it from contains 
      <?php
        $userType=0;
        if($_SESSION["isMerchant"]){
          $userType=1;
          echo 'if(!MerchantCantReturn()){
            return;
          }';
        }
         for($i=0;$i<$counter;$i++){//Here fix the loop to send requests
            echo '
            $.ajax({
          type:"post",
          url:"returnOrder.php",
          data:{
            updateOrder:"1",
            o_id:'.$_GET['o_id'].',
            p_id:document.getElementById("p_id'.$i.'").innerHTML,
            substracted:document.getElementById("substracted'.$i.'").innerHTML,
            amount:document.getElementById("amount'.$i.'").innerHTML,
            orderPrice:orderPrice,
            isMerchant:'.$userType.'
          },
          success:function(response){
            console.log(response); 
            
          }
          });';
         
         }
      ?>
      $.ajax({
        type:'post',
        url:'returnOrder.php',
        data:{
          createReturn:'1',
          o_id:<?php echo $_GET['o_id'];?>,
          c_id:<?php echo $_SESSION["login_user"] ?>,
        },
        success:function(response){
          console.log(response);
        }
        });
       
        window.location = 'account.php';
    }
  }
</script>
</body>
</html>
