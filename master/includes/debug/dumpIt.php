<?php
/*************************/
/*  Debugging functions  */
/*************************/


// dump SERVER variables
function dumpServer()
// -----------------------
{
  echo "<br>------------------<br>";
   echo "<pre>";
   echo "<br>_SERVER Array<br>";
   print_r($_SERVER);
  echo "</pre>";
}

// dump all rows of a result set
function dumpRowResults($index,$row)  // input: row Number, row result
//----------------------------------
{
//   for ($set = array (); $row = $result->fetch_assoc(); $set[] = $row);
   $set = array ();
   $set[] = $row;
   echo "<pre>"; echo "row $index<br>"; print_r($set); "</pre>";
}

// dump Super Globals
function dumpSupers()
//-------------------
{
  echo "<br>------------------<br>";
   echo "<pre>";
   echo "<br>_REQUEST Array<br>";
   print_r($_REQUEST);
  echo "</pre>";

   dumpGetVars();
   dumpPostVars();
}

// dump $_GET Variables
function dumpGetVars()
//--------------------
{
  echo "<br>------------------<br>";
  echo "<pre>";
    echo "_GET Array<br>";
    print_r($_GET);
  echo "</pre>";
echo "<br>------------------<br>";
}

// dump $_POST Variables
function dumpPostVars()
//---------------------
{
  echo "<br>------------------<br>";
  echo "<pre>";
    echo "_POST Array<br>";
    print_r($_POST);
  echo "</pre>";
echo "<br>------------------<br>";
}

// dump $_SESSION Variables
function dumpSessionVars()
//---------------------
{
  echo "<br>------------------<br>";
  echo "<pre>";
    echo "_SESSION Array<br>";
    print_r($_SESSION);
  echo "</pre>";
echo "<br>------------------<br>";
}

// dump Cookies
function dumpCookies()
//--------------------
{
  echo "<br>------------------<br>";
  echo "<pre>";
    echo "_GET Array<br>";
    print_r($_COOKIE);
  echo "</pre>";
echo "<br>------------------<br>";
}


// print out file name and line number
function debug($fd, $file, $line)
//-------------------------------
{
  $txt = "File: " . $file . "  Line: " . $line . "\n";
  fwrite($fd, $txt);
}

function dumpArray($myArray)
//--------------------------
{
  echo "<br>------------------";  
  echo "Elements in Array: " . count($myArray) . "<br>";
  echo "<pre>";
    print_r($myArray);
  echo "</pre>";
  echo "<br>------------------<br><br>";
}

// Print $_GET Variables
function printGetVars($fd)
//--------------------
{
   fwrite($fd, "\n------------------\n");
   fwrite($fd, "_GET Array\n");
   $results = print_r($_GET, true);
   fwrite($fd, $results);
   fwrite($fd, "\n------------------\n");
}

// Print Cookies
function printCookies($fd)
//--------------------
{
  fwrite($fd, "\n------------------\n");
  fwrite($fd, "_COOKIE Array\n");
  $results = print_r($_COOKIE, true);
   fwrite($fd, $results);
  fwrite($fd, "\n------------------\n");
}



// Example
//$selectFlag=="" ? $flag="XX " : $flag=$selectFlag;

