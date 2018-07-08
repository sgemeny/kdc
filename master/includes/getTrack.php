<?php
    global $trackingID, $itemID, $qty, $serving, $water;
    global $calories, $protein, $fat, $carbs, $fiber;
    global $sugars, $phos, $potas, $sodium, $gramsPer;
    global $itemName, $uomDesc;

    global $totWeight, $totWater, $totCalories, $totProtein;
    global $totFat, $totCarbs, $totFiber, $totSugars, $totPhos;
    global $totPotas, $totSodium;

    require_once('./logError.php');
    require_once('./setupTrackInfo.php');

  function getTrackForPhp($conn, $userID, $startDate)
  //------------------------------------
  {
    global $totWeight, $totWater, $totCalories, $totProtein;
    global $totFat, $totCarbs, $totFiber, $totSugars, $totPhos;
    global $totPotas, $totSodium;

//logError( "At getTrackForPhp " . $startDatei);

    global $trackingID, $itemID, $qty, $serving, $water;
    global $calories, $protein, $fat, $carbs, $fiber;
    global $sugars, $phos, $potas, $sodium, $gramsPer;
    global $itemName, $uomDesc;

    $totWeight = $totWater = $totCalories = $totProtein = 0;
    $totFat = $totCarbs = $totFiber = $totSugars = $totPhos = 0;
    $totPotas = $totSodium = 0;

    echo '<tbody>';
      // get specific day's tracking info
      if ($stmt = setupTrackInfo($conn, $userID, $startDate))
      {
        while (mysqli_stmt_fetch($stmt))
        {
          getStuffForPHP($conn, $userID, $startDate);
        }
        mysqli_stmt_close($stmt);
      }
    echo '</tbody>';

    echo '<tfoot id="logFooter">';
        echo '<tr>';
          echo '<td colspan="3">Totals</td>';
          echo '<td class="nutriCol" dataVal="' . $totWeight . '">' . number_format($totWeight) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totWater . '">' . number_format($totWater) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totCalories . '">' . number_format($totCalories) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totProtein . '">' . number_format($totProtein) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totFat . '">' . number_format($totFat) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totCarbs . '">' . number_format($totCarbs) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totFiber . '">' . number_format($totFiber) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totSugars . '">' . number_format($totSugars) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totPhos . '">' . number_format($totPhos) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totPotas . '">' . number_format($totPotas) . '</td>';
          echo '<td class="nutriCol" dataVal="' . $totSodium . '">' . number_format($totSodium) . '</td>';
          echo '<td class="hidden"></td>';  // gramsPerUnit not totaled
          echo '<td class="hidden"></td>';  // button col
          echo '<td class="hidden" class="trackID"></td>';  // trackingID col
        echo '</tr>';
      echo '</tfoot>';
//    echo '</tbody>';
}

  function getStuffForPHP($conn, $userID, $startDate)
  //-----------------------
  {
//logError( "At getStuffForPHP " . $startDatei);
    global $trackingID, $itemID, $qty, $serving, $water;
    global $calories, $protein, $fat, $carbs, $fiber;
    global $sugars, $phos, $potas, $sodium, $gramsPer;
    global $itemName, $uomDesc;

    global $totWeight, $totWater, $totCalories, $totProtein;
    global $totFat, $totCarbs, $totFiber, $totSugars, $totPhos;
    global $totPotas, $totSodium;

     echo '<tr class="rightJustify">';
       echo '<td class="editable" dataVal="' . $qty . '">' . phpGetItemText($qty,2) . '</td>';
       echo '<td>' .$uomDesc . '</td>';
       echo '<td class="hidden">' . $itemID . '</td>';
       echo '<td class="itemName">' . $itemName . '</td>';
       echo '<td class="editable" dataVal="' . $serving . '">' . phpGetItemText($serving, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $water . '">' . phpGetItemText($water, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $calories . '">' . phpGetItemText($calories, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $protein . '">' . phpGetItemText($protein, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $fat . '">' . phpGetItemText($fat, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $carbs . '">' . phpGetItemText($carbs, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $fiber . '">' . phpGetItemText($fiber, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $sugars . '">' . phpGetItemText($sugars, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $phos . '">' . phpGetItemText($phos, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $potas . '">' . phpGetItemText($potas, 0) . '</td>';
       echo '<td class="nutriCol" dataVal="' . $sodium . '">' . phpGetItemText($sodium, 0) . '</td>';
       echo '<td class="hidden" dataVal="' . $gramsPer . '">' . $gramsPer . '</td>';
       echo '<td><button type="button" class="delButton">Remove</td>';
       echo '<td class="hidden" value="' . $trackingID . '" >' . $trackingID . '</td>';
     echo '</tr>';

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
?>

