<?php

require_once ( 'jquery.php' );

function getGroceryItems($conn, $btnCap)
//----------------------------
{
  echo '<input type="hidden" name="itemChoice" id="itemChoice" />';

  $sql = "SELECT GroceryNameID, GroceryName FROM GROCERIES ";
  $sql .= "ORDER BY GroceryName";
  selectItem($conn, $sql, $btnCap);
}

function selectItem($conn, $sql, $btnCap="Select")
//----------------------------------------
{
//  $sql = "SELECT GroceryNameID, GroceryName FROM GROCERIES ";
//  $sql .= "ORDER BY GroceryName";
  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  {
    if (mysqli_stmt_execute($stmt) )
    {
        if (mysqli_stmt_bind_result( $stmt
                                   , $ID
                                   , $item
                                   ) )
        mysqli_stmt_store_result($stmt);
        $sts = true;
    } // execute
  } // prepare

  if ($sts)
  {
    // create drop down box
    echo '<div class="wrapper" id="foodWrapper">';
     echo '<div id="chooseFoodHolder" class="item-list">';
       echo '<input type="input" placeholder="Search for Food..." id="foodInput"
                   class="bigInput"
                   onkeyup="foodFilter(event)" autocomplete="off">';

       echo '<button type="submit" id="btnSearchFood" class="searchButton">
                   <i class="fa fa-search"></i> </button>';

       if ($btnCap != "")
       {
           echo '<input id="btnSelectFood" name="btnSelectFood" 
                        class="selectButton"
                        type="button" value="' . $btnCap . '">';
       }
       echo '<div id="foodChooser" class="chooser hidden">';
         echo '<ul id="foodItemChooser">';
          // Fetch one at a time from result
          while (mysqli_stmt_fetch($stmt))
          {
            echo '<li data-id=' . $ID . '>' . $item .  '</li>';
          }
          mysqli_stmt_close($stmt);
         echo '</ul>';
       echo '</div>';   // end of chooser
     echo "</div>"; // end of chooseHolder
    echo "</div>";     // end of wrapper
   }
   else error_log("mysql ERROR: " . mysqli_error($conn), 3, "/tmp/myErrors.log");
}

?>

<script>
  var textHighlighted = false;

  function foodFilter(event)
  // ------------------------
  {  // check for space bar
     var x = event.which || event.keyCode;
     if (x == 0x20) // space
         return false; // nothing to do;
<<<<<<< HEAD

     if (x==09)  // tab key
     {
       $("#foodChooser").removeClass("hidden");
       $("#foodChooser").show();
       return;
     }

     if ( (textHighlighted==true) && (window.getSelection().toString()) == "")
     {
       $("#foodItemChooser li").show();
       textHighlighted = false;
     }

=======

     if (x==09)  // tab key
     {
       $("#foodChooser").removeClass("hidden");
       $("#foodChooser").show();
       return;
     }

     if ( (textHighlighted==true) && (window.getSelection().toString()) == "")
     {
       $("#foodItemChooser li").show();
       textHighlighted = false;
     }

>>>>>>> 33c30fb1a16e67a313ae9edac41fe5cbed5db827
     if ( $("#foodInput").val().length == 0 )
              $("#foodItemChooser li").show();

     var hideIt = false;
     var text = $("#foodInput").val().toLowerCase();
     $("#foodItemChooser li").each(function(ndx, optn)
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
           }); // each word
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
         }
//     }  // if visible

       if (hideIt)
          $(this).hide();
       else
          $(this).show();
     }); // each option
  }

$(document).ready( function() {
// ----------------------------

  $("#foodItemChooser").on('click', 'li', function(e)
  // ------------------------------------
  {
      $("#foodInput").val($(this).text());
      $("#itemChoice").prop('value', $(this).attr('data-id'));
      $("#foodChooser").hide();
  });

  $("#foodInput").select(function(e)
  // ------------------------------------
  {
     textHighlighted = true;
  });

  $("body").click(function(e)
  // ------------------------------------
  {
     if ( ! $(e.target).parent().hasClass('item-list'))
           $("#foodChooser").hide();

  });

  $("#foodInput").focus(
  // ----------------------
     function(){
        $(this).val('');
    });

  $("#foodInput").on('click', function(e)
  // ------------------------------------
  {
     $("#foodChooser").removeClass("hidden");
     $("#foodItemChooser li").each(function(ndx, optn)
     {
       $(this).show;
     });
     $("#foodChooser").show();
  });

  $("#btnSearchFood").click(function()
  // ------------------------------------
  {
     if ( $("#foodChooser").hasClass("hidden") )
     {
        $("#foodChooser").removeClass("hidden");
        $("#foodChooser").show();
     }
     else 
       $("#foodChooser").toggle();
  });

});  // end on page loaded


</script>

