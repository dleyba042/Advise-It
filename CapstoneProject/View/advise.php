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
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <h1>Welcome to the Advise-It Tool</h1>

    <?php

      //WHat we really need to do is
      $sql = "SELECT * FROM `StudentTokens`";
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

    $token = generateUnique($existingTokens);

        
      
    ?>
    </form>

  </body>
</html>