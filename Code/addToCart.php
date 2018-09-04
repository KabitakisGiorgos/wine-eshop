<?php
  include('session.php');
  $found=false;
  if(isset($_POST["p_id"])){
    
    for($i=0;$i<count($_SESSION['p_id']);$i++)
    {
      if(strcmp($_SESSION['p_id'][$i],$_POST["p_id"])==0){
        $_SESSION["amount"][$i]++;
        $found=true;
      } 
    }
    if(!$found){
      $_SESSION["p_id"][]=$_POST["p_id"];
      $_SESSION["price"][]=$_POST["price"];
      $_SESSION["name"][]=$_POST["name"];
      $_SESSION["amount"][]=1;
    }
  }

  if(isset($_POST["showcart"])){
    if(!array_key_exists('p_id',$_SESSION) || empty($_SESSION['p_id'])) {
      echo "";
    }else{
      if(count($_SESSION['p_id'])==0){
        echo "";
      }else{
        for($i=0;$i<count($_SESSION['p_id']);$i++)
        {
          $finalprice=$_SESSION["price"][$i]*$_SESSION["amount"][$i];
          echo '<li class="list-group-item">
                  <h5 id="Name">'.$_SESSION["name"][$i].'</h5>
                  <h5 id="price">'.$finalprice.'</h5>               
                  <h5 id="amount">'. $_SESSION["amount"][$i].'</h5>
                  <h5 style="display:none;" id="p_id">'.$_SESSION["p_id"][$i].'</h5>
                  <div class="circle pull-right" onclick="removeItem('.$_SESSION["p_id"][$i].')";>
                    <img style="width:20px; margin-left:5.5px; margin-top:5px;" src="https://png.icons8.com/material/100/ffffff/trash.png">
                  </div>
                </li>';
        }
      }
    }
  }

  if(isset($_POST['estimatePrice'])){
    $amount=0;
    $price=0;
    if(!array_key_exists('p_id',$_SESSION) || empty($_SESSION['p_id'])) {
      echo "";
    }else{
      if(count($_SESSION['p_id'])==0){
        echo "";
      }else{
        for($i=0;$i<count($_SESSION['p_id']);$i++)
        {
          $amount=$amount+$_SESSION["amount"][$i];
          $price=$price+$_SESSION["price"][$i]*$_SESSION["amount"][$i];
        }
        if($_SESSION['isMerchant'] && $amount>22 && $amount<25){
          $price=$price-0.05*$price;
        }else if($_SESSION['isMerchant'] && $amount>=25){
          $price=$price-0.1*$price;
        }
        echo $price;
      }
    }
  }

  if(isset($_POST['removeId'])){
    for($i=0;$i<count($_SESSION['p_id']);$i++)
    {
      if(strcmp($_SESSION['p_id'][$i],$_POST["removeId"])==0){
        unset($_SESSION["name"][$i]);
        unset($_SESSION["price"][$i]);
        unset($_SESSION["amount"][$i]);
        unset($_SESSION["p_id"][$i]);
        $_SESSION["name"] = array_values($_SESSION["name"]);
        $_SESSION["price"] = array_values($_SESSION["price"]);
        $_SESSION["amount"] = array_values($_SESSION["amount"]);
        $_SESSION["p_id"] = array_values($_SESSION["p_id"]);
        
      } 
    }

  }

  if(isset($_POST["deleteCart"])){
    for($i=0;$i<count($_SESSION['p_id']);$i++)
    {
      unset($_SESSION["name"][$i]);
      unset($_SESSION["price"][$i]);
      unset($_SESSION["amount"][$i]);
      unset($_SESSION["p_id"][$i]);
    }
    unset($_SESSION["name"]);
    unset($_SESSION["price"]);
    unset($_SESSION["amount"]);
    unset($_SESSION["p_id"]);
  }
?>