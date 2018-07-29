<?php
echo "<html>";

  echo '<head>';
  echo '<meta charset="utf-8">';
  echo '<title>Kidney Diet Tracker</title>';

  // font awesome
  echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

  // Bootstrap core CSS
  echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >';

  echo '<link rel="stylesheet" type="text/css" href="/master/css/style.css">';

  $user = $_SESSION["userName"];
  echo '</head>';

  echo '<body>';
  echo ' <div class="container">';
  echo '  <div id="banner">';
  echo '    <a href="../../" title="Kidney Diet Central" id="kdc-logo">KidneyDietCentral</a>';
  echo '  </div>'; // banner

function showBannerMsg($msg = "Welcome to Kidney Diet Central!") 
{
  echo '   <h2 id="pageTitle">' . $msg . '</h2>';
}

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



