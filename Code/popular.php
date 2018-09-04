<!DOCTYPE html>
<html lang="en">
<head>
  <title>Products Page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="./css/styles.css">    
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<?php
 include('session.php');
 include('connect.php');

$popular_q="SELECT p_id,name,retail,wholesale,photo,SUM(amount) FROM winesdb.wines INNER JOIN winesdb.contains ON winesdb.wines.p_id = winesdb.contains.p_id_FK   
              WHERE winesdb.contains.ο_id_FK in(
              SELECT o_id FROM winesdb.orders 
              WHERE EXTRACT(MONTH FROM winesdb.orders.date) = EXTRACT(MONTH FROM CURDATE())-1
              AND EXTRACT(YEAR FROM winesdb.orders.date)  = EXTRACT(YEAR FROM CURDATE())
          ) GROUP BY p_id,name,retail,wholesale,photo ORDER BY SUM(amount) DESC LIMIT 10";

$result=mysqli_query($conn,$popular_q);

$wines_q = "SELECT * FROM winesdb.wines;";          
  $varieties_q = "SELECT DISTINCT variety FROM winesdb.varieties;";
  $wineries_q = "SELECT DISTINCT winery FROM winesdb.wines;";

  $wines = mysqli_query($conn,$wines_q);
  $varieties = mysqli_query($conn,$varieties_q);
  $wineries = mysqli_query($conn,$wineries_q);


  $counter=0;
  $max_price=$max_amount=$max_year=0;
  
  if ($wines->num_rows > 0) {
    while($row = $wines->fetch_assoc()) {
      if($row["saleNo"]>$max_amount){ $max_amount=$row["saleNo"]; }
      if($row["year"]>$max_year){ $max_year=$row["year"]; }
      if($_SESSION['isMerchant']){
        if($row["wholesale"]>$max_price){ $max_price=$row["wholesale"]; }        
      }
      else{
        if($row["retail"]>$max_price){ $max_price=$row["retail"]; }                
      }          
    }
  }
  $wines->data_seek(0);

$filter_q="";
$filtered="";
$dateErr=$salesErr=$priceErr=""; 
$validateform=true; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = test_input($_POST["name"]);
    // check if name only contains letters and whitespace
    if (strlen($name)>50) {
      $nameErr = "Name too big"; 
      $validateform = false;
    }

    $mindate = test_input($_POST["mindate"]);
    $maxdate = test_input($_POST["maxdate"]);
    // check if name only contains letters and whitespace
    if ($mindate>$maxdate) {
      $dateErr = "Min Date needs to be smaller than Max"; 
      $validateform = false;
    }
  
    $minprice = test_input($_POST["minprice"]);
    $maxprice = test_input($_POST["maxprice"]);
    if ($minprice>$maxprice) {
      $priceErr = "Min Price needs to be smaller than Max"; 
      $validateform = false;
    }

    $minsales = test_input($_POST["minsales"]);
    $maxsales = test_input($_POST["maxsales"]);
    if ($minsales>$maxsales) {
      $salesErr = "Min Sales Number needs to be smaller than Max"; 
      $validateform = false;
    }
    
    if($validateform){
      
      //Let the games begin.
      //Building dynamic Query 
      include('connect.php');
      if($_SESSION['isMerchant']){ 
        $filter_q="SELECT p_id,name,wholesale,photo,SUM(amount) FROM winesdb.wines INNER JOIN winesdb.contains ON winesdb.wines.p_id = winesdb.contains.p_id_FK   
           WHERE winesdb.contains.ο_id_FK in(
           SELECT o_id FROM winesdb.orders 
           WHERE EXTRACT(MONTH FROM winesdb.orders.date) = EXTRACT(MONTH FROM CURDATE())-1
           AND EXTRACT(YEAR FROM winesdb.orders.date)  = EXTRACT(YEAR FROM CURDATE())
        )";             
        // $filter_q='SELECT DISTINCT name,photo,wholesale,p_id FROM winesdb.wines INNER JOIN winesdb.varieties ON winesdb.wines.p_id = winesdb.varieties.p_id_FK WHERE 1';
      }
      else{
        $filter_q="SELECT p_id,name,retail,photo,SUM(amount) FROM winesdb.wines INNER JOIN winesdb.contains ON winesdb.wines.p_id = winesdb.contains.p_id_FK   
        WHERE winesdb.contains.ο_id_FK in(
           SELECT o_id FROM winesdb.orders 
           WHERE EXTRACT(MONTH FROM winesdb.orders.date) = EXTRACT(MONTH FROM CURDATE())-1
           AND EXTRACT(YEAR FROM winesdb.orders.date)  = EXTRACT(YEAR FROM CURDATE())
        )";
        // $filter_q='SELECT DISTINCT name,photo,retail,p_id FROM winesdb.wines INNER JOIN winesdb.varieties ON winesdb.wines.p_id = winesdb.varieties.p_id_FK WHERE 1';          
      }
      if (!empty($_POST["name"])) {
        $name = str_replace('_', ' ', $name);                                        
        $filter_q .= " AND name = '$name'";
      }

      if (!empty($_POST["red"]) && !empty($_POST["white"]) && !empty($_POST["rose"]) ) {
        $red=$_POST["red"];
        $white=$_POST["white"];
        $rose=$_POST["rose"];
        $filter_q .= " AND (color = '$red' OR color = '$white' OR color='$rose')";
      }
      else if(!empty($_POST["red"]) && !empty($_POST["white"])){
        $red=$_POST["red"];
        $white=$_POST["white"];
        $filter_q .= " AND (color = '$red' OR color = '$white')";
      }
      else if(!empty($_POST["white"]) && !empty($_POST["rose"])){
        $white=$_POST["white"];
        $rose=$_POST["rose"];
        $filter_q .= " AND (color = '$white' OR color='$rose')";
      }
      else if(!empty($_POST["red"]) && !empty($_POST["rose"])){
        $red=$_POST["red"];
        $rose=$_POST["rose"];
        $filter_q .= " AND (color = '$red' OR color='$rose')";
      }
      else if(!empty($_POST["red"])){
        $red = $_POST["red"];
        $filter_q .= " AND color = '$red'";         
      }
      else if(!empty($_POST["white"])){
        $white=$_POST["white"];
        $filter_q .= " AND color = '$white'";
      }
      else if(!empty($_POST["rose"])){
        $rose=$_POST["rose"];
        $filter_q .= " AND color = '$rose'";
      }

      if($mindate>1901){
        $filter_q.=" AND year >= '$mindate'";          
      }

      if($maxdate < $max_year){
        $filter_q.=" AND year <= '$maxdate'";          
      }

      if($minsales>0){
        $filter_q.=" AND saleNo >= '$minsales'";          
      }

      if($maxsales < $max_amount){
        $filter_q.=" AND saleNo <= '$maxsales'";          
      }

      if($minprice>0){
        if($_SESSION['isMerchant']){            
          $filter_q.=" AND wholesale >= '$minprice'"; 
        }
        else{
          $filter_q.=" AND retail >= '$minprice'";             
        }         
      }

      if($maxprice < $max_price){
        if($_SESSION['isMerchant']){                        
          $filter_q.=" AND wholesale <= '$maxprice'"; 
        }
        else{
          $filter_q.=" AND retail <= '$maxprice'";             
        }         
      }

      $openvar=false;
      $openwine=false;

      foreach($_POST as $key => $value) {
        // echo "$key = $value";
        if(strcmp($key,"name")!=0 &&
          strcmp($key,"mindate")!=0 && strcmp($key,"maxdate")!=0 && 
          strcmp($key,"minsales")!=0 && strcmp($key,"maxsales")!=0 &&
          strcmp($key,"minprice")!=0 && strcmp($key,"maxprice")!=0){
          if(strcmp($key[0],"v")==0){
            $nkey = substr("$key",1);
            $nkey = str_replace('_', ' ', $nkey);              
            if($openvar){
               $filter_q.=" OR variety = '$nkey'";                
            }
            else{
              $filter_q.=" AND (variety = '$nkey'";                                
              $openvar=true;
            }
          }
          if(strcmp($key[0],"w")==0 && strcmp($key[1],"i")==0){
            $nkey = substr("$key",2);
            $nkey = str_replace('_', ' ', $nkey);                              
            if($openvar){
              //close
              $filter_q.=")";                                                
              $openvar=false;
            }
            if($openwine){
              $filter_q.=" OR winery = '$nkey'";                                      
            }
            else{
              $filter_q.=" AND (winery = '$nkey'";                                                
              $openwine=true;                                
            }
          }              
        }
        else{
          if($openvar){
            $filter_q.=")";
            $openvar=false;                            
          }
          if($openwine){
            //close wine
            $filter_q.=")";                                                              
            $openwine=false;              
          }
        }
      }
      $filter_q.=" GROUP BY p_id,name,retail,wholesale,photo ORDER BY SUM(amount) DESC LIMIT 10";
      echo '<script>';
      echo 'console.log('. json_encode( $filter_q ) .')';
      echo '</script>';
      
      $filtered = mysqli_query($conn,$filter_q);
                
    }    
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}


$conn->close();  
?>

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
        <li><a href="products.php">Home</a></li>
        <li class="active"><a href="popular.php">Popular Products</a></li>
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
<div class="filter">
    <img style="position:absolute; left:7px;top:8px; width:20px;" src="https://png.icons8.com/material/100/424242/filter.png">
</div>
<div class="filter_container open">
  <div class="relative_container" style="position:relative;">
    <h4 style="position:absolute; top:80px; left:15px;">Filters</h4>
    <div style="height:100px">  </div>
    <div style="width:90%; padding:5%;"> 
      <form method="post" action="popular.php">
        <div class="filter-group form-group">
            <label>Name</label>
            <input name="name" class="form-control" type="text">
            <!-- <div class="Error"> <?php echo $nameErr;?></div> -->
        </div>
        <div class=" form-check">
            <label>Color</label>
            <div>
              <input class="form-check-input"  type="checkbox" name="red" value="red" >Red
              <input class="form-check-input"  type="checkbox" name="rose" value="rose" >Rose
              <input class="form-check-input"  type="checkbox" name="white" value="white">White
            </div>
        </div>
        <div class="filter-group form-group">
            <div  class="varietydiv">
              <label>Varieties</label>
              <img  style=" cursor:pointer; width:15px;" src="https://png.icons8.com/metro/100/333333/chevron-down.png">
            </div>
            <div id="vardrop" class="dropdown">
            <?php
              if ($varieties->num_rows > 0) {
                $i=0;
                while($row = $varieties->fetch_assoc()) {
                  if($counter%3==0){
                    echo '<div>
                        <input class="" name="v'.$row["variety"].'" type="checkbox">
                          <label id="v'.$row["variety"].'" style="visibility:visible;">
                            '.$row["variety"].' 
                          </label>
                        </div>';
                  }
                  $i++;
                }
                $varieties->data_seek(0);                
              }
            ?>
            </div>
        </div>
        <div class="filter-group form-group">
            <div class="winerydiv">
              <label>Winery</label>
              <img style=" cursor:pointer; width:15px;" src="https://png.icons8.com/metro/100/333333/chevron-down.png">
            </div>
            <div id="windrop" class="dropdown">
            <?php
              if ($wineries->num_rows > 0) {
                $i=0;
                while($row = $wineries->fetch_assoc()) {
                  if($counter%3==0){
                    echo '<div>
                        <input class="" name="wi'.$row["winery"].'" type="checkbox">
                          <label id="wi"'.$row["winery"].' style="visibility:visible;">
                            '.$row["winery"].' 
                          </label>
                        </div>';
                  }
                  $i++;
                }
                $wineries->data_seek(0);                
              }
            ?>
            </div>
        </div>
        <div class="filter-group form-group">
          <label style="display:block;">Date</label>
          <label>Min</label>
          <input name="mindate" class="form-control" type="number" style="width:35%; display:inline-block;" value=1901 min="1901">
          <label>Max</label>
          <input name="maxdate" class="form-control" type="number" style="width:35%; display:inline-block;" value=<?php echo $max_year;?> max= <?php echo $max_year;?>>
          <div class="Error"> <?php echo $dateErr;?></div>
        </div>

        <div class="filter-group form-group">
          <label style="display:block;">Price</label>
          <label>Min</label>
          <input name="minprice" class="form-control" type="number" style="width:35%; display:inline-block;" value=0 min="0">
          <label>Max</label>
          <input name="maxprice" class="form-control" type="number" style="width:35%; display:inline-block;" value=<?php echo $max_price;?> max= <?php echo $max_price;?> >
          <div class="Error"> <?php echo $priceErr;?></div>
        </div>

        <div class="filter-group form-group">
          <label style="display:block;">Sales Number</label>
          <label>Min</label>
          <input name="minsales" class="form-control" type="number" style="width:35%; display:inline-block;" value=0 min="0">
          <label>Max</label>
          <input name="maxsales" class="form-control" type="number" style="width:35%; display:inline-block;" value=<?php echo $max_amount;?> max= <?php echo $max_amount;?>>
          <div class="Error"> <?php echo $salesErr;?></div>   
        </div>

        <button id="filterSubmit" class="btn btn-info" style="margin-top:10px;">Apply Filters</button>
      </form>
    </div>
  </div>
