<?php

require_once ('./dbErr.php');
require_once ('./jquery.php');
require_once ('./logError.php');

echo '<input type="hidden" name="recipeChoice" id="recipeChoice" />';
echo '<input type="hidden" name="owner" id="owner" />';

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
    echo '<div class="wrapper" id="recipeWrapper">';
     echo '<div id="chooseRecipeHolder" class="item-list">';
       echo '<input type="input" placeholder="Search Recipe..." id="recipeInput"
                   class="bigInput"
                   onkeyup="recipeFilter(event)" autocomplete="off">';

      echo '<button type="submit" id="btnSearchRecipe" class="searchButton">
                   <i class="fa fa-search"></i> </button>';

       if ($btnCap != "")
       {
           echo '<input id="btnSelectRecipe" name="btnSelectRecipe"
                        class="selectButton"
                        type="button" value="' . $btnCap . '">';
       }

       echo '<div id="recipeChooser" class="chooser hidden">';
         echo '<ul id="recipeItemChooser">';

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
  var textHighlighted = false;

  function recipeFilter(event)
  // ------------------------
  {  // check for space bar
     var x = event.which || event.keyCode;
     if (x == 0x20)
         return false; // space, nothing to do;

     if ( (textHighlighted==true) && (window.getSelection().toString()) == "")
     {
       $("#recipeItemChooser li").show();
       textHighlighted = false;
     }

     if ( $("#recipeInput").val().length == 0 )
              $("#recipeItemChooser li").show();

     var hideIt = false;
     var text = $("#recipeInput").val().toLowerCase();
     $("#recipeItemChooser li").each(function(ndx, optn)
     {
//       if ( $(this).is(":visible") )
//       { // Showing
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
           });  // each word
         } // multiple words
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
         } // single word
//       }  // if visible

       if (hideIt)
          $(this).hide();
       else
          $(this).show();
     }); // each option
  }

$(document).ready( function() {
// ----------------------------

  $("#recipeItemChooser").on('click', 'li', function(e)
  // ------------------------------------
  {
     var data = $(this).attr('data-id').split("+");
     $("#recipeChoice").prop('value', data[0]);
     $("#owner").prop('value', data[1]);
     $("#recipeInput").val($(this).text());
     $("#recipeChooser").hide();

  });

  $("body").click(function(e)
  // ------------------------------------
  {
     if ( ! $(e.target).parent().hasClass('item-list') )
           $("#recipeChooser").hide();

  });

  $("#recipeInput").click(function()
  // ------------------------------------
  {
     $("#recipeChooser").removeClass("hidden");
     $("#recipeChooser").show();
  });

  $("#btnSearchRecipe").click(function()
  // ------------------------------------
  {
     $("#recipeChooser").toggle();
  });

});  // end on page loaded


</script>

