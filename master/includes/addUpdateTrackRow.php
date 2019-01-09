<?php
/*********** For Testing ****************
$_POST["data"] = '[{"userID":"1","sqlDate":"Jan 7, 2019","itemID":"234","Qty":"1.00","UOM_Desc":"Cup","servingAmt":244,"Water":"215.208","Calories":"102.47999999999999","Protein":"41.480000000000004","Fat":"24.400000000000002","Carbs":"27.572","Fiber":"2.6839999999999997","Sugars":"22.936","Phosphorus":"12.200000000000001","Potassium":"180.56","Sodium":"4.88","gramsPerUnit":244,"trackingID":"0"}]';
/***************************************/

  session_start();

  $self = $_SERVER['PHP_SELF'];

  require_once ('./dbConnect.php');
  $conn = dbConnect();

  $sts = false;
  if (isset($_POST["data"]))
  {
    $updates = json_decode($_POST["data"]);

    $dt = new DateTime($updates[0]->sqlDate);
    $now = new DateTime('now');
    $today = new DateTime(date('Y-m-d'));
    $time = $today->diff($now);
    $dt->add($time);
    $sqlDate = $dt->format('Y-m-d H:i:s');

    $sqlAppend  = "INSERT INTO userLog ( dateEntered, userID, itemID, Qty, servingAmt, Water, Calories, Protein";
    $sqlAppend .= ", Fat, Carbs, Fiber, Sugars, Phosphorus, Potassium, Sodium, UOM_Desc, gramsPerUnit)";
    $sqlAppend .= " VALUES('".$sqlDate."',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $sqlUpdate  = "UPDATE userLog ";
    $sqlUpdate .= "SET  Qty=?, servingAmt=?, Water=?, Calories=? ";
    $sqlUpdate .= ", Protein=?, Fat=?, Carbs=?, Fiber=?, Sugars=? ";
    $sqlUpdate .= ", Phosphorus=?, Potassium=?, Sodium=?";
    $sqlUpdate .= " WHERE trackingID=? ";
    $updateTypes = 'ddddddddddddi';

    $userID = $updates[0]->userID;
    $trackingID = $updates[0]->trackingID;

    if ($trackingID == 0)
    { // append new record
      $sql = $sqlAppend;
      if (($stmt = $conn->prepare($sql)))
      { // bind input
        if ( mysqli_stmt_bind_param($stmt, 'iiddddddddddddsd', $userID, $itemID, $qty, $wt
                                   , $Water, $Calories, $Protein, $Fat, $Carbs, $Fiber
                                   , $Sugars, $Phos, $Pot, $Sodium, $uomDesc, $gramsPer) )
           $sts = true;
        else //echo "bind failed: " . mysqli_error($conn) . "<br>";
            error_log("\nSQL bind failed: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
      }

      else  //echo "prepare failed: " . mysqli_error($conn) . "<br>";
          error_log("\nSQL prepare failed: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
    } // if trackid==0

    else
    { // update existing record
      $sql = $sqlUpdate;
    if (($stmt = $conn->prepare($sqlUpdate)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmt, 'ddddddddddddi', $qty, $wt, $Water
                                 , $Calories, $Protein, $Fat, $Carbs, $Fiber, $Sugars
                                 , $Phos, $Pot, $Sodium, $trackingID) )
         $sts = true;
      else //echo "bind failed: " . mysqli_error($conn) . "<br>";
        error_log("\nSQL bind failed: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
    }

    else  //echo "prepare failed: " . mysqli_error($conn) . "<br>";
        error_log("\nSQL prepare failed: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
    } // if trackid==0

    if (!$sts) 
    {
      mysqli_stmt_close($stmt);
      return false;
    }

    // Save row to user log file
    $qty = $updates[0]->Qty;
    $uomDesc = $updates[0]->UOM_Desc;
    $itemID = $updates[0]->itemID;
    $wt = $updates[0]->servingAmt;
    $Water = $updates[0]->Water;
    $Calories = $updates[0]->Calories;
    $Protein = $updates[0]->Protein;
    $Fat = $updates[0]->Fat;
    $Carbs = $updates[0]->Carbs;
    $Fiber = $updates[0]->Fiber;
    $Sugars = $updates[0]->Sugars;
    $Phos= $updates[0]->Phosphorus;
    $Pot= $updates[0]->Potassium;
    $Sodium = $updates[0]->Sodium;
    $gramsPer = $updates[0]->gramsPerUnit;

    if ( !mysqli_stmt_execute($stmt) )
    {
        //echo "execute failed: " . mysqli_error($conn) . "<br>";
        mysqli_stmt_close($stmt);
        error_log("\nSQL execute failed: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
        echo json_encode("false");
        return;
    } 

    if ($trackingID == 0) $trackingID = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    echo json_encode("$trackingID");
  } // if isset POST
?>

