<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Advise-It Admin</title>

    <!-- CDN for JQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" 
    integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" 
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Datatables CSS-->
    <link rel = "stylesheet" href = "rhttps://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <!-- Datatables JS-->
    <script type="text/javascript" charset="UTF-8" src = "https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <!-- Internal CSS -->
    <link rel="stylesheet" href="../Styles/style.css" />

  </head>
  <body>

  <div class= "header_footer">
    <h1>Admin Page</h1>
  </div>

  <?php
 
    ini_set('display_errors',1);
    error_reporting(E_ALL);
    require_once("../Model/Model.php");

    $model = new Model(); 

    $dbData = $model->retrieveAllData();

  if (!empty($dbData)) 
  {
      echo "<table id = 'myTable' style='width:80%; margin:auto;'>
        
    <thead>

    <tr>
        <th>
            Unique Token
        </th>
        <th>
            Fall Quarter
        </th>
        <th>
            Winter Quarter
        </th>
        <th>
            Spring Quarter
        </th>
        <th>
            Summer Quarter
        </th>
        <th>
            Last Saved
        </th>
        <th>
            Advisor
        </th>
        <th>
            School Year
        </th>
    </tr>
    </thead>
    <tbody>";

      $len = count($dbData);

      for ($i = 0; $i < $len; $i++)
      {
          echo"<tr>";
          echo "<td>".$dbData[$i]["token"]."</td>";
          echo "<td>".$dbData[$i]["fall"]."</td>";
          echo "<td>".$dbData[$i]["winter"]."</td>";
          echo "<td>".$dbData[$i]["spring"]."</td>";
          echo "<td>".$dbData[$i]["summer"]."</td>";
          echo "<td>".$dbData[$i]["last_saved"]."</td>";
          echo "<td>".$dbData[$i]["advisor"]."</td>";
          echo "<td>".$dbData[$i]["school_year"]."</td>";
          
          echo"</tr>";          
      }

      echo "</tbody>
            </table> ";

  }


    ?>

    <script> 
    $('#myTable').DataTable({
        responsive: true
    });

    </script>
    
 
  </body>
</html>




