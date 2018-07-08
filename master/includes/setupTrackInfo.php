<?
// Select, prepare, bind, execute sql to read
// user's tracking info
function setupTrackInfo($conn, $userID, $beginDate)
// ----------------------------------------------------
{
    global $trackingID, $itemID, $qty, $serving, $water;
    global $calories, $protein, $fat, $carbs, $fiber;
    global $sugars, $phos, $potas, $sodium, $gramsPer;
    global $itemName, $uomDesc;

    $sql  = "SELECT trackingID, itemID, Qty, servingAmt, Water, Calories, Protein, ";
    $sql .= "Fat, Carbs, Fiber, Sugars, Phosphorus, ";
    $sql .= "Potassium, Sodium, gramsPerUnit, name, UOM_Desc ";
    $sql .= "FROM userLog, ( SELECT GroceryNameID ID, GroceryName name ";
    $sql .= "                FROM GROCERIES names ";
    $sql .= "                  UNION SELECT ID,recipeName ";
    $sql .= "                   FROM RecipeMaster  ";
    $sql .= "              )AS names ";
    $sql .= "WHERE userID=? ";
    $sql .= "AND itemID=ID ";
    $sql .= "AND dateEntered between DATE('" . $beginDate. "') ";
    $sql .= "AND DATE_ADD(DATE('" . $beginDate ."'), INTERVAL 1 DAY) ";
    $sql .= "ORDER BY trackingID ";

//logError("\n\nSQL\n" . $sql);
 if ( ($stmt = $conn->prepare($sql)) )
 {
   // bind input
   if ( mysqli_stmt_bind_param($stmt, 'i', $userID))
   {
      if (mysqli_stmt_execute($stmt) )
      {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_bind_result( $stmt
                              , $trackingID
                              , $itemID
                              , $qty
                              , $serving
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
                              , $itemName
                              , $uomDesc
                              ) )
       {
//$rows = mysqli_stmt_num_rows($stmt);
//logError("\nAt Bind: numRows= " . $rows);
         return $stmt;
       } // bind_result
       else logError("bind result failed " . mysqli_error($conn));
     } // execute
     else logError("execute failed " . mysqli_error($conn));

   } // if bind_param
   else logError("bind input failed " . mysqli_error($conn));
  } // if prepare
  else logError("prepare failed " . mysqli_error($conn));

  error_log("\nSQL ERROR: " . mysqli_error($conn));
  if ($stmt) mysqli_stmt_close($stmt);
  return false;
}
?>

