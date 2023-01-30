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
        $schoolYear = $model->getSchoolYear();
        $_SESSION["saved"] = $model->makeNewPlan($_SESSION["token"], $schoolYear);
        //INIT PLAN DB for this start school year so the token doesnt get thrown out
        $model->initPLanDB($_SESSION["token"]);
        $model->displayInitialPlanScreen($schoolYear, $_SESSION["token"]);
      }
    } else 
    {
      //retrieve data they are visiting to edit or view
      if (empty($_POST)) 
      {

        $otherInfo = $model->retreiveAdvisorAndTime($_SESSION["token"]);

        $_SESSION["saved"] = $otherInfo[0]["last_saved"];
        $_SESSION["advisor"] = $otherInfo[0]["advisor"];
        $model->displayEmptyPostScreen($_SESSION["token"]);

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

        //POST goes in order from top to bottom
        //I send advisor so that throws the count off by one 
        // but if we minus count($_Post) by one and thn we divide by four
        //we know how many to update
        $yearsToUpdate = (count($_POST) - 1) / 4;
        $firstYear = $yearsToUpdate; 

        //generate previous button
        $initialYear = $model->getInitialYear($_SESSION["token"]);
        $initialYear = $initialYear[0]["initial_year"];
        $firstKey = array_key_first($_POST);
        $arrYear = explode("_",$firstKey);
        $arrYear = $arrYear[1];
        $model->displayAfterSave($yearsToUpdate, $firstYear, $_SESSION["token"], $initialYear, $arrYear);
      }
    }
    ?>

  </form>

 <script src='../JS/advise.js' type='text/javascript'> </script>

  </body>
</html>