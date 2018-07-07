<?php
  session_start();

  // required for PHP < 5.5
  require ("bcrypt.php");

  require_once ('dbConnect.php');
  $conn = dbConnect();

  if (isset($_POST["data"]))
  {
     $data = json_decode($_POST["data"]) ;
     $userName = $data->userName;
     $pword = $data->password;

     $bcrypt = new Bcrypt(15);

     // Is this a valid user?
     $sql = "SELECT ID, password, memberLevel FROM users WHERE userName =  ?  LIMIT 1;";
     if (($stmt = $conn->prepare($sql)))
     { // bind input
       if ( mysqli_stmt_bind_param($stmt, 's', $userName))
       { // execute
          if (mysqli_stmt_execute($stmt) )
          {
            $password="";
            // bind output
            if (mysqli_stmt_bind_result($stmt, $user, $password, $memberLevel) )
            {
              if (mysqli_stmt_fetch($stmt))
              {
                // note: verify will return empty string if NO match
                $isGood = $bcrypt->verify($pword, $password);
                if  (!$isGood)
                {
                   echo "false Not a Valid Password";
                   return false;
                }
                else
                {   // this is a valid user
                    $_SESSION["userID"] = $user;
                    $_SESSION["userName"] = $userName;
                    $_SESSION["memberLevel"] = $memberLevel;
                    echo json_encode($user);
                    return true;
                } // isGood
              } // fetch
              else
              {  // user not valid
                 echo "false Not a Valid User Name";
                 return false;
              }
            } // bind_result
          } // execute

       } // bind_params
       // close stmt
       mysqli_stmt_close($stmt);
     } // stmt prepare
     else
     {   // Some sort of mysql error
         error_log("SQL: " . $sql . "\n", 3, "/tmp/myErrors.log");
         error_log("mysql ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
         return false;
     }
  }
?>

