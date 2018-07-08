<?php
  session_start();
  $sessionID= session_ID();

  require_once ('includes/jquery.php');
  require_once('includes/server.php');
  require_once('includes/getUser.php');

//  header("Location:  menu.php?db=3");

//exit(0);
  
//  require_once('includes/server.php');
//  require_once('includes/getUser.php');

//require_once('includes/logError.php');

  // start the app
//  header("Location:  menu.php?db=3");

//echo "got here " . __LINE__ . "<br>";
//  exit(0);
?>

<script>
$(document).ready( function() {
//------------------------------
  window.location.href = './menu.php';
});  // end on page loaded

</script>
