<?php


function dbAlert($location)
// ---------------------
{
   static $firstRun = true;

   if ($firstRun)
   {
       echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >';
       $firstRun = false;
   }

   echo '<div class="alert alert-danger">';
     echo '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>';
     echo '<a href="' . $location . '" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
     echo 'There Was An Error Accessing the Data';
   echo '</div>';
}


function sqlErr($file, $msg, $conn)
//------------------------
{
   static $firstRun = true;

   if ($firstRun)
   {
       date_default_timezone_set('US/Eastern');
       $firstRun = false;
   }

   $dayTime =  date('Y-m-d') . "  " . date('g:i A');
   $myMsg=  $dayTime . "\tFILE:  " . $file . " **" .$msg . "    \n    ";

   error_log($myMsg .   mysqli_error($conn) . "\n", 3, "/tmp/kdcErrors.log");
   echo "FAIL";
   return;
}


?>

