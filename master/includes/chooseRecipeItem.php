<?php

require_once ('./dbErr.php');
require_once ('./jquery.php');
require_once ('./logError.php');

// Create & display recipe select
function selectRecipe($conn, $btnCap="")
//-----------------------------------------
{
  $userID=$_SESSION['userID'];

  $sql= "SELECT RecipeName, ID, ownerID ";
  $sql.= "FROM RecipeMaster ";
  $sql.= "WHERE (ownerID=? AND isPublic=0) OR isPublic=1 ";
  $sql.= "ORDER BY RecipeName ";

  if (($stmt = $conn->prepare($sql)))
  {
    if ( mysqli_stmt_bind_param($stmt, 'i', $userID))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (mysqli_stmt_bind_result( $stmt
                                   , $item
                                   , $ID
                                   , $ownerID
                                   ) )
        mysqli_stmt_store_result($stmt);
        $sts = true;
      } // execute
    } // bind param
  } // prepare

  $retVal = 0;
  if ($sts)
  {
    // create drop down box
    echo '<div id="wrapper">';
     echo '<div id="chooseHolder" class="item-list">';
       echo '<input type="input" placeholder="Search.." id="myInput"
                   onkeyup="myFilter(event)" autocomplete="off">';

       echo '<button id="btnSelectItem" name="btnSelectItem" type="button"
                    <i class="fa fa-search"></i></button>';
       echo '<div id="chooser" class="hidden">';
         echo '<ul id="itemChooser">';

          // Fetch one at a time from result
          while (mysqli_stmt_fetch($stmt))
          {
            echo '<li data-id=' . $ID . '+' . $ownerID . '>' . $item .  '</li>';
          }
          mysqli_stmt_close($stmt);
         echo '</ul>';
       echo '</div>';   // end of chooser
     echo "</div>"; // end of chooseHolder
    echo "</div>";     // end of wrapper
   }
   else 
   {
     sqlErr(__FILE__, "line" . __LINE__, $conn);
     exit();
   }
   return $retVal;
}

?>

<script>
  function myFilter(event)
  // ------------------------
  {  // check for space bar
     var x = event.which || event.keyCode;
     if (x == 0x20)
         return false; // space, nothing to do;

     if ( $("#myInput").val().length == 0 )
              $("#itemChooser li").show();

     var hideIt = false;
     var text = $("#myInput").val().toLowerCase();
     $("#itemChooser li").each(function(ndx, optn)
     {
       if ( $(this).is(":visible") )
       { // Showing
         var myPhrase = $(this).text().toLowerCase();
         var words = text.split(" ");

         if (words.length >1)
         { // multipe words
           $(words).each(function(ndx, item)
           {
             if (item==" ")
                 return false;  // ignore extra spaces

             // we need all words, so once we set hideIt to true
             // no need to continue this loop
             if (myPhrase.indexOf(item) > -1)
                 hideIt = false
             else
             {
                hideIt = true;
                return false;
             }
           });
         }
         else
         {  // only 1 word or less
            if (myPhrase.indexOf(words[0]) > -1)
            {
              hideIt = false;
            }
            else
            {
              hideIt = true;
            }
         }
       }  // if showing,  ignore hidden, previously ruled out

       if (hideIt)
          $(this).hide();
     }); // each option
  }

$(document).ready( function() {
// ----------------------------

  $("#itemChooser").on('click', 'li', function(e)
  // ------------------------------------
  {
     var data = $(this).attr('data-id').split("+");
     $("#choice").prop('value', data[0]);
     $("#owner").prop('value', data[1]);
     $("#myInput").val($(this).text());
     checkIfCanEdit();
alert( $(this).text() + '\nchoice=' + data[0] + " owner=" + data[1] );
     $("#chooser").hide();

  });

  $("body").click(function(e)
  // ------------------------------------
  {
     if ( ! $(e.target).parent().hasClass('item-list') )
           $("#chooser").hide();

  });

  $("#myInput").click(function()
  // ------------------------------------
  {
     $("#chooser").show();
  });

  $("#btnSelectItem").click(function()
  // ------------------------------------
  {
     $("#chooser").toggle();
  });

});  // end on page loaded


</script>

