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
        $model->initPLanDB($_SESSION["token"], YEAR_STARTER);

        $_SESSION["fall"] = "";
        $_SESSION["winter"] = "";
        $_SESSION["spring"] = "";
        $_SESSION["summer"] = "";
        $_SESSION["advisor"] = "";

        include("form_template.php");
      }

    }  
    else
    {
      //retrieve data they are visiting to edit or view
      if(empty($_POST))
      {

        $plans = $model->retreivePlansInOrder($_SESSION["token"]);
        $_SESSION["fall"] = $plans[0]["fall"];
        $_SESSION["winter"] = $plans[0]["winter"];
        $_SESSION["spring"] = $plans[0]["spring"];
        $_SESSION["summer"] = $plans[0]["summer"];

        $otherInfo = $model->retreiveAdvisorAndTime($_SESSION["token"]);
        $_SESSION["saved"] = $otherInfo[0]["last_saved"];
        $_SESSION["advisor"] = $otherInfo[0]["advisor"];

        include("form_template.php");

      }
      //then its a new save
      else
      {
    
        //Save into the database
        $newSaved = date_create('now')->format('Y-m-d H:i:s');
        //100 is 

        $model->updatePlanTable(YEAR_STARTER,$_POST["fall"],$_POST["winter"]
        ,$_POST["spring"],$_POST["summer"],$_SESSION["token"]);

        $model->updateTokenTable($_SESSION["token"], $newSaved, $_POST["advisor"]);

        //Update our session variables
        $_SESSION["saved"] = $newSaved;
        $_SESSION["fall"] = $_POST["fall"];
        $_SESSION["winter"] = $_POST["winter"];
        $_SESSION["spring"] = $_POST["spring"];
        $_SESSION["summer"] = $_POST["summer"];
        $_SESSION["advisor"] = $_POST["advisor"];

        //Display the updated page
        include("form_template.php");

      }

    }

      
    ?>

 <script src='../JS/advise.js' type='text/javascript'> </script>

  </body>
</html>