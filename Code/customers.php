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

<div id="body">
<div class="container">    
  <div class="row">
    <h3>Good Customers</h3>
    <div class="col-sm-12 cartdiv">
       <ul id="goodList" class="list-group cartul">
          <li id="header" class="list-group-item">
                <h5 id="name">Name</h5>
                <h5 id="surname">SurName</h5>
                <h5 id="amount">Total Amount</h5>               
          </li>
          <?php 
            include("connect.php");
            $goodcustomers_q="SELECT c_id,name,surname,SUM(amount) FROM winesdb.clients INNER JOIN winesdb.orders ON winesdb.clients.c_id = winesdb.orders.c_id_FK
                              WHERE debt = 0 GROUP BY c_id,name,surname ORDER BY SUM(amount) DESC";
            $goodcustomers=mysqli_query($conn,$goodcustomers_q);
          
            while($row=$goodcustomers->fetch_assoc()){
              echo '<li class="list-group-item">
                      <h5 id="Name">'.$row["name"].'</h5>
                      <h5 id="price">'.$row["surname"].'</h5>               
                      <h5>'.$row["SUM(amount)"].'</h5>
                    </li>';
            }
            $conn->close();
        ?>
       </ul>
    </div>
    <h3>Bad Customers</h3>
    <div class="col-sm-12 cartdiv">
      <ul class="list-group cartul">
        <li id="header" class="list-group-item">
                <h5 id="name">Name</h5>
                <h5 id="surname">SurName</h5>
                <h5 id="amount">Total Debt</h5>               
        </li>  
        <?php 
          include("connect.php");
          $badcustomers_q="SELECT c_id,name,surname,debt FROM winesdb.clients WHERE debt!=0 ORDER BY debt DESC;";
          $badcustomers=mysqli_query($conn,$badcustomers_q);
          
          while($row=$badcustomers->fetch_assoc()){
            
            echo '<li class="list-group-item">
                    <h5 id="Name">'.$row["name"].'</h5>
                    <h5 id="price">'.$row["surname"].'</h5>               
                    <h5 id="amount">'.$row["debt"].'</h5>
                  </li>';
          }
          $conn->close();
        ?>
      </ul>
    </div>
  </div>
</div><br><br>

<footer class="footer">
  <p>Online Store Copyright</p>  
  <p>George Mavridis, George Kampitakis, George Todoulakis</p>
</footer>

</body>
</html>
