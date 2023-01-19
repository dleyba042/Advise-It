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
    
      <div id="index_div">
        <form action="advise.php" method="get">
          <h1>Welcome to the Advise-It Tool</h1>
          <button id="plan_button" class= "index_button" type="submit">Create Plan</button>
        </form>   
        <button id="admin_button" class = "index_button" >Admin Login</button>
        <div id = "entry_div">
          <label>Username: <input type = "text" id = "user_name"></label> 
          <label>Password: <input type = "text" id = "password"> </label>
          <button class = "index_button" id ="login_button"> Login</button>
        </div>      
      </div>  
    
    
      
    
      <script src='../JS/index.js' type='text/javascript'> </script>
  </body>
</html>
