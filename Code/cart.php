<!DOCTYPE html>
<html lang="en">
<head>
  <title>Cart Page</title>
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
  include("session.php");

?>
<div id="body">
<div class="container">    
  <div class="row">
    <div class="col-sm-12 cartdiv">
      <h3>Shopping Cart</h3>
       <ul id="mylist" class="list-group cartul">
       <script>
          window.onload=function ShowCart(){
                estimatePrice();
                console.log("test");
                $.ajax({
                  type:'post',
                  url:'addToCart.php',
                  data:{
                    showcart:"cart"
                  },
                  success:function(response){
                  document.getElementById("mylist").innerHTML= "<li id="+"header"+" class="+"list-group-item>"+
                                                                  "<h5 id="+"Name"+">Name</h5>"+
                                                                  "<h5 id="+"price"+">Price</h5>"+              
                                                                  "<h5 id="+"amount"+">No Of Products</h5>"+
                                                                "</li>" + response; 
                  CanOrder();
                  }
                });
              }

              function CanOrder(){
                $.ajax({
                  type:'post',
                  url:'newOrder.php',
                  data:{
                    canOrder:"1",
                    c_id:<?php echo $_SESSION["login_user"];?>,
                  },
                  success:function(response){
                    console.log(response);
                    if(response=="1"){
                      document.getElementById("cantOrder").innerHTML="";
                      document.getElementById("confirm").disabled=false;
                      <?php 
                        if(array_key_exists('p_id',$_SESSION) && !empty($_SESSION['p_id'])) {
                  if($_SESSION["isMerchant"]&&count($_SESSION['p_id'])>0){
                    echo 'MinimumOrder();';
                  } }
                ?>  
                    }else{
                      document.getElementById("cantOrder").innerHTML="A Lannister always pays his debts. Why don't you?";
                      document.getElementById("confirm").disabled=true;
                    }
                  }
                });
              }

              function MinimumOrder(){
               
                $.ajax({
                  type:'post',
                  url:'newOrder.php',
                  data:{
                    minimumOrder:"1",
                  },
                  success:function(response){
                    if(response=="1"){
                      document.getElementById("cantOrder").innerHTML="";
                      document.getElementById("confirm").disabled=false;
                    }else{
                      console.log("here");
                      document.getElementById("cantOrder").innerHTML="U must buy more wines";
                      document.getElementById("confirm").disabled=true;
                    }
                  }
                });
              }

              function estimatePrice(){
                $.ajax({
                  type:'post',
                  url:'addToCart.php',
                  data:{
                    estimatePrice:"price"
                  },
                  success:function(response){
                    document.getElementById("cartPrice").innerHTML=response;       
                  }
                });
              }

              function CancelOrder(){
                if (confirm('Are you sure you want to delete the cart?')) {
                  $.ajax({
                  type:'post',
                  url:'addToCart.php',
                  data:{
                    deleteCart:"cart"
                  },
                  success:function(response){
                    console.log("cartdeleted");
                    location.reload();
                  }
                });
                }else{ 
                  //
                }
              }

              function removeItem(id){
                $.ajax({
                  type:'post',
                  url:'addToCart.php',
                  data:{
                    removeId:id
                  },
                  success:function(response){
                    location.reload();
                  }
                });
              }

              function CommitOrder(c_id,amount){
               
                $.ajax({
                  type:'post',
                  url:'newOrder.php',
                  data:{
                    newOrder:'1',
                    c_id:c_id,
                    amount:amount,
                  },
                  success:function(response){
                    var o_id=response;
                    console.log(response);
                      //edo exei mpei pleon i paraggelia stin basi ara tha steiloyme epanilimena ajax requests me ta periexomena toy cart oste na mpainoyne ena ena sto contains
                      <?php
                      if(array_key_exists('p_id',$_SESSION) && !empty($_SESSION['p_id'])) {
                      for($i=0;$i<count($_SESSION['p_id']);$i++){
                        echo '$.ajax({
                          type:"post",
                          url:"newOrder.php",
                          data:{
                            containsAdd:"1",
                            amount:  '.$_SESSION["amount"][$i].',
                            p_id: '.$_SESSION["p_id"][$i].',
                            o_id:o_id,
                          },
                          success:function(response){
                            console.log(response);
                                  $.ajax({
                                    type:"post",
                                    url:"addToCart.php",
                                    data:{
                                      deleteCart:"cart"
                                    },
                                  success:function(response){
                                    console.log("cartdeleted");
                                    location.reload();
                                  }
                              }); 
                          }
                        });';
                      }
                      }?>
                  }
                });
              }
       </script>
      
       </ul>
      <label>Total Price: </label>
      <div style="display:inline-block;" id="cartPrice"></div>
      <div style="color:red;" id="cantOrder"></div>
      <button id="confirm" onclick="CommitOrder('<?php echo $_SESSION["login_user"]; ?>',document.getElementById('cartPrice').innerHTML);" class="btn btn-success">Confirm Order</button>
      <button onclick="CancelOrder();" class="btn btn-danger">Clear Cart</button>       
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
