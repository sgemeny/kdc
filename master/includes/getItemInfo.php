<?php
/************************  for testing *
  $_POST["data"] = ' {"GroceryNameID":"448"} ';
/***************************************/

  session_start();

  $self = $_SERVER['PHP_SELF'];

  require_once ('./dbConnect.php');
  require_once ('./utils.php');
  $conn = dbConnect();

  if (isset($_POST["data"]))
  {
     $updates = json_decode($_POST["data"]);

  $sql =  "SELECT GroceryName, Water, Calories, ";
  $sql .= "Protein, Fat, Carbs, Fiber, Sugars, Phosphorus, Potassium, Sodium, ";
  $sql .= "gramsPerUnit, Descr ";
  $sql .= "FROM GROCERIES, UOM_Tbl ";
  $sql .= "WHERE GroceryNameID=? ";
  $sql .= "AND groc_UOM=ID";
  $sts = false;

  $itemNo = $updates->GroceryNameID;
  if (($stmt = $conn->prepare($sql)))
  { // bind input
    if ( mysqli_stmt_bind_param($stmt, 'i', $itemNo))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (mysqli_stmt_bind_result( $stmt
                                   , $grocName
                                   , $water
                                   , $calories
                                   , $protein
                                   , $fat
                                   , $carbs
                                   , $fiber
                                   , $sugars
                                   , $phos
                                   , $potas
                                   , $sodium
                                   , $gramsPer
                                   , $uomDescr
                                   ) )
        {
          mysqli_stmt_store_result($stmt);
          $sts = true;
        }  // bind output
      } // execute
    } // bind input params
  } // prepare

  if ($sts)
  {
     mysqli_stmt_fetch($stmt);
     mysqli_stmt_close($stmt);

    // Note: array indexes must match cell indexes in nutrients.js & app_config.php
    $results = array( 1
                    , $uomDescr
                    , $itemNo
                    , $grocName
                    , $gramsPer   // weight
                    , $water
                    , $calories
                    , $protein
                    , $fat
                    , $carbs
                    , $fiber
                    , $sugars
                    , $phos
                    , $potas
                    , $sodium
                    , $gramsPer
                    );
//echo 'BEFORE:  <pre>';
//print_r($results);
//echo '</pre>';

    $len = count($results) -1;  // don't multiply 1st 3 or last 2 elements
    for ($i=5; $i<$len; $i++)
    {
      if ($results[$i] == -1) $results[$i] = -1.0;
      else $results[$i] *= $gramsPer;
    }
//echo 'AFTER: <pre>';
//print_r($results);
//echo '</pre>';

    echo json_encode($results);
    return true;
  } // sts

  // if we get here, an error occurred, so post it to log file
  error_log("\nSQL: " . $sql . "\nmysql ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
} // isset post data
?>

