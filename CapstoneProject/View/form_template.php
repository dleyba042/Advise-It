<?php
//Lets set my variables here
//All previously saved info 
//and it will deal with updates too
$token = "https://dleyba-brown.greenriverdev.com/CapstoneProject/View/advise.php?planID=". $_SESSION["token"];
$saved = $_SESSION["saved"];
$fall = $_SESSION["fall"];
$winter = $_SESSION["winter"];
$spring = $_SESSION["spring"];
$summer = $_SESSION["summer"];

?>

<div id='token_div'>
      <h4> <?php echo "Link to view and edit: ". $token ?></h4>
      </div>

<form action="#" id="plan_form" method = "post">  
    <div id = "main_container">  
      
      <div class = "textContainer">
        <h5>Fall</h5>
        <textarea id = "fall" name="fall"><?php echo $fall?></textarea>   
      </div>  

      <div class = "textContainer">
        <h5>Winter</h5>
        <textarea id = "winter" name="winter"><?php echo $winter?></textarea>  
      </div>  

      <div class = "textContainer">
        <h5>Spring</h5>
        <textarea id = "spring" name="spring"><?php echo $spring?></textarea>     
      </div>  
      
      <div class = "textContainer">
        <h5>Summer</h5>
        <textarea id = "summer" name="summer"><?php echo $summer?></textarea>  
      </div>  

    </div>
    <br>
    <br>
    <br>
    </form>

    <div id = "button_div">
        <button id="submit_button" type="submit" form="plan_form"> SAVE </button>  
    </div>

    <h5 style = 'text-align:center;'> Last saved on <?php echo $saved ?></h5>;