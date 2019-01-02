<?php
  session_start();
  $sessionID = session_ID();

  if(!isset($_SESSION['userName']))
  {
      logError("menu.php - username not set");
      header("Location: " . "starthere.php");
      exit();
  }
  $self = $_SERVER['PHP_SELF'];

  $_SESSION['MYPATH'] = dirname(dirname(__FILE__));

  require_once ('includes/banner.php');
  require_once ('includes/dbConnect.php');
  require_once ('includes/displayButtons.php');
     
  showBannerMsg("Welcome " . $_SESSION["userName"] . " to KDC Main Menu");
  $conn = dbConnect();

  echo '<form id="frmChooseRecipe" action="'.$self.'" method="get" >';
  echo '<input type="hidden" name="username" id="username" value="' . $_SESSION["userName"] .'" />';

  $btns = array( RECIPES =>"Recipes" 
               , GROCERIES => "Food List"
               , TRACK =>"Track Food"
               , DAILYS =>"Show Log"
               , USDA =>"USDA Food"
               , MEMBER =>"Member Home"
               , LOGOUT =>"Log Out"
               );

  displayButtons($btns);
//logError("menu.php - member level = " .  $_SESSION["MEMBER_LEVEL"]);
?>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>

$(document).ready( function() {
// ----------------------------

  $("#btnRecipes").click(function(event)
  // ------------------------------------
  {
    var url =  "includes/recipes.php";
    document.location.href = url;
  });

  $("#btnGroceries").click(function(event)
  // ------------------------------------
  {
    var url =  "includes/showGroceries.php";
    document.location.href = url;
  });

  $("#btnDaily").click(function(event)
  // ------------------------------------
  {
    var url =  "includes/dailyTotals.php";
    document.location.href = url;
  });

  $("#btnUSDA").click(function(event)
  // ------------------------------------
  {
    window.open("foodList.php", "_blank","resizable=yes,top=400,left=550,width=400,height=400");
  });

  $("#btnTrack").click(function(event)
  // ------------------------------------
  {
    var url =  "includes/track.php";
    document.location.href = url;
  });

  $("#btnDaily").click(function(event)
  // ------------------------------------
  {
    var url =  "includes/dailyTotals.php";
    document.location.href = url;
  });

  $("#btnLogOut").click(function(event)
  // ------------------------------------
  {
    var url =  "login";
    document.location.href = url;
  });

  $("#btnMember").click(function(event)
  // ------------------------------------
  {
    var url =  "member-home";
    document.location.href = url;
  });

});  // doc ready


</script>

  </div> <!-- end of container (started in banner.php) -->
 </body>
</html>

