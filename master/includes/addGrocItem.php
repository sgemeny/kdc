<?php
/*************  for testing  *
$_POST["data"] = '{"GroceryName":"Fish, salmon, Atlantic, wild, raw, fillet","NDB_No":"15076","Water":"0.685","Calories":"1.420","Protein":"0.198","Fat":"0.063","Carbs":"0.000","Fiber":"0.000","Phosphorus":"2.000","Potassium":"4.900","Sodium":"0.440","gramsPerUnit":"198.0","groc_UOM":4,"gramsPerCup":1}';
/*************/

  session_start();
  $self = $_SERVER['PHP_SELF'];

  require_once ('dbConnect.php');
  require_once ('dbErr.php');
  require_once ('logError.php');
  $conn = dbConnect();

  $bindType = "";
  $aBindTo = array();
  $aValues = array();
  if (isset($_POST["data"]))
  {
    $updates = json_decode($_POST["data"]);

    $sql  = "INSERT INTO GROCERIES SET GroceryName=?";
    $data = (array)$updates;

    $items= array("GroceryName", "NDB_No", "Water", "Calories", "Protein", "Fat", "Carbs", "Sugars", "Fiber", "Phosphorus", "Potassium", "Sodium", "gramsPerUnit", "groc_UOM", "gramsPerCup");
    $iTypes = array("NDB_No","groc_UOM");
    foreach ($items as $itemName)
    {
      $aBindTo[] = $itemName;
      $aValues[] = (property_exists($updates, $itemName)) ? $data[$itemName] : "-1";
      if ($itemName == "GroceryName") 
      {
         $bindType .= "s";
         continue;
      }
      elseif (in_array($itemName, $iTypes)) 
        $bindType .= "i";
      else 
         $bindType .= "d";

      $sql .= ", " . $itemName . " = ?";
    } 

/******************
$msg = "updates:\n" . print_r($updates, true);
logError($msg);

$msg = "data Array:\n" . print_r($data, true);
logError($msg);
$msg = "bindType Array\n" . print_r($bindtype, true) . "\n";
logError($msg);
$msg = "aBindTo Array\n" . print_r($aBindTo, true);
logError($msg);
$msg = "aValues Array\n" . print_r($aValues, true);
logError($msg);
/************************
echo "<pre>"; 
echo "updates<br>";
print_r($updates); 
echo "data Array<br>";
print_r($data); 
echo "bindType Array<br>";
print_r($bindType); 
echo "<br>";
echo "abindTo Array<br>";
print_r($aBindTo); 
echo "aValues Array<br>";
print_r($aValues); 
echo "</pre>";
/*********************/

    // prepare
    $sts = false;
    if (($stmt = $conn->prepare($sql)))
    {
      // bind input
      $sts = call_user_func_array('mysqli_stmt_bind_param'
                                 , array_merge ( array($stmt, $bindType)
                                               , refValues($aValues)
                                               )
                                 );
      $newID =0;
      if ($sts)
      {
         // execute
         if (mysqli_stmt_execute($stmt) )
           $newID = mysqli_insert_id($conn);
         else
            sqlErr( __FILE__, "Execute Error ", $conn  );
      } // if sts

      else
      { // bind error
        sqlErr( __FILE__, "Bind Error ", $conn  );
      }

    } // if prepare

    else
    { // prepare error
       sqlErr( __FILE__, "Prepare Error ", $conn  );
    }

    mysqli_stmt_close($stmt);
    echo json_encode($newID);
  } // if post

  else 
  {
       header('HTTP/1.1 500 Internal Server Error');
       exit("Something went wrong when we tried to save your data. Please try again later. Sorry for any inconvenience");
  } // if post


function refValues($arr)
//-----------------------
{ 
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+ 
    { 
        $refs = array(); 
        foreach($arr as $key => $value) 
            $refs[$key] = &$arr[$key]; 
         return $refs; 
     } 
     return $arr; 
}
?>

