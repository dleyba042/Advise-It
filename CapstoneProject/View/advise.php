<?php 
##lets connect to our DB right here!!
ini_set('display_errors',1);
error_reporting(E_ALL);
date_default_timezone_set("America/Los_Angeles");

require_once("../Model/Model.php");

//Our model will just exist in a class since this is a simple project
$model = new Model(); 
  
//Then We either redirect or display the results
if($_SERVER['QUERY_STRING'])
{
  //This parses the URL and create an associative array that holds the query string
  parse_str($_SERVER["QUERY_STRING"], $queryToken);
     
  if (!$model->checkValid($queryToken["planID"])) 
  {
    header('Location: https://dleyba-brown.greenriverdev.com/CapstoneProject/View/');
  }
  else
  {
    //Start session and set token
    session_start();
    $_SESSION["token"] =  $queryToken["planID"];
  }  
}
else
{
    //Just Start the session 
    session_start(); 
}

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

    <h1>Welcome to the Advise-It Tool</h1>
    <?php

    //UPDATE our session variables and then display again
    if(!empty($_POST))
    {
      //Give JS alert message
      echo "<script src='../JS/advise.js' type='text/javascript'> </script>";

      //Save into the database
      $newSaved = date_create('now')->format('Y-m-d H:i:s');
      $model->updateData($_POST["fall"],$_POST["winter"],$_POST["spring"],$_POST["summer"],$newSaved,$_SESSION["token"]);

      //Update our session variables
      $_SESSION["saved"] = $newSaved;
      $_SESSION["fall"] = $_POST["fall"];
      $_SESSION["winter"] = $_POST["winter"];
      $_SESSION["spring"] = $_POST["spring"];
      $_SESSION["summer"] = $_POST["summer"];

      //Display the updated page
      include("form_template.php");

    }
    else if (!$_SERVER['QUERY_STRING']) 
    {
      //Create a new plan and identifier
      $returnData = $model->makeNewPlan();

      $_SESSION["token"] = $returnData["token"];
      $_SESSION["saved"] = $returnData["saved"];
      $_SESSION["fall"] = "";
      $_SESSION["winter"] = "";
      $_SESSION["spring"] = "";
      $_SESSION["summer"] = "";
     
      include("form_template.php");

    }
    else
    {
    //Post is empty and we already know this token so display the data that is stored  
    //Retrieve all info associated with that particular code and then display
    
      $currentData = $model->retrieveData($_SESSION["token"]);

      $_SESSION["saved"] = $currentData[0]["last_saved"];
      $_SESSION["fall"] = $currentData[0]["fall_quarter"];
      $_SESSION["winter"] = $currentData[0]["winter_quarter"];
      $_SESSION["spring"] = $currentData[0]["spring_quarter"];
      $_SESSION["summer"] = $currentData[0]["summer_quarter"];
    
      include("form_template.php");
    }
    
    ?>

  </body>
</html>