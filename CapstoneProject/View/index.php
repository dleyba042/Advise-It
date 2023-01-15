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
   
   <?php

      if(!$_SERVER['QUERY_STRING'])
      {

     echo
       " 
        <form action='advise.php' method='get'>
        <h1>Welcome to the Advise-It Tool</h1>
        <button type='submit'>Create a Plan?</button>
        <script src='index.js'></script>
        </form>
        ";

      }
      else
      {
        ##THIS is where I will display the editable and savable version of any viewed Plan
        echo $_SERVER['QUERY_STRING'];
      }


    ?>



   
  </body>
</html>
