<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);
date_default_timezone_set("America/Los_Angeles");

require_once("../Model/Model.php");

//Our model will just exist in a class since this is a simple project
$model = new Model();

define("YEAR_STARTER",100);
  
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
  </head>
  <body>

  <form action="#" id="plan_form" method = "post">  

  <div class= "header_footer">
    <h1>Student Plans</h1>
  </div>
  
  <div id='token_div'>
      <h4> <?php echo "Link to view and edit plans: <a href = ' ". $displayToken."'>". $displayToken."</a>" ?></h4>
  </div>

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
        $_SESSION["saved"] = $model->makeNewPlan($_SESSION["token"], YEAR_STARTER - 1, YEAR_STARTER + 1);

        //generate their first entry into the plan database
        $initialYear = $model->initPLanDB($_SESSION["token"], YEAR_STARTER);

        $_SESSION["fall"] = "";
        $_SESSION["winter"] = "";
        $_SESSION["spring"] = "";
        $_SESSION["summer"] = "";
        $_SESSION["advisor"] = "";
        $_SESSION["year_num"] = YEAR_STARTER;

        echo "<h1>" . $initialYear . " School Year </h1>";
        include("form_template.php");

      }
    }  
    else
    {
      //retrieve data they are visiting to edit or view
      if(empty($_POST))
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
          $_SESSION["year_num"] = $plans[$i]["year_num"];
          $_SESSION["school_year"] = $plans[$i]["school_year"];

          echo "<h1> ".$plans[$i]["school_year"]."</h1>";
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
        
        //FOR the TOTAL amount of years update the plan TABLE
        $count = $model->determinePlanCount($_SESSION["token"]);

        //Get the start and end years
        $yearsToIterate = $model->determineStartAndEndYear($_SESSION["token"]);

        //NOw we have all the years to iterate thru for our update
        //JUST iterate that one year then
        if($yearsToIterate[0]["newest"] == $yearsToIterate[0]["oldest"])
        {
          $year = $yearsToIterate[0]["newest"];

          //THEN just display the one
          $model->updatePlanTable($year,$_POST["fall_$year"],$_POST["winter_$year"]
        ,$_POST["spring_$year"],$_POST["summer_$year"],$_SESSION["token"]);
        
        //Update our session variables
        //INCLUDING THE PLAN YEAR  
        $_SESSION["fall"] = $_POST["fall_$year"];
        $_SESSION["winter"] = $_POST["winter_$year"];
        $_SESSION["spring"] = $_POST["spring_$year"];
        $_SESSION["summer"] = $_POST["summer_$year"];
        $_SESSION["year_num"] = $year;

        //NEED TO GET SCHOOL YEAR ASSOCIATED WITH THIS PLAN
        $_SESSION["school_year"] = $model->getSchoolYear($_SESSION["token"],$year)[0]["school_year"];

        echo "<h1> ".$_SESSION["school_year"]."</h1>";
        include("form_template.php");

        }
        else
        //Check all years and display the right ones (ALL FOR NOW)
        {
          
        for($i = $yearsToIterate[0]["oldest"]; $i <= $yearsToIterate[0]["newest"]; $i++)
        {

        $model->updatePlanTable($i,$_POST["fall_$i"],$_POST["winter_$i"]
        ,$_POST["spring_$i"],$_POST["summer_$i"],$_SESSION["token"]);
        
        //Update our session variables
        //INCLUDING THE PLAN YEAR  
        $_SESSION["fall"] = $_POST["fall_$i"];
        $_SESSION["winter"] = $_POST["winter_$i"];
        $_SESSION["spring"] = $_POST["spring_$i"];
        $_SESSION["summer"] = $_POST["summer_$i"];
        $_SESSION["year_num"] = $i;

        //NEED TO GET SCHOOL YEAR ASSOCIATED WITH THIS PLAN
        $_SESSION["school_year"] = $model->getSchoolYear($_SESSION["token"],$i)[0]["school_year"];

        echo "<h1> ".$_SESSION["school_year"]."</h1>";
        
        //Display the updated page
        include("form_template.php");

          }

         }

         //TODO
         //TODO
         //TODO BUILD IN BUTTONS TO ADJUST THE YEARS AND ADD PLANS
         // WHEN a PLAN is added need to do an AJAX query to init a DB entry for that new plan 
         //So that when a post is made it will be saved properly

        //
        //UPDATE `Token_Info` `next_previous_year`= 98 , WHERE 1
//63d2dde199e7d
        //Use those in my for loop to update everything!!!!!
     
/*
USE WHEN NEW YEARS ARE ADDED
            UPDATE `Token_Info` SET
    `next_previous_year`= 98 
    WHERE 
    `token` = "63d2dde199e7d"
*/
 
        // FOR TESTING ->  63d2cacd79f01 -> has multiple years already if I want to use it

      }
    }
    ?>

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