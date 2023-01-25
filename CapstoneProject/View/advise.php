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
 // else
 // {
    //Start session and set token
    session_start();
    $_SESSION["token"] =  $queryToken["planID"];
 // }  


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

      $_SESSION["saved"] = $model->makeNewPlan($_SESSION["token"]);
      $_SESSION["fall"] = "";
      $_SESSION["winter"] = "";
      $_SESSION["spring"] = "";
      $_SESSION["summer"] = "";
      $_SESSION["advisor"] = "";
     
      include("form_template.php");

    }  
    else
    {
      //retrieve data
      if(empty($_POST))
      {

        $currentData = $model->retrieveData($_SESSION["token"]);

        $_SESSION["saved"] = $currentData[0]["last_saved"];
        $_SESSION["fall"] = $currentData[0]["fall_quarter"];
        $_SESSION["winter"] = $currentData[0]["winter_quarter"];
        $_SESSION["spring"] = $currentData[0]["spring_quarter"];
        $_SESSION["summer"] = $currentData[0]["summer_quarter"];
        $_SESSION["advisor"] = $currentData[0]["advisor"];

        include("form_template.php");

      }
      //then its a new save
      else
      {
        //Save into the database
        $newSaved = date_create('now')->format('Y-m-d H:i:s');
        $model->updateData($_POST["fall"],$_POST["winter"],$_POST["spring"],$_POST["summer"],$newSaved,$_SESSION["token"]
        ,$_POST["advisor"]);

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