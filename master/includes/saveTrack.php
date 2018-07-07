<?php
/*************  for testing  *
$_POST["data"] = '[{"userID":"1"},[{"Qty":"1.00"},{"UOM_DESC":"Serving"},{"itemID":"50019"},{"servingAmt":"51.549999237061"},{"Water":"23.017074659348"},{"Calories":"134.82386800461"},{"Protein":"2.9383499565125"},{"Fat":"4.4075249347687"},{"Carbs":"20.55813969574"},{"Fiber":"0.74747498893738"},{"Sugars":"1.0103799850464"},{"Phosphorus":"34.847799484253"},{"Potassium":"38.672809427643"},{"Sodium":"163.78981257591"},{"gramsPerUnit":"51.549999237061"},{"trackingID":"0"}],[{"Qty":"1.00"},{"UOM_DESC":"Each"},{"itemID":"72"},{"servingAmt":"50.0000"},{"Water":"38.05"},{"Calories":"71.5"},{"Protein":"6.3"},{"Fat":"4.75"},{"Carbs":"0.35"},{"Fiber":"0"},{"Sugars":"0.2"},{"Phosphorus":"99"},{"Potassium":"69"},{"Sodium":"71"},{"gramsPerUnit":"50.0000"},{"trackingID":"0"}]]';
/*************/

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

    $sqlAppend  = "INSERT INTO userLog ( userID, itemID, Qty, servingAmt, Water, Calories";
    $sqlAppend .= ", Protein, Fat, Carbs, Fiber, Sugars, Phosphorus, Potassium ";
    $sqlAppend .= ", Sodium, UOM_Desc, gramsPerUnit)";
    $sqlAppend .= " VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";   
    $appentTypes = 'iiddddddddddddsd';

    $sqlUpdate  = "UPDATE userLog ";
    $sqlUpdate .= "SET  Qty=?, servingAmt=?, Water=?, Calories=? ";
    $sqlUpdate .= ", Protein=?, Fat=?, Carbs=?, Fiber=?, Sugars=? ";
    $sqlUpdate .= ", Phosphorus=?, Potassium=?, Sodium=?";
    $sqlUpdate .= " WHERE trackingID=? ";
    $updateTypes = 'ddddddddddddi';

    $userID = $updates[0]->userID;
    $updates = array_slice($updates, 1);
// echo "<pre>"; print_r($updates);  echo "</pre><br>";

    // Setup for append query
    if (($stmtA = $conn->prepare($sqlAppend)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmtA, 'iiddddddddddddsd', $userID, $itemID, $qty, $wt
                                 , $Water, $Calories, $Protein, $Fat, $Carbs, $Fiber
                                 , $Sugars, $Phos, $Pot, $Sodium, $uomDesc, $gramsPer) )
         $sts = true;
      else sqlErr(__FILE__, "input bind failed: ", $conn);
    }

    else sqlErr(__FILE__, "prepare failed: ", $conn);

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
      else sqlErr(__FILE__, "input bind failed: ", $conn);
    }

    else  sqlErr(__FILE__, "prepare failed: ", $conn);

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

