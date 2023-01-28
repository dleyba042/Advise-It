<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);
date_default_timezone_set("America/Los_Angeles");

require_once("../Model/Model.php");

//Our model will just exist in a class since this is a simple project
$model = new Model();

  //Then We either redirect or display the results
  //This parses the URL and create an associative array that holds the query string
  parse_str($_SERVER["QUERY_STRING"], $queryToken);

  if (empty($_POST) && !$model->checkValid($queryToken["planID"])) 
  {
    header('Location: https://dleyba-brown.greenriverdev.com/CapstoneProject/View/');
  }

  //Start session and set token
  if (session_status() === PHP_SESSION_NONE) 
  {
    session_start();
  }

  $_SESSION["token"] =  $queryToken["planID"];
  $displayToken = "https://dleyba-brown.greenriverdev.com/CapstoneProject/View/advise.php?planID=". $_SESSION["token"];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Advise-It Tool</title>
    <link rel="stylesheet" href="../Styles/style.css" />

    <!-- CDN for JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" 
    integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  </head>
  <body>

   
  <div class= "header_footer">
    <h1>Student Plans</h1>
  </div>
  
  <div id='token_div'>
      <h4> <?php echo "Link to view and edit plans: <a href = ' ". $displayToken."'>". $displayToken."</a>" ?></h4>
  </div>

  <button type="button" name="prev_button" id="prev_button" value = "<?php echo $_SESSION["token"] ?>"> add previous year</button>

  <form action="#" id="plan_form" method = "post"> 

    <?php

    //New plan then
    if(array_key_exists("new",$_POST))
    {
      //Used to verify the token hasnt been modified
      //could be expanded
      if(strcmp($_SESSION["verifier"],$_SESSION["token"]) != 0)
      {
        echo "NO sql injection please!!";
      } else
      {
        //generate their first entry into the plan database
        $initialYear = $model->initPLanDB($_SESSION["token"]);

        $_SESSION["saved"] = $model->makeNewPlan($_SESSION["token"], $initialYear);
        $_SESSION["fall"] = "";
        $_SESSION["winter"] = "";
        $_SESSION["spring"] = "";
        $_SESSION["summer"] = "";
        $_SESSION["advisor"] = "";
        $_SESSION["school_year"] = $initialYear;

        echo "<h1>" . $initialYear . " School Year </h1>";
        include("form_template.php");
      }
    } else 
    {
      //retrieve data they are visiting to edit or view
      if (empty($_POST)) 
      {
        $otherInfo = $model->retreiveAdvisorAndTime($_SESSION["token"]);
        $_SESSION["saved"] = $otherInfo[0]["last_saved"];
        $_SESSION["advisor"] = $otherInfo[0]["advisor"];

        $plans = $model->retreivePlansInOrder($_SESSION["token"]);

        //for each plan in order
        //update my variables and put them in session
        //then add to the form template
    
        for ($i = 0; $i < count($plans); $i++) 
        {
          $_SESSION["fall"] = $plans[$i]["fall"];
          $_SESSION["winter"] = $plans[$i]["winter"];
          $_SESSION["spring"] = $plans[$i]["spring"];
          $_SESSION["summer"] = $plans[$i]["summer"];
          $_SESSION["school_year"] = $plans[$i]["school_year"];

          echo "<h1> " . $plans[$i]["school_year"] . " School Year</h1>";
          include("form_template.php");
        }
      }
      //then its a new save
      else 
      {
        //Save into the easy stuff first
        $newSaved = date_create('now')->format('Y-m-d H:i:s');
        $model->updateTokenTable($_SESSION["token"], $newSaved, $_POST["advisor"]);

        //update their session
        $_SESSION["saved"] = $newSaved;
        $_SESSION["advisor"] = $_POST["advisor"];

        $yearsToUpdate = $model->getSchoolYearsInOrder($_SESSION["token"]);

        //Update all years associated with this token
        for ($i = 0; $i < count($yearsToUpdate); $i++) 
        {
          $year = $yearsToUpdate[$i]["school_year"];

          $model->updatePlanTable($year, $_POST["fall_$year"], $_POST["winter_$year"]
            , $_POST["spring_$year"], $_POST["summer_$year"], $_SESSION["token"]
          );

          //Update our session variables
          $_SESSION["fall"] = $_POST["fall_$year"];
          $_SESSION["winter"] = $_POST["winter_$year"];
          $_SESSION["spring"] = $_POST["spring_$year"];
          $_SESSION["summer"] = $_POST["summer_$year"];
          $_SESSION["school_year"] = $year;
        
          echo "<h1> " . $_SESSION["school_year"] . " School Year </h1>";

          //Display the updated page
          include("form_template.php");
        }
      }
    }
    ?>

    <button type="button" name="next_button" id="next_button" value = "<?php echo $_SESSION["token"] ?>"> add next year</button>

    <div id = "button_div">
      <button class = "plan_button" id="submit_button" type="submit" form="plan_form"> SAVE </button>  
      <button class = "plan_button" id = "print_button" type="button"> PRINT </button>
    </div>

    <div id= "bottom_div" class="header_footer">
      <h5> Advisor: <input type = "text" name = "advisor" value ="<?php echo htmlspecialchars($advisor)?>"></h5>
      <h5 > Last saved on <?php echo $saved ?></h5>
    </div>

  </form>

 <script src='../JS/advise.js' type='text/javascript'> </script>

  </body>
</html>