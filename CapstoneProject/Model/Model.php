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

      
}
?>