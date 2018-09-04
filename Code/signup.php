<!DOCTYPE html>
<html lang="en">
<head>
  <title>Sign Up Page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="./css/styles.css">  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<?php 
  $accNo=$name=$fname=$email=$pass=$phoneNo=$address="";
  $accNoErr=$nameErr=$fnameErr=$emailErr=$passErr=$phoneNoErr=$addressErr="";
  $validateform = true;
  $duplicateUser="";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["name"])) {
      $nameErr = "Name is required";
      $validateform = false;
    } else {
      $name = test_input($_POST["name"]);
      // check if name only contains letters and whitespace
      if (strlen($name)>30) {
        $nameErr = "Name too big"; 
        $validateform = false;
      }
    }

    if (empty($_POST["accountNumb"])) {
      $accNoErr = "Account number is required";
      $validateform = false;
    } else {
      $accNo = test_input($_POST["accountNumb"]);
      // check if name only contains letters and whitespace
      if (strlen($accNo)>15) {
        $accNoErr = "Account number too big"; 
        $validateform = false;
      }
    }

    if (empty($_POST["fname"])) {
      $fnameErr = "First Name is required";
      $validateform = false;      
    } else {
      $fname = test_input($_POST["name"]);
      // check if name only contains letters and whitespace
      if (strlen($fname)>30) {
        $fnameErr = "Name too big";
        $validateform = false;        
      }
    }
  
    if (empty($_POST["email"])) {
      $emailErr = "Email is required";
      $validateform = false;      
    } else {
      $email = test_input($_POST["email"]);
      // check if e-mail address is well-formed
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format"; 
        $validateform = false;        
      }
    }

    if (empty($_POST["password"])) {
      $passErr = "Password is required";
      $validateform = false;      
    } else {
      $pass = test_input($_POST["password"]);
      // check if e-mail address is well-formed
      if (strlen($pass)>10) {
        $passErr = "Password too big";
        $validateform = false;        
      }
    }

    if (empty($_POST["phoneNo"])) {
      $phoneNoErr = "PhoneNo is required";
      $validateform = false;      
    } else {
      $phoneNo = test_input($_POST["phoneNo"]);
         //check if e-mail address is well-formed
         if (strlen($phoneNo)>10) {
         $phoneNoErr = "PhoneNo too big";
         $validateform = false;         
      }
    }

    if (empty($_POST["address"])) {
      $addressErr = "Address is required";
      $validateform = false;      
    } else {
      $address = test_input($_POST["address"]);
      // check if e-mail address is well-formed
      if (strlen($addressErr)>10) {
        $addressErr = "Address too big"; 
        $validateform = false;        
      }
    }
    if($validateform){
      registerClient($name,$fname,$email,$pass,$phoneNo,$address,$accNo); // Add accountNo
    }
  }
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function registerClient($name,$fname,$email,$pass,$phoneNo,$address,$accNo){
    $reload=false;
    include("connect.php");

    $sql = "INSERT INTO winesdb.clients (name, password, email, surname, phoneNo, address, accNo) 
    VALUES ('".$name."', '".$pass."', '".$email."','".$fname."','".$phoneNo."','".$address."', '".$accNo."');";
    if (!mysqli_query($conn,$sql))
    {
      global $duplicateUser;
      $duplicateUser="User Already Exists";
      $reload=true;
    }else{
      $duplicateUser="";
     
      $sql = "SELECT c_id FROM winesdb.clients WHERE email='".$email."'";
      $result=mysqli_query($conn,$sql);
      $row = $result->fetch_assoc();
      //echo $row["c_id"];
     
      if(strcmp($_POST["client"],"merchant")==0){
       $sql = "INSERT INTO winesdb.merchants (c_id) VALUES ('".$row["c_id"]."');";
        mysqli_query($conn,$sql);
        echo "merchant";
      }else{
       $sql = "INSERT INTO winesdb.users (c_id) VALUES ('".$row["c_id"]."');";
       mysqli_query($conn,$sql);
      }
      header("Location: index.php");
      exit;
    }
    $conn->close();
  }
?>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Logo</a>
    </div>
  </div>
</nav>

<div id="body">
<div class="container">    
  <div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
      <form method="post" action="signup.php">
            <a href="index.php" class="signupBack"><span class="glyphicon glyphicon-circle-arrow-left"></span> Back</a>            
        <h2>Sign Up Form</h2>  
        <div class="Error"><?php echo $duplicateUser;?></div>
        <div class=" form-group">
            <label>Name</label>
            <input name="name" class="form-control" type="text">
            <div class="Error"> <?php echo $nameErr;?></div>
        </div>
        <div class=" form-group">
            <label>First Name</label>
            <input name="fname" class="form-control" type="text">
            <div class="Error"><?php echo $fnameErr;?></div>
        </div>
        <div class=" form-group">
            <label>Email</label>
            <input name="email" class="form-control" type="email">
            <div class="Error"><?php echo $emailErr;?></div>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input name="password" class="form-control" type="password">
          <div class="Error"> <?php echo $passErr;?></div>
        </div>
        <div class="form-group">
          <label>Account Number</label>
          <input name="accountNumb" class="form-control" type="password">
          <div class="Error"> </div>
        </div>
         <div class=" form-group">
            <label>Phone Number</label>
            <input name="phoneNo" class="form-control" type="text">
            <div class="Error"><?php echo $phoneNoErr;?></div>
        </div>
        <div class=" form-group">
            <label>Address</label>
            <input name="address" class="form-control" type="text">
            <div class="Error"> <?php echo $addressErr;?></div>
        </div>
        <div class="form-check">
            <input class="form-check-input"  type="radio" name="client" value="user" checked>User
            <input class="form-check-input"  type="radio" name="client" value="merchant">Merchant
        </div>
        <button type="submit" class="btn btn-success">Submit</button>
        <!-- Upon Click Make Checks and Redirect if successfull -->
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
<script src="./js/javascript.js"></script>
</body>
</html>

