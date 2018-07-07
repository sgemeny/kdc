<?php
  require_once("server.php");

function app_config()
{
  $workDB = array(
               'hostname' => 'localhost',
               'username' => 'williao8_sue',
               'password' => 'Pink1bun_',
               'database' => 'dietModified_2016_05_21'
             );

  $masterDB = array(
               'hostname' => 'localhost',
               'username' => 'williao8',
               'password' => 'mareseatoatsanddoeseatoats',
               'database' => 'williao8_dietModified_20160521'
//               'database' => 'williao8_diet_test'
             );
  $db=  $_SESSION['db'];

  if ($db == MASTER)
     return $masterDB;
  else
     return $workDB;
}

?>

