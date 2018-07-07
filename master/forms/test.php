<?php
/*************** TESTING *****************************/

  $_SESSION["db"] = 3;    // masterDB


//  $recipeID = 50165;  // Ftest recipe
//  $recipeID = 50160;  // Atest recipe
//  $recipeID = 50146;  // test1 recipe
//  $recipeID = 50147;  // test2 recipe big test
//  $recipeID = 50163;  // test3 recipe choc. chips
//  $recipeID = 50164;  // test4 recipe
//  $recipeID = 50047;    // pie crust
// $recipeID = 50026;    // Italian sausage
//  $recipeID = 50025;    // Italian sausage seasoning
//  $recipeID = 50110;	// salad dressing, Italian
  $recipeID = 50138;	// sue's breakfast
//  $recipeID = 50139;	// sue's coffe
//  $recipeID = 50076;	// meatloaf sauce
//  $recipeID = 50183;	// Sue's breakfast, miniwheats
//  $recipeID = 50139;	// Sue's coffee

  require_once ('../includes/dbConnect.php');
  require_once ('../includes/dbErr.php');
  $conn = dbConnect();
echo "RecipeID=" . $recipeID . "<br>";


  processRecipe($recipeID, $conn);
/******************************************************/

  global $weights, $volumes, $specials, $xlates;
  global $recipeUOM_ID, $groceryUOM_ID;

  // nutrient vals for recipe master
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
  global $totalWeight, $recipeGramsPerCup;


