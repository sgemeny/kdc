<?php
  require_once("server.php");

function app_config()
{
//  $myPath = $_SESSION['MYPATH'] . '/dbConfig64';
//  $db = json_decode(base64_decode(file_get_contents($myPath)), true);

  if ($_SESSION['MYPATH']=="") 
      $_SESSION['MYPATH'] = dirname(__FILE__,3);

  $myPath = $_SESSION['MYPATH'] . '/dbConfig64';
  $db = json_decode(base64_decode(file_get_contents($myPath)), true);
  return $db;
}

?>

