<?php 
##lets connect to our DB right here!!
ini_set('display_errors',1);
error_reporting(E_ALL);

require("/home/dleybab1/dbcreds.php");
$cnxn = mysqli_connect($host,$username,$password,$database)
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
        $result = mysqli_query($cnxn,$sql);
        while($row = mysqli_fetch_array($result))
        {
          echo $row['Unique_Token'];
        }
        echo "<h3> I will be sure to advise you now!!</h3>"
    ?>
    </form>

  </body>
</html>