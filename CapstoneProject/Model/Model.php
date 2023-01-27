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
      function initPLanDB($token, $yearNum)
      {

        $insertSql = "INSERT INTO `Plan_Info`(`year_num`,`token`,`school_year`) 
                  VALUES (:yearNum, :token, :schoolYear)";

        $schoolYear = date("Y");
        $month = date("m");
        if($month <= 7) 
        {
          $schoolYear--;
        }

        $insertStatement = $this->_dbo->prepare($insertSql);
        $insertStatement->bindParam(":token", $token);
        $insertStatement->bindParam(":yearNum", $yearNum);  
        $insertStatement->bindParam(":schoolYear", $schoolYear);                 
        $insertStatement->execute();

        return $schoolYear;
    
      }

      function updatePlanTable($year,$fall,$winter,$spring,$summer,$token)
      {

        $updateSql = "UPDATE Plan_Info
        SET `fall` = :fall , `winter` = :winter, `spring` = :spring ,`summer` = :summer
        WHERE `year_num` = :yearNum 
        AND `token` = :token ";

        $updateStatement = $this->_dbo->prepare($updateSql);
        $updateStatement->bindParam(":yearNum", $year);
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
      function makeNewPlan($token,$prev,$future)
      {
      
      //Statement to insert the token  
      $insertSql = "INSERT INTO `Token_Info`(`token`,`last_saved`, `next_previous_year`, `next_future_year`) VALUES (:token,:saved, :prev, :future)";
      $saved = date_create('now')->format('Y-m-d H:i:s');

      $insertStatement = $this->_dbo->prepare($insertSql);
      $insertStatement->bindParam(":token", $token);
      $insertStatement->bindParam(":saved", $saved);
      $insertStatement->bindParam(":prev", $prev);
      $insertStatement->bindParam(":future", $future);
      $insertStatement->execute();

      return $saved;  

      }


      function determinePlanCount($token)
      {
        $count = "SELECT Count(*)FROM `Plan_Info` WHERE `token` = :token";
        $statement = $this->_dbo->prepare($count);
        $statement->bindParam(":token", $token);
        $statement->execute();
        return $statement->fetchAll();
      }

      function determineStartAndEndYear($token)
      {
        $sql = "SELECT `next_previous_year` AS `oldest`, `next_future_year` AS `newest` FROM `Token_Info` WHERE `token` = :token ";
        $statement = $this->_dbo->prepare($sql);
        $statement->bindParam(":token", $token);
        $statement->execute();
        $years =  $statement->fetchAll(PDO::FETCH_ASSOC);
    
        //THESE NEED TO BE ADJUSTED AS THEY HOLD THE PLACE OF NEXT VALID YEAR NOT A YEAR THAT CURRENTLY EXISTS
        $years[0]["oldest"]++; 
        $years[0]["newest"]--;

        return $years;
      }

      function getSchoolYear($token,$yearNum)
      {
          $sql = "SELECT `school_year` FROM `Plan_Info` 
          WHERE `token`  = :token AND `year_num` = :yearNum ";
          $statement = $this->_dbo->prepare($sql);
          $statement->bindParam(":token", $token);
          $statement->bindParam(":yearNum", $yearNum);
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
        $selectSQL = "SELECT `fall`,`winter`,`spring`,`summer`, `school_year`, `year_num` FROM `Plan_Info` 
        WHERE token = :token
        ORDER BY `year_num`";
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
        WHERE token = :token;";

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
          $selectSQL = "SELECT Token_Info.token, Token_Info.last_saved, Token_Info.advisor,Plan_Info.fall, Plan_Info.winter, Plan_Info.spring, Plan_Info.summer, Plan_Info.year_num
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

    function initOldPlan($token)
    {

      //TODO just testing ajax IT WORKSSSSSSSSSSSS

      $save = date_create('now')->format('Y-m-d H:i:s');

      $updateSql = "UPDATE Token_Info
      SET `last_saved` = :saved
      WHERE `token` = :token ";

      $updateStatement = $this->_dbo->prepare($updateSql);
      $updateStatement->bindParam(":saved", $save);
      $updateStatement->bindParam(":token", $token);
      $updateStatement->execute();



        //TODO FiGURE HTIS OUT
        /*

        $getYear = "SELECT  `next_previous_year` WHERE `token` = :token";

        $yearStatement = $this->_dbo->prepare($getYear);
        $yearStatement->bindParam(":token", $token);
        $yearStatement->execute();
        */


    
    }
      
}
?>