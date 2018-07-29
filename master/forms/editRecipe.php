<?php
  if (session_status() == PHP_SESSION_NONE) 
  {
    session_start();
    if(!isset($_POST['userName']))
    {
      require_once ('../includes/getUser.php');
    }
  }
//  require_once ('../includes/logError.php');
  global $canBeSubRecipe;

  // placed first for these these plugins
  require_once ( '../includes/jquery.php' );
  require_once ('../includes/analyticstracking.php');

  $postIt = "../plugins/impromptu/jQuery-Impromptu-master/dist/jquery-impromptu.js";
  $postItCss = "../plugins/impromptu/jQuery-Impromptu-master/dist/jquery-impromptu.css";
  echo '<script type="text/javascript" src="'.$postIt.'"></script>';
  echo '<link rel="stylesheet" media="all" type="text/css" href="'.$postItCss.'">';

  // this comes 1st, it has general style sheet reference in it
  require_once ('../includes/banner.php');

  // style sheet specific to this page 
  echo '<link rel="stylesheet" media="all" type="text/css" href="../css/editRecipe.css">';

  require_once ('../includes/dbConnect.php');
  require_once ('../includes/fractions.php');
  require_once ('getRecipeInfo.php');
  require_once ('../includes/displayButtons.php');


  $conn = dbConnect();

  $thisScript = $_SERVER['PHP_SELF'];

  if (isset($_GET["chosenRecipe"]))
  {
      $chosenRecipe=$_GET["chosenRecipe"];
  }

  elseif (isset($_POST["chosenRecipe"]))
  {
      $chosenRecipe=$_POST["chosenRecipe"];
  }

  else
  {
      echo "No Recipe Chosen<br>";
      // now what????
  }


  $sql  = "SELECT RecipeName, servingSize, isSubRecipe, isPublic, Comments ";
  $sql .= "FROM RecipeMaster ";
  $sql .= "WHERE RecipeMaster.ID = ?";

  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  { 
    if ( mysqli_stmt_bind_param($stmt, 'i', $chosenRecipe))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (mysqli_stmt_bind_result( $stmt
                                   , $recipeName
                                   , $servSize
                                   , $isSubRecipe
                                   , $isPublic
                                   , $comments
                                   ) )
        mysqli_stmt_store_result($stmt);
        $sts = true;
      } // execute
    } // bind input
  } // prepare

  if (!$sts)
  {
    sqlErr(__FILE__, "line " . __LINE__, $conn);
//    error_log("mysql ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
   exit();
  } 

  mysqli_stmt_fetch($stmt);
  showBannerMsg("Edit " . $recipeName);


  // Display Button
  echo '<form action="../includes/chooseRecipe.php">';
    $btns = array( MENU => "Main Menu"
                 , CHOOSE =>"Choose New Recipe"
                 , SHOW =>"Show Recipe"
                 , ADD => "Add New Item"
                 , SAVE => "Save Recipe"
                 , CANCEL => "Cancel"
                 , CHANGE => "Edit Name"
                 );

    displayButtons($btns);
  echo '</form>';
//  echo '</div>'; // end of container (started in banner.php) 

  echo '<div id="recipeHolder"i class="container">';
  echo '<form name="frmEdit" action="'.$thisScript.'" method="get">';
  echo '<input id=dirty type"number" class="hidden" value="0">';
  echo '<input type="hidden" name="choice" id="choice" value="' . $chosenRecipe .'" />';

  echo '<div class = "myHead">';
      echo '<label for "serving" id="specialLabel">Serving Size(g)</label>';
      $fld_serving = '<input type="number" id="serving"';
      $fld_serving .= 'value="'.number_format($servSize, 1, '.', '').'" ';
      $fld_serving .= 'onchange="setDirty()"';
      $fld_serving .= ' min="1" max="999" size=5 length=5 step=".1"/>';
      echo $fld_serving;

      echo '<div id="sub">';
      echo '<label for "subMenu"> Can this recipe be used in another recipe?</label>';
      if ($isSubRecipe)
      {
         echo '<input type="radio" name="subMenu" id="subMenuYes" value="Yes" checked>Yes';
         echo '<input type="radio" name="subMenu" id="subMenuNo" value="No">No';
      }
      else
      {
         echo '<input type="radio" name="subMenu" id="subMenuYes" value="Yes">Yes';
         echo '<input type="radio" name="subMenu" id="subMenuNo" value="No" checked>No';
      }
      echo '</div>';     // sub

      echo '<div id="pub">';
         echo '<label for "pubMenu"> Can this recipe be seen by anyone?</label>';
         if ($isPublic)
         {
            echo '<input type="radio" name="pubMenu" id="pubMenuYes" value="Yes" checked>Yes';
            echo '<input type="radio" name="pubMenu" id="pubMenuNo" value="No">No';
         }
         else
         {
            echo '<input type="radio" name="pubMenu" id="pubMenuYes" value="Yes">Yes';
            echo '<input type="radio" name="pubMenu" id="pubMenuNo" value="No" checked>No';
         }
      echo '</div>';     // pub
  echo '</div>';     // myHead

  $canBeSubRecipe = displayRecipe($conn, $chosenRecipe, $comments);
 echo '<div id="comments">';  

  echo '<input type="number" id="canBeSub" class="hidden" value="'.$canBeSubRecipe.'"> </input>';
  echo '<input type="number" id="pubRecipe" class="hidden" value="'.$isPublic.'"> </input>';
  echo '<div class="divCaption" id="editCaption">Comments</div>';
  echo '<textarea id="addComment" oninput=setDirty() placeholder="Type your comments here" maxlength="500" rows="6" cols="145">';
     echo $comments;
  echo '</textarea>';
 echo '</div>';  // comments div

  echo '<div class="hidden" id="namePopUp">';
    echo '<input type="text" id="newName" value="'.$recipeName.'"> </input>';
    echo '<input name="btnEnter" id="btnEnter" class="myButton" type="button" value="Enter" >';
    echo '<input name="btnCncl" id="btnCncl" class="myButton" type="button" value="Cancel" >';
  echo '</div>';

  echo '</form>';  // edit form
  echo '</div>'; // recipeHolder

  // <!-- Placed at the end of the document so the pages load faster -->
  // other add ons
  $buttons= "../scripts/buttons.js";
  $fractionTable= "../scripts/fractions.js";
  echo '<script src="'.$buttons.'"></script>';
  echo '<script src="'.$fractionTable.'"></script>';
?>


  <script>
  var pageDirty = false;
  var nameChanged = false;
  var canBeSubRecipe;

  // Array of Weight measures
  var weights = ['Ounce', 'Pound', 'Gram', 'Each', 'CommentLine'];

  // Array of Volumes
  var measures = [ "Teaspoon", "Tablespoon", "cup", "Each", "Quart", "Pint","Ounce", "Pound", "Gallon", "Can","Gram", "CommentLine", "Liters"];
  var measureIDs =[1,2,3,4,7,8,9,13,14,20,23,24];

  function setDirty()
  //-------------------
  {
    pageDirty = true;
    $("#btnSave").prop('disabled', false);
    $("#btnCancel").prop('disabled', false);
  }

  function updateUOMselect(obj, gramsPerCup)
  //---------------------------------------
  {
    var objLen = $('option', obj).size();
    var wtLen = weights.length;

    setDirty();

    canBeSub = 1;
    switch (gramsPerCup)
    {
//      case 0: // comment line
//        break;

      case 1: // remove volumes from select, cannot be subRecipe
           canBeSub = 0;

           $('option', obj).each(function()
           {
             if ( weights.indexOf($(this).text()) == -1)
                   $(this).remove();
           });
           break;
    
      case 0: // comment line
      default: // add volumes to select
      if (objLen == wtLen)
      {
         $('option', obj).remove();  // remove weights, put back in order
         $.each(measureIDs, function(idx, val)
         {
            var id = measureIDs[idx];
            var name = measures[idx];
            var fld =  '<option value=' + id + '>' + name + '</option>';
            $("select", obj).append(fld)
         }); 
      }
      break;
    }
    if (canBeSub==0)
    {
         $('#subMenuNo').prop('checked', true);
         $('input[name=subMenu]').attr('disabled', true)
    }
    else
    {
         $('#subMenuYes').prop('checked', true);
         $('input[name=subMenu]').removeAttr("disabled");
    }
  }

  function updateThis(obj)
  // --------------------
  { // User changed grocery item, update UOM select
    var opt = $(":selected",obj).val().split("|");
    var gramsPerCup = parseInt(opt[1]);

    // get unit of measure select field & update to match new item
    var uom = $(obj).closest('tr').find('td').eq(2);

    updateUOMselect(uom, gramsPerCup);
  }


$(document).ready( function() {
// ----------------------------
  var title = 'WARNING';
  var txt = 'Are you sure you want to leave this page?<br><br>';
  var txt = txt + 'Your changes will be lost?';
  var myButtons = { "Yes, Your Changes will NOT be saved!": true, "No, Stay On Page": false };
  var canEdit=1;

  
  canBeSub = $("#canBeSub").val();
  if (canBeSub == 0) $('input[name=subMenu]').attr("disabled",true);

  function jConfirm(text, title, btns, callback) 
  // ------------------------------------
  {
    $.prompt(text, {
        title: title,
        html: text,
        buttons: btns,
        focus: 1,
        submit:function(e,v,m,f)
        {
           callback(v);
        }
    });
  };

  function btnMenuCallBack(v)
  // ------------------------------------
  {
     if (v) // leave page without saving
     {
        var url = "../menu.php";
        document.location.href = url;
     }
  }

  $("input[type='number'").on('click', function(e) 
  // ------------------------------------
  {
    if (e.which == 1) setDirty();

  });

  $("input[type='number'").on('keydown', function(e) 
  // ------------------------------------
  {
     var badKeys = [1, 2, 3, 37, 38, 39, 40, 109, 110, 189, 190];

     if (badKeys.indexOf(e.which) > -1)
           return false;

     setDirty();
//     if (e.which==190 || e.which==189)
//        return false;   // don't accept '.' or '-'
  });

 
  $("#btnMenu").click(function(event)
  // ------------------------------------
  {
    if (pageDirty)
    {
      jConfirm(txt,title, myButtons, btnMenuCallBack);
    }
    else
    {
      var url = "../menu.php";
      document.location.href = url;
    }
  });

  function btnChooseCallBack(v)
  // ------------------------------------
  {
     if (v) // leave page without saving
     {
        var url =  "../includes/recipes.php";
        document.location.href = url;
     }
  }

  $("#btnChoose").click(function(event)
  // ------------------------------------
  {
    if (pageDirty)
    {
      jConfirm(txt,title, myButtons, btnChooseCallBack);
    }
    else
    {
      var url =  "../includes/recipes.php";
      document.location.href = url;
    }
  });

  function btnShowCallBack(v)
  // ------------------------------------
  {
     if (v) // leave page without saving
     {
        var chosenRecipe=$("#choice").val();
        var url = "../includes/showRecipe.php?cmd="+ SHOW +"&chosenRecipe="+chosenRecipe+"&canEdit="+canEdit;;
        document.location.href = url;
     }
  }

  $("#btnShow").click(function(event)
  // ------------------------------------
  {
    if (pageDirty)
    {
      jConfirm(txt,title, myButtons, btnShowCallBack);
    }
    else
    {
      var chosenRecipe=$("#choice").val();
      var url = "../includes/showRecipe.php?cmd="+ SHOW +"&chosenRecipe="+chosenRecipe+"&canEdit="+canEdit;;
      document.location.href = url;
    }
  });

  function btnCancelCallBack(v)
  // ------------------------------------
  {
     if (v) // leave page without saving
     {
        event.preventDefault();
        location.reload();
     }
  }

  $("#btnCancel").click(function(event)
  // ------------------------------------
  {
    if (pageDirty)
    {
      jConfirm(txt,title, myButtons, btnCancelCallBack);
    }
  });
 

  $( "#btnAdd" ).click(function( event ) 
  // ------------------------------------
  {
    addRow();
  });
 

  $( "#btnSave" ).click(function( event ) 
  // ------------------------------------
  {
    saveRecipe();
  });
 
  $( "#btnHide" ).click(function( event ) 
  // ------------------------------------
  {
/******
     $('#tblRecipe .idCol').each(function(row, id)
     {
        if ($(id).hasClass("hidden"))
             $(id).removeClass("hidden");
        else
             $(id).addClass("hidden");
     });
******/

     $('#tblRecipe .seqCol').each(function(row, seq)
     {
        if ($(seq).hasClass("hidden"))
             $(seq).removeClass("hidden");
        else
             $(seq).addClass("hidden");
     });
  });

  $( "#btnChangeName" ).click(function( event ) 
  // ------------------------------------
  {
       $("#btnChangeName").prop('disabled', true);
       $("#namePopUp").removeClass("hidden");
       $("#newName").focus();
  });

  $("#btnEnter").click(function(event)
  // ------------------------------------
  {
     var txt = $("#newName").val();
     if (txt.length > 0)
     {
        txt = "Edit Recipe: " + txt;
        $("#pageTitle").text(txt);
        nameChanged = true;
        setDirty();
     }
     $("#btnChangeName").prop('disabled', false);
     $("#namePopUp").addClass("hidden");
  });

  $("#btnCncl").click(function(event)
  // ------------------------------------
  {
     $("#namePopUp").addClass("hidden");
  });

  $("#subMenuYes").click(function(event)
  // ------------------------------------
  {
      setDirty();
  });  //subMenu

  $("#subMenuNo").click(function(event)
  // ------------------------------------
  {
      setDirty();
  });  //subMenu

  $("#pubMenuYes").click(function(event)
  // ------------------------------------
  {
      setDirty();
  });  //pubMenu

  $("#pubMenuNo").click(function(event)
  // ------------------------------------
  {
      setDirty();
  });  //pubMenu


  $("#addComment").change(function(event)
  // ------------------------------------
  {
     setDirty();
  });  

  function addRow()
  // -------------------
  {
     var nextSeq = parseInt( $('#tblRecipe tr:last').find("#seq").val() ) +5;
     var recipeID = $('#tblRecipe').find("#recipeNum").text()


     // Prepare a new screen row 
     var newtr = $("#tblRecipe tr:last").clone();

     updateUOMselect( newtr.find("td").eq(2), 0);

     newtr.find("#numChooser").val("1");
     newtr.find("#fractionChooser").val("0");
     newtr.find("#UOM").val("24");
     newtr.find("#UOM").prop("disabled", false);

     newtr.find("#grocItem").val("310|1.0000");
     newtr.find("#Directions").val("Type Directions Here");
     newtr.find("#seq").val(nextSeq);
     setDirty();

     var arrayData=[];
     arrayData = 
     {
          "recipeMasterID" : recipeID
        , "nextSeq" : nextSeq
     }
     var myData = JSON.stringify(arrayData);
//     console.log(myData);
//a=1;
     $.ajax(
      {
        url: "./addRow.php",
        type: "post",   
        data: {"data" : myData},
        success: function( data, status)  // callback 
             { 
               if (status=="success") 
               {
                 if ( (data.toLowerCase().indexOf("warning") >= 0) || (data.toLowerCase().indexOf("error") >= 0) )
                 {
                   alert("Error Occurred, Unable to save data");
                 }
                 else
                 {
                   var newId = $.parseJSON(data);
                   if (newId)
                   {
                      newtr.find("#detailID").val(newId);
                      // Display the new row on screen
                      $("#tblRecipe tr:last").after(newtr);
                      $('#tblRecipe tr:last td:first input').focus();
                   }
                 }
               }
               else alert("Error Occurred, Unable to ADD a new row!");
             },
        error: function(xhr)
              {
                alert( "An error occured: " + xhr.status + " " + xhr.statusText);
              }
      });

  }

  function saveRecipe()
  // -------------------
  {
     if (pageDirty)
     {
        var tblData="";
        var arrayRow=0;
        var recipe=[];
        var arrayData=[];
        var isSubMenu=0;
        var isPublic=0;

        if (document.getElementById('subMenuYes').checked)
            isSubMenu=1;
 
        if (document.getElementById('pubMenuYes').checked)
            isPublic=1;

        var name=$('#newName').val();
           arrayData[0] ={ "RecipeName" : name
                         , "ID" : $("#choice").val()
                         , "servingSize" : $("#serving").val()
                         , "isSubRecipe" : isSubMenu
                         , "isPublic" : isPublic
                         , "Comments" : $("#addComment").val()
                         };
        $('#rowData tr').each(function(row, tr)
        {
            var idx = row+1;
            var qtyWhole = parseInt($(tr).find("#Qty").val(), 10);

            var strFrac = $(tr).find("#fractionChooser option:selected").text();
            var fracIndex = fractions.indexOf(strFrac);
//            var qty = nums[numIndex] + decimals[fracIndex];
            var qty = qtyWhole + decimals[fracIndex];
            var itemInfo= $(tr).find("select#grocItem").val().split("|");

            arrayData[idx] = 
            { 
                "ID" : $(tr).find("#detailID").val()
              , "Sequence" : $(tr).find("#seq").val()
              , "Qty" : qty
              , "UOM_ID" : $(tr).find("select#UOM").val()
//              , "Item" :  $(tr).find("select#grocItem").val()
              , "Item" : itemInfo[0] 
              , "Instruction" : $(tr).find("#Directions").val()
            }
        }) // each row
        
        var myData = JSON.stringify(arrayData);
//        console.log(myData);
//a=1;
        $.ajax(
         {
           url: "saveRecipe.php",
           type: "post",   
           data: {"data" : myData},
           success: function( data, status)  // callback 
              { 
                if (status=="success") 
                {
                  if ( (data.indexOf("false") >= 0) )
                  {
                     alert("Error Occurred, Unable to save data");
                  }
                  else
                  {
                     alert("Your changes were Successfully Completed");
                     pageDirty = false;
                     $("#btnSave").prop('disabled', true);
                     $("#btnCancel").prop('disabled', true);
                  }
                }
                else alert("Error Occurred, Unable to save data");
              },
           error: function(xhr)
              {
                alert( "An error occured: " + xhr.status + " " + xhr.statusText);
              }
         });
     } // pageDirty
  } // saveRecipe

});  // end on page loaded

  </script>

</body>
</html>

