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

        $otherInfo = $model->retreiveAdvisorAndTime($_SESSION["token"]);
        $_SESSION["saved"] = $otherInfo[0]["last_saved"];
        $_SESSION["advisor"] = $otherInfo[0]["advisor"];

        $plans = $model->retreivePlansInOrder($_SESSION["token"]);


        //TODO test on retreival  
         //How bout we say
        //for every year we have 
        //update the variables and set the id's for the php properly
        //then add a from template and use those variables for each one we update


        //INSERT INTO `Plan_Info`(`fall`, `winter`, `spring`, `summer`, `year_num`, `token`) VALUES ("fall","rwg","fawrgwr","frwgwrl",99,"63d2cacd79f01")


        // FOR TESTING ->  63d2cacd79f01

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

          echo "<h1> " . $plans[$i]["school_year"] . "</h1>";
          include("form_template.php");
        }
      

      }
      //then its a new save
      else
      {

        echo "SSHOULD UPDATE";

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