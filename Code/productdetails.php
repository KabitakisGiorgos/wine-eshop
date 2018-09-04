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
<?php 
  include("session.php");
  include("connect.php");
   //echo $_GET["p_id"];
  $product_q="SELECT * FROM winesdb.wines WHERE p_id=".$_GET["p_id"].";";
  $productInfo=mysqli_query($conn,$product_q);

  $variety_q="SELECT variety FROM winesdb.varieties WHERE p_id_FK=".$_GET["p_id"].";";
  $varietyInfo=mysqli_query($conn,$variety_q);
  $row=$productInfo->fetch_assoc();
  $img=base64_encode($row["photo"]); 
  //call also varieties
  $conn->close();
?>
<script>
  function addItem(id,price,elId){
    var name=document.getElementById(elId).innerHTML;
    console.log(name);
    console.log("here");
    $.ajax({
      type:'post',
      url:'addToCart.php',
      data:{
        p_id:id,
        price:price,
        name:name,
      },//HERE
      success:function(response){
        console.log("itemAdded");//here check that works or put a proper message in client
      }
    });
  }
</script>
 
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
<div id="body">
<div class="container">    
  <div class="row">
    <div class="col-sm-12 detailsdiv">
        <img style="max-height:300px" src="data:image/jpg;charset=utf8;base64,<?php echo $img; ?>" class="img-responsive" style="width:20%" alt="Image">
        <button onclick="addItem('<?php echo $row["p_id"];?>','<?php if($_SESSION['isMerchant']){echo $row["wholesale"];}else{ echo $row["retail"];}?>','pName');" class="btn btn-success pull-right">Add to Cart</button>
        <div>
            <h4>Name :</h4><div style="display:inline;"id="pName"><?php echo $row["name"];?></div>                           
        </div>
        <div>
            <h4>Color :</h4><?php echo $row["color"];?>                             
        </div>
        <div>
            <h4>Year :</h4><?php echo $row["year"];?>                             
        </div>
        <div>
            <h4>Winery :</h4><?php echo $row["winery"];?>                             
        </div>
        <div>
            <h4>SaleNo :</h4><?php echo $row["saleNo"];?>                             
        </div>
        <div>
            <h4>Price :</h4><?php if($_SESSION['isMerchant']){
              echo $row["wholesale"];
            }else{
              echo $row["retail"];
            }?>                             
        </div>
        <div>
            <h4>Description :</h4><?php echo $row["description"]?>                             
        </div>
        <div>
            <h4>Varieties :</h4><?php while($row=$varietyInfo->fetch_assoc()){
              echo $row['variety'].'<br>';
            }
            ?>                             
        </div>
    </div>
  </div>
</div><br><br>
</div>
<footer class="footer">
  <p>Online Store Copyright</p>  
  <p>George Mavridis, George Kampitakis, George Todoulakis</p>
  
</footer>

</body>
</html>