function processRecipe($recipeID, $conn)
//-----------------------------------------
{
  global $weights, $volumes, $specials, $xlates;
  global $recipeUOM_ID, $groceryUOM_ID, $totVolume;

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
  global $totalWeight, $recipeGramsPerCup;

  // Note: should probably read these from UOM_Tbl.
  // for expediency, I did it this way
  $weights = array(9 => 28.375, 13 => 454, 23 => 1); // ounce, pound, gram
  $specials = array (4, 20);  // each, can

  getVolumes($conn);  // create array of volumes.
  getXlate($conn);
/*************
echo "<pre>";
echo "Volumes for Type Recipe<br>";
print_r($volumes);
echo "<br>XLATE Table<br>";
print_r($xlates);
echo "</pre>";
echo "<br>";
/*************/

  // get the recipe
  $sql = "select RecipeDetail.RecipeMasterID AS RecipeMasterID
               , RecipeDetail.ID AS detailID
               , RecipeDetail.Sequence AS sequence
               , RecipeDetail.Qty AS Qty
               , RecipeDetail.UOM_ID AS recipeUOM
               , RecipeDetail.GroceryNameID AS grocID
               , items.GroceryName AS GroceryName
               , items.groc_UOM AS grocUOM
               , items.gramsPerUnit as gramsPerUnit
               , items.gramsPerCup AS gramsPerCup
               , 0 AS totWeight  # place holder
               , 0 AS subRecipe  # place holder
               , IF(items.Water<0, 0, items.Water)
               , IF(items.Calories<0, 0, items.Calories)
               , IF(items.Protein<0, 0, items.Protein)
               , IF(items.Fat<0, 0, items.Fat)
               , IF(items.Carbs<0, 0, items.Carbs)
               , IF(items.Fiber<0, 0, items.Fiber)
               , IF(items.Sugars<0, 0, items.Sugars)
               , IF(items.Phosphorus<0, 0, items.Phosphorus)
               , IF(items.Potassium<0, 0, items.Potassium)
               , IF(items.Sodium <0, 0, items.Sodium)
         from (RecipeDetail join GROCERIES items) 
         where (RecipeDetail.GroceryNameID = items.GroceryNameID)
         and RecipeMasterID=?
         UNION
            select RecipeDetail.RecipeMasterID AS RecipeMasterID
                 ,RecipeDetail.ID AS detailID
                 ,RecipeDetail.Sequence AS sequence
                 ,RecipeDetail.Qty AS Qty
                 ,RecipeDetail.UOM_ID AS recipeUOM
                 ,RecipeDetail.GroceryNameID AS grocID
                 ,items.RecipeName AS GroceryName
                 ,3 AS grocUOM  # recipes are always in cups
                 ,items.gramsPerCup AS gramsPerUnit
                 ,items.gramsPerCup AS gramsPerCup
                 ,items.totWeight AS itemTotWeight
                 ,items.isSubRecipe as subRecipe
                 , IF(Water=-1, 0, Water)
                 , IF(Calories=-1, 0, Calories)
                 , IF(Protein=-1, 0, Protein)
                 , IF(Fat=-1, 0, Fat)
                 , IF(Carbs=-1, 0, Carbs)
                 , IF(Fiber=-1, 0, Fiber)
                 , IF(Sugars=-1, 0, Sugars)
                 , IF(Phosphorus=-1, 0, Phosphorus)
                 , IF(Potassium=-1, 0, Potassium)
                 , IF(Sodium =-1, 0, Sodium)
            from (RecipeDetail join RecipeMaster items)
            where (RecipeDetail.GroceryNameID = items.ID)
            and RecipeMasterID = ?
            order by Sequence";

  $sts = false;
  $water = $calories = 0;
  if ( ($stmt = $conn->prepare($sql)) )
  {
   // bind input
   if ( mysqli_stmt_bind_param($stmt, 'ii', $recipeID, $recipeID))
   {
      if (mysqli_stmt_execute($stmt) )
      {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_bind_result( $stmt
                                   , $recipeID
                                   , $detailID
                                   , $sequence
                                   , $Qty
                                   , $recipeUOM
                                   , $grocID
                                   , $GroceryName
                                   , $grocUOM
                                   , $gramsPerUnit
                                   , $gramsPerCup
                                   , $itemTotWeight
                                   , $subRecipe
                                   , $waterPerGram
                                   , $caloriesPerGram
                                   , $proteinPerGram
                                   , $fatPerGram
                                   , $carbsPerGram
                                   , $fiberPerGram
                                   , $sugarPerGram
                                   , $phosPerGram
                                   , $potPerGram
                                   , $sodiumPerGram
                                   ))
        {
          // do something
          while (mysqli_stmt_fetch($stmt))
          {
               global $totalWeight, $totVolume;
 echo "<br>";
echo "recipeID=" . $recipeID . 
     " recipeUOM=" . $recipeUOM .
     " grocUOM=" . $grocUOM .
     " subRecipe=" . $subRecipe .
     " itemTotWeight=" . $itemTotWeight .
     " <br>";
               $rType = uomType($recipeUOM);  // recipe uom type
               $gType = uomType($grocUOM);  // grocery uom type

 echo "grocID=" . $grocID . " GroceryName=" . $GroceryName . "<br>";
 echo "gUOM=" . $grocUOM . " gType is " . $gType . "<br>";
 echo "rUOM=" . $recipeUOM . " rType is " . $rType . "<br>";
               $Weight =0;
               $volume =0;
               switch ($rType)
               {
                 case "c":   // comment
                   continue;
                   break;

                 case "w":   // weight
                   $Weight = $weights[$recipeUOM] * $Qty;
  echo "Weight is " . $Weight . " QTY=" . $Qty . "<br>";
                   break;

                 case "v":   // volume
                   $CTRM = searchForId($recipeUOM, 3);
                   $GTRM = searchForId($recipeUOM, $grocUOM);
 echo "CTRM is " . $CTRM . "<br>";
 echo "GTRM is " . $GTRM . "<br>";

echo "Qty=" . $Qty . "<br>";
                   if ( ($gType == "w") || ($gType=="e") || ($subRecipe==true) )
                   {
                        $Weight = $Qty * $gramsPerCup * $CTRM;
echo "gramsPerCup=" . $gramsPerCup . "<br>";
                   }
                   else 
                   {
                        $Weight = $Qty * $gramsPerUnit * $GTRM;
echo "gramsPerUnit=" . $gramsPerUnit . "<br>";
                   }
echo "Weight=" . $Weight . "<br>";

                   break;

                 case "e";
                   $GTRM = searchForId($recipeUOM, $grocUOM);
echo "GTRM=". $GTRM . "recipeUOM (ID1)=" . $recipeUOM ." grocUOM (ID2)=" . $grocUOM .  "<br>";
                    if (($gType=="v")  && ($subRecipe==true))
                    {  
                       $Weight = $Qty * $itemTotWeight;
echo "Weight is " . $Weight . " Qty is " . $Qty . "<br>";
                    }
                    else if ($gType == "e")
                    {
echo "gramsPerUnit= " . $gramsPerUnit . "<br>";
echo "GTRM is " . $GTRM . "<br>";
                          $Weight = $Qty * $gramsPerUnit * $GTRM;
                    }
                   else
                   {
                    echo $GroceryName . " is NOT Valid<br>";
                      $Weight =0;
                   }
                   break;

                 default:
                   echo "UH OH!<br>";
                   $Weight =0;
                   break;
               } // end switch

               if ($Weight != 0)
               {
                 $water  += ($waterPerGram * $Weight);
echo "water=" . $water . "<br>";
echo "water per gram=" . $waterPerGram . " Weight in Recipe=" . $Weight . "<br>";
echo "water per gram * weight=" . $water . "<br>";
                 $calories += ($caloriesPerGram * $Weight);
echo "Calories per gram=" . $calories . " Calories in recipe=" . $caloriesPerGram * $Weight . "<br>";
echo "Calories per gram * weight=" . $calories. "<br>";
                 $carbs += ($carbsPerGram * $Weight);
                 $protein += ($proteinPerGram * $Weight);
                 $fat += ($fatPerGram * $Weight);
                 $fiber += ($fiberPerGram * $Weight);
                 $sugar += ($sugarPerGram * $Weight);
                 $phos += ($phosPerGram * $Weight);
                 $pot += ($potPerGram * $Weight);
                 $sodium += ($sodiumPerGram * $Weight);

                 $volume = $Weight / $gramsPerCup;
                 $totVolume += $volume;
                 $totalWeight += $Weight;
//  echo $GroceryName . " waterPerGram=" . $waterPerGram . " Weight=" . round($Weight,3) .  " volume=" . round($volume,5) . " gramsPerCup=" . $gramsPerCup . "<br>";
               }
          } // while fetch

echo "<br><br>";
echo "Total Weight=" . $totalWeight . "<br>";
echo "Water total weight=" . $water . "<br>";
echo "Calories total weight=" . $calories . "<br>";
          if ($totalWeight==0) $invTotWeight=0;
          else $invTotWeight = 1.0 / $totalWeight;
          $water *= $invTotWeight;
          $calories *= $invTotWeight;
          $carbs *= $invTotWeight;
          $protein *= $invTotWeight;
          $fat *= $invTotWeight;
          $fiber *= $invTotWeight;
          $sugar *= $invTotWeight;
          $phos *= $invTotWeight;
          $pot *= $invTotWeight;
          $sodium *= $invTotWeight;
echo "<br><br>Recipe perGram<br>";
          $recipeGramsPerCup = $totalWeight / $totVolume;

  echo "Recipe total weight =" . round($totalWeight,3) . "<br>";
  echo "Recipe total volume =" .  round($totVolume,3) . "<br>";
  echo "Recipe grams per cup = " . $recipeGramsPerCup . "<br><br>";
        $sts = true;
        }
      } // if bind result
    } // if execute
  } // if prepare

  if (!$sts)
      sqlErr(__FILE__, "line: " . __LINE__, $conn);
  if ($stmt) mysqli_stmt_close($stmt);
  return $sts;
}

