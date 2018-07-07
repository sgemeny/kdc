<?php
/**********TEST DATA*****************************
    $_POST["data"] = '{"recipeMasterID":"50158","nextSeq":30}';
/*******************i****************************/

  session_start();

  require_once ('../includes/dbErr.php');
  require_once ('../includes/dbConnect.php');
  $conn = dbConnect();

  $newId=0;
  $sts=false;
  if (isset($_POST["data"]))
  {
    $data = json_decode($_POST["data"]);
    $recipeMasterID = $data->recipeMasterID;
    $nextSeq =$data->nextSeq;

    $sql  = "INSERT INTO RecipeDetail( RecipeMasterID, Sequence) VALUES (?, ?)";
    if (($stmt = $conn->prepare($sql)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmt, 'ii', $recipeMasterID, $nextSeq))
      { // execute
        if (mysqli_stmt_execute($stmt) )
        {
          $newId = mysqli_insert_id($conn);
          $sts = true;
           mysqli_stmt_close($stmt);
        } // execute
        else sqlErr(__FILE__, "line " . __LINE__, $conn);  // execute failed
      } // bind params
      else  sqlErr(__FILE__, "line " . __LINE__, $conn);   // bind failed
    } // prepare
    else sqlErr(__FILE__, "line " . __LINE__, $conn);      // prepare failed

    if (!$sts)
    {
      // if we get here, an error occurred, so post it to log file
//      error_log("\nSQL: " . $sql . "\nmysql ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
//      echo "ERROR: " . mysqli_error($conn);  // return error code to caller
    }
    echo json_encode($newId);
  }
//  else
//  {
//    echo "ID Not Set";
//  }

?>

