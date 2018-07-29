<?php

/***********for testing *********
  session_start();
  $userID=22;
  $chosenRecipe=50065;
  $comments=NULL;
  $_SESSION["db"] = 3;    // masterDB

  require_once ('dbConnect.php');
  require_once ('fractions.php');
  require_once ('logError.php');
  $conn = dbConnect();

  getRecipe($conn, $chosenRecipe, $comments);
/***************************************/


function getRecipe($conn, $chosenRecipe, $comments)
//--------------------------------------------------
{
  global $fractions;
  global $decimals;
  global $strNums;
  global $nums;

  global $totWeight;
  global $waterPerGram ;
  global $caloriesPerGram ;
  global $proteinPerGram ;
  global $fatPerGram;
  global $carbsPerGram ;
  global $fiberPerGram ;
  global $sugarPerGram ;
  global $phosPerGram;
  global $potPerGram ;
  global $sodiumPerGram;

  // Get the desired recipe and display it as a table
//  $sql  = "SELECT Name, Qty, Descr, Instruction ";
//  $sql .= "FROM getRecipe WHERE RecipeMasterID = ?";

  $sql = "SELECT RecipeDetail.RecipeMasterID AS RecipeMasterID
     , name
     , RecipeDetail.GroceryNameID AS GroceryNameID
     , RecipeDetail.ID AS detailID
     , RecipeDetail.Sequence AS Sequence
     , RecipeDetail.Qty AS Qty
     , RecipeDetail.Instruction AS Instruction
     , UOM_Tbl.Descr AS Descr
     , RecipeDetail.UOM_ID AS recipeUOM
FROM
 (
   select names.GroceryName AS name
        , names.GroceryNameID AS ID
   from GROCERIES names
   union
   select names.RecipeName AS recipeName
        , names.ID AS ID
   from RecipeMaster names
)as itemNames join RecipeDetail join UOM_Tbl
where ((itemNames.ID = RecipeDetail.GroceryNameID)
and    (RecipeDetail.UOM_ID = UOM_Tbl.ID)
and     RecipeMasterID=?)
order by RecipeDetail.RecipeMasterID,RecipeDetail.Sequence";


  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  { // bind input
    if ( mysqli_stmt_bind_param($stmt, 'i', $chosenRecipe ))
    {
      if (mysqli_stmt_execute($stmt) )
      {
//        if (mysqli_stmt_bind_result( $stmt, $grocName, $qty, $descr, $instructs) );
        if (mysqli_stmt_bind_result( $stmt, $recipeMasterID, $grocName
                                   , $GroceryNameID, $detailID, $seq, $qty
                                   , $instructs, $descr, $recipeUOM
                                   ) );
        mysqli_stmt_store_result($stmt);
        $sts = true;
      } // execute
    } // bind input
  }
/****************/
if (!$sts)
{
  logError( mysqli_errno($conn) . "- " . mysqli_error($conn) );
  echo "SQL Error<br>";
  exit(-2);
}
/****************/

  if ($sts)
  {
    $idx = 0;
    echo '<table id="showRecipe">';
    echo '<thead>';
    echo '<th>Item</th>';
    echo '<th class="multiCol" colspan="2">Quantity</th>';
    echo '<th>Directions</th>';
    echo '</thead>';

    // display recipe detail records in table 
    while (mysqli_stmt_fetch($stmt))
    {
      $qtyWhole = floor($qty);
      $qtyFrac = round($qty - $qtyWhole, 3, PHP_ROUND_HALF_DOWN);
      $ndx = array_search($qtyWhole, $nums);
      $number = $strNums[$ndx];
      $ndx = array_search($qtyFrac, $decimals);
      $fraction = $fractions[$ndx];

      $strQty = $number . " " .$fraction;

      echo '<tr>';
        echo '<td class="grocItem">'.$grocName.'</td>';
        echo '<td>'.$strQty.'</td>';
        echo '<td >'.$descr.'</td>';
        echo '<td class="Directions">'.$instructs.'</td>';
      echo '</tr>';
      ++$idx;
    } // while
    mysqli_stmt_close($stmt);
    echo '</table>';


    echo '<div class="divCaption" id="commentCap">Comments</div>';
    echo '<div id="commentBox">';
      if( empty($comments)) echo "No Comments have been entered.";
      else echo $comments;
    echo '</div>';  // commentBox
  }

}


$waterPerGram=0;
$caloriesPerGram;
$proteinPerGram;
$fatPerGram;
$carbsPerGram;
$fiberPerGram;
$sugarPerGram;
$phosPerGram;
$potPerGram;
$sodiumPerGram;
$totWeight;

function showNutrients($conn, $chosenRecipe, $servSize)
//--------------------------------------------------------
{
  global $waterPerGram;
  global $caloriesPerGram;
  global $proteinPerGram;
  global $fatPerGram;
  global $carbsPerGram;
  global $fiberPerGram;
  global $sugarPerGram;
  global $phosPerGram;
  global $potPerGram;
  global $sodiumPerGram;
  global $totWeight;

        echo '<div class="totalsHolder">';
/*******************************************************
Not showing this any more
  	  echo '<table class="totals hidden" id="tbl_perGram">';
//  	  echo '<table class="totals" id="tbl_perGram">';
          echo '<thead>';
	    echo '<th>Water</th>';
	    echo '<th>Calories</th>';
	    echo '<th>Protein</th>';
	    echo '<th>Fat</th>';
	    echo '<th>Carbs</th>';
	    echo '<th>Fiber</th>';
	    echo '<th>Sugars</th>';
	    echo '<th>Phos.</th>';
	    echo '<th>Pot.</th>';
	    echo '<th>Sodium</th>';
          echo '</thead>';
            echo '<tbody id="perGramData">';
	      echo '<tr>';
               echo '<td>'.number_format($waterPerGram, 2, '.', '').'</td>';
               echo '<td>'.number_format($caloriesPerGram, 2, '.', '').'</td>';
               echo '<td>'.number_format($proteinPerGram, 2, '.', '').'</td>';
               echo '<td>'.number_format($fatPerGram, 2, '.', '').'</td>';
               echo '<td>'.number_format($carbsPerGram, 2, '.', '').'</td>';
               echo '<td>'.number_format($fiberPerGram, 2, '.', '').'</td>';
               echo '<td>'.number_format($sugarPerGram, 2, '.', '').'</td>';
               echo '<td>'.number_format($phosPerGram, 2, '.', '').'</td>';
               echo '<td>'.number_format($potPerGram, 2, '.', '').'</td>';
               echo '<td>'.number_format($sodiumPerGram, 2, '.', '').'</td>';
	      echo '</tr>';
            echo '</tbody>';
	  echo '</table>';  // tbl_perGram
*******************************************************/

    	  echo '<table class="totals" id="tbl_perServing">';
           echo '<thead>';
	    echo '<th>Water</th>';
	    echo '<th>Calories</th>';
	    echo '<th>Protein</th>';
	    echo '<th>Fat</th>';
	    echo '<th>Carbs</th>';
	    echo '<th>Fiber</th>';
	    echo '<th>Sugars</th>';
	    echo '<th>Phos<sup>*</sup></th>';
	    echo '<th>Pot<sup>*</sup></th>';
	    echo '<th>Sodium<sup>*</sup></th>';
          echo '</thead>';

	  echo '<tr>';
            echo '<td>'.number_format($waterPerGram * $servSize, 2, '.', '').'</td>'; 
            echo '<td>'.number_format($caloriesPerGram*$servSize, 2, '.', '').'</td>'; 
            echo '<td>'.number_format($proteinPerGram*$servSize, 2, '.', '').'</td>'; 
            echo '<td>'.number_format($fatPerGram*$servSize, 2, '.', '').'</td>'; 
            echo '<td>'.number_format($carbsPerGram*$servSize, 2, '.', '').'</td>'; 
            echo '<td>'.number_format($fiberPerGram*$servSize, 2, '.', '').'</td>'; 
            echo '<td>'.number_format($sugarPerGram*$servSize, 2, '.', '').'</td>'; 
            echo '<td>'.number_format($phosPerGram*$servSize, 2, '.', '').'</td>'; 
            echo '<td>'.number_format($potPerGram*$servSize, 2, '.', '').'</td>'; 
            echo '<td>'.number_format($sodiumPerGram*$servSize, 2, '.', '').'</td>'; 
	  echo '</tr>';
 	  echo '</table>';

          echo '<div id="info">';
            echo '<sup>*</sup> Values are in mg.  All others are in grams';
          echo '</div>';   // info

        echo '</div>'; // end of totalsHolder
 }
?>


