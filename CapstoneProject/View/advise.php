<?php 
##lets connect to our DB right here!!
ini_set('display_errors',1);
error_reporting(E_ALL);

require("/home/dleybab1/PDOConfig.php");
$cnxn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD)
or die("Error Connecting to database");

  /*
  checks the received token against existing tokens for validation
   */
  function checkValid($existing, $received)
  {
    $good = false;
      foreach ($existing as $token) 
      {
        if ($token['Unique_Token'] == $received) 
        {
          $good = true;
          break;
        }
      }
        return $good;
  }

//Then We either redirect or display the results
if($_SERVER['QUERY_STRING'])
{
      $sql = "SELECT `Unique_Token` FROM `StudentPlans`";
      $statement = $cnxn->prepare($sql);
      $statement->execute();
      $existingTokens = $statement->fetchAll((PDO::FETCH_ASSOC));
      //This parses the URL and create an associative array that holds the query string
      parse_str($_SERVER["QUERY_STRING"], $queryToken);

  if (!checkValid($existingTokens, $queryToken["planID"])) {
    header('Location: https://dleyba-brown.greenriverdev.com/CapstoneProject/View/');
  }
  else
  {
    //Start session and set token
    session_start();
    date_default_timezone_set("America/Los_Angeles");
    $_SESSION["token"] =  $queryToken["planID"];
  }  
}
else
{
    //Start the session 
    session_start();
    date_default_timezone_set("America/Los_Angeles");
}

//START A PHP SESSION
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
      $fall = $_POST["fall"];
      $winter = $_POST["winter"];
      $spring = $_POST["spring"];
      $summer = $_POST["summer"];
      $newSaved = date_create('now')->format('Y-m-d H:i:s');
      

      //I will save here but now I just want to see display updated
      $updateSql = "UPDATE `StudentPlans` SET `fall_quarter` = :fall,`winter_quarter`= :winter, 
      `spring_quarter`= :spring, `summer_quarter` = :summer, `last_saved` = :saved 
      WHERE `unique_token` = :token "; 
      
      $updateStatement = $cnxn->prepare($updateSql);
      $updateStatement->bindParam(":token", $_SESSION["token"]);
      $updateStatement->bindParam(":saved", $newSaved);
      $updateStatement->bindParam(":fall", $fall);
      $updateStatement->bindParam(":winter", $winter);
      $updateStatement->bindParam(":spring", $spring);
      $updateStatement->bindParam(":summer", $summer);
      $updateStatement->execute();

      //Update our session variables
      $_SESSION["saved"] = $newSaved;
      $_SESSION["fall"] = $fall;
      $_SESSION["winter"] = $winter;
      $_SESSION["spring"] = $spring;
      $_SESSION["summer"] = $summer;

      //Display the updated page
      include("form_template.php");


      /*
      UPDATE `StudentPlans` SET `unique_token`=[value-1],`fall_quarter`=[value-2],`winter_quarter`=[value-3],
      `spring_quarter`=[value-4],`summer_quarter`=[value-5],`last_saved`=[value-6] WHERE 1

      */

    }
    else if (!$_SERVER['QUERY_STRING']) 
    {

      //Fetch a list of all the tokens created so far
      $sql = "SELECT `Unique_Token` FROM `StudentPlans`";
      $statement = $cnxn->prepare($sql);
      $statement->execute();
      $existingTokens = $statement->fetchAll((PDO::FETCH_ASSOC));

      /*
      generates a unique ID and checks it against the existing ID's.
      Generates a replacement while the generated ID exists in the database.
      */
      function generateUnique($existing)
      {
        $generatedtoken = null;
        $keepGoing = false;

        do {
          $generatedtoken = uniqid();
          foreach ($existing as $token) {
            if ($generatedtoken == $token['Unique_Token']) {
              $keepGoing = true;
              break;
            }
          }
        } while ($keepGoing);

        return $generatedtoken;
      }

      //Stratement to insert the token  
      $insertSql = "INSERT INTO `StudentPlans`(`unique_token`,`last_saved`) VALUES (:token,:saved)";
      $saved = date_create('now')->format('Y-m-d H:i:s');
      $token = generateUnique($existingTokens);
      $insertStatement = $cnxn->prepare($insertSql);
      $insertStatement->bindParam(":token", $token);
      $insertStatement->bindParam(":saved", $saved);
      $insertStatement->execute();

      $_SESSION["token"] = $token;
      $_SESSION["saved"] = $saved;
      $_SESSION["fall"] = "";
      $_SESSION["winter"] = "";
      $_SESSION["spring"] = "";
      $_SESSION["summer"] = "";
     
      include("form_template.php");
  
    }
    else
    {  
    //TODO  
    //Post is empty and we already know this token so display the data that is stored  
    //Retrieve all info associated with that particular code
    //set my session variables here
    //Should be failry simple now
    //Our session token has been set at the top of the code already we just need to retreive and
    //display the data
    //the rest of the code should be done by now
    ##THIS is where I will display the editable and savable version of any viewed Plan
    include("form_template.php");

    }
    

    
    ?>

    

  </body>
</html>