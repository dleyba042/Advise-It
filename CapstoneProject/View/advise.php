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

        $sql = "SELECT * FROM `StudentTokens`";
        //Using PDO now so prepare
        $statement = $cnxn->prepare($sql);
        //Execute the PDO statement
        $statement->execute();
        
        while($result = $statement->fetch((PDO::FETCH_ASSOC)))
        {
          echo $result['Unique_Token'] . "<br>";
        }
        echo "<h3> I will be sure to advise you now!!</h3>"
    ?>
    </form>

  </body>
</html>