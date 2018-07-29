<?php
  session_start();
  $self = $_SERVER['PHP_SELF'];

  if(!isset($_SESSION['userID']))
  {
    header("Location: " . "../../index.php");
    exit();
  }  

  require_once ('banner.php');
  echo '<link href="../css/foodStyle.css" rel="stylesheet"> ';

  require_once ('dbConnect.php');
  require_once ('displayButtons.php');
  require_once ('chooseRecipeItem.php');
  require_once ('logError.php');

  showBannerMsg("Choose A Recipe");
  
  $conn = dbConnect();

  if (isset($_GET["cmd"])) $cmd = $_GET["cmd"];
  else $cmd = CHOOSE;

  echo '<form id="frmShowRecipes" action="'.$self.'" method="get" >';
  echo '<input type="hidden" name="userID" id="userID" value="' . $_SESSION["userID"] .'" />';
  echo '<input type="hidden" name="userName" id="userName" value="' . $_SESSION["userName"] .'" />';

  echo '<div id="addItemBox" class="hidden">';
   echo '<label for "itemName">New Name</label>';
   echo '<input type="text" id="itemName" tabindex="1" required>';
   echo '<input name="btnEnter" id="btnEnter" class="myButton" 
                type="button" value="Enter" tabindex="2" >';
   echo '<input name="btnCnclBox" class="myButton" id="btnCnclBox" 
                type="button" value="Cancel" tabindex=3>';
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
//  echo '<input type="hidden" name="choice" id="choice" />';
//  echo '<input type="hidden" name="owner" id="owner" />';
    selectRecipe($conn, "");  // recipe selector
} // choose Recipe

  // At bottom to load last
  $buttons= "../scripts/buttons.js";
  echo '<script src="'.$buttons.'"></script>';
  require_once ( 'jquery.php' );
  require_once ('analyticstracking.php');
?>

 <script>
 function checkIfCanEdit()
 // ----------------------
 {
    var chosenRecipe = $("#recipeChoice").val();   
    var owner = $("#owner").val();   
    var user = $("#userID").val();

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
    canEdit = checkIfCanEdit();
	var chosenRecipe = $("#recipeChoice").val();
    var url =  "showRecipe.php?cmd="+ SHOW +"&chosenRecipe="+chosenRecipe+"&canEdit="+canEdit;
     document.location.href = url;
  });

  $("#btnEdit").click(function(event)
  // ------------------------------------
  {
    var userName = $("#userName").val();
    var userID = $("#userID").val();
	var chosenRecipe = $("#recipeChoice").val();

    $("#btnCmd").prop('value', EDIT);
    window.location.href = "../forms/editRecipe.php?cmd="+ EDIT +"&chosenRecipe="+chosenRecipe;
    exit();
  });

  $("#btnAdd").click(function(event)
  // ------------------------------------
  {
     $("#btnLine").addClass("hidden");
//     $("#chooseRecipe").addClass("hidden");
     $("#chooseHolder").addClass("hidden");
     $("#pageTitle").text("Add Recipe");
     $("#addItemBox").removeClass("hidden");
     $("#itemName").focus();

  });

  function editItem(myItem)
  // ------------------------------------
  {
//    var chosenRecipe = $("#recipeChooser").val();
    $("#recipeChoice").prop('value', myItem);
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
//     $("#chooseRecipe").removeClass("hidden");
     $("#chooseHolder").removeClass("hidden");
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

  function updateChooser(owner, id, newItem)
  // ------------------------------------
  {
     // update "chooser" select
     $("#itemChooser li").each(function(ndx)
     {
        if ( $(this).text().toUpperCase() >= newItem.toUpperCase())
        { // insert new item here
          newOption = $('<li data-id=' + owner + '+' + id + '>' + newItem + '</li>');
          $('#itemChooser li:eq(ndx)').after(newOption);
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

     // This approach is nasty & time consuming, but I
     // couldn't get the more elegant approaches to compile correctly
     // var exists = $("#recipeChooser option[value='" +itemName+"']").length
     // var exists = $("#recipeChooser").find('option[value="'+itemName +'"]').length > 0
     var found = false;
     $("#itemChooser li").each(function()
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
                       updateChooser(owner, itemID, itemName);
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
