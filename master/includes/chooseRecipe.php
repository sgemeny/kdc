<?php

require_once ('./dbErr.php');
require_once ('./jquery.php');
require_once ('./logError.php');

// Create & display recipe select

function selectRecipe($conn, $btnCap="")
//-----------------------------------------
{
  $userID=$_SESSION['userID'];

  $sql= "SELECT RecipeName, ID, ownerID ";
  $sql.= "FROM RecipeMaster ";
  $sql.= "WHERE (ownerID=? AND isPublic=0) OR isPublic=1 ";
  $sql.= "ORDER BY RecipeName ";

  if (($stmt = $conn->prepare($sql)))
  {
    if ( mysqli_stmt_bind_param($stmt, 'i', $userID))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (mysqli_stmt_bind_result( $stmt
                                   , $recipe
                                   , $ID
                                   , $ownerID
                                   ) )
        mysqli_stmt_store_result($stmt);
        $sts = true;
      } // execute
    } // bind param
  } // prepare

  $retVal = 0;
  if ($sts)
  {
    // create drop down box
    echo '<div id="chooseRecipe">';
      echo '<label for="recipeChooser">Recipe </label>';
      echo '<select name="chosenRecipe" id="recipeChooser" onchange="itemSelected()">';

      // Fetch one at a time from result
      $firstOne = true;
      while (mysqli_stmt_fetch($stmt))
      {
          echo "<option value=" . $ID . "+" . $ownerID . ">$recipe</option>";
          if ($firstOne)
          {
             $retVal=$ownerID;
             $firstOne=false;
          }
      }
      mysqli_stmt_close($stmt);
      echo '</select>';

      if ($btnCap != "")
          echo '<input id="btnSelectRecipe" class="myButton" name="btnSelectRecipe" type="button" value="' . $btnCap . '">';
    echo "</div>";     // end of choose Recipe div
  }
  else
  {
//     echo error_log("mysql ERROR: " . mysqli_error($conn), 3, "/tmp/myErrors.log");
       sqlErr(__FILE__, "line" . __LINE__, $conn);
     exit();
  }
  return $retVal;
} // choose Recipe
?>

<script>
  function itemSelected()
  // ------------------------------------
  {
     var opt = $("#recipeChooser").val().split("+");
     var recipeID = opt[0];
     var owner = opt[1];
     var user = $("#userID").val();
     if (owner == user) $("#btnEdit").prop('disabled', false);
     else $("#btnEdit").prop('disabled', true);
  };
</script>

