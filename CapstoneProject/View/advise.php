<?php 
##lets connect to our DB right here!!
ini_set('display_errors',1);
error_reporting(E_ALL);

require("/home/dleybab1/PDOConfig.php");
$cnxn = new PDO(DB_DSN,DB_USERNAME,DB_PASSWORD)
or die("Error Connecting to database");

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

      //WHat we really need to do is
      $sql = "SELECT `Unique_Token` FROM `StudentPlans`";
        //Using PDO now so prepare
      $statement = $cnxn->prepare($sql);
        //Execute the PDO statement
      $statement->execute();

      //Fetch a list of all the tokens created so far
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
          foreach ($existing as $token) 
          {
            if ($generatedtoken == $token['Unique_Token']) 
            {
              $keepGoing = true;
              break;
            }
          }
        } while ($keepGoing);

        return $generatedtoken;
      }

      //Stratement to insert the token  
      $insertSql = "INSERT INTO `StudentPlans`(`unique_token`) VALUES (:token)";  
      $token = generateUnique($existingTokens);
      $insertStatement = $cnxn->prepare($insertSql);
      $insertStatement->bindParam(":token",$token);
      $insertStatement->execute();

    echo
      "
      <div id='token_div' style = 'border: 10px solid black; text-align:center';>
      <h4> Link with unique token: 
      https://dleyba-brown.greenriverdev.com/CapstoneProject/View/?planID=".$token."</h4>
      </div>
      ";

    ?>

    <form action="#" method = "post">  
    <div id = "main_container">  
      

      <div class = "textContainer">

        <h5>Fall</h5>
        <textarea id = "fall">
        </textarea>  
      
      </div>  

      <div class = "textContainer">

        <h5>Winter</h5>
        <textarea id = "winter">
        </textarea>  
        
      
      </div>  

      <div class = "textContainer">

        <h5>Spring</h5>
        <textarea id = "spring">
        </textarea>  
      
      </div>  

      <div class = "textContainer">

        <h5>Summer</h5>
        <textarea id = "summer">
        </textarea>  
      
      </div>  

    </div>
    <br>
    <br>
    <br>
    </form>

    <div id = "button_div">
        <button id="submit_button" type="submit"> SAVE </button>  
    </div>
   
    <?php

    $lastSavedValue = date_create('now')->format('Y-m-d H:i:s');

    echo "<h5 style = 'text-align:center; '> Last saved on $lastSavedValue </h5";

    ?>


  </body>
</html>