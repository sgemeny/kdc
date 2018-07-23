<?php
  session_start();
  $sessionID= session_ID();
  
  require_once ('includes/jquery.php');
  require_once('includes/server.php');
  require_once('includes/getUser.php');

//require_once('includes/logError.php');
//logError("start here SESSION ID " . $sessionID);
//logError("start here SESSION userID " . $_SESSION["userID"]);
//logError("start here SESSION userName " . $_SESSION["userName"]);
//logError("start here SESSION MEMBER_LEVEL " . $_SESSION["MEMBER_LEVEL"]);

  // start the app
//  header("Location:  menu.php?db=3");
//  exit(0);
?>

<script>
$(document).ready( function() {
//------------------------------
  window.location.href = './menu.php';
});  // end on page loaded

</script>

