<!doctype html>
<html>
<?php

function showBanner($msg = "Welcome to Kidney Diet Central!")
{
  $styleSheet = "../css/styleSheet.css";
  if (!file_exists($styleSheet)) $styleSheet = "css/styleSheet.css";
  $user = $_SESSION["userName"];

  echo '<head>';
  echo '<meta charset="utf-8">';
  echo '<title>Kidney Diet Tracker</title>';
  echo '<link rel="stylesheet" type="text/css" href="'.$styleSheet.'">';
?>

<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '246219625835565'); // Insert your pixel ID here.
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=246219625835565&ev=PageView&noscript=1"
/></noscript>
<!-- DO NOT MODIFY -->
<!-- End Facebook Pixel Code -->

<?php
  echo '</head>';

  echo '<body>';
  echo ' <div id="container">';
  echo '  <div id="banner">';
  echo '    <a href="../../" title="Kidney Diet Central" id="kdc-logo">KidneyDietCentral</a>';
  echo '  </div>'; // banner

/***************************
  echo '   <div id="logOut">';
  echo '     <input id="btnLogOut" name="btnLogOut" type="button" class="myButton" value="Log Out">';
  echo '   </div>';  // logOut
/***************************/
  echo '   <h2 id="pageTitle">' . $msg . '</h2>';

//<!--  </div>       end of container div -->

}
?>


