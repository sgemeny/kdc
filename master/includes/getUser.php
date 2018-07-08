<?php
  require_once ('logError.php');

  // get WordPress functionality
  define('WP_USE_THEMES', false);
  require_once($_SERVER['DOCUMENT_ROOT']. "/wp-blog-header.php");

//  $current_user = wp_get_current_user();
//  $userName = $current_user->user_firstname;
//  $userID = $current_user->ID;

  $userID= get_current_user_id();
  $result = get_userdata($userID);
      $userName = $result->user_login;
      if ($userName != false)
      {
          $_SESSION["userID"] = $userID;
          $_SESSION["userName"] = $userName;
          $_SESSION["MEMBER_LEVEL"] = OPTIMIZEMEMBER_CURRENT_USER_ACCESS_LEVEL;
      }
      else // not logged in
      {
         header("Location: " . "../index.php");
         exit(0);
      }
//logError("getUser SESSION userID " . $_SESSION["userID"]);
//logError("getUser SESSION userName " . $_SESSION["userName"]);
//logError("getUser SESSION MEMBER_LEVEL " . $_SESSION["MEMBER_LEVEL"]);

?>

