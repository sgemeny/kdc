<?php
//@ob_start();
session_start();

function logError($msg)
//-----------------------------
{
   static $firstRun = true;
/**********
echo "msg= " . $msg . "<br>";
echo "file=" . $file . "<br>";
echo "<pre>";
debug_print_backtrace();
echo "</pre>";
**********/
   if ($firstRun)
   {
       date_default_timezone_set('US/Eastern');
       $firstRun = false;
   }

   $dayTime =  date('Y-m-d') . "  " . date('g:i A');
   $myMsg=  "" . $dayTime . " ** " .$msg . "\n";
//   error_log($myMsg . "\n", 3, "/tmp/kdcErrors.log");
   error_log($myMsg, 3, "/tmp/kdcErrors.log");
   return;
}


?>

