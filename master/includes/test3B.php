<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link rel="stylesheet" href="../css/foodStyle.css">
</head>

<body>

<?php
/******************** FOR TESTING *************************/
  require_once ('./jquery.php');
  require_once ('dbErr.php');
  require_once ('logError.php');
  require_once ( './dbConnect.php');
  $conn = dbConnect();

   echo '<input type="hidden" name="choice" id="choice" value=' . $itemNo . ' />';


  getGroceryItems($conn, "Search Food");
/******************** END FOR TESTING *************************/

function getGroceryItems($conn, $btnCap)
//----------------------------
{
  $sql = "SELECT GroceryNameID, GroceryName FROM GROCERIES ";
  $sql .= "ORDER BY GroceryName";
  selectItem($conn, $sql, $btnCap);
}


function selectItem($conn, $sql, $btnCap="Choose")
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
</body>

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
alert( $(this).text() + '\n' + $(this).attr('data-id'));
//     e.stopPropagation();
//     $("#myInput").val(e.target.textContent);
      $("#myInput").val($(this).text());
      $("#choice").prop('value', $(this).attr('data-id'));
      $("#chooser").hide();
  });

  $("body").click(function(e)
  // ------------------------------------
  {
//     e.stopPropagation();
//     x= e.target.textContent;
     if ( ! $(e.target).parent().hasClass('item-list') )
           $("#chooser").hide();

     y=1;
  });

/************
  $("#itemChooser").change(function()
  // ------------------------------------
  {
    var myItem = $("#itemChooser").val();
    var myItemTxt =  $("#itemChooser option:selected").text();
    $("#choice").prop('value', myItem);
    alert("myItem= " + myItem + " " + myItemTxt);
  });
/************/

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

