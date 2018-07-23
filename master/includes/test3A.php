<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
body {
    font-family: Arial;
}

* {
    box-sizing: border-box;
}

  #myInput
  {
//    background-color:  yellow;
//    margin-top: -20px; 
    width:24.5%;
    padding: 10px;
    font-size: 17px;
    border: 1px solid grey;
    float: left;
    background: #f1f1f1;
  }

  #myUL
  {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }

  #btnSelectItem
  {
    float: left;
    width: 5%;
    padding: 10px;
    background: #2196F3;
    color: white;
    font-size: 20px;
    border: 1px solid grey;
    border-left: none;
    cursor: pointer;
  }

  #btnSelectItem:hover
  {
    background: #0b7dda;
  }

  #chooser
  {
    background-color: pink;
  }

  #chooseItem
  {
    background-color: LightBlue;
  }

  #itemChooser
  {
    background-color: linen; 
    width: 30%;
    margin-top: -20px; 
  }
</style>
</head>

<body>

<?php
/******************** FOR TESTING *************************/
  require_once ('./jquery.php');
  require_once ('dbErr.php');
  require_once ('logError.php');
  require_once ( './dbConnect.php');
  $conn = dbConnect();

   $itemNo = 0;
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
    echo '<div id="chooseItem">';
      echo '<input type="text" placeholder="Search.." id="myInput" onkeyup="myFilter(event)">';
      echo '<button id="btnSelectItem" name="btnSelectItem" type="button" 
                    <i class="fa fa-search"></i></button>';
/**********
      echo '<div id="chooser">';
        echo '<ul id="itemChooser">'; 
          // Fetch one at a time from result
          while (mysqli_stmt_fetch($stmt))
          { 
            echo "<li data_id=" . '"' . $ID . ">$item</li>";
          }
          mysqli_stmt_close($stmt);
        echo '</select>';
      echo '</div>';   // end of chooser
/**********/
    echo "</div>";     // end of chooseItem
   }
   else error_log("mysql ERROR: " . mysqli_error($conn), 3, "/tmp/myErrors.log");
}

?>
</body>

<script>

$(document).ready( function() {
// ----------------------------
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

     $("#itemChooser li").each(function(ndx, optn)
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
//           $("#itemChooser option").eq(ndx).hide();
           $("#itemChooser li").eq(ndx).hide();
     }); // each option
  }


/*****
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

  

  $("#itemChooser").change(function()
  // ------------------------------------
  {
    var myItem = $("#itemChooser").val();
    var myItemTxt =  $("#itemChooser option:selected").text();
    $("#choice").prop('value', myItem);
    alert("myItem= " + myItem + " " + myItemTxt);
  });
/*****/

});  // end on page loaded

</script>

