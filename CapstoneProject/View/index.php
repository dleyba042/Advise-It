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

      ini_set('display_errors',1);
      error_reporting(E_ALL);

      if(!$_SERVER['QUERY_STRING'])
      {

     echo
       " 
        <form action='advise.php' method='get'>
          <div style='text-align:center;'>
            <h1 style='text-align:center;'>Welcome to the Advise-It Tool</h1>
            <button style='font-size:1.5rem; 'type='submit'>Create a Plan?</button>
            
          </div>
        </form>
        ";

      }
      else
      {
        ##THIS is where I will display the editable and savable version of any viewed Plan
        echo $_SERVER['QUERY_STRING'];
      }


    ?>

    <script src='index.js'></script>


   
  </body>
</html>
