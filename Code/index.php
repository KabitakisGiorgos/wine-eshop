<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login Page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="./css/styles.css">  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<?php
  
  $error="";
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "winesdb";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  mysqli_set_charset($conn, "utf8");
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  session_start();
  if($_SERVER["REQUEST_METHOD"]=="POST"){
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    mysqli_set_charset($conn, "utf8");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 
    $emailReg=mysqli_real_escape_string($conn,$_POST["email"]);
    $passReg=mysqli_real_escape_string($conn,$_POST["password"]);
    //check string format

    $c_id_query="SELECT c_id FROM winesdb.clients WHERE email='".$emailReg."' and password='".$passReg."';";
    $c_id = mysqli_query($conn,$c_id_query);
    $array = mysqli_fetch_array($c_id,MYSQLI_ASSOC);
    $count = mysqli_num_rows($c_id);

    if($count == 1) {
      $_SESSION['login_user'] = $array["c_id"];
      
      header("location: products.php");
   }else {
      $error = "Your Login Name or Password is invalid";
   }
  }
  $conn->close();  
?>
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Logo</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="signup.php"></span> Sign Up</a></li>
      </ul>
    </div>
  </div>
</nav>
<div id="body">
<div class="container logincont">    
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
      <form action=""  method = "post">  
        <div class="card form-group">
            <h2>Login</h2>
            <div class="Error"><?php echo $error; ?></div>
            <div>
                <label>Email</label>
                <input class="form-control" type="email" name="email">
            </div>
            <div>
                <label for="">Password</label>
                <input class="form-control" type="password" name="password">
            </div>
            <button class="btn btn-success" type ="submit" value = "Submit">Log In</button> 
              <br />
            <!-- <div class="logindiv"><a href="index.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></div> -->
        </div>
      </form>
    </div>
    <div class="col-sm-4"> 
    </div>
  </div>
</div><br>


</div><br><br>
</div>
<footer class="footer">
  <p>Online Store Copyright</p>  
  <p>George Mavridis, George Kampitakis, George Todoulakis</p>
</footer>

</body>
</html>