/*****
  echo $GroceryName . " recipeUOM=" . $recipeUOM . ", grocUOM=" . $grocUOM . " Weight=" . round($Weight,3) .  ", volume= " . round($volume,5) . "<br>";
  echo "totalWeight= " . $totalWeight . ", Water=" . round($Water, 4) ." gramsPerCup=" . $gramsPerCup . "<br><br>";
/********/

  echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>';


function isWeight($uom)
//---------------------
{
  global $weights;
  return (array_key_exists($uom, $weights));
}

function isVolume($uom)
//---------------------
{
//echo "isVolume uom=" .$uom . "<br>";
  global $volumes;
  return (array_key_exists($uom, $volumes));
}

function isSpecial($uom)
//---------------------
{
  global $specials;
  return (array_key_exists($uom, $specials));
}

function uomType($uom)
//---------------------
{
   if (isWeight($uom)) return "w";  // weight
   if (isVolume($uom)) return "v";  // volume
   if ($uom == 24) return "c";      // comment
   return "e";  // each or can
}



function getVolumes($conn)
// ------------------
{
  global $volumes;

  $volumes = array();
  $sts = false;
  $qry = "SELECT recipeUOM_ID, multiplier FROM UOM_Xlate WHERE groceryUOM_ID=3";
  if ( ($stmt = $conn->prepare($qry)) )
  {
    if (mysqli_stmt_execute($stmt) )
    {
      mysqli_stmt_store_result($stmt);
      if (mysqli_stmt_bind_result( $stmt, $recipeUOM_ID, $multiplier) )
      {
        while (mysqli_stmt_fetch($stmt))
        {
           $volumes[$recipeUOM_ID] = $multiplier;
        }
        $sts = true;
      } // if bind result
    } // if execute
  } // prepare

  if (!$sts) error_log("\nSQL ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
  if ($stmt) mysqli_stmt_close($stmt);
  return $sts;
}

function getXlate($conn)
// ------------------
{
  global $xlates;
  global $recipeUOM_ID, $groceryUOM_ID;

  $xlates = array();
  $sts = false;
  $qry = "SELECT recipeUOM_ID, groceryUOM_ID, multiplier FROM UOM_Xlate ORDER BY groceryUOM_ID";
  if ( ($stmt = $conn->prepare($qry)) )
  {
    if (mysqli_stmt_execute($stmt) )
    {
      mysqli_stmt_store_result($stmt);
      if (mysqli_stmt_bind_result( $stmt, $recipeUOM_ID, $groceryUOM_ID, $multiplier) )
      {
        while (mysqli_stmt_fetch($stmt))
        {
           $xlates[] = array("recipeUOM"=>$recipeUOM_ID, "grocUOM"=>$groceryUOM_ID, "mult"=>$multiplier);
        }
        $sts = true;
      } // if bind result
    } // if execute
  } // prepare

  if (!$sts) error_log("\nSQL ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
  if ($stmt) mysqli_stmt_close($stmt);

  return $sts;
}

function searchForId($id1, $id2)
//----------------------------------
{
   global $xlates;
echo "HELLO id1=" . $id1 . " id2=" . $id2 . "<br>";
   foreach ($xlates as $key => $val) {
       if ( ($val['recipeUOM'] === $id1) && ($val['grocUOM']===$id2) )
       {
           return $xlates[$key]['mult'];
       }
//     echo "key is " . $key . "<br>";
//     echo "<pre>";
//     print_r($val);
//     echo "</pre>";
   }
   return null;
}

?>


