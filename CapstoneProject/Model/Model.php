<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

class Model
{
    private $_dbo;

    function __construct() 
     {
        require("/home/dleybab1/PDOConfig.php");
        try 
        {
            $this->_dbo = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        }
        catch(PDOException $e)
        {
            echo "Error connecting to Database " . $e->getMessage();
        }
     }

     /**
      * ensures the token received exists in the database
      * @param mixed $received
      * @return bool
      */
  
        function checkValid($received)
        {

            $sql = "SELECT `token` FROM `Token_Info`";
            $statement = $this->_dbo->prepare($sql);
            $statement->execute();
            $existing = $statement->fetchAll((PDO::FETCH_ASSOC));
            
            $good = false;
            foreach ($existing as $token) 
            {
                if ($token['token'] == $received) 
                {
                $good = true;
                break;
                }
            }
                return $good;
        }


      /**
       * Generates a token and checks it against the existing ID's in
       * the databse until it is guaranteed unique.
       * @param mixed $existing
       * @return string
       */
      function generateUnique($existing)
      {
        $generatedtoken = null;
        $keepGoing = false;

        do 
        {
          $generatedtoken = uniqid();
          foreach ($existing as $token) 
          {
            if ($generatedtoken == $token['token']) 
            {
              $keepGoing = true;
              break;
            }
          }
        } while ($keepGoing);

        return $generatedtoken;
      }

      /**
       * 
       *
       * @param mixed $token
       * @param mixed $yearNum
       * @return int|string the shcool year that this plan was created
       */
      function initPLanDB($token)
      {
        $insertSql = "INSERT INTO `Plan_Info`(`token`,`school_year`) 
                  VALUES (:token, :schoolYear)";

        $schoolYear = date("Y");
        $month = date("m");
        if($month <= 7) 
        {
          $schoolYear--;
        }

        $insertStatement = $this->_dbo->prepare($insertSql);
        $insertStatement->bindParam(":token", $token);
        $insertStatement->bindParam(":schoolYear", $schoolYear);                 
        $insertStatement->execute();

        return $schoolYear;
      }

      function updatePlanTable($schoolYear,$fall,$winter,$spring,$summer,$token)
      {

        $updateSql = "UPDATE Plan_Info
        SET `fall` = :fall , `winter` = :winter, `spring` = :spring ,`summer` = :summer
        WHERE `school_year` = :schoolYear
        AND `token` = :token ";

        $updateStatement = $this->_dbo->prepare($updateSql);
        $updateStatement->bindParam(":schoolYear", $schoolYear);
        $updateStatement->bindParam(":fall", $fall);
        $updateStatement->bindParam(":winter", $winter);
        $updateStatement->bindParam(":spring", $spring);
        $updateStatement->bindParam(":summer", $summer);
        $updateStatement->bindParam(":token", $token);
        $updateStatement->execute();
      }

      function updateTokenTable($token, $saved, $advisor)
      {
        $updateSql = "UPDATE Token_Info
        SET `advisor` = :advisor, 
        `last_saved` = :saved
        WHERE `token` = :token ";

        $updateStatement = $this->_dbo->prepare($updateSql);
        $updateStatement->bindParam(":token", $token);
        $updateStatement->bindParam(":advisor", $advisor);
        $updateStatement->bindParam(":saved", $saved);
        $updateStatement->execute();
      }

      /**
       * creates a DB entry for the passed token and the initial save time.
       * This save time is then returned to be displayed.
       * @return string of datetime
       */
      function makeNewPlan($token,$initialYear)
      {
      
      //Statement to insert the token  
      $insertSql = "INSERT INTO `Token_Info`(`token`,`last_saved`, `initial_year`) VALUES 
      (:token,:saved, :initial)";
      
      $saved = date_create('now')->format('Y-m-d H:i:s');

      $insertStatement = $this->_dbo->prepare($insertSql);
      $insertStatement->bindParam(":token", $token);
      $insertStatement->bindParam(":saved", $saved);
      $insertStatement->bindParam(":initial", $initialYear);
      $insertStatement->execute();

      return $saved;  
      }

      function getSchoolYearsInOrder($token)
      {
          $sql = "SELECT `school_year` FROM `Plan_Info` 
          WHERE `token`  = :token 
          ORDER BY `school_year`";
          $statement = $this->_dbo->prepare($sql);
          $statement->bindParam(":token", $token);
          $statement->execute();
          return  $statement->fetchAll(PDO::FETCH_ASSOC);        
      }
      

       /**
        * get every plan associated with this token in sorted order by plan number
        * @param mixed $token
        * @return array
        */

      function retreivePlansInOrder($token)
      {
        $selectSQL = "SELECT `fall`,`winter`,`spring`,`summer`, `school_year` FROM `Plan_Info` 
        WHERE token = :token
        ORDER BY `school_year`";
        $selectStatement = $this->_dbo->prepare($selectSQL);
        $selectStatement->bindParam(":token", $token);
        $selectStatement->execute();

       return $selectStatement->fetchAll((PDO::FETCH_ASSOC));
      }

       /**
        * retreive advisor andtim saved info associated with this token
        * @param mixed $token
        * @return array
        */

      function retreiveAdvisorAndTime($token)
      {
        $selectSQL = "SELECT `advisor`, `last_saved` FROM `Token_Info` 
        WHERE token = :token ";

        $selectStatement = $this->_dbo->prepare($selectSQL);
        $selectStatement->bindParam(":token", $token);
        $selectStatement->execute();

       return $selectStatement->fetchAll((PDO::FETCH_ASSOC));
      }


      //
      /**
        * Retrieves all data currently stored in the database
        * @param mixed $token
        * @return array
        */

        function retrieveAllData()
        {
          $selectSQL = "SELECT Token_Info.token, Token_Info.last_saved, Token_Info.advisor,Plan_Info.fall, Plan_Info.winter, Plan_Info.spring, Plan_Info.summer, Plan_Info.school_year
          FROM  Token_Info
          JOIN Plan_Info WHERE Token_Info.token = Plan_Info.token ";
  
          $selectStatement = $this->_dbo->prepare($selectSQL);
          $selectStatement->execute();

        return $selectStatement->fetchAll((PDO::FETCH_ASSOC));

        }
       
     /**
      * retrieves an array of all existing tokens in db so as not to duplicate
      * @return array
      */

    function getExistingTokens()
    {
      $sql = "SELECT `token` FROM `Token_Info`";
      $statement = $this->_dbo->prepare($sql);
      $statement->execute();
      return $statement->fetchAll((PDO::FETCH_ASSOC));
    } 

     /**
      * Create initial entry in plan_info table for this new school year
      * @param mixed $token
      * @param mixed $year
      * @return mixed
      */

    function initNewPlanYear($token,$year)
    {
        $insertSql = "INSERT INTO `Plan_Info`(`token`,`school_year`) 
        VALUES (:token, :schoolYear)";

        $insertStatement = $this->_dbo->prepare($insertSql);
        $insertStatement->bindParam(":token", $token);
        $insertStatement->bindParam(":schoolYear", $year);                 
        $insertStatement->execute();
    }

    function getInitialYear($token)
    {
       $sql = "SELECT `initial_year` 
       FROM `Token_Info` WHERE `token` = :token";
       $statement = $this->_dbo->prepare($sql);
       $statement->bindParam(":token", $token);
       $statement->execute();
       return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    function yearExists($token, $year)
    {
      $check = "SELECT `school_year` FROM `Plan_Info` WHERE `token` = :token AND `school_year` = :year";
      $statement = $this->_dbo->prepare($check);
      $statement->bindParam(":token", $token);
      $statement->bindParam(":year", $year);
      $statement->execute();

      $count = $statement->fetchAll(PDO::FETCH_ASSOC);
      return count($count) != 0;
    }

    function createNewPlanEntry($token,$year,$fall,$winter,$spring,$summer)
    {
      $insertSql = "INSERT INTO `Plan_Info`(`school_year`, `fall`, `winter`, `spring`, `summer`, `token`) 
        VALUES (:schoolYear,:fall,:winter,:spring,:summer,:token)";

        $statement = $this->_dbo->prepare($insertSql);
        $statement->bindParam(":schoolYear", $year);
        $statement->bindParam(":fall", $fall);
        $statement->bindParam(":winter", $winter);
        $statement->bindParam(":spring", $spring);
        $statement->bindParam(":summer", $summer);
        $statement->bindParam(":token", $token);
        $statement->execute();
    }

    function getSchoolYear()
    {
      $schoolYear = date("Y");
        $month = date("m");
        if($month <= 7) 
        {
          $schoolYear--;
        }
      return $schoolYear;
    }

    function displayInitialPlanScreen($schoolYear, $token)
    {
      $_SESSION["fall"] = "";
      $_SESSION["winter"] = "";
      $_SESSION["spring"] = "";
      $_SESSION["summer"] = "";
        $_SESSION["advisor"] = "";
        $_SESSION["school_year"] = $schoolYear;

      $prev = $schoolYear - 1;
      $next = $schoolYear + 1;

      //Dynamically create the button so we can limit the years added
      echo "<button type='button' name='prev_button' id='prev_button' value = '$token:$prev:$schoolYear'> add previous year</button>";
      //div to append plans to
      echo "<div id = 'all_plans'> ";
      echo "<h1>" . $schoolYear . " School Year </h1>";
      include("form_template.php");
      echo "</div> ";
      //Generate next button
      echo "<button type='button' name='next_button' id='next_button' value = '$token:$next:$schoolYear'> add next year</button>"; 

      include("footer.php");
    }

    function displayEmptyPostScreen($token)
    {
        $plans = $this->retreivePlansInOrder($token);
        $initialYear = $this->getInitialYear($token);
        $initialYear = $initialYear[0]["initial_year"];
      
         //for each plan in order
        //update my variables and put them in session
        //then add to the form template
        for ($i = 0; $i < count($plans); $i++) 
        {
          if($i == 0)
          {
            $prev = $plans[$i]["school_year"] - 1;
            //Create previous button
            echo "<button type='button' name='prev_button' id='prev_button' value = '{$token}:$prev:$initialYear'> add previous year</button>";
            //div to append plans to
            echo "<div id = 'all_plans'> ";
          }

          $_SESSION["fall"] = $plans[$i]["fall"];
          $_SESSION["winter"] = $plans[$i]["winter"];
          $_SESSION["spring"] = $plans[$i]["spring"];
          $_SESSION["summer"] = $plans[$i]["summer"];
          $_SESSION["school_year"] = $plans[$i]["school_year"];

          echo "<h1> " . $plans[$i]["school_year"] . " School Year</h1>";
          include("form_template.php");          

          if($i == count($plans) - 1)
          {
            echo "</div> ";
            $next = $plans[$i]["school_year"] + 1;
            echo "<button type='button' name='next_button' id='next_button' value = '$token:$next:$initialYear'> add next year</button>"; 
          }
        }

        include("footer.php");
    }

    function displayAfterSave($yearsToUpdate,$firstYear,$token, $initialYear,$arrYear)
    {
      while($yearsToUpdate >= 1)
        {
        //Update all years associated with this token
          if($yearsToUpdate == $firstYear)
          {
            //Must do it here to get the right value for previous
            $prev = $arrYear - 1;
            //Create previous button
            echo "<button type='button' name='prev_button' id='prev_button' value = '$token:$prev:$initialYear'> add previous year</button>";
            //div to append plans to
            echo "<div id = 'all_plans'> ";
          }
          
          $yearExists = $this->yearExists($_SESSION["token"], $arrYear);
          //Update our session variables and get keys
          $keys = $this->updateSession($arrYear);

          //either create new entry or update existing
          if(!$yearExists)
          {
            $this->createNewPlanEntry($_SESSION["token"],$arrYear, $_POST[$keys[0]], $_POST[$keys[1]]
            , $_POST[$keys[2]], $_POST[$keys[3]]);
          }
          else
          {
            $this->updatePlanTable($arrYear, $_POST[$keys[0]], $_POST[$keys[1]]
            , $_POST[$keys[2]], $_POST[$keys[3]], $_SESSION["token"]
          );
          }
          echo "<h1> " . $_SESSION["school_year"] . " School Year </h1>";
          //Display the updated page
          include("form_template.php");
          if($yearsToUpdate == 1)
          {
            $next = $arrYear + 1;
            echo "</div> ";
            echo "<button type='button' name='next_button' id='next_button' value = '{$_SESSION['token']}:$next:$initialYear'> add next year</button>"; 
          }
          $arrYear++;
          $yearsToUpdate -= 1;
        }
        include("footer.php");
    }

    function updateSession($year)
    {
          $key1 = "fall_$year";
          $key2 = "winter_$year";
          $key3 = "spring_$year";
          $key4 = "summer_$year";

          $_SESSION["fall"] = $_POST[$key1];
          $_SESSION["winter"] = $_POST[$key2];
          $_SESSION["spring"] = $_POST[$key3];
          $_SESSION["summer"] = $_POST[$key4];
          $_SESSION["school_year"] = $year;

          return array($key1, $key2, $key3, $key4);
    }

      
}
?>