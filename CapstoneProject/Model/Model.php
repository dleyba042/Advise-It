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
      * TEST HELPER METHOD
      * @param mixed $string
      * @return string
      */
     function parseData($string)
      {

        if(strlen($string) == 0)
        {
          return " ";
        }

        $newString = "";
        $len = 0;

        for ($x = 0; $x < strlen($string); $x++) 
        {
           if($string[$x] == " ")
           { 
              while($string[$x] == " " && $len < 3)
              {
                $len++;
              }
              if($len >= 3)
              {
                $newString.= "\n";             
                $len = 0;
              }
              else
              {
                $newString.= $string[$x];
              }        
           }
           else
           {
            $newString.= $string[$x]; 
           }
        }
      return $newString;
      }
    
     /**
      * ensures the token received exists in the database
      * @param mixed $received
      * @return bool
      */
  
        function checkValid($received)
        {

            $sql = "SELECT `Unique_Token` FROM `StudentPlans`";
            $statement = $this->_dbo->prepare($sql);
            $statement->execute();
            $existing = $statement->fetchAll((PDO::FETCH_ASSOC));
            
            $good = false;
            foreach ($existing as $token) 
            {
                if ($token['Unique_Token'] == $received) 
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
            if ($generatedtoken == $token['Unique_Token']) 
            {
              $keepGoing = true;
              break;
            }
          }
        } while ($keepGoing);

        return $generatedtoken;
      }

       /**
        * Updates the user data for the assoiated token
        * receives the textarea entries the new save time anb the token as parameters
        * @param mixed $fall
        * @param mixed $winter
        * @param mixed $spring
        * @param mixed $summer
        * @param mixed $newSaved
        * @param mixed $token
        * @return void
        */

      function updateData($fall,$winter,$spring,$summer,$newSaved,$token)
      {
         //I will save here but now I just want to see display updated
      $updateSql = "UPDATE `StudentPlans` SET `fall_quarter` = :fall,`winter_quarter`= :winter, 
      `spring_quarter`= :spring, `summer_quarter` = :summer, `last_saved` = :saved 
      WHERE `unique_token` = :token ";
      
      $updateStatement = $this->_dbo->prepare($updateSql);
      $updateStatement->bindParam(":token", $token);
      $updateStatement->bindParam(":saved", $newSaved);
      $updateStatement->bindParam(":fall", $fall);
      $updateStatement->bindParam(":winter", $winter);
      $updateStatement->bindParam(":spring", $spring);
      $updateStatement->bindParam(":summer", $summer);
      $updateStatement->execute();

      }

      /**
       * Generates a new token and creates a DB entry for that token and the initial save.
       * This information is then returned so it can be displayed.
       * @return array<string>
       */
      function makeNewPlan()
      {
        //Fetch a list of all the tokens created so far
      $sql = "SELECT `Unique_Token` FROM `StudentPlans`";
      $statement = $this->_dbo->prepare($sql);
      $statement->execute();
      $existingTokens = $statement->fetchAll((PDO::FETCH_ASSOC));
      
      //Stratement to insert the token  
      $insertSql = "INSERT INTO `StudentPlans`(`unique_token`,`last_saved`) VALUES (:token,:saved)";
      $saved = date_create('now')->format('Y-m-d H:i:s');

      //Generate a Unique ID using Model Object
      $token = $this->generateUnique($existingTokens);

      $insertStatement = $this->_dbo->prepare($insertSql);
      $insertStatement->bindParam(":token", $token);
      $insertStatement->bindParam(":saved", $saved);
      $insertStatement->execute();

      $returnArray = array();

      $returnArray["token"] = $token;
      $returnArray["saved"] = $saved;

      return $returnArray;  

      }

       /**
        * Retrieves all data currently stored associated with the provided token
        * @param mixed $token
        * @return array
        */

      function retrieveData($token)
      {
        $selectSQL = "SELECT `fall_quarter`,`winter_quarter`,`spring_quarter`,`summer_quarter`,
      `last_saved` FROM `StudentPlans` WHERE unique_token = :token ";

      $selectStatement = $this->_dbo->prepare($selectSQL);
      $selectStatement->bindParam(":token", $token);
      $selectStatement->execute();

      return $selectStatement->fetchAll((PDO::FETCH_ASSOC));
      }

      
      
}
?>