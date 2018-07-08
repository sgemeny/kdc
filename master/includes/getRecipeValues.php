<?php
/************************  for testing *
  $_POST["data"] = ' {"ID":"50173"} ';
/***************************************/

  session_start();

  $self = $_SERVER['PHP_SELF'];

  require_once ('./dbConnect.php');
  require_once ('./utils.php');
  $conn = dbConnect();

  if (isset($_POST["data"]))
  {
     $updates = json_decode($_POST["data"]);

     $sql =  "SELECT RecipeName, ";
     $sql .= "Water, Calories, Protein, Fat, Carbs, Fiber, Sugars, Phosphorus, ";
     $sql .= "Potassium, Sodium, servingSize FROM RecipeMaster WHERE ID = ? ";

  $sts = false;

  $ID = $updates->ID;

  if (($stmt = $conn->prepare($sql)))
  { // bind input
    if ( mysqli_stmt_bind_param($stmt, 'i', $ID))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (mysqli_stmt_bind_result( $stmt
                                   , $recipeName
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
                    , "Serving"
                    , $ID
                    , $recipeName
                    , $gramsPer
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

    $len = count($results) -1;
    for ($i=5; $i<$len; $i++)
    {
      $results[$i] *= $gramsPer;
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

