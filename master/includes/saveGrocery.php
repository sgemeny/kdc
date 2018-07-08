<?php
/*************  for testing  *
$_POST["data"] = ' {"items":[{"GroceryNameID":"353"},{"GroceryName":"Lasagna- Restaurant with meat"},{"NDB_No":"36041"},{"Water":"12."},{"Calories":"0.0000"},{"Protein":"0.0000"},{"Fat":"0.0000"},{"Carbs":"0.0000"},{"Fiber":"0.0000"},{"Sugars":"0.0000"},{"Phosphorus":"0.0000"},{"Potassium":"0.0000"},{"Sodium":"0.0000"},{"gramsPerUnit":"0.0000"},{"groc_UOM":"1"}]} ';
/*************/

/*************  for testing *
$_POST["data"] = ' {"items":[{"GroceryNameID":"251"}, {"NDB_NO":"16103"},{"GroceryName":"Beans, Refried"},{"Water":"0.762"},{"Calories":"0.910"},{"Protein":"0.054"},{"Fat":"0.012"},{"Carbs":"0.153"},{"Sugars":"0.005"},{"Phosphorus":"1.110"},{"Potassium":"3.360"},{"Sodium":"4.490"},{"gramsPerUnit":"238.000"},{"groc_UOM":"3"}]} ';
*************/

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
    $things = current($updates);

    $sql  = "UPDATE GROCERIES ";
    $where = " WHERE ";
 
    foreach($things as $key => $value)
    {
      $fldValue = current($value);
      $fldName = key($value);

      switch ($fldName)
      {
        default:
         logError( "ERROR: UnKnown field name! " . $fldName . "\n");
        break;

        case 'GroceryNameID':
          $where .=   $fldName.  "='".$fldValue."'";
        break;

        case 'GroceryName':
          $sql .= "SET " . $fldName . " = ? ";
          $bindType .= "s";
          $aValues[] = $fldValue;
          $aBindTo[] = $fldName;
        break;

        case 'NDB_No':
          $sql .= ", " . $fldName . " = ? ";
          $bindType .= "i";
          $aValues[] = $fldValue;
          $aBindTo[] = $fldName;
        break;

        case 'Water':
        case 'Calories':
        case 'Protein':
        case 'Fat':
        case 'Carbs':
        case 'Fiber':
        case 'Sugars':
        case 'Phosphorus':
        case 'Potassium':
        case 'Sodium':
        case 'gramsPerUnit':
        case 'gramsPerCup':
          $sql .= ", " . $fldName . " = ? ";
          $bindType .= "d";
          $aValues[] = $fldValue;
          $aBindTo[] = $fldName;
          break;

        case 'groc_UOM':
          $sql .= ", " . $fldName . " = ? ";
          $bindType .= "i";
          $aValues[] = $fldValue;
          $aBindTo[] = $fldName;
          break;
      }
    }  // foreach
    $query = $sql . $where;

    // prepare
    $sts = false;
    if (($stmt = $conn->prepare($query)))
    {
      // bind input
      $sts = call_user_func_array('mysqli_stmt_bind_param'
                                 , array_merge ( array($stmt, $bindType)
                                               , refValues($aValues)
                                               )
                                 );
      if ($sts)
      {
         // execute
         if (mysqli_stmt_execute($stmt) )
         {
	   mysqli_stmt_close($stmt);
           $sts=False;
         }
         else
         {
            sqlErr( __FILE__, "Execute Error ", $conn  );
         }
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

