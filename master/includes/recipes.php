<!doctype html>
<?php
  session_start();
  $self = $_SERVER['PHP_SELF'];

  if(!isset($_SESSION['userID']))
  {
    header("Location: " . "../../index.php");
    exit();
  }  
  require_once ('dbConnect.php');
  require_once ('banner.php');
  require_once ('displayButtons.php');
  require_once ('chooseRecipeItem.php');
  require_once ('logError.php');

  
//logError("Recipes SESSION userID " . $_SESSION["userID"]);
//logError("Recipes SESSION userName " . $_SESSION["userName"]);
//logError("Recipes SESSION level " . $_SESSION["MEMBER_LEVEL"]);
//logError("Recipes path= " . getcwd());
  

  showBanner("Choose A Recipe");
  $conn = dbConnect();

  if (isset($_GET["cmd"])) $cmd = $_GET["cmd"];
  else $cmd = CHOOSE;

  echo '<form id="frmShowRecipes" action="'.$self.'" method="get" >';
  echo '<input type="hidden" name="userID" id="userID" value="' . $_SESSION["userID"] .'" />';
  echo '<input type="hidden" name="userName" id="userName" value="' . $_SESSION["userName"] .'" />';

  echo '<div id="addItemBox" class="hidden">';
   echo '<label for "itemName">New Name</label>';
   echo '<input type="text" id="itemName" required>';
   echo '<input name="btnEnter" id="btnEnter" class="myButton" type="button" value="Enter" >';
   echo '<input name="btnCnclBox" class="myButton" id="btnCnclBox" type="button" value="Cancel">';
  echo '</div>';  // addItemBox

  switch ($cmd)
  {
      case CHOOSE:
        chooseRecipe($conn);
      break;

  }
  echo "</form>";

function chooseRecipe($conn)
//-----------------------
{
  $btns = array( MENU => "Main Menu"
               , SHOW =>"Show Recipe"
               , EDIT => "Edit Recipe"
               , ADD => "Add New Recipe"
               );

  displayButtons($btns);
  echo '<input type="hidden" name="choice" id="choice" />';
  echo '<input type="hidden" name="owner" id="owner" />';
  selectRecipe($conn, "");  // recipe selector
} // choose Recipe

  $buttons= "../scripts/buttons.js";
  echo '<script src="'.$buttons.'"></script>';
  require_once ( 'jquery.php' );
  require_once ('analyticstracking.php');
?>

 <script>
 function checkIfCanEdit()
 // ----------------------
 {
//    var opt = $("#recipeChooser").val().split("+");
//    var chosenRecipe = opt[0];
//    var owner = opt[1];
    var chosenRecipe = $("#choice").val();   
    var owner = $("#owner").val();   
    var user = $("#userID").val();

//alert("owner: " + owner + ", user: " + user);
    if (owner == user)
    { 
       canEdit=1;
       $("#btnEdit").prop('disabled',false);
    }
    else
    {
      canEdit=0;
      $("#btnEdit").prop('disabled', true);
    }
    return canEdit;
 }

$(document).ready( function() {
// ----------------------------
  checkIfCanEdit();

  $("#btnMenu").click(function(event)
  // ------------------------------------
  {
     var url =  "../starthere.php";
     document.location.href = url;
  });

  $("#btnShow").click(function(event)
  // ------------------------------------
  {
//    var opt = $("#recipeChooser").val().split("+");
//    var chosenRecipe = opt[0];
    canEdit = checkIfCanEdit();
	var chosenRecipe = $("#choice").val();
//    $("#choice").prop('value', chosenRecipe);
//    $("#btnCmd").prop('value', SHOW);

    var url =  "showRecipe.php?cmd="+ SHOW +"&chosenRecipe="+chosenRecipe+"&canEdit="+canEdit;

     document.location.href = url;
  });

  $("#btnEdit").click(function(event)
  // ------------------------------------
  {
    var userName = $("#userName").val();
    var userID = $("#userID").val();
	var chosenRecipe = $("#choice").val();

    $("#btnCmd").prop('value', EDIT);
    var url =  "../forms/editRecipe.php?cmd="+ EDIT +"&chosenRecipe="+chosenRecipe;
    document.location.href = url;
/*****************
    var cmd =  "../forms/editRecipe.php?cmd="+ EDIT +"&chosenRecipe="+chosenRecipe;
    var url =  "../forms/editRecipe.php?cmd="+ EDIT +"&chosenRecipe="+chosenRecipe;
    $.post("../forms/editRecipe.php"
          , { userID : userID
            , userName : userName 
            , cmd : "+ EDIT +"
            , chosenRecipe : chosenRecipe
            }
         , function(data, status, xhr) 
         {
           alert( "success" );
         })
//         jqXHR.done(function() { alert( "second success" ); })
//         jqXHR.fail(function() { alert( "error" ); })
//         jqXHR.always(function() { alert( "finished" ); });
// Perform other work here ...
//    window.location.href = url;
/*****************/
  });

  $("#btnAdd").click(function(event)
  // ------------------------------------
  {
     $("#btnLine").addClass("hidden");
     $("#chooseRecipe").addClass("hidden");
     $("#pageTitle").text("Add Recipe");
     $("#addItemBox").removeClass("hidden");
     $("#itemName").focus();

  });

  function editItem(myItem)
  // ------------------------------------
  {
//    var chosenRecipe = $("#recipeChooser").val();
    $("#choice").prop('value', myItem);
    $("#btnCmd").prop('value', EDIT);
//    var url =  $("#subDir").val() + "forms/editRecipe.php?cmd="+ EDIT +"&chosenRecipe="+myItem;
    var url =  "../forms/editRecipe.php?cmd="+ EDIT +"&chosenRecipe="+myItem;
    document.location.href = url;
  }

  function restoreButtons()
  // ------------------------------------
  {
     $("#addItemBox").addClass("hidden");
     $("#btnLine").removeClass("hidden");
     $("#chooseRecipe").removeClass("hidden");
     $("#pageTitle").text("Choose A Recipe");
  }

  $("#btnCnclBox").click(function(event)
  // ------------------------------------
  {
      restoreButtons();
       $("#itemName").val("");
  });

  $("#btnEnter").click(function(event)
  // ------------------------------------
  {
     if ( ($.trim( $("#itemName").val())).length > 0)
     {
       addItem();
       restoreButtons();
       $("#itemName").val("");
     }
     else
        alert("Please enter an Item Name!");
  });

  $("#btnCancel").click(function(event)
  // ------------------------------------
  {
    pageDirty = false;
    $("#btnCmd").prop('value', CHOOSE);
    $("#frmShowRecipes").submit();
  });

  function updateChooser(id, newItem)
  // ------------------------------------
  {
     // update "chooser" select
     $("#recipeChooser option").each(function(ndx, option)
     {
        if ( option.text.toUpperCase() >= newItem.toUpperCase())
        { // insert new item here
          newOption = $('<option value="' + id + '">' + newItem + '</option>');
          $("#recipeChooser option").eq(ndx).before(newOption);
          return false;
        }
    });
  }

  function addItem()
  // ----------------
  {
     var itemName = $("#itemName").val();
     var owner = $("#userID").val();
     var arrayData = { "owner" : owner,  "RecipeName" : itemName };
     var itemData = JSON.stringify(arrayData).replace(/'/g, "\\'")
     var itemId;


     // This approach is nasty & time consuming, but I
     // couldn't get the more elegant approaches to compile correctly
     // var exists = $("#recipeChooser option[value='" +itemName+"']").length
     // var exists = $("#recipeChooser").find('option[value="'+itemName +'"]').length > 0
     var found = false;
     $("#recipeChooser option").each(function()
     {
       if ($(this).text().toUpperCase()==itemName.toUpperCase())
       {
           alert(itemName +  " Already Exists.  Please Try Another");
           found = true;
           $("#itemName").val("");
           return false;
       }

     });  // for each
     if (found)
        return false;
//console.log(itemData);

     $.ajax(
     {
       url: "./addRecipe.php",
       type: "post",
       data: {"data" : itemData},
       success: function( data, status)  // callback
                {
                   if (status=="success")
                   {
                     var itemID = $.parseJSON(data);
                     if (itemID)
                     {
                       updateChooser(itemID, itemName);
                       editItem(itemID);
                       alert("Your Item was Successfully Added");
                     }
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

});  // doc ready


</script>

  </div> <!-- end of container (started in banner.php) -->
 </body>
</html>
