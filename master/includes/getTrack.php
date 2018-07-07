<?php
  global $trackingID;
  global $itemID;
  global $qty;
  global $serving;
  global $water;
  global $calories;
  global $protein;
  global $fat;
  global $carbs;
  global $fiber;
  global $sugars;
  global $phos;
  global $potas;
  global $sodium;
  global $gramsPer;
  global $itemName;

function getTrackInfo($conn, $userID)
//-------------------------------------
{
  global $trackingID;
  global $itemID;
  global $qty;
  global $serving;
  global $water;
  global $calories;
  global $protein;
  global $fat;
  global $carbs;
  global $fiber;
  global $sugars;
  global $phos;
  global $potas;
  global $sodium;
  global $gramsPer;
  global $itemName;
  global $uomDesc;

/*****************
  $sql  = "SELECT trackingID, itemID, Qty, servingAmt, Water, Calories, Protein, ";
  $sql .= "Fat, Carbs, Fiber, Sugars, Phosphorus, ";
  $sql .= "Potassium, Sodium, gramsPerUnit, name, UOM_Desc ";
  $sql .= "FROM userLog, getItemNames ";
  $sql .= "WHERE userID=? ";
  $sql .= "AND dateEntered between curdate() and now() ";
  $sql .= "AND itemID = ID ";
  $sql .= "ORDER BY dateEntered";
/*****************/

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
  $sql .= "AND dateEntered between curdate() and now() ";
  $sql .= "ORDER BY trackingID ";


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
         return $stmt;
       } // bind_result
       else logError("bind result failed " . mysqli_error($conn));
     } // execute
     else logError("execute failed " . mysqli_error($conn));
     
   } // if bind_param
   else logError("bind input failed " . mysqli_error($conn));
  } // if prepare
  else logError("prepare failed " . mysqli_error($conn));

  // echo "ERROR: " . mysqli_error($conn) . "<br>";
  error_log("\nSQL ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
  if ($stmt) mysqli_stmt_close($stmt);
  return false;
}

?>

