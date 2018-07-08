<?php
/************************  for testing *
  $_POST["data"] = '{"owner":"20","RecipeName":"garbonzo"}';
  $_POST["data"] = '{"owner":"20","RecipeName":"McDonald\'s McChicken"}';
***************************************/

  session_start();

  $self = $_SERVER['PHP_SELF'];

  require_once ('dbConnect.php');
  $conn = dbConnect();

  if (isset($_POST["data"]))
  {
    $stripped = stripslashes($_POST["data"]);
    $updates = json_decode($stripped);

    $recipeName = $updates->RecipeName;
    $owner = $updates->owner;

/************ will need to add these at some point *********
    $sql .= ", isSubRecipe";
    $sql .= ", isPublic";
***********************************************************/


    $sql =  "INSERT INTO RecipeMaster (RecipeName, ownerID) VALUES (?, ?)";
    if (($stmt = $conn->prepare($sql)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmt, 'si', $recipeName, $owner))
      { // execute
        if (mysqli_stmt_execute($stmt) )
        {
          $newId = mysqli_insert_id($conn);
           echo json_encode($newId);
           return true;
        } // execute
      } // bind params
      mysqli_stmt_close($stmt);
    } // prepare

    // if we get here, an error occurred, so post it to log file
    error_log("\nSQL: " . $sql . "\nmysql ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");

    $newID=0;
    echo json_encode($newID);
    return false;
  }
?>

