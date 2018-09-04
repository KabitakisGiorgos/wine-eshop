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
<script>
    function CloseAccount(c_id,debt){
        if (confirm('Are you sure you want to close your account?')) {
            $.ajax({
                type:'post',
                url:'deleteAccount.php',
                data:{
                    c_id:c_id,
                    debt:debt,
                },
                success:function(response){
                    console.log(response);
                    if(response=="1"){
                        window.location.replace("signout.php");
                    }else{
                        document.getElementById("debtError").innerHTML="Cant close Account U owwwwwwwwwn us";
                    }
                }
            });
        }else{
            //nothing
        }
    }
</script>
<?php 
    include("session.php");
    include("connect.php");
    $client_q="SELECT * FROM winesdb.clients WHERE c_id=".$_SESSION['login_user'].";";
    $result=mysqli_query($conn,$client_q);
    $conn->close();
    $row=$result->fetch_assoc();
?>
<div id="body">
<div class="container">    
  <div class="row">
    <div class="col-sm-12 detailsdiv">
        <h3>Account Info</h3>
        <div style="color:red;font-size:30px;" id="debtError"></div>
        <button onclick="CloseAccount('<?php echo $row['c_id'];?>',<?php echo $row["debt"] ?>);"class="btn btn-danger pull-right">Close Account</button>
        <div>
            <h4>Name :</h4><?php echo $row["name"] ?>                             
        </div>
        <div>
            <h4>Surname :</h4><?php echo $row["surname"] ?>                            
        </div>
        <div>
            <h4>Email :</h4><?php echo $row["email"] ?>                             
        </div>
        <div>
            <h4>PhoneNo :</h4><?php echo $row["phoneNo"] ?>                             
        </div>
        <div>
            <h4>Address :</h4><?php echo $row["address"] ?>                             
        </div>
        <div>
            <h4>AccountNo :</h4><?php echo $row["accNo"] ?>                             
        </div>
        <div>
            <h4>Debt :</h4><?php echo $row["debt"] ?>                             
        </div>
        <h3>Orders</h3>
        
        <ul class="list-group accountul">
            <li id="header" class="list-group-item">
                <h5 id="id">Order Id</h5>
                <h5 id="date">Date</h5>
                <h5 id="amount">Total Amount</h5>               
                <h5 id="state">State</h5>
            </li>
            <?php
                include("connect.php");
                $clientOrders_q="SELECT * FROM winesdb.orders WHERE c_id_FK=".$_SESSION['login_user'].";";
                $result=mysqli_query($conn,$clientOrders_q);
                $conn->close();
                while($row=$result->fetch_assoc()){
                    if($row["state_paid"]){
                        $state="Paid";
                    }else{
                        $state="Pending";
                    }
                    echo '<li onclick="OpenOrder('.$row["o_id"].')"class="list-group-item">
                            <h5 id="id">'.$row["o_id"].'</h5>
                            <h5 id="date">'.$row["date"].'</h5>
                            <h5 id="amount">'.$row["amount"].'</h5>               
                            <h5 id="state">'.$state.'</h5>
                        </li>';
                }
            ?>
        </ul>
        <h3>Transactions</h3>
        <?php
            include("connect.php");  
            $mindate=1901;
            $maxdate=1901;
            $validateform=true;
            $dateErr=""; 
            if ($_SERVER["REQUEST_METHOD"] == "GET") {
                if(isset($_GET["mindate"]) || isset($_GET["maxdate"]))
                {       
                  $c_id = $_SESSION['login_user'];       
                  $mindate = test_input($_GET["mindate"]);
                  $maxdate = test_input($_GET["maxdate"]); 
                  
                  if(!empty($_GET["mindate"]) && !empty($_GET["maxdate"])){                  
                    if ($mindate>$maxdate) {
                      $dateErr = "Min Date needs to be smaller than Max"; 
                      $validateform = false;
                    }
                  }

                  if($validateform){
                    $pay_filter_q="SELECT * FROM winesdb.payments WHERE c_id_FK=$c_id";
                    $order_filter_q="SELECT * FROM winesdb.orders WHERE c_id_FK=$c_id";
                    $return_filter_q="SELECT * FROM winesdb.returns WHERE c_id_FK=$c_id";
                    if(!empty($_GET["mindate"])){
                      $pay_filter_q.=" AND date>='$mindate'";
                      $order_filter_q.=" AND date>='$mindate'";
                      $return_filter_q.=" AND date>='$mindate'";
                    }           
                    if(!empty($_GET["maxdate"])){
                      $pay_filter_q.=" AND date<='$maxdate'";
                      $order_filter_q.= " AND date<='$maxdate'";
                      $return_filter_q.=" AND date<='$maxdate'";
                    }  
                    echo '<script>';
                    echo 'console.log('. json_encode( $pay_filter_q ) .')';
                    echo '</script>';         
                    $pay_filtered=mysqli_query($conn,$pay_filter_q);
                    $orders_filtered=mysqli_query($conn,$order_filter_q);
                    $returns_filtered=mysqli_query($conn,$return_filter_q);
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
        <form method="get" action='account.php'>
          <div class="filter-group form-group">
          <input type="hidden" value="<?php echo $_GET["o_id"] ?>" name="o_id" />
            <label style="display:block;">Date</label>
            <label>Min</label>
            <input name="mindate" class="form-control" type="date" style="width:15%; display:inline-block;">
            <label>Max</label>
            <input name="maxdate" class="form-control" type="date" style="width:15%; display:inline-block;">
            <div class="Error"> <?php echo $dateErr;?></div>
            <button id="filterSubmit" class="btn btn-info" style="margin-top:10px;">Get Transactions</button>
          </div>
        </form>
        <br><br>
        <h3>Orders</h3>
        <ul class="list-group accountul">
            <li id="header" class="list-group-item">
                <h5 id="id">Order Id</h5>
                <h5 id="date">Date</h5>
                <h5 id="amount">Total Amount</h5>               
                <h5 id="state">State</h5>
            </li>
            <?php
                include("connect.php");
                $clientOrders_q="SELECT * FROM winesdb.orders WHERE c_id_FK=".$_SESSION['login_user'].";";
                $result=mysqli_query($conn,$clientOrders_q);
                $conn->close();
                if(!isset($_GET["mindate"]) && !isset($_GET["maxdate"]))
                {
                    while($row=$result->fetch_assoc()){
                        if($row["state_paid"]){
                            $state="Paid";
                        }else{
                            $state="Pending";
                        }
                        echo '<li onclick="OpenOrder('.$row["o_id"].')"class="list-group-item">
                                <h5 id="id">'.$row["o_id"].'</h5>
                                <h5 id="date">'.$row["date"].'</h5>
                                <h5 id="amount">'.$row["amount"].'</h5>               
                                <h5 id="state">'.$state.'</h5>
                            </li>';
                    }
                }
                else{
                    if(!empty($_GET["mindate"]) || !empty($_GET["maxdate"])){
                        while($row=$orders_filtered->fetch_assoc()){
                            if($row["state_paid"]){
                                $state="Paid";
                            }else{
                                $state="Pending";
                            }
                            echo '<li onclick="OpenOrder('.$row["o_id"].')"class="list-group-item">
                                    <h5 id="id">'.$row["o_id"].'</h5>
                                    <h5 id="date">'.$row["date"].'</h5>
                                    <h5 id="amount">'.$row["amount"].'</h5>               
                                    <h5 id="state">'.$state.'</h5>
                                </li>';
                        }
                    }
                    else{
                        while($row=$result->fetch_assoc()){
                            if($row["state_paid"]){
                                $state="Paid";
                            }else{
                                $state="Pending";
                            }
                            echo '<li onclick="OpenOrder('.$row["o_id"].')"class="list-group-item">
                                    <h5 id="id">'.$row["o_id"].'</h5>
                                    <h5 id="date">'.$row["date"].'</h5>
                                    <h5 id="amount">'.$row["amount"].'</h5>               
                                    <h5 id="state">'.$state.'</h5>
                                </li>';
                        }
                    }
                }
            ?>
        </ul>
        <h3>Payments</h3>
        <ul class="list-group accountul">
            <li id="header" class="list-group-item">
                <h5 id="id">Pay Id</h5>
                <h5 id="date">Date</h5>
                <h5 id="amount">Total Amount</h5>               
            </li>
            <?php
                if(!isset($_GET["mindate"]) && !isset($_GET["maxdate"]))
                {
                    include("connect.php");
                    $clientOrders_q="SELECT * FROM winesdb.payments WHERE c_id_FK=".$_SESSION['login_user'].";";
                    $result=mysqli_query($conn,$clientOrders_q);
                    $conn->close();
                    while($row=$result->fetch_assoc()){
                        echo '<li class="list-group-item">
                                <h5 id="id">'.$row["pay_id"].'</h5>
                                <h5 id="date">'.$row["date"].'</h5>
                                <h5 id="amount">'.$row["amount"].'</h5>               
                            </li>';
                    }
                }
                else{
                    if(!empty($_GET["mindate"]) || !empty($_GET["maxdate"])){
                        while($row=$pay_filtered->fetch_assoc()){
                            echo '<li class="list-group-item">
                                    <h5 id="id">'.$row["pay_id"].'</h5>
                                    <h5 id="date">'.$row["date"].'</h5>
                                    <h5 id="amount">'.$row["amount"].'</h5>               
                                </li>';
                        }
                    }
                    else{
                        include("connect.php");
                        $clientOrders_q="SELECT * FROM winesdb.payments WHERE c_id_FK=".$_SESSION['login_user'].";";
                        $result=mysqli_query($conn,$clientOrders_q);
                        $conn->close();
                        while($row=$result->fetch_assoc()){
                            echo '<li class="list-group-item">
                                    <h5 id="id">'.$row["pay_id"].'</h5>
                                    <h5 id="date">'.$row["date"].'</h5>
                                    <h5 id="amount">'.$row["amount"].'</h5>               
                                </li>';
                        }
                    }
                }
            ?>
        </ul>
        <h3>Returns</h3>
        <ul class="list-group accountul">
            <li id="header" class="list-group-item">
                <h5 id="id">Return Id</h5>
                <h5 id="date">Date</h5>
            </li>
            <?php
                if(!isset($_GET["mindate"]) && !isset($_GET["maxdate"]))
                {
                    include("connect.php");
                    $clientOrders_q="SELECT * FROM winesdb.returns WHERE c_id_FK=".$_SESSION['login_user'].";";
                    $result=mysqli_query($conn,$clientOrders_q);
                    $conn->close();
                    while($row=$result->fetch_assoc()){
                        echo '<li class="list-group-item">
                                <h5 id="id">'.$row["r_id"].'</h5>
                                <h5 id="date">'.$row["date"].'</h5>
                            </li>';
                    }
                }
                else{
                    if(!empty($_GET["mindate"]) || !empty($_GET["maxdate"])){
                        while($row=$returns_filtered->fetch_assoc()){
                            echo '<li class="list-group-item">
                                    <h5 id="id">'.$row["r_id"].'</h5>
                                    <h5 id="date">'.$row["date"].'</h5>
                                </li>';
                        }
                    }
                    else{
                        include("connect.php");
                        $clientOrders_q="SELECT * FROM winesdb.returns WHERE c_id_FK=".$_SESSION['login_user'].";";
                        $result=mysqli_query($conn,$clientOrders_q);
                        $conn->close();
                        while($row=$result->fetch_assoc()){
                            echo '<li class="list-group-item">
                                    <h5 id="id">'.$row["r_id"].'</h5>
                                    <h5 id="date">'.$row["date"].'</h5>
                                </li>';
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
<script type="text/javascript">
    function OpenOrder(id){  
        location.href ="orderdetails.php?o_id="+id;
    }
</script>
</body>
</html>
