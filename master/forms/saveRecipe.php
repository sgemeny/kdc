<?php
/**************** Test Data *************************
$_POST["data"] ='[{"RecipeName":"Cinnamon Sugar Cookies/Dialysis & Kidney friendly","ID":"50213","servingSize":"60.0","isSubRecipe":1,"isPublic":0,"Comments":""},{"ID":"1651","Sequence":"5","Qty":2,"UOM_ID":"3","Item":"26","Instruction":"Add to bowl"},{"ID":"1651","Sequence":"10","Qty":2,"UOM_ID":"4","Item":"72","Instruction":"Add to bowl"}]';
/*******************************************************

// case 1
/*******************************************************
$_POST["data"] ='[{"RecipeName":"potato casserole","ID":"50048","servingSize":"10.0","isSubRecipe":0,"Comments":""},{"ID":"1393","Sequence":"5","Qty":6,"UOM_ID":"3","Item":"266","Instruction":"peel, slice thin"},{"ID":"1398","Sequence":"10","Qty":8,"UOM_ID":"9","Item":"263","Instruction":"crumble"}]';
/*******************************************************/

/*******************************************************
// case 2
$_POST["data"] ='[{"RecipeName":"Sues Breakfast","ID":"50138","servingSize":"1219.5","isSubRecipe":0,"Comments":"Delicious breakfast.  Eat it everyday."},{"ID":"1235","Sequence":"5","Qty":1,"UOM_ID":"4","Item":"50016","Instruction":"Toasted"},{"ID":"1236","Sequence":"10","Qty":0.5,"UOM_ID":"2","Item":"68","Instruction":"spread evenly on toast"},{"ID":"1237","Sequence":"15","Qty":1,"UOM_ID":"4","Item":"72","Instruction":"fried, over medium"},{"ID":"1244","Sequence":"20","Qty":1,"UOM_ID":"3","Item":"50139","Instruction":" "}]';
/*******************************************************/

  session_start();

  $self = $_SERVER['PHP_SELF'];

  require_once ('../includes/dbConnect.php');
  require_once ('../includes/dbErr.php');
  require_once ('processRecipe.php');

  global $conn;

  $conn = dbConnect();
if ($conn==NULL) echo "failed to connect";
  if (isset($_POST["data"]))
  {
    $updates = json_decode($_POST["data"]);
/**
echo "<pre>";
print_r($updates);
echo "</pre>";
echo "<br>";
/**/

    $recipeName = $updates[0]->RecipeName;
    $recipeID = $updates[0]->ID;
    $servingSize = $updates[0]->servingSize;
    $isSubRecipe = $updates[0]->isSubRecipe;
    $isPublic = $updates[0]->isPublic;
    $Comments = $updates[0]->Comments;

    // update recipe detail records
    // ----------------------------
    if (!updateRecipeDetails($conn, $updates))
    {
      sqlErr(__FILE__, "line: " . __LINE__, $conn);
      return;
    }

    // Recipe Calculated Nutrients per Gram, total weight & grams per cup
    // -------------------------------------------------------------------
    global $water;
    global $calories;
    global $protein;
    global $fat;
    global $carbs;
    global $fiber;
    global $sugar;
    global $phos;
    global $pot;
    global $sodium;
    global $totWeight;
    global $totalWeight, $recipeGramsPerCup;

    $sts = processRecipe($recipeID, $conn);

//error_log("\nDEBUG: " . "water=". $water . ", totWeight=" . $totalWeight . "\n" , 3, "/tmp/myErrors");

    $totWeight = $totalWeight;
    $gramsPerCup = $recipeGramsPerCup;
    if ($sts)
    {
      // Update Recipe Master with Nutrient Totals (Query results)
      // ---------------------------------------------------------------
      $sql =  "UPDATE RecipeMaster ";
      $sql .= "SET RecipeName=?, servingSize=?, isSubRecipe=?, isPublic=?, Comments=?, totWeight=?, ";
      $sql .= "gramsPerCup=?, Water=?, Calories=?, Protein=?, Fat=?, Carbs=?, Fiber=?, Sugars=?, ";
      $sql .= "Phosphorus=?, Potassium=?, Sodium=? WHERE ID = ? ";

      $sts=false;
      if (($stmt = $conn->prepare($sql)))
      {
        // bind input params
        if ( mysqli_stmt_bind_param($stmt, 'sdiisddddddddddddi', $recipeName, $servingSize, $isSubRecipe, $isPublic, $Comments, $totWeight, $gramsPerCup, $water, $calories, $protein, $fat, $carbs, $fiber, $sugar, $phos, $pot, $sodium, $recipeID))
        {
          if (mysqli_stmt_execute($stmt) )
          {  // all done with recipe master update
            mysqli_stmt_close($stmt);
            $sts=true;
          }
        }  // bind
      } // prepare
    } // sts

    // if we get here, check for error and post it to log file
    if (!$sts)
    {
      sqlErr(__FILE__, "line: " . __LINE__, $conn);
      echo json_encode("false");
      return;
    }
    echo json_encode("true");
    return;
  } // isset data


function updateRecipeDetails($conn, $updates)
//-------------------------------------
{
  $sql = "UPDATE RecipeDetail SET Qty = ?, GroceryNameID = ?, Instruction = ?, UOM_ID = ?, Sequence = ? WHERE ID = ?";
  if (($stmt = $conn->prepare($sql)))
  {
    // bind input
    if ( mysqli_stmt_bind_param($stmt, 'disiii', $qty, $item, $inst, $uom, $seq, $id ))
    {
      $len = sizeof($updates);
      for ($i=1; $i<$len; $i++)
      {
        $id = $updates[$i]->ID;
        $qty = $updates[$i]->Qty;
        $uom = $updates[$i]->UOM_ID;
        $item= $updates[$i]->Item;
        $inst=$updates[$i]->Instruction;
        $seq=$updates[$i]->Sequence;

        $sts = mysqli_stmt_execute($stmt);
        if (!$sts)
        {  
           sqlErr(__FILE__, "line: " . __LINE__, $conn);
           return false;
        }
      } // for each
      mysqli_stmt_close($stmt);
      return true;
    } // bind
  } // prepare
  return false;
}



/**
echo "<pre>";
print_r($key);
print_r($ele);
echo "</pre><br>";

echo "key=" . $key . "<br>";
echo "<pre>"; print_r($ele); echo "</pre><br>";
echo  "item=" . $ele->Item . "<br>";

echo "<pre>"; print_r( $updates[$key]); echo "</pre><br>";

echo "multiplier=" . $multiplier . "<br>";
echo "gramsPerCup=" . $gramsPerCup . "<br>";
echo "qty=" . $qty . "<br>";
**/

?>

