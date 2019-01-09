<?php
/*************  for testing  *
$_POST["data"] = '[{"userID":"1"},{"sqlDate":"Apr 4, 2018"},[{"Qty":"1.00"},{"UOM_DESC":"Each"},{"itemID":"279"},{"servingAmt":"119.0000"},{"Water":"53.55"},{"Calories":"312.97"},{"Protein":"15.47"},{"Fat":"14.042"},{"Carbs":"33.082"},{"Fiber":"1.309"},{"Sugars":"7.378"},{"Phosphorus":"166.6"},{"Potassium":"238"},{"Sodium":"744.94"},{"gramsPerUnit":"119.0000"},{"trackingID":"0"}],[{"Qty":"1.00"},{"UOM_DESC":"Each"},{"itemID":"283"},{"servingAmt":"71.0000"},{"Water":"25.986"},{"Calories":"229.33"},{"Protein":"2.414"},{"Fat":"11.005"},{"Carbs":"30.246"},{"Fiber":"2.769"},{"Sugars":"0.142"},{"Phosphorus":"90.17"},{"Potassium":"423.16"},{"Sodium":"134.19"},{"gramsPerUnit":"71.0000"},{"trackingID":"0"}]]';
/*************/

//require_once ('./logError.php');

// Save multiple rows
// -------------------
  session_start();

  $self = $_SERVER['PHP_SELF'];

  require_once ('./dbConnect.php');
  $conn = dbConnect();

  $bindType = "";
  $trackIDs = array();  // save record IDs to return to client
  $sts = false;
  if (isset($_POST["data"]))
  {
    $updates = json_decode($_POST["data"]);
    $things = current($updates);

    $userID = $updates[0]->userID;
//    $dt = strtotime($updates[1]->sqlDate);
    $dt = new DateTime($updates[1]->sqlDate);
    $now = new DateTime('now');
    $today = new DateTime(date('Y-m-d'));
    $time = $today->diff($now);
    $dt->add($time);
    $sqlDate = $dt->format('Y-m-d H:i:s');
//echo "sqlDate " . $sqlDate . "<br>";

    $sqlAppend  = "INSERT INTO userLog ( dateEntered, userID, itemID, Qty, servingAmt";
    $sqlAppend .= ", Water, Calories";
    $sqlAppend .= ", Protein, Fat, Carbs, Fiber, Sugars, Phosphorus, Potassium ";
    $sqlAppend .= ", Sodium, UOM_Desc, gramsPerUnit)";
//    $sqlAppend .= " VALUES( '"$sqlDate."',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )";
    $sqlAppend .= " VALUES('".$sqlDate."',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $appentTypes = 'iiddddddddddddsd';
//    $sqlAppend .= " VALUES( (DATE '" . $sqlDate . "'),?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,? )";

    $sqlUpdate  = "UPDATE userLog ";
    $sqlUpdate .= "SET  Qty=?, servingAmt=?, Water=?, Calories=? ";
    $sqlUpdate .= ", Protein=?, Fat=?, Carbs=?, Fiber=?, Sugars=? ";
    $sqlUpdate .= ", Phosphorus=?, Potassium=?, Sodium=?";
    $sqlUpdate .= " WHERE trackingID=? ";
    $updateTypes = 'ddddddddddddi';

    $updates = array_slice($updates, 2);
//echo "<pre>"; print_r($updates);  echo "</pre><br>";
//$txt = var_export($updates);
//logError("\n" . $sqlDate . "\n" .  $txt);

    // Setup for append query
    if (($stmtA = $conn->prepare($sqlAppend)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmtA, 'iiddddddddddddsd', $userID, $itemID, $qty, $wt
                                 , $Water, $Calories, $Protein, $Fat, $Carbs, $Fiber
                                 , $Sugars, $Phos, $Pot, $Sodium, $uomDesc, $gramsPer) )
         $sts = true;
      else sqlErr(__FILE__, "stmtA: input bind failed: ", $conn);
    }

    else sqlErr(__FILE__, "stmtA: prepare failed: ", $conn);

    if (!$sts) 
    {
      mysqli_stmt_close($stmtA);
      return false;
    }

    // setup for update query
    $sts=false;
    if (($stmtU = $conn->prepare($sqlUpdate)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmtU, 'ddddddddddddi', $qty, $wt, $Water
                                 , $Calories, $Protein, $Fat, $Carbs, $Fiber, $Sugars
                                 , $Phos, $Pot, $Sodium, $trackingID) )
         $sts = true;
      else sqlErr(__FILE__, "stmtB: input bind failed: ", $conn);
    }

    else  sqlErr(__FILE__, "stmtB: prepare failed: ", $conn);

    if (!$sts) 
    {
      mysqli_stmt_close($stmtU);
      return false;
    }

    // Save each row to user log file
    foreach($updates as $row => $value)
    {
//echo "ROW: " . ($row) ."<pre>"; print_r($value); echo "</pre><br>";
      $qty = $value[0]->Qty;
      $uomDesc = $value[1]->UOM_DESC;
      $itemID = $value[2]->itemID;
      $wt = $value[3]->servingAmt;
      $Water = $value[4]->Water;
      $Calories = $value[5]->Calories;
      $Protein = $value[6]->Protein;
      $Fat = $value[7]->Fat;
      $Carbs = $value[8]->Carbs;
      $Fiber = $value[9]->Fiber;
      $Sugars = $value[10]->Sugars;
      $Phos= $value[11]->Phosphorus;
      $Pot= $value[12]->Potassium;
      $Sodium = $value[13]->Sodium;
      $gramsPer = $value[14]->gramsPerUnit;
      $trackingID = $value[15]->trackingID;

      if ($trackingID == 0)  // new row append
      {
        if ( mysqli_stmt_execute($stmtA) )
        {
           $trackIDs[] = mysqli_insert_id($conn);
        }
        else
        { // fail
          mysqli_stmt_close($stmtA);
          sqlErr(__FILE__, "execute failed: ", $conn);
          echo json_encode("false");
          return;
        } 
      }

      else // existing row, update
      {
        if ( !mysqli_stmt_execute($stmtU) )
        {
          mysqli_stmt_close($stmtU);
          sqlErr(__FILE__, "execute failed: ", $conn);
          echo json_encode("false");
          return;
        } 
      } //else
    } // each row
    mysqli_stmt_close($stmtA);
    mysqli_stmt_close($stmtU);
    echo json_encode($trackIDs);
//    return true;
  } // if isset POST
?>

