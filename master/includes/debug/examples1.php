<?php
// --------------------------------------------------------
// Accessing jQuery/JSON Array
// --------------------------------------------------------
$strData = ' {"items":[{"GroceryNameID":"251"},{"NDB_NO":"16103"},{"GroceryName":"Beans, Refried"},{"Water":"0.762"},{"Calories":"0.910"},{"Protein":"0.054"},{"Fat":"0.012"},{"Carbs":"0.153"},{"Sugars":"0.005"},{"Phosphorus":"1.110"},{"Potassium":"3.360"},{"Sodium":"4.490"},{"gramsPerUnit":"238.000"},{"groc_UOM":"3"}]} ';

$docRoot = $_SERVER['DOCUMENT_ROOT'];
require_once ($docRoot . '/includes/dbConnect.php');
$conn = dbConnect($docRoot);

$updates = json_decode($strData,true);
echo '<br>';

var_dump($updates);
echo '<br>';
echo '<br>';
echo 'Updates Key=' . key($updates) . '<br>';
echo 'Updates Value='. current($updates) . '<br>';
$things = current($updates);

$len = count($updates['items']);
echo '<br>';
echo "items len: " . $len ;
echo '<br>';

echo "things len: " . count($things);
echo '<br>';

$tmp = $things[0];
$k = key($tmp);
echo 'k='. $k . '<br>';
echo 'current=' . current($tmp) . '<br>';
echo '<br>';

echo $things[0]['GroceryNameID'] . '<br>';
echo $things[1]['NDB_NO'] . '<br>';
echo $things[2]['GroceryName'] . '<br>';
echo '<br>';

foreach($things as $key => $value)
{
   $fldValue = current($value);
   $fldName = key($value);
   echo "field is: " . $fldName . " " . $fldValue .  '<br>';
}
echo '<br>';

// --------------------------------------------------------
// Parameterized Read Query
// --------------------------------------------------------
// parameters
$fldVal = 251;

// sql
$sql  = "SELECT GroceryName FROM GROCERIES ";
$sql .= "WHERE GroceryNameID = ? ";
echo "sql= " . $sql . '<br>';

// prepare
if (!($stmt = $conn->prepare($sql)))
   echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;

// bind
if ($sts = mysqli_stmt_bind_param($stmt, "i", $fldVal))
   echo "Bound<br>";
else
{
   echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
   exit();
}  

// execute
if (mysqli_stmt_execute($stmt) )
{
  echo "executed<br>";
  echo "no err: " . $conn->error . '<br>';

  // bind result variables
  $grocName="";
  mysqli_stmt_bind_result($stmt, $grocName);

  // get the data
  while (mysqli_stmt_fetch($stmt))
  {
    printf("%s %i\n", $grocName, $fldVal);
  }

  // close stmt
  mysqli_stmt_close($stmt);
}
else
  echo "Execute FAILED: " . $conn->error . '<br>';

echo '<br>execute "READ" done<br>';

// --------------------------------------------------------
// Parameters Update Example
// --------------------------------------------------------

// parameters
$fldVal = 251;
$newName = "Beans, Refried";
$gramsUnit = 156.32;

$sql  = "UPDATE GROCERIES ";
$sql .= "SET GroceryName = ?, gramsPerUnit = ? ";
$sql .= "WHERE GroceryNameID" .  "='".$fldVal."'";
echo "sql= " . $sql . '<br>';

// prepare
if (!($stmt = $conn->prepare($sql)))
   echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;

// bind input
if ($sts = mysqli_stmt_bind_param($stmt, "sd", $newName, $gramsUnit))
   echo "Bound<br>";
else
{
   echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
   exit();
}  

// execute
if (mysqli_stmt_execute($stmt) )
{
  echo "executed UPDATE done<br>";
  echo "no err: " . $conn->error . '<br>';

  // close stmt
  mysqli_stmt_close($stmt);
}

// --------------------------------------------------------
// Parameters Update Example 2:  using varible column names
// --------------------------------------------------------

$columnID = "GroceryNameID"; 
$colName = "GroceryName"; 
$fldVal = 251;
$newName = "Beans, Refried 123";


$sql  = "UPDATE GROCERIES ";
$sql .= "SET " . $colName . " = ? ";
$sql .= "WHERE " . $columnID . "='".$fldVal."'";
echo "sql= " . $sql . '<br>';

// prepare
if (!($stmt = $conn->prepare($sql)))
   echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;

// bind input
if ($sts = mysqli_stmt_bind_param($stmt, "s", $newName))
   echo "Bound<br>";
else
{
   echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
   exit();
}  

// execute
if (mysqli_stmt_execute($stmt) )
{
  echo "executed UPDATE done<br>";
  echo "no err: " . $conn->error . '<br>';

  // close stmt
  mysqli_stmt_close($stmt);
}

// --------------------------
// PHP testing array function
// --------------------------
// this does not maintain desired order
  $myArray = array();
  $myArray[1] = "one";
  $myArray[2] = "two";
  $myArray[0] = "zero";

  echo "<br>myArray Contains<br>";
  print_r($myArray);

// Must use a place holder- this works
  $myArray = array();
  $myArray[0] = "";
  $myArray[1] = "one";
  $myArray[2] = "two";
  $myArray[0] = "zero";

  echo "<br>myArray Contains<br>";
  print_r($myArray);

// different test -- gives desired results as well!
  $tmpArray = array();
  $tmpArray[] = "";
  $tmpArray[] = "one";
  $tmpArray[] = "two";
  $tmpArray[0] = "zero";

  echo "<br>tmpArray Contains<br>";
  print_r($tmpArray);
?>

