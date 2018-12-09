<?php
 require_once ('app_config.php');
 require_once ('dbErr.php');

 // Secure Connection Script
 function dbConnect()
 { 
   $db = app_config();
/***************
echo '<pre>';
print_r($db);
echo "</pre>";
/***************/
   $conn = new mysqli( $db['hostname']
	             , $db['username']
	             , $db['password']
	             , $db['database']
	             );
   if (mysqli_connect_errno()) 
   {
        $stak = debug_backtrace(false);
        echo "<pre>";
        $msg = print_r($stak);
        echo "</pre>";
        $msg .= "\n";
        error_log($myMsg, 3, "/tmp/kdcErrors.log");
        sqlErr(__FILE__, "Failed on Connect, Error Num: " . mysqli_connect_errno(), "NULL");
   }
   return $conn;
   //	END	Secure Connection Script
 }
?>

