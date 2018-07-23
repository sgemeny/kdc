<?php
   // Databases
   if (!defined("WORK")) define("WORK", 2);      // local server
   if (!defined("MASTER")) define("MASTER", 3);  // production server
   $_SESSION['db']= MASTER;   // set server
?>

