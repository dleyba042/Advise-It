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
$advisor = $_SESSION["advisor"];

?>

<div class= "header_footer">
   <h1>Advise-It Tool</h1>
</div>

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
    

    <div id = "button_div">
        <button class = "plan_button" id="submit_button" type="submit" form="plan_form"> SAVE </button>  
        <button class = "plan_button" id = "print_button" type="button"> PRINT </button>
    </div>

    <div id= "bottom_div" class="header_footer">
      <h5> Advisor: <input type = "text" name = "advisor" value ="<?php echo htmlspecialchars($advisor)?>"></h5>
      <h5 > Last saved on <?php echo $saved ?></h5>
    </div>
    </form>