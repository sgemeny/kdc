<!doctype html>
<?php

/*************** TESTING *****************************
  $_SESSION["db"] = 3;    // masterDB

  require_once ('../includes/dbConnect.php');
  require_once ('../includes/dbErr.php');
  require_once ('../includes/logError.php');

  $recipeID = 50164;  // Etest
  $conn = dbConnect();
  echo "RecipeID=" . $recipeID . "<br>";

  echo '<div id="comments">';
    echo '<div class="divCaption" id="editCaption">Comments</div>';
    echo '<textarea id="addComment" oninput=setDirty() placeholder="Type your comments here" maxlength="500" rows="6" cols="145">';
      echo $comments;
   echo '</textarea>';
  echo '</div>';  // comments div

  displayRecipe($conn, $recipeID, $comments);

/******************************************************/

  
function displayRecipe($conn, $chosenRecipe, $comments)
//------------------------------------------------------
{
  global $fractions;
  global $decimals;
  global $strNums;
  global $nums;
  global $canBeSubRecipe;

  // put Units of Measure into an array
  // ----------------------------------
  $sql  = "SELECT ID, Descr FROM UOM_Tbl";
  if (($stmt = $conn->prepare($sql)))
  { // no input params, so just execute
    if (mysqli_stmt_execute($stmt) )
    {
      if (mysqli_stmt_bind_result($stmt, $id, $descr) )
      $sts = true;
    }
  } // prepare

  if ($sts)
  {
    $UOM = array(array());
    $idx = 0;
    while (mysqli_stmt_fetch($stmt))
    {
      $UOM[$idx]["ID"]= $id;
      $UOM[$idx]["Descr"]= $descr;
      ++$idx;
    } // while

    $numUOMrows = $idx;
    mysqli_stmt_close($stmt);
  }  // sts

  // put grocery items & sub-recipes into array
  // ----------------------------------
  $sql  = "SELECT GroceryNameId ID, GroceryName Name, gramsPerCup FROM GROCERIES ";
  $sql .= "UNION SELECT ID, RecipeName Name, gramsPerCup FROM RecipeMaster ";
  $sql .= "WHERE isSubRecipe=1 ORDER BY Name";

  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  { // no input params, so just execute
    if (mysqli_stmt_execute($stmt) )
    {
      if (mysqli_stmt_bind_result($stmt, $id, $itemName, $gramsPerCup)  )
      $sts = true;
    }
  } // prepare

  if (!$sts)
  {
    dbAlert("../menu.php");
    sqlErr(__FILE__, "Error with GROCERIES table", $conn);
    exit();
  }
  else
  {
    $Groceries = array(array());
    $idx = 0;
    while (mysqli_stmt_fetch($stmt))
    {
      $Groceries[$idx]["ID"]= $id;
      $Groceries[$idx]["Item"]= $itemName;
      $Groceries[$idx]["GRAMS"]= $gramsPerCup;
      ++$idx;
    } // while
    $numGrocRows = $idx;
    mysqli_stmt_close($stmt);
  } // sts

  // Now get the recipe
  // ----------------------------------
  $sql  = "SELECT Qty, UOM_ID, GroceryNameID, Instruction, ID AS detailID, Sequence AS seq ";
  $sql .= "FROM RecipeDetail ";
  $sql .= "WHERE RecipeMasterID = ? ";
  $sql .= "ORDER BY Sequence";

  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  {
    if ( mysqli_stmt_bind_param($stmt, 'i', $chosenRecipe))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (!mysqli_stmt_bind_result($stmt, $quantity, $UOM_ID, $itemID, $instructs, $ID, $seq) )
        {
          dbAlert("../menu.php");
          sqlErr(__FILE__, "Error with RecipeDetail YY table", $conn);
          exit();
          // error ************************
        }
        mysqli_stmt_store_result($stmt);
        $recipeRows =  mysqli_stmt_num_rows($stmt);

        if ( $recipeRows==0)
        { // new recipe, add first row
          mysqli_stmt_close($stmt);
          require_once ('./addFirstRow.php');
        } // recipe==0
        $sts = true;
      } // execute;
    } // bind input
  } // prepare

  if (!$sts)
  {
    dbAlert("../menu.php");
    sqlErr(__FILE__, "Error with RecipeDetail XX table", $conn);
    exit();
  }
  else
  {
    // Array of Weight measures
    $weights = array('Ounce', 'Pound', 'Gram', 'Each', 'CommentLine');
    $canBeSubRecipe=1;

    // display Table Headers
    // ----------------------------------
     echo '<div>';
      echo '<table id="tblRecipe" border="1" padding="5">';
      echo '<caption class=hidden id=recipeNum>'.$chosenRecipe.'</caption>';
      echo '<thead>';
        echo '<th class="qty multiCol" colspan="2">Qty</th>';
        echo '<th class="uom">UOM</th>';
        echo '<th class="item">Item</th>';
        echo '<th class="dirs">Directions</th>';
        echo '<th class="seqCol">Seq#</th>';
        echo '<th class="idCol hidden">ID</th>';
      echo '</thead>';

    // Build Table
    // ----------------------------------
    $idx = 0;
    echo '<tbody id="rowData">';
    while (mysqli_stmt_fetch($stmt))
    { // for each recipe item
      $disableFlag="";
      $fld_UOM = '<select id="UOM" onchange="setDirty(this)"' . $disableFlag . '>';
      $fld_Instr  = '<input type="text" id="dirs" name="dirs" size="55"';
      $fld_Instr .= 'value="'.$instructs.'" ';
      $fld_Instr .= 'onchange="setDirty(this)" />';
      $fld_Instr .= '</input>';

      $fld_item = '<select id="grocItem" $Groceries[$i]["ID"] onchange="updateThis(this)" />';
      $fld_seq = '<input type="number" id="seq" name="sequence" onchange="setDirty(this)" min="0" value="'.$seq.'" />';
      $fld_ID = '<input type="text" id="detailID" name="detailID" size="50"  value="'.$ID.'" />';
      $fld_grams = '<input type="number" id="grams" name="grams"/>';

      $diableFlag = "";
      echo '<tr>';
        // Break quantity into whole number & fraction
        // -------------------------------------------
        $qtyWhole = floor($quantity);
        $qtyFrac = round($quantity - $qtyWhole, 3, PHP_ROUND_HALF_DOWN);

        $ndx = array_search($qtyFrac, $decimals);
        $fraction = $fractions[$ndx];

        // Quantity Whole
        // -------------------------------
        $fld_num = '<input type="number" id="Qty" min="0"';
        $fld_num .= 'value=' . $qtyWhole . '>';
        $fld_num .= '</select>';
        echo '<td>' . $fld_num . '</td>';

        // Quantity Fraction drop down
        // -------------------------------
        $fld_frac = '<select name="fractionChooser" id="fractionChooser" onchange="setDirty(this)">';
        foreach ($fractions as $key => $value)
        {
          $decimal = $decimals[$key];
          $selectFlag="";
          if ($decimal == $qtyFrac) $selectFlag = " selected='selected'";
          $fld_frac .= '<option value="' . $decimal .'"';
          $fld_frac .= $selectFlag .'>' . $value . '</option>';
        }
        $fld_frac .= '</select>';
        echo '<td>' . $fld_frac . '</td>';

        // Grocery Item Drop Down
        // -------------------------------
        $gramsCup=1;  // gramsPerCup for selected grocery item
        for ($x=0; $x<$numGrocRows; $x++)
        {
             $arayItem = $Groceries[$x]["Item"];
             $itemID==$Groceries[$x]["ID"] ? $selectFlag = " selected=selected" : $selectFlag = "";
             if ($selectFlag <>"") $gramsCup =  $Groceries[$x]["GRAMS"];
             
             $val = (string)$Groceries[$x]["ID"] . "|" . (string)$Groceries[$x]["GRAMS"];
             $fld_item .= '<option value="'.$val.'"';
             $fld_item .= $selectFlag.'>'.$arayItem.'</option>';
        }
        $fld_item .= '</select>';
        if ($gramsCup==1) $canBeSubRecipe = 0;

        // Unit of Measure Drop Down
        // -------------------------------
        for ($n=0; $n<$numUOMrows; $n++)
        {
             $selectFlag="";
             $uom = $UOM[$n]["Descr"];

             if (($gramsCup==1) && (in_array($uom, $weights)) || ($gramsCup!=1) )
             {
               $UOM_ID==$UOM[$n]["ID"] ? $selectFlag = " selected=selected" : $selectFlag = "";

               $fld_UOM .= '<option value="'.$UOM[$n]["ID"].'"';
               $fld_UOM .= $selectFlag.' " >'.$uom.'</option>';
             }
        }
        $fld_UOM .= '</select>';
        $UOMrow = '<td>'.$fld_UOM.'</td>';    // UOM cell
        echo '<td>'.$fld_UOM.'</td>';    // UOM cell

        echo '<td>'.$fld_item.'</td>';   // grocery item cell

        // Directions
        echo '<td>'.$fld_Instr.'</td>';  // Directions (instructions) cell

        // Recipe Sequence
        echo '<td class="seqCol">'.$fld_seq.'</td>';    // recipeDetail sequence cell

        // Recipe Detail ID
        echo '<td class="idCol hidden">'.$fld_ID.'</td>';     // recipeDetail ID cell
      echo '</tr>';                        // end of row
    } // while
    echo '</tbody>';
    mysqli_stmt_close($stmt);
    echo '</table>';
   echo '</div>';  // table div
//if ($canBeSubRecipe) logError(__FILE__ . "can be a subrecipe");
//else logError(__FILE__ . "can NOT be a subrecipe");
   return $canBeSubRecipe;
  }
}

/*** $(":selected",obj).text()  **/
?>


