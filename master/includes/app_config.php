<?php
  require_once("server.php");

function app_config()
{
  $myPath = $_SESSION['MYPATH'] . '/dbConfig64';
//echo "myPath= " . $myPath . "<br>";
  $db = json_decode(base64_decode(file_get_contents($myPath)), true);
  return $db;
}

?>

