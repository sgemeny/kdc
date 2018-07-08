<?php
/*************  for testing  *
$_POST["data"] = '{"trackingID":"309"}';
/*************/

  session_start();

  $self = $_SERVER['PHP_SELF'];

  require_once ('./dbConnect.php');
  $conn = dbConnect();

  if (isset($_POST["data"]))
  {
    $updates = json_decode($_POST["data"]);
    $sql = "DELETE FROM userLog WHERE trackingID = ?";

    $trackID = $updates->trackingID;

// echo "<pre>"; print_r($updates);  echo "</pre><br>";
   $sts = false;
   $rowa =0;
   if (($stmt = $conn->prepare($sql)))  // prepare
   { 
      if ( mysqli_stmt_bind_param($stmt, 'i', $trackID) ) // bind input
      {
         if ( mysqli_stmt_execute($stmt) )  // execute
         {
            $rows = mysqli_affected_rows($conn);
            $sts = true;
         }
         else
         {
            error_log("\nSQL execute failed: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
         }
      } // bind
      else 
          error_log("\nSQL bind failed: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
   } // prepare

   else
        error_log("\nSQL prepare failed: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");

   mysqli_stmt_close($stmt);

   $retVal = array();
   
   $retVal[]=$sts ? "true" : "false";
   $retVal[]=$rows;

   echo json_encode($retVal);
  } // if isset POST
?>

