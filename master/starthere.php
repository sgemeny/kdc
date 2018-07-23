<?php
  session_start();
  $sessionID= session_ID();

  require_once ('includes/jquery.php');
  require_once('includes/server.php');
  require_once('includes/getUser.php');

?>

<script>
$(document).ready( function() {
//------------------------------
  window.location.href = './menu.php';
});  // end on page loaded

</script>