</div>
<div class="container"> 
    <h3>Popular Products</h3>        
    <?php
    if(!isset($_POST["name"]))
    {
      $counter=0;
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          if($_SESSION["isMerchant"]){
            $price = $row["wholesale"];
          }
          else{
            $price = $row["retail"];  
          }
          $img=base64_encode($row["photo"]);      
          $name=$row["name"];
          $amount=$row["SUM(amount)"];
          if($counter%3==0){
            echo '<div class="container">
                    <div class="row">
                      <div class="col-sm-4">
                        <div class="cardcontainer" >
                          <a href="productdetails.php?p_id='.$row["p_id"].'">
                            <div class="prodCard">
                              <div style="display:none;" id="p_id'.$counter.'" value="'.$name.'">'.$name.'</div>
                              <img src="data:image/jpg;charset=utf8;base64,'.$img.'" class="img-responsive" style="max-height:300px" alt="Image">
                              <h6>'.$row["name"].'</h6>
                              <span class="glyphicon glyphicon-euro">'.$price.'</span>
                              <p> Amount: '.$amount.'</p>
                            </div>
                          </a>
                          
                        </div>
                      </div>';  
          }
          else{
            echo'<div class="col-sm-4">
                  <div class="cardcontainer" >
                    <a href="productdetails.php?p_id='.$row["p_id"].'">
                      <div class="prodCard">
                        <div style="display:none;" id="p_id'.$counter.'" value="'.$name.'">'.$name.'</div>
                        <img src="data:image/jpg;charset=utf8;base64,'.$img.'" class="img-responsive" style="height:300px" alt="Image">
                        <h6>'.$row["name"].'</h6>
                        <span class="glyphicon glyphicon-euro">'.$price.'</span>
                        <p> Amount: '.$amount.'</p>
                      </div>
                    </a>
                  </div>
                </div>';
            if($counter%2==0){
              echo'</div>    
                </div><br>';
            }
          }
          $counter++;
        }
        if(($counter-1)%2!=0){
          echo'</div>    
          </div><br>';
        }
      }
      else{
        echo "No Wines where found";  
      }
    }
    else{
      $counter=0;
    if ($filtered->num_rows > 0) {
      while($row = $filtered->fetch_assoc()) {
        if($_SESSION["isMerchant"]){
          $price = $row["wholesale"];
        }
        else{
          $price = $row["retail"];  
        }
        $img=base64_encode($row["photo"]);      
        $name=$row["name"];
        $amount=$row["SUM(amount)"];
        if($counter%3==0){
          echo '<div class="container">
                  <div class="row">
                    <div class="col-sm-4">
                      <div class="cardcontainer" >
                        <a href="productdetails.php?p_id='.$row["p_id"].'">
                          <div class="prodCard">
                            <div style="display:none;" id="p_id'.$counter.'" value="'.$name.'">'.$name.'</div>
                            <img src="data:image/jpg;charset=utf8;base64,'.$img.'" class="img-responsive" style="max-height:300px" alt="Image">
                            <h6>'.$row["name"].'</h6>
                            <span class="glyphicon glyphicon-euro">'.$price.'</span>
                            <p> Amount: '.$amount.'</p>
                          </div>
                        </a>
                      </div>
                    </div>';  
        }
        else{
          echo'<div class="col-sm-4">
                <div class="cardcontainer" >
                  <a href="productdetails.php?p_id='.$row["p_id"].'">
                    <div class="prodCard">
                      <div style="display:none;" id="p_id'.$counter.'" value="'.$name.'">'.$name.'</div>
                      <img src="data:image/jpg;charset=utf8;base64,'.$img.'" class="img-responsive" style="height:300px" alt="Image">
                      <h6>'.$row["name"].'</h6>
                      <span class="glyphicon glyphicon-euro">'.$price.'</span>
                      <p> Amount: '.$amount.'</p>
                    </div>
                  </a>
                </div>
              </div>';
          if($counter%2==0){
            echo'</div>    
              </div><br>';
          }
        }
        $counter++;
      }
      if(($counter-1)%2!=0){
        echo'</div>    
        </div><br>';
      }
    }
    else{
      echo "No Wines where found";  
    }
    }
  ?>
  </div>
</div><br>

</div><br><br>
</div>
<footer class="footer">
  <p>Online Store Copyright</p>  
  <p>George Mavridis, George Kampitakis, George Todoulakis</p>
  
</footer>
<script>    
    $('.filter').on('click', function(e) {
      $('.filter_container').toggleClass("open"); //you can list several class names 
      e.preventDefault();
    });

    $('.varietydiv').on('click', function(e) {
      $('#vardrop').toggleClass("opendrop"); //you can list several class names 
      e.preventDefault();
    });

    $('.winerydiv').on('click', function(e) {
      $('#windrop').toggleClass("opendrop"); //you can list several class names 
      e.preventDefault();
    });  
</script>

</body>
</html>
