<?php

/*********************for testing **********
  session_start();

  $self = $_SERVER['PHP_SELF'];

  require_once ('../includes/dbConnect.php');
  $conn = dbConnect();

  $sql  = "SELECT Qty, UOM_ID, GroceryNameID, Instruction, detailID, seq ";
  $sql .= "FROM Exploded WHERE RecipeMasterID = ?";
echo "sql=" . $sql . "<br>";

  $chosenRecipe = 50158;
/*************************************************/
  $nextSeq = 5;

    // Create 1st row for this recipe
    $qry  = "INSERT INTO RecipeDetail( RecipeMasterID, Sequence) VALUES (?, ?)";

    $sts = false;
    if (($stmt1 = $conn->prepare($qry)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmt1, 'ii', $chosenRecipe, $nextSeq))
      { // execute
        if (mysqli_stmt_execute($stmt1) )
        {
            mysqli_stmt_close($stmt1);
            $sts=true;
        } // execute
        else sqlErr(__FILE__, __LINE__, $conn);  // execute failed

      } // bind params
      else sqlErr(__FILE__, __LINE__, $conn);  // bind failed
    } // prepare
    else sqlErr(__FILE__, __LINE__, $conn);  // prepare failed

    if ($sts)
    {
      $sts = false;
      // now, read the newly created record
      if (($stmt = $conn->prepare($sql)))
      {
        if ( mysqli_stmt_bind_param($stmt, 'i', $chosenRecipe))
        {
          if (mysqli_stmt_execute($stmt) )
          {
            if (mysqli_stmt_bind_result($stmt
                                       , $quantity
                                       , $UOM_ID
                                       , $itemID
                                       , $instructs
                                       , $ID
                                       , $seq
                                       ) )

              mysqli_stmt_store_result($stmt);
              $recipeRows =  mysqli_stmt_num_rows($stmt);
              if ($recipeRows >0) $sts = true;
          } // execute
          else sqlErr(__FILE__, __LINE__, $conn);  // execute failed
        } // bind
        else sqlErr(__FILE__, __LINE__, $conn);  // bind failed
      } // prepare
      else sqlErr(__FILE__, __LINE__, $conn);  // prepare failed
    } // sts
?>

