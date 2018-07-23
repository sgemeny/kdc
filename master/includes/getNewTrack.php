<?php
/**************** FOR TESTING **************************
$_POST["data"] = ' {"beginDate":"Apr 5, 2018","userID":"1"} ';

  function phpGetItemText($val, $dec)
  //-------------------------
  {
     if ($val == -1.0) return "N/A";
     else return number_format($val,$dec);
  }
/**************** FOR TESTING **************************/

    global $trackingID, $itemID, $qty, $serving, $water;
    global $calories, $protein, $fat, $carbs, $fiber;
    global $sugars, $phos, $potas, $sodium, $gramsPer;
    global $itemName, $uomDesc;
    global $Rows, $footer;

    global $totWeight, $totWater, $totCalories, $totProtein;
    global $totFat, $totCarbs, $totFiber, $totSugars, $totPhos;
    global $totPotas, $totSodium;

    require_once('./logError.php');
    require_once('./setupTrackInfo.php');
//logError("getNewTrack");

//logError($_POST["data"]);
  if (isset($_POST["data"]))
  {
    require_once ( './dbConnect.php');
    $updates = json_decode($_POST["data"]);

    $userID = $updates->userID;
    $dt = strtotime( $updates->beginDate );
    $sqlDate = date('Y-m-d', $dt);
//logError("START:  sqlDate: " . $sqlDate);
    $conn = dbConnect();

    if ($conn!=NULL)
    {
      getTrackForJquery($conn, $userID, $sqlDate);
      getFooter();
//$txt = var_export($Rows, true);
//logError("\nRows\n" . $txt);
//logError("\nfooter\n" . $footer);
      $retVal = array( 1, $Rows, $footer );
//$txt = var_export($retVal, true);
//logError("\n\nretVal\n" . $txt);

//$txt=json_encode($retVal);
//logError("\n\njson\n" . $txt);
      echo json_encode($retVal);
    }
    else
    {
        echo '<h1>db connect error</h1>';
        LogError("__FILE__ " . "__LINE__ " . "db connect error");
        $retVal[0] = 0;
    }
  }

  function getTrackForJquery($conn, $userID, $startDate)
  //------------------------------------
  {
//logError( "At getTrackForJquery " . $startDate);

    global $trackingID, $itemID, $qty, $serving, $water;
    global $calories, $protein, $fat, $carbs, $fiber;
    global $sugars, $phos, $potas, $sodium, $gramsPer;
    global $itemName, $uomDesc;
    global $Rows, $footer;

      $totWeight = $totWater = $totCalories = $totProtein = 0;
      $totFat = $totCarbs = $totFiber = $totSugars = $totPhos = 0;
      $totPotas = $totSodium = 0;

      // get specific day's tracking info
      if ($stmt = setUpTrackInfo($conn, $userID, $startDate))
      {
$n =0;
         // get specific day's tracking info
         while (mysqli_stmt_fetch($stmt))
         {
++$n;
            getStuffForJQuery($userID, $startDate);
         }
//logError ("Num records=" . $n);
         mysqli_stmt_close($stmt);
      }
else logError("uhOh!\n");
}

  function getStuffForJQuery($userID, $startDate)
  //------------------------------------
  {
    global $trackingID, $itemID, $qty, $serving, $water;
    global $calories, $protein, $fat, $carbs, $fiber;
    global $sugars, $phos, $potas, $sodium, $gramsPer;
    global $itemName, $uomDesc;
    global $Rows;

    global $totWeight, $totWater, $totCalories, $totProtein;
    global $totFat, $totCarbs, $totFiber, $totSugars, $totPhos;
    global $totPotas, $totSodium;
//logError( "At getStuffForJQuery " . $startDatei);

      // process one row of data
      $row = '<tr class="rightJustify">';
            $row .= '<td class="editable" dataVal="' . $qty . '">' . phpGetItemText($qty,2) . '</td>';
            $row .= '<td>' .$uomDesc . '</td>';
            $row .= '<td class="hidden">' . $itemID . '</td>';
            $row .= '<td class="itemName">' . $itemName . '</td>';
            $row .= '<td class="editable" dataVal="' . $serving . '">' . phpGetItemText($serving, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $water . '">' . phpGetItemText($water, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $calories . '">' . phpGetItemText($calories, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $protein . '">' . phpGetItemText($protein, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $fat . '">' . phpGetItemText($fat, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $carbs . '">' . phpGetItemText($carbs, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $fiber . '">' . phpGetItemText($fiber, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $sugars . '">' . phpGetItemText($sugars, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $phos . '">' . phpGetItemText($phos, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $potas . '">' . phpGetItemText($potas, 0) . '</td>';
            $row .= '<td class="nutriCol" dataVal="' . $sodium . '">' . phpGetItemText($sodium, 0) . '</td>';
            $row .= '<td class="hidden" dataVal="' . $gramsPer . '">' . $gramsPer . '</td>';
            $row .= '<td><button type="button" class="delButton">Remove</td>';
            $row .= '<td class="hidden" value="' . $trackingID . '" >' . $trackingID . '</td>';
      $row .= '</tr>';
      $Rows[] = $row;

          $totWeight += $serving;
          $totWater += $water;
          $totCalories += $calories;
          $totProtein += $protein;
          $totFat += $fat;
          $totCarbs += $carbs;
          $totFiber += $fiber;
          $totSugars += $sugars;
          $totPhos += $phos;
          $totPotas += $potas;
          $totSodium += $sodium;
}

function getFooter()
// ----------------
{
    global $totWeight, $totWater, $totCalories, $totProtein;
    global $totFat, $totCarbs, $totFiber, $totSugars, $totPhos;
    global $totPotas, $totSodium;
    global $footer;
//echo "At getFooter<br>";
//logError("At getFooter\n");
//logError("totWeight=" . $totWeight);
//logError("totWater=" . $totWater);
//logError("totCalories=" . $totCalories);

//      $footer = '<tfoot id="logFooter">';
        $footer .= '<tr>';
          $footer .= '<td colspan="3">Totals</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totWeight . '">' . number_format($totWeight) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totWater . '">' . number_format($totWater) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totCalories . '">' . number_format($totCalories) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totProtein . '">' . number_format($totProtein) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totFat . '">' . number_format($totFat) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totCarbs . '">' . number_format($totCarbs) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totFiber . '">' . number_format($totFiber) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totSugars . '">' . number_format($totSugars) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totPhos . '">' . number_format($totPhos) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totPotas . '">' . number_format($totPotas) . '</td>';
          $footer .= '<td class="nutriCol" dataVal="' . $totSodium . '">' . number_format($totSodium) . '</td>';
          $footer .= '<td class="hidden"></td>';  // gramsPerUnit not totaled
          $footer .= '<td class="hidden"></td>';  // button col
          $footer .= '<td class="hidden" class="trackID"></td>';  // trackingID col
        $footer .= '</tr>';
//      $footer .= '</tfoot>';
}

function phpGetItemText($val, $dec)
//-------------------------
{
     if ($val == -1.0) return "N/A";
     else return number_format($val,$dec);
}

?>

