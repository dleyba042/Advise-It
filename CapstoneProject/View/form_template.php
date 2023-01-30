<?php
$saved = $_SESSION["saved"];
$fall = $_SESSION["fall"];
$winter = $_SESSION["winter"];
$spring = $_SESSION["spring"];
$summer = $_SESSION["summer"];
$advisor = $_SESSION["advisor"];
$year = $_SESSION["school_year"];
?>
    <div class = "plan_container">  
      
      <div class = "textContainer">
        <h5>Fall <?php echo $year?></h5>
        <textarea id = "fall" name="fall_<?php echo $year?>"><?php echo $fall?></textarea>   
      </div>  

      <div class = "textContainer">
        <h5>Winter  <?php echo ($year + 1)?></h5>
        <textarea id = "winter" name="winter_<?php echo $year?>"><?php echo $winter?></textarea>  
      </div>  

      <div class = "textContainer">
        <h5>Spring  <?php echo ($year + 1)?></h5>
        <textarea id = "spring" name="spring_<?php echo $year?>"><?php echo $spring?></textarea>     
      </div>  
      
      <div class = "textContainer">
        <h5>Summer  <?php echo ($year + 1)?></h5>
        <textarea id = "summe" name="summer_<?php echo $year?>"><?php echo $summer?></textarea>  
      </div>  

    </div>
    <br>
    
    