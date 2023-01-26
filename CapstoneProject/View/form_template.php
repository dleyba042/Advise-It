<?php
//Lets set my variables here
//All previously saved info 
//and it will deal with updates to

$saved = $_SESSION["saved"];
$fall = $_SESSION["fall"];
$winter = $_SESSION["winter"];
$spring = $_SESSION["spring"];
$summer = $_SESSION["summer"];
$advisor = $_SESSION["advisor"];

?>

    <div class = "plan_container">  
      
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
    
    