<?php
$saved = $_SESSION["saved"];
$advisor = $_SESSION["advisor"];
?>

<div id = "button_div">
    <button class = "plan_button" id="submit_button" type="submit" form="plan_form"> SAVE </button>  
    <button class = "plan_button" id = "print_button" type="button"> PRINT </button>
</div>

<div id= "bottom_div" class="header_footer">
    <h5> Advisor: <input type = "text" name = "advisor" value ="<?php echo htmlspecialchars($advisor)?>"></h5>
    <h5 > Last saved on <?php echo $saved ?></h5>
</div>