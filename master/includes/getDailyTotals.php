<?php
/************************  for testing *
  $_POST["data"] = '{"from":"2015-11-19","to":"2015-11-20","userID":"20"}';
/***************************************/

  session_start();

  $self = $_SERVER['PHP_SELF'];

  require_once ('dbConnect.php');

  $conn = dbConnect();

  if (isset($_POST["data"]))
  {
     $updates = json_decode($_POST["data"]);
     $userID = $updates->userID;
     $from = $updates->from;
     $to = $updates->to; 
//     date_default_timezone_set('US/Eastern');
//     $to = date_create($updates->to);
//     date_add($to, date_interval_create_from_date_string('1 days'));
//     $to = date_format($to, 'm-d-Y');


     $sql  = "SELECT date(dateEntered), dayname(dateEntered) as day ";
     $sql .=      ", sum(Water) ";
     $sql .=      ", sum(Calories) ";
     $sql .=      ", sum(Protein) ";
     $sql .=      ", sum(Fat) ";
     $sql .=      ", sum(Carbs) ";
     $sql .=      ", sum(Fiber) ";
     $sql .=      ", sum(Sugars) ";
     $sql .=      ", sum(Phosphorus) ";
     $sql .=      ", sum(Potassium) ";
     $sql .=      ", sum(Sodium) ";
     $sql .= "FROM userLog ";
     $sql .= "WHERE dateEntered BETWEEN ? AND ? ";
     $sql .= "AND userID=? ";
     $sql .= "GROUP BY DATE(dateEntered) ";
     $sql .= "ORDER BY dateEntered ";
//echo "sql= " . $sql . "<br>";
//echo "from= " . $from .  "<br>";
//echo "to= " . $to .  "<br>";

    $sts = false;
    if (($stmt = $conn->prepare($sql)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmt, 'ssi', $from, $to, $userID))
      {
        if (mysqli_stmt_execute($stmt) )
        {
          if (mysqli_stmt_bind_result( $stmt
                                   , $dateEntered
                                   , $day
                                   , $water
                                   , $calories
                                   , $protein
                                   , $fat
                                   , $carbs
                                   , $fiber
                                   , $sugars
                                   , $phos
                                   , $potas
                                   , $sodium
                                   ) )
          {
            mysqli_stmt_store_result($stmt);
            $sts = true;
          }  // bind output
        } // execute
      } // bind input params
    } // prepare

   if ($sts)
   {
     $results="";
     while ( mysqli_stmt_fetch($stmt) )
     { 
       $results .= '<tr>';
         $results .=  '<td class="dateCol">' . $dateEntered . '</td>';
         $results .=  '<td class="dayCol">' . $day . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($water) . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($calories) . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($protein) . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($fat) . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($carbs) . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($fiber) . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($sugars) . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($phos) . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($potas) . '</td>';
         $results .=  '<td class="rightJustify">' . number_format($sodium) . '</td>';
       $results .=  '</tr>';
     }

//  error_log("\nRESULTS\n" . $results . "\n", 3, "/tmp/myErrors.log");

     mysqli_stmt_close($stmt);

    echo json_encode($results);
    return true;
  } // sts

  // if we get here, an error occurred, so post it to log file
  error_log("\nSQL: " . $sql . "\nmysql ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
} // isset post data
?>

