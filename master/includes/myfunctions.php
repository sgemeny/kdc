<?php
//@ob_start();
//session_start();
/****************************************************************************
  IMPORTANT!!!!  This file MUST be included in the themes functions.php file
/****************************************************************************/

add_action('init', 'myStartSession', 1);
add_action('wp_logout', 'myEndSession');
add_action('wp_login', 'myEndSession');

require_once($_SERVER['DOCUMENT_ROOT'] . '/master/includes/logError.php');

function myStartSession() 
{
//debug_print_backtrace();
  if(!session_id()) 
  {
     session_start();
     $current_user = wp_get_current_user();

     if ($current_user->ID != 0)
     {
       $_SESSION["userName"] =  $current_user->user_login;
       $_SESSION["userID"] = $current_user->ID;
echo 'user is ' . $_SESSION["userName"] . ' ' . $_SESSION["userID"] . "<br>";
     }
  }
else echo "session_id NOT set<br>";
}
function myEndSession() 
{
  session_destroy ();
}
?>
