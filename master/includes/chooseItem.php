<?php

function getGroceryItems($conn, $btnCap)
//----------------------------
{
  $sql = "SELECT GroceryNameID, GroceryName FROM GROCERIES ";
  $sql .= "ORDER BY GroceryName";
  selectItem($conn, $sql, $btnCap);
}

/*************
function getFoodItems($conn)
//----------------------------
{
  $sql  = "SELECT GroceryNameID, GroceryName FROM GROCERIES ";
  $sql .= "WHERE groc_UOM=4 OR groc_UOM=18 ";
  $sql .= "ORDER BY GroceryName ";
  selectItem($conn, $sql);
}
/*************/

function selectItem($conn, $sql, $btnCap="Select")
//----------------------------------------
{
//  $sql = "SELECT GroceryNameID, GroceryName FROM GROCERIES ";
//  $sql .= "ORDER BY GroceryName";
  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  {
    if (mysqli_stmt_execute($stmt) )
    {
        if (mysqli_stmt_bind_result( $stmt
                                   , $ID
                                   , $item
                                   ) )
        mysqli_stmt_store_result($stmt);
        $sts = true;
    } // execute
  } // prepare

  if ($sts)
  {
    // create drop down box
    echo '<div id="chooseItem">';
      echo '<label for="itemChooser">Choose Food </label>';
      echo '<select name="itemChooser" id="itemChooser">';

      // Fetch one at a time from result
      while (mysqli_stmt_fetch($stmt))
      {
          echo "<option value=$ID>$item</option>";
      }
      mysqli_stmt_close($stmt);
      echo '</select>';

      if ($btnCap != "")
         echo '<input id="btnSelectItem" class="myButton" name="btnSelectItem" type="button" value="' . $btnCap . '">';
//      echo '<input id="btnSelectItem" class="myButton" name="btnSelectItem" type="button" value="Add to List">';
    echo "</div>";     // end of chooser Item
   }
   else error_log("mysql ERROR: " . mysqli_error($conn), 3, "/tmp/myErrors.log");
}

?>

