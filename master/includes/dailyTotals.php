<!doctype html>
<?php

  session_start();

  $self = $_SERVER['PHP_SELF'];
//  $referrer =  $_SERVER['HTTP_REFERER'];

  if(!isset($_SESSION['userID']))
  {
    header("Location: " . "logIn.php");
    exit();
  }

  $userID =  $_SESSION["userID"];

echo '<html>';
echo '<head>';
  echo ' <meta charset="utf-8">';
  echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
  echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
  echo '<title>Daily Log</title>';

  require_once ( 'jquery.php' ); // must be defined before jquery UI
  echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" ></script>';
  echo '<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>';

  // Bootstrap core CSS
  echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >';

  // Custom styles for this template -->';
  echo '<link href="../css/custom.css" rel="stylesheet"> ';
  echo '<link href="../css/style.css" rel="stylesheet"> ';
  echo '<link href="../css/dailyTotals.css"  media="all"  rel="stylesheet"> ';

  // jQuery UI stylesheet
  echo '<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/humanity/jquery-ui.css">';

  require_once ('../scripts/fb_pixel.js');
echo '</head>';

  echo '<body>';
  require_once ('dbConnect.php');
  require_once ('displayButtons.php');


  $conn = dbConnect();
      echo '<nav class="navbar navbar-inverse navbar-fixed-top"> ';
        echo '<div class="container">';
          echo '<div class="navbar-header">';
            echo '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>';

            echo '<a href="../../" class="pull-left"><img src="../images/kdcLogo.png" alt="KDC" ></a>';
          echo '</div>';   // navbar header -->

          echo '<div id="navbar" class="collapse navbar-collapse">';
            echo '<ul class="nav navbar-nav">';
              if ( $_SESSION["MEMBER_LEVEL"] >2)
                     echo '<li><a href="../starthere.php">Main Menu</a></li>';

              echo '<li><a id="home" href="../../member-home">Member Area</a></li>';
              echo '<li><a id="track" href="track.php">Back to Track</a></li>';
            echo '</ul>';
          echo '</div>'; // nav-collapse -->
        echo '</div>';  // container
      echo '</nav>';


    echo '<section id="section-to-print">';

    echo '<container>';
     echo "<h3>Daily Log</h3><br>";
       echo '<div id="buttons">';
         if ($_SESSION["MEMBER_LEVEL"] >2)
              $btns = array( MENU => "Main Menu", DAILYS =>"Get Log", TRACK => "Track Food");
         else $btns = array( DAILYS =>"Get Log" );

         displayButtons($btns);
       echo '</div>';

      echo '<form id="frmTrack">';

        echo '<input type="hidden" name="userID" id="userID" value="' . $userID .'" />';
//        echo '<input type="hidden" name="caller" id="caller" value="' . $referrer .'" />';

        echo '<div id="dateRange">';
            echo '<div id="fromHolder">';
              echo '<label for "from">From</label>';
              echo '<input type="text" name="from" id="from" class="dateType">';
             echo '</div>';  // fromHolder
            echo "<br>";

            echo '<div id="toHolder">';
              echo '<label for "to">To</label>';
              echo '<input type="text" name="to" id="to" class="dateType">';
            echo '</div>';  // toHolder

        echo '</div>'; // dateRange



      echo '</form>';

      echo '<div id="tableHolder">';
        echo '<table id="tblDailys" class="hidden">';
          echo '<thead>';
            echo '<th class="dateCol">Date</th>';
            echo '<th class="dayCol">Day</th>';
            echo '<th class="stdCol1">Water</th>';
            echo '<th class="stdCol1">Calories</th>';
            echo '<th class="stdCol1">Protein</th>';
            echo '<th class="stdCol1">Fat</th>';
            echo '<th class="stdCol1">Carbs</th>';
            echo '<th class="stdCol1">Fiber</th>';
            echo '<th class="stdCol1">Sugars</th>';
            echo '<th class="stdCol1">Phos.</th>';
            echo '<th class="stdCol1">Pot.</th>';
            echo '<th class="stdCol1">Sodium</th>';
          echo '</thead>';

          echo '<tbody></tbody>';
        echo '</table>';
     echo '</div>'; // tableHolder
    echo '</div>'; // container
  echo '</section>';
?>
    
<script>

  $("#btnDaily").click(function(event)
  // ------------------------------------
  {
     getReport();
  });

  function getReport()
  // ------------------------------------
  {
    var userID = $("#userID").val();
    var from = $("#from").val();
    var to = $("#to").val();
    var sts = true;

    if (from == '')
    {
       alert('Please enter a valid "from" date');
       sts=false;
    }

    if (to == '')
    {
       alert('Please enter a valid "to" date');
       sts=false;
    }

    if (!sts) return;

    var start = new Date(from);
    var stop = new Date(to);

    if (!( stop > start))
    {
        alert("The TO date MUST be later than the From date!" );
        return;
    }

    var arrayData = { "from" : from, "to" : to, "userID" : userID };
    var itemData = JSON.stringify(arrayData);

     $.ajax(
     {
       url: "./getDailyTotals.php",
       type: "post",
       data: {"data" : itemData},
       success: function( data, status)  // callback
         {
           if (status=="success")
           {
             var itemInfo = $.parseJSON(data);
             $("#tblDailys tbody").html(itemInfo);
             $("#tblDailys").removeClass("hidden");
           }
           else
           {
             alert("Error Occurred, Unable to save data");
           }
         },
       error: function(xhr)
                {
                  alert( "An error occured: " + xhr.status + " " + xhr.statusText);
                }
    });
  }

  function logOff()
  // --------------
  {
     $.get("../includes/logOut.php");
     alert("Your have successfully logged out.");
     document.location.href = "../../index.php";
  }


  $(function() 
  {
      $( "#from" ).datepicker( {dateFormat: "yy-mm-dd"});
      $( "#to" ).datepicker({dateFormat: "yy-mm-dd"});
  });


$(document).ready( function() {
// ----------------------------

  $("#btnMenu").click(function(event)
  // ------------------------------------
  {
     url = "../starthere.php";
     document.location.href = url;
  });

  $("#btnTrack").click(function(event)
  // ------------------------------------
  {
     url = "track.php";
     document.location.href = url;
  });

});

  </script>

</body>
</html>

