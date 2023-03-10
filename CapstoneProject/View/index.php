<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);
date_default_timezone_set("America/Los_Angeles");

require_once("../Model/Model.php");

session_start();
//Our model will just exist in a class since this is a simple project
$model = new Model(); 
$token = $model->generateUnique($model->getExistingTokens());
$_SESSION["verifier"] = $token;

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Advise-It Home</title>
    <link rel="stylesheet" href="../Styles/style.css" />
  </head>
  <body>
    
      <div id="index_div">
        <form action="advise.php?planID=<?php echo $token ?>" method="post">
          <h1>Welcome to the Advise-It Tool</h1>
          <button id="plan_button" class= "index_button" type="submit">Create Plan</button>
          <input type="text" style="display: none;" name="new" value = "true">
        </form>   
        <button id="admin_button" class = "index_button" >Admin Login</button>
        <div id = "hidden_div" class="hidden">
          <label>Username: <input type = "text" id = "user_name"></label> 
          <span class="hidden" id = "user_warning">Incorrect username entered</span>
          <label>Password: <input type = "text" id = "password"> </label>
          <span class="hidden" id = "pass_warning">Incorrect password entered</span>
          <button class = "index_button" id ="login_button"> Login</button>
        </div>      
      </div>  
    
      <script src='../JS/index.js' type='text/javascript'> </script>
  </body>
</html>
