<?php
/******************** FOR TESTING *************************/
  require_once ('./jquery.php');
  require_once ('dbErr.php');
  require_once ('logError.php');
  require_once ( './dbConnect.php');
  $conn = dbConnect();

  getGroceryItems($conn, "Search Food");
/******************** FOR TESTING *************************/

function getGroceryItems($conn, $btnCap)
//----------------------------
{
  $sql = "SELECT GroceryNameID, GroceryName FROM GROCERIES ";
  $sql .= "ORDER BY GroceryName";
  selectItem($conn, $sql, $btnCap);
}


function selectItem($conn, $sql, $btnCap="Select")
//----------------------------------------
{
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
    echo '<div id="chooseItem">';
     echo '<input type="text" placeholder="Search.." id="myInput" class="dropbtn" onkeyup="myFilter(event)">';
      if ($btnCap != "")
         echo '<input id="btnSelectItem" class="myButton" name="btnSelectItem" type="button" value="' . $btnCap . '">';
      echo '<div id="chooser"</div>';
        echo '<select name="itemChooser" id="itemChooser">';

          // Fetch one at a time from result
          while (mysqli_stmt_fetch($stmt))
          {
            echo "<option value=$ID>$item</option>";
          }
          mysqli_stmt_close($stmt);
        echo '</select>';
      echo '</div>';   // end of chooser
    echo "</div>";     // end of chooseItem
   }
   else error_log("mysql ERROR: " . mysqli_error($conn), 3, "/tmp/myErrors.log");
}

?>

<script>

  function myFilter(event)
  // ------------------------
  {  // check for space bar and backspace
     var x = event.which || event.keyCode;
     if (x == 0x20) 
         return false; // space, nothing to do;

     if (x==8)
        $("#itemChooser").children('option').show();

     var hideIt = false;
     var text = $("#myInput").val().toLowerCase();
     $("#itemChooser option").each(function(ndx, optn)
     {
       if ($(this).css('display') != 'none')  // ignore if hidden, already ruled out
       {
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
       }  // if showing

       if (hideIt) 
           $("#itemChooser option").eq(ndx).hide();
     }); // each option
  }



$(document).ready( function() {
// ----------------------------
  function itemSelected()
  // ------------------------------------
  {
     var opt = $("#recipeChooser").val().split("+");
     var recipeID = opt[0];
     var owner = opt[1];
     var user = $("#userID").val();
     if (owner == user) $("#btnEdit").prop('disabled', false);
     else $("#btnEdit").prop('disabled', true);
  };

});  // end on page loaded

</script>

