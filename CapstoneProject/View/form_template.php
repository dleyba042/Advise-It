<?php
//Lets set my variables here
//All previously saved info 
//and it will deal with updates too
$token = "https://dleyba-brown.greenriverdev.com/CapstoneProject/View/advise.php?planID=". $_SESSION["token"];
$saved = $_SESSION["saved"];

?>

<div id='token_div'>
      <h4> <?php echo "Link to view and edit: ". $token ?></h4>
      </div>

<form action="#" method = "post">  
    <div id = "main_container">  
      
      <div class = "textContainer">

        <h5>Fall</h5>
        <textarea id = "fall">
        </textarea>  
      
      </div>  

      <div class = "textContainer">

        <h5>Winter</h5>
        <textarea id = "winter">
        </textarea>  
        
      </div>  

      <div class = "textContainer">

        <h5>Spring</h5>
        <textarea id = "spring">
        </textarea>  
      
      </div>  

      <div class = "textContainer">

        <h5>Summer</h5>
        <textarea id = "summer">
        </textarea>  
      
      </div>  

    </div>
    <br>
    <br>
    <br>
    </form>

    <div id = "button_div">
        <button id="submit_button" type="submit"> SAVE </button>  
    </div>

    <h5 style = 'text-align:center;'> Last saved on <?php echo $saved ?></h5>;