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

        $schoolYear = date("Y");
        $month = date("m");
        if($month <= 7) 
        {
          $schoolYear--;
        }

        $_SESSION["saved"] = $model->makeNewPlan($_SESSION["token"], $schoolYear);
        $_SESSION["fall"] = "";
        $_SESSION["winter"] = "";
        $_SESSION["spring"] = "";
        $_SESSION["summer"] = "";
        $_SESSION["advisor"] = "";
        $_SESSION["school_year"] = $schoolYear;

        $token = $_SESSION["token"];
        $prev = $schoolYear - 1;
        $next = $schoolYear + 1;

        //Dynamically create the button so we can limit the years added
        echo "<button type='button' name='prev_button' id='prev_button' value = '{$_SESSION['token']}:$prev:$schoolYear'> add previous year</button>";
        //div to append plans to
        echo "<div id = 'all_plans'> ";
        echo "<h1>" . $schoolYear . " School Year </h1>";
        include("form_template.php");
        echo "</div> ";
        //Generate next button
        echo "<button type='button' name='next_button' id='next_button' value = '{$_SESSION['token']}:$next:$schoolYear'> add next year</button>"; 

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

        $initialYear = $model->getInitialYear($_SESSION["token"]);
        $initialYear = $initialYear[0]["initial_year"];
      
         //for each plan in order
        //update my variables and put them in session
        //then add to the form template
        for ($i = 0; $i < count($plans); $i++) 
        {
          if($i == 0)
          {
            $prev = $plans[$i]["school_year"] - 1;
            //Create previous button
            echo "<button type='button' name='prev_button' id='prev_button' value = '{$_SESSION['token']}:$prev:$initialYear'> add previous year</button>";
            //div to append plans to
            echo "<div id = 'all_plans'> ";
          }

          $_SESSION["fall"] = $plans[$i]["fall"];
          $_SESSION["winter"] = $plans[$i]["winter"];
          $_SESSION["spring"] = $plans[$i]["spring"];
          $_SESSION["summer"] = $plans[$i]["summer"];
          $_SESSION["school_year"] = $plans[$i]["school_year"];

          echo "<h1> " . $plans[$i]["school_year"] . " School Year</h1>";
          include("form_template.php");          

          if($i == count($plans) - 1)
          {
            echo "</div> ";
            $next = $plans[$i]["school_year"] + 1;
            echo "<button type='button' name='next_button' id='next_button' value = '{$_SESSION['token']}:$next:$initialYear'> add next year</button>"; 
          }
        }


      }
      //then its a new save
      else 
      {

      //  var_dump($_POST);

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
        //$yearsToUpdate = $model->getSchoolYearsInOrder($_SESSION["token"]);
        $yearsToUpdate = (count($_POST) - 1) / 4;
        $firstYear = $yearsToUpdate; //TODO MAYBE WRONG

        //generate previous button
        $initialYear = $model->getInitialYear($_SESSION["token"]);
        $initialYear = $initialYear[0]["initial_year"];

        $firstKey = array_key_first($_POST);
        $arrYear = explode("_",$firstKey);
        $arrYear = $arrYear[1];

        echo "YEARS to update = $yearsToUpdate";
        
        while($yearsToUpdate >= 1)
        {
        //Update all years associated with this token
        
          if($yearsToUpdate == $firstYear)
          {
            //Must do it here to get the right value for previous
            $prev = $arrYear - 1;
          
            //Create previous button
            echo "<button type='button' name='prev_button' id='prev_button' value = '{$_SESSION['token']}:$prev:$initialYear'> add previous year</button>";
            //div to append plans to
            echo "<div id = 'all_plans'> ";
          }

          $key1 = "fall_$arrYear";
          $key2 = "winter_$arrYear";
          $key3 = "spring_$arrYear";
          $key4 = "summer_$arrYear";
          $yearExists = $model->yearExists($_SESSION["token"], $arrYear);

          //either create new entry or update existing
          if(!$yearExists)
          {
            $model->createNewPlanEntry($_SESSION["token"],$arrYear, $_POST[$key1], $_POST[$key2]
            , $_POST[$key3], $_POST[$key4]);
          }
          else
          {
            $model->updatePlanTable($arrYear, $_POST[$key1], $_POST[$key2]
            , $_POST[$key3], $_POST[$key4], $_SESSION["token"]
          );
          }

          //Update our session variables
          $_SESSION["fall"] = $_POST[$key1];
          $_SESSION["winter"] = $_POST[$key2];
          $_SESSION["spring"] = $_POST[$key3];
          $_SESSION["summer"] = $_POST[$key4];
          $_SESSION["school_year"] = $arrYear;
        
          echo "<h1> " . $_SESSION["school_year"] . " School Year </h1>";

          //Display the updated page
          include("form_template.php");

          if($yearsToUpdate == 1)
          {
            $next = $arrYear + ((count($_POST) - 1) / 4) - 1;
            echo "</div> ";
            echo "<button type='button' name='next_button' id='next_button' value = '{$_SESSION['token']}:$next:$initialYear'> add next year</button>"; 
          }
        
          $arrYear++;
          $yearsToUpdate -= 1;
        }
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