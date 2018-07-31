<?php
  if ( !session_id() )
	session_start();

  require_once("./server.php");
  require_once("./defines.php");
  require_once ('jquery.php' );
  require_once ('logError.php');
  require_once ('getUser.php');

  $userID = $_SESSION["userID"];
  $self = $_SERVER['PHP_SELF'];

echo '<html>';
echo '<head>';
  echo ' <meta charset="utf-8">';
  echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
  echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
  echo '<title>Track Food</title>';

  echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" ></script>';

  // Bootstrap core CSS 
  echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >';

  // Font-Awsome
    echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';

  // Custom styles for this template -->';
  echo '<link href="../css/custom.css" rel="stylesheet"> ';
  echo '<link href="../css/style.css" rel="stylesheet"> ';
  echo '<link href="../css/track.css" rel="stylesheet"> ';
  echo '<link href="../css/datepicker.css" rel="stylesheet"> ';
  echo '<link href="../css/foodStyle.css" rel="stylesheet"> ';


  // jQuery-UI
  echo '<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>';

  require_once ( '../scripts/fb_pixel.js' );
echo '</head>';

echo '<body>';
  require_once ( './dbConnect.php');
  require_once ( './displayButtons.php');
  require_once ( './chooseRecipeItem.php');
  require_once ( './chooseFoodItem.php');
  require_once ( './getTrack.php');
  require_once ( './getNewTrack.php');
  require_once ( './cancelModal.php');

   $conn = dbConnect();
   if ($conn==NULL) 
   {
     echo '<h1>db connect error</h1>';
     exit(-1);
   }

  $nutrients= "../scripts/nutrients.js";
  $numbers = "../scripts/numbers.js";

     /*****************
     * Navigation Bar
     ******************/
      echo '<nav class="navbar navbar-inverse navbar-fixed-top"> ';
        echo '<div class="container">';
          echo '<div class="navbar-header">';
            echo '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>';

            echo '<a href="#" onClick=mainMenu() class="pull-left"><img src="../images/kdcLogo.png" alt="KDC" ></a>';
          echo '</div>';   // navbar header -->

        echo '<div id="navbar" class="collapse navbar-collapse">';
          echo '<ul class="nav navbar-nav">';

            if ( $_SESSION["MEMBER_LEVEL"] >2)
                  echo '<li><a href="#" onClick="kdcMenu();">KDC Main Menu</a></li>';
          
            echo '<li><a id="home" href="#" onClick=memberHome()>Member Area</a></li>';
            echo '<li><a id="btnLog" href="#" onClick="showLog();">Show Log</a></li>';
            echo '<li><a id="saveAll" href="#" onClick="saveItems();">Save All</a></li>';
            echo '<li><a id="cnclIt" href="#" onClick="cnclEdits();">Cancel</a></li>';
          echo '</ul>';
        echo '</div>'; // nav-collapse -->
      echo '</div>';  // container
    echo '</nav>';

/**************/
  echo '<div id=outerBox class="container">';
    echo '<form id="frmTrack" action="'.$self.'" method="get" >';
      echo '<input type="hidden" name="userID" id="userID" value="' . $userID .'" />';
    echo '</form>';  // frmTrack

    // Set up date, recipe and foood choosers
    echo '<div id="trackHeader">';
      echo '<div id="dateHolder">';
        $today = date('M d, Y');
        $startDate = date('Y-m-d');
        $msg = 'Food Log For '; 

        echo '<button type="button" class="datebtn calendar-btn"> <span class="fa fa-calendar"></span></button>';
        echo '<input id="btnChange" class="datebtn input-button" name="btnChange" type="button" value="' . $today . '"/`>';
        echo '<input id="sqlDate" class="hidden" type input name="sqlDate" value="' . $today . '">';
      echo '</div>';   // dateHolder;
    echo '<span class="fa fa-arrow-left"></span>Click here to change date';

      echo '<div id="chooserHolder">';  // AQUA
        selectRecipe($conn, "Add Recipe to List");
        getGroceryItems($conn, "Add Food To List");
      echo '</div>'; // chooserHolder   aqua
    echo '</div>';  // trackHeader   yellow
  echo '</container>'; // outerBox

  // build nutrition table
  echo '<div class="container">';
   echo '<div id = "nutrientContainer">';
    echo '<table id="log">';
      echo '<thead>';
        echo '<th colspan="2">Qty</th>';   // qty & uom description (ex. slice, cup)
        echo '<th  class="hidden">itemID</th>';
        echo '<th class="itemName">Item</th>';
        echo '<th class="stdCol">Weight</th>';
        echo '<th class="stdCol">Water</th>';
        echo '<th class="stdCol">Calories</th>';
        echo '<th class="stdCol">Protein</th>';
        echo '<th class="stdCol">Fat</th>';
        echo '<th class="stdCol">Carbs</th>';
        echo '<th class="stdCol">Fiber</th>';
        echo '<th class="stdCol">Sugars</th>';
        echo '<th class="stdCol">Phos.</th>';
        echo '<th class="stdCol">Pot.</th>';
        echo '<th class="stdCol">Sodium</th>';
        echo '<th class="hidden">gramsPerUnit</th>';
        echo '<th>Option</th>';
        echo '<th class="hidden" value=>trackID</th>';
      echo '</thead>';

      getTrackForPhp($conn, $userID, $startDate);
    echo '</table>';
   echo '</div>';  // nutrientContainer
 echo '</div>';  // container

/**************/
  // <!-- Placed at the end of the document so the pages load faster -->
//  require_once ( 'jquery.php' );

  // other add ons
  echo '<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/1.4.5/numeral.min.js"></script>';
  echo '<script src="'.$nutrients.'"></script>';
  echo '<script src="'.$numbers.'"></script>';
echo '</body>';
echo '</html>';
?>
<script>

  var pageDirty = 0;
  var firstNewRow = 0;

  function verifyInput(rowIndex)
  // -----------------------
  {
    var row = $("#log tbody").find('tr').eq(rowIndex);
    var cell = row.find('td').eq(QTY).has('input');
    var qty, wt;

    if (cell.length > 0)
       qty = $("input", cell).val();
    else
       qty = row.find('td').eq(QTY).val();

    if (!( $.isNumeric(qty) ))
    {
       alert("Please Enter a Quantity");
       row.find('td').eq(QTY).focus();
       return false;
    }

    cell = row.find('td').eq(WEIGHT).has('input');
    if (cell.length > 0)
       wt = $("input", cell).val();
    else
       wt = row.find('td').eq(WEIGHT).val();

    if (!( $.isNumeric(wt) ))
    {
       alert("Please Enter a Weight");
       row.find('td').eq(WEIGHT).focus();
       return false;
    }
    return true;
  }

  function incDirty()
  //-------------------
  {
    ++pageDirty;
    $("#btnSave").prop('disabled', false);
    $("#btnCancel").prop('disabled', false);
  }

  function zeroDirty()
  //-------------------
  {
    pageDirty=0;
    $("#btnSave").prop('disabled', true);
    $("#btnCancel").prop('disabled', true);
  }

  function decDirty(rowIndex)
  // ---------------------------
  {
    --pageDirty;
    if (pageDirty <=0) $("#btnSave").prop('disabled', true);
  }

  function cnclEdits()
  // ------------------------------------
  {
    if (pageDirty)
    {
      pageName="";
      formName = frmTrack;
      $('#cancelModal').modal('show')
    }
  };

  function memberHome()
  // -----------------------
  {
    pageName = '../../member-home';
    if (!pageDirty)
      document.location.href = pageName;
    else
      $('#cancelModal').modal('show');
  }

  function kdcMenu()
  // -----------------------
  {
    pageName = '../starthere.php';
    if (!pageDirty)
      document.location.href = pageName;
    else
      $('#cancelModal').modal('show');
  }

  function mainMenu()
  // -----------------------
  {
    pageName="../../";
    if (!pageDirty)
    {
       document.location.href = pageName;
    }
    else
    {
      $('#cancelModal').modal('show');
    }
  }

  function showLog()
  // -----------------------
  {
    pageName="dailyTotals.php";
    if (!pageDirty)
    {
       document.location.href = pageName;
    }
    else
    {
      $('#cancelModal').modal('show');
    }
  }

  function addToTotals(rowIndex)
  // ------------------------------------
  {
    var tblRow =$("#log tbody").find('tr').eq(rowIndex);

    // for each column in footer add corresponding colum in tblRow
    $("#log tfoot td").each( function(i, cell)
    {
      switch (i)
      {
        case QTY:
        case GRAMSPER-FOOTER_OFFSET:    // i=12
        case OPTION_BTN-FOOTER_OFFSET:  // i=13
        case TRACK_ID-FOOTER_OFFSET:    // i=17
          break;

        case WEIGHT-FOOTER_OFFSET:              // 1
          var itemWeight = parseFloat(tblRow.find('td').eq(WEIGHT).attr("dataVal"));
          var sum = parseFloat($(cell).attr("dataVal"));
          sum += itemWeight;
          cell.textContent = sum.toFixed();
          cell.value = sum.toFixed();
          $(cell).attr("dataVal", sum);
          break;

        default:
          val = parseFloat(tblRow.find("td:eq("+(i+FOOTER_OFFSET)+")").attr("dataVal"));
          if (val == -1) val =0;
          var sum = parseFloat($(cell).attr("dataVal"));
          sum += val;
          $(cell).attr("dataVal", sum);
          cell.textContent = sum.toFixed();
          cell.value = sum.toFixed();
          break;
      }
    });
  }
                                               
  // The weight has been changed, so determine the new quantity
  // represented by the new weight (newWeight/gramsPerUnit)
  // All other nutrient cells: newValue = oldWeight/oldQuantity * newQty

  function wtChanged(cell)
  // ------------------------------------
  {
     var newWeight=$("input", cell).val();
//     var oldWeight = cell.attr('dataVal');

     // check input is valid
     if (!( $.isNumeric(newWeight) ))
     {
        $("input", cell).focus();
        return false;
     }

     newWeight=parseFloat(newWeight).toFixed(2);

    var rowIndex= $(cell).closest('tr').index();
    var tblRow = $("#log tbody").find('tr').eq(rowIndex);
    var gramsPer = parseFloat(tblRow.find('td').eq(GRAMSPER).attr("dataVal"));
    var newQty = newWeight/gramsPer
    var oldQty = tblRow.find('td').eq(QTY).attr("dataVal");

    tblRow.find("td").each( function(i, cell)
    {
      switch (i)
      {
        case QTY:
          var htm;
          var q = newQty.toFixed(2);
          if ($("input", cell).length == 0)
          {
             htm = q;
          }
          else
          {
             htm  = '<input type="number"';
             htm += 'value="'+q+ '"';
             htm += 'min=".1" max="9999" step=".1">';
          }
          tblRow.find('td').eq(QTY).html(htm);
          tblRow.find('td').eq(QTY).val(q);
          $(cell).attr("dataVal", newQty);
        break;

        case ITEM:
        case ITEMID:
        case GRAMSPER:
        case UOM_DESC:
        case OPTION_BTN:
        case TRACK_ID:
        break;

        case WEIGHT:
          var oldWeight = $(cell).attr("dataVal");
          $(cell).attr("dataVal",newWeight);
          cell.value=newWeight;
          cell.textContext=newWeight;

          // update totals
          var sum = $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).attr("dataVal");
          sum -= oldWeight;
          sum += (gramsPer*newQty);
          sum = sum.toFixed();
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).val(sum);
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).text(sum);
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).attr("dataVal", sum);
          break;

        default:
          var oldVal = parseFloat($(cell).attr("dataVal"));
          if (oldVal == -1)
          {
            oldVal =0;
            unitVal = 1;
          }
          else
          {
            var unitVal = oldVal/oldQty;          // value per gram
            var newVal = unitVal*newQty;
            cell.value = newVal.toFixed();
            cell.textContent = newVal.toFixed();
            $(cell).attr("dataVal", newVal);
          }

          // update totals
          var sum = $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).attr("dataVal");
          sum -= oldVal;
          sum += (unitVal*newQty);
          sum = sum.toFixed();
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).attr("dataVal", sum);
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).val(sum);
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).text(sum);
          break;
      }
    });
  }

  // The Quantity has changed, so determine the new weight
  // represented by the new quantity
  //     newWeight = oldWeight/oldQty * newQty

  function qtyChanged(cell)
  // ------------------------------------
  {
     var newQty=$("input", cell).val();
     var oldQty = cell.attr('dataVal');

     // check input is valid
     if (!( $.isNumeric(newQty) ))
     {
        $("input", cell).focus();
        return false;
     }

     newQty=parseFloat(newQty).toFixed(2);

    var tblRow = $(cell).closest('tr');
    var gramsPer = parseFloat(tblRow.find('td').eq(GRAMSPER).attr("dataVal"));
    var oldWeight = tblRow.find('td').eq(WEIGHT).attr("dataVal");
    oldWeight = parseFloat(oldWeight);

    var oldQty = oldWeight / gramsPer;

    tblRow.find("td").each( function(i, cell)
    {
      switch (i)
      {
        case QTY:
          cell.value=newQty;
          cell.textContext=newQty;
          $(cell).attr("dataVal", newQty);
        break;

        case ITEM:
        case ITEMID:
        case GRAMSPER:
        case UOM_DESC:
        case OPTION_BTN:
        case TRACK_ID:
          break;

        case WEIGHT:
          var newVal = newQty * gramsPer;
          var htm;
//          newVal = newVal.toFixed(1);
          if ($("input", cell).length == 0)
          {
             htm = newVal.toFixed(0);
          }
          else
          {
             htm  = '<input type="number"';
             htm += 'value="'+newVal+ '"';
             htm += 'min="0" step="1">';
          }
          tblRow.find('td').eq(WEIGHT).html(htm);
          tblRow.find('td').eq(WEIGHT).val(newVal);
          tblRow.find('td').eq(WEIGHT).attr("dataVal", newVal);

          // update totals
          var sum = $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).attr("dataVal");
          sum = sum.replace(/\,/g,'');
          sum = parseFloat(sum);

          sum -= oldWeight;
          sum += (gramsPer*newQty);
          sum = sum.toFixed();
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).val(sum);
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).text(sum);
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).attr("dataVal", sum);
        break;

        default:
          var oldVal = $(this).attr("dataVal");
          oldVal = oldVal.replace(/\,/g,'');
          oldVal = parseFloat(oldVal);
          unitVal = oldVal / oldQty;

          if (oldVal == -1)
          {
            oldVal = 0;
            newVal = 1;
          }
          else
          {
            newVal = newQty *unitVal;
            $(cell).attr("dataVal", newVal);
            newVal = newVal.toFixed();
            cell.value = newVal;
            cell.textContent = newVal;
          }

          // update totals
          var unitVal = oldVal/oldQty;          // value per gram
          var newVal = unitVal*newQty;

          var sum = $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).attr("dataVal");
          sum -= oldVal;
          sum += (unitVal*newQty);
          sum = sum.toFixed();
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).val(sum);
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).text(sum);
          $("#log tfoot").find('td').eq(i-FOOTER_OFFSET).attr("dataVal", sum);
          break;
      }
    });
  };

  // Save all new or edited rows
  function saveItems()
  // ----------------
  {
    var userID = $("#userID").val();

    // save new rows
    myData=[];
    myData[0] =  {"userID" : userID };
    var beginDate =  $("#sqlDate").val();
    myData[1] = {"sqlDate" : beginDate };
    var idx = 0;
    var breakFlag = false;

    $('#log tbody tr').each(function(rowIndex, tr)
    {
      if (!$(tr).find('td:eq('+OPTION_BTN+')').children().hasClass("saveButton") )
      {  // row not edited or new, continue
         return true;  // true = continue
      }

      if (!verifyInput(rowIndex))
      {
         breakFlag = true;
         return false;  // false = break;
      }

      var item = [];
      $(tr).find("td").each(function(i, cell)
      {
          switch (i)
          {
            case QTY:
              var data =  $(cell).attr("dataVal");
              item.push( {"Qty" : data} );
              $(cell).text(parseFloat(data).toFixed(2));
            break;

            case UOM_DESC:
              item.push( {"UOM_DESC" :  cell.textContent} );
            break;

           case ITEMID:
              item.push( {"itemID" :  cell.textContent} );
           break;

           case ITEM:
           break;

           case WEIGHT:
              var data =  $(cell).attr("dataVal");
              item.push( {"servingAmt" : data} );
              $(cell).text(parseFloat(data).toFixed());
             break;

           case WATER:
              item.push( {"Water" : $(cell).attr("dataVal")} );
             break;

           case CALORIES:
              item.push( {"Calories" : $(cell).attr("dataVal")} );
             break;

           case PROTEIN:
              item.push( {"Protein" : $(cell).attr("dataVal")} );
             break;

           case FAT:
              item.push( {"Fat" : $(cell).attr("dataVal")} );
             break;

           case CARBS:
              item.push( {"Carbs" : $(cell).attr("dataVal")} );
             break;

           case FIBER:
              item.push( {"Fiber" : $(cell).attr("dataVal")} );
             break;

           case SUGARS:
              item.push( {"Sugars" : $(cell).attr("dataVal")} );
             break;

           case PHOS:
              item.push( {"Phosphorus" : $(cell).attr("dataVal")} );
             break;

           case POT:
              item.push( {"Potassium" : $(cell).attr("dataVal")} );
             break;

           case SODIUM:
              item.push( {"Sodium" : $(cell).attr("dataVal")} );
             break;

           case GRAMSPER:
              item.push( {"gramsPerUnit" : $(cell).attr("dataVal")} );
             break;

           case TRACK_ID:
              item.push( {"trackingID" : cell.textContent} );
             break;

           default:
             break;
          } // switch
      });
      myData.push(item);
    });  // each row

    if (breakFlag) 
       return false;

    var itemData = JSON.stringify(myData);

    $.ajax(
    {
      url: "./saveTrack.php",
      type: "post",
      data: {"data" : itemData},
      success: function( data, status)  // callback
      {
        trackIDs = $.parseJSON(data);
        if ( trackIDs != "false")
        {
            firstNewRow = $("#log tbody tr").length;
            updateRows(trackIDs);
            zeroDirty();
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

  function updateRows(ids)
  // ---------------------
  {
    $('#log tbody tr').each(function(rowIndex, tr)
    {
      if ($(tr).find('td:eq('+OPTION_BTN+')').children().hasClass("saveButton") )
      {  
         var rowIndex = $(this).closest('tr').index();

         var row = $("#log tbody").find('tr').eq(rowIndex);
         var qty = row.find('td').eq(QTY).val();
         row.find('td').eq(QTY).addClass("editable");

         var weight = row.find('td').eq(WEIGHT).attr("dataVal");
         row.find('td').eq(WEIGHT).addClass("editable");

         // add tracking id to newly saved row
         var trackingID = row.find('td').eq(TRACK_ID).text();
         if (trackingID == 0) trackingID = ids.shift();
         row.find('td').eq(TRACK_ID).text(trackingID);
         row.find('td').eq(TRACK_ID).val(trackingID);

         // Change 'Save' button to 'Remove'
         row.find('td').eq(OPTION_BTN).html('<button type="button" class="delButton">Remove</button>');
      }
    });
  }


/***********************
function logOff()
//-------------------
{
  var myData = { "userName" : $("#username").val() };

  $.ajax(
  {
    url: "./logOut.php",
    type: "post",
    data: {"data" : JSON.stringify(myData)},
    success: function( data, status)  // callback
             {
                if (status=="success")
                {
                    alert("Your have successfully logged out.");
                    var url = "../../index.php";
                    document.location.href = url;
                }
                else
                {
                   alert("Error Occurred, Unable to log out");
                }
             },
    error: function(xhr)
             {
               alert( "An error occured: " + xhr.status + " " + xhr.statusText);
             }
  });
}
/***********************/

$(document).ready( function() {
// ----------------------------
//  $('#btnSelectRecipe').show();

  firstNewRow=$("#log tbody tr").length;

  var title = 'WARNING';
  var txt = 'Are you sure you want to leave this page?<br><br>';
  var txt = txt + 'Your changes will be lost?';
  var myButtons = { "Yes, Your Changes will NOT be saved!": true, "No, Stay On Page": false };

  var today = new Date;

  $( "#btnChange" ).datepicker(
      {
          dateFormat: "M d, yy"
        , today
        , onClose: function(selectedDate)
          {
//            alert ("working " +  $("#sqlDate").val());
            $("#sqlDate").val(selectedDate);
            doSomething();
          }
      });

   function doSomething()
  //--------------------------
  {  // Do something
     var userID = $("#userID").val();
     var beginDate = $("#sqlDate").val();

     // get new data & re-build table
     var arrayData = { "beginDate" : beginDate, "userID" : userID };
     var itemData = JSON.stringify(arrayData);

     $.ajax(
     {
           url: "./getNewTrack.php",
           type: "post",
           data: {"data" : itemData},
           success: function( data, status)  // callback
           {
             result =  $.parseJSON(data);
             if ( result[0] == "1")
             {
               $("#log tbody").empty();
//               if ($("#log tfoot").length >0) 
                      $("#log tfoot").empty();
//               else
//                      $("#log tfoot").append('<tfoot id="logFooter">i</tfoot>')

               if (result[1] != null)
                     $("#log tbody").append(result[1]);

               $("#log tfoot").append(result[2]);
             }
             else
             {
               alert("Error Occurred, Unable to get data");
             }
           },
           error: function(xhr)
           {
             alert( "An error occured: " + xhr.status + " " + xhr.statusText);
           }
     }); // ajax
  }


/***********************************/

  // On initialization, set each cell 'value' property
  // for later manipulation
  // each row
  $('#log tbody tr').each(function(rowIndex, tr)
  {
     // each cell
     $(tr).find("td").each(function(i, cell)
     {
         $(tr).find('td').eq(i).val($(tr).find('td').eq(i).text() )
     });
  });

  // Append a new row to tracking table
  function newRow(itemID, itemInfo)
  // -------------------------------
  {
    incDirty();

    // Quantity field
    var x = 1.0;
    var qty = x.toFixed(2);
    var row  = '<tr class="rightJustify">';
    row += '<td dataVal="' + qty + '"><input type="number" value=' + '"' + qty + '"' ;;  // Qty
    row += ' min=".1" max="9999" step=".1"';
    row += '</td>';

    row += '<td>' + itemInfo[UOM_DESC] + '</td>';   // serving
    row += '<td class="hidden">' + itemID + '</td>';   // ItemID
    row += '<td  class="itemName">' + itemInfo[ITEM] + '</td>';

    // Weight Field
//    var gramsPer = parseFloat(itemInfo[GRAMSPER]).toFixed(1);
    var gramsPer = parseFloat(itemInfo[GRAMSPER]).toFixed(2);
    row += '<td dataVal="' + itemInfo[GRAMSPER] + '"><input type="number"';
    row += ' value=' + '"' + gramsPer + '"' ;
    row += ' min="0" step="1">';
    row += '</td>';

   row += '<td class="nutriCol" dataVal="' + itemInfo[WATER] + '">' +  getItemText(itemInfo[WATER]) + '</td>';
   row += '<td class="nutriCol" dataVal="' + itemInfo[CALORIES] + '">' +  getItemText(itemInfo[CALORIES]) + '</td>';
   row += '<td class="nutriCol" dataVal="' + itemInfo[PROTEIN] + '">' +  getItemText(itemInfo[PROTEIN]) + '</td>';
   row += '<td class="nutriCol" dataVal="' + itemInfo[FAT] + '">' +  getItemText(itemInfo[FAT]) + '</td>';
   row += '<td class="nutriCol" dataVal="' + itemInfo[CARBS] + '">' +  getItemText(itemInfo[CARBS]) + '</td>';
   row += '<td class="nutriCol" dataVal="' + itemInfo[FIBER] + '">' +  getItemText(itemInfo[FIBER]) + '</td>';
   row += '<td class="nutriCol" dataVal="' + itemInfo[SUGARS] + '">' +  getItemText(itemInfo[SUGARS]) + '</td>';
   row += '<td class="nutriCol" dataVal="' + itemInfo[PHOS] + '">' +  getItemText(itemInfo[PHOS]) + '</td>';
   row += '<td class="nutriCol" dataVal="' + itemInfo[POT] + '">' +  getItemText(itemInfo[POT]) + '</td>';
   row += '<td class="nutriCol" dataVal="' + itemInfo[SODIUM] + '">' +  getItemText(itemInfo[SODIUM]) + '</td>';
   row += '<td class="nutriCol hidden" dataVal="' + itemInfo[GRAMSPER] + '"></td>';
//   row += '<td></td>';  // gramsPer

//     echo '<td><button type="button" class="editButton">Edit</td>';
   row += '<td><button type="button" class="saveButton">Save</td>';
   row += '<td class="hidden" class="trackID">0</td>';  // trackingID col
   row += '</tr>';

   var len = itemInfo.length-1;
   var servingSize = itemInfo[len];

   $("#log tbody").append(row);

//   var rowIndex = $("#log tbody tr:last").index();
//$("#log tbody tr:last").find('td').eq(QTY).focus();

   var rowIndex = $("#log tbody tr").length -1;
$("#log tbody").find('tr').eq(rowIndex).find('td').eq(QTY).focus();

   addToTotals(rowIndex);


   $("#btnSave").prop('disabled', false);
   $("#btnCancel").prop('disabled', false);
  }

  function getItemText(val)
  //-------------------------
  {
     if (val == -1.0) return "N/A";
     else return val.toFixed();
  }


  $("#btnSelectFood").click(function(event)
  // ------------------------------------
  {
     if ( $("#itemChoice").val() == "" )
          return;
     var itemID = $("#itemChoice").val();
     var arrayData = { "GroceryNameID" : itemID };
     var itemData = JSON.stringify(arrayData);

     $.ajax(
     {
       url: "./getItemInfo.php",
       type: "post",
       data: {"data" : itemData},
       success: function( data, status)  // callback
         {
           if (status=="success")
           {
             var itemInfo = $.parseJSON(data);
             newRow(itemID, itemInfo);
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
  });
                                               
  $("#btnSelectRecipe").click(function(event)
  // ------------------------------------
  {
     if ($("#recipeChoice").val() == "")
         return;
     var recipID = $("#recipeChoice").val();

     var opt = $("#recipeChoice").val().split("+");
     var recipeID = opt[0];
     var recipeOwner = opt[1];

     var arrayData = { "ID" : recipeID };
     var itemData = JSON.stringify(arrayData);

     $.ajax(
     {
       url: "./getRecipeValues.php",
       type: "post",
       data: {"data" : itemData},
       success: function( data, status)  // callback
         {
           if (status=="success")
           {
             var itemInfo = $.parseJSON(data);
 $("#btnSelectRecipe").blur();
             newRow(recipeID, itemInfo);
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
  });

/***********************
  function editQty(rowIndex, oldQty)
  // ------------------------------------
  {
    var row = $("#log tbody").find('tr').eq(rowIndex);
    var newQty = parseFloat(row.find('td').eq(QTY).attr("dataVal"));
    row.find('td').eq(QTY).text(newQty.toFixed(2));

    var newQty = parseFloat(row.find('td').eq(QTY).attr("dataVal"));
    row.find('td').eq(QTY).text(newQty.toFixed(2));

    var gramsPer = row.find('td').eq(GRAMSPER).attr("dataVal");
    var oldWeight = row.find('td').eq(WEIGHT).attr("dataVal");

    // update weight
    var newWeight = (newQty * gramsPer).toFixed(2);
    row.find('td').eq(WEIGHT).val(newWeight);
    row.find('td').eq(WEIGHT).text(newWeight);
    row.find('td').eq(WEIGHT).attr("dataVal", newWeight);

   // update totals
    var sum = $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).attr("dataVal");

    sum -= oldWeight;
    sum += (gramsPer*newQty);
    sum = sum.toFixed(1);
    $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).val(sum);
    $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).text(sum);
    $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).attr("dataVal", sum);

    // update nutrient cols & totals
    row.find("td.nutriCol").each( function(i, cell)
    {
      var oldVal = $(cell).attr("dataVal");
      var unitVal = oldVal/oldQty;          // value per gram
      var newVal = unitVal*newQty;
      $(cell).attr("dataVal", newVal);
      cell.value = newVal.toFixed();
      cell.textContent = newVal.toFixed();

      // update totals
      var sum = parseFloat($("#log tfoot").find('td').eq(i+2).attr("dataVal"));
      sum -= oldVal;
      sum += (unitVal*newQty);
      sum = sum.toFixed();
      $("#log tfoot").find('td').eq(i+2).val(sum);
      $("#log tfoot").find('td').eq(i+2).text(sum);
      $("#log tfoot").find('td').eq(i+2).attr("dataVal", sum);
    });

    // change "remove" button to "save"
    row.find('td').eq(OPTION_BTN).html('<button type="button" class="saveButton">Save</button>');
    incDirty();
  }
/***********************/

/***********************
  function editWeight(rowIndex, oldWeight)
  // ------------------------------------
  {
    var row = $("#log tbody").find('tr').eq(rowIndex);
    var newWeight = parseFloat(row.find('td').eq(WEIGHT).attr("dataVal"));
    row.find('td').eq(WEIGHT).text(newWeight.toFixed(0));

    // update quantity
    var gramsPer = parseFloat(row.find('td').eq(GRAMSPER).attr("dataVal"));
    var newQty = newWeight/gramsPer
    var oldQty = row.find('td').eq(QTY).attr("dataVal");
    row.find('td').eq(QTY).text(newQty.toFixed(2));
    row.find('td').eq(QTY).attr("dataVal", newQty);

    // update total weight
    var sum = parseFloat($("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).attr("dataVal"));
    sum -= parseFloat(oldWeight);
    sum += (gramsPer*newQty);
    sum = sum.toFixed();
    $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).val(sum);
    $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).text(sum);
    $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).attr("dataVal", sum);

    // update nutrient columns
    row.find("td.nutriCol").each( function(i, cell)
    {
      var oldVal = parseFloat($(cell).attr("dataVal"));
      if (oldVal >-1)
      {
        var unitVal = oldVal/oldQty;          // value per gram
        var newVal = unitVal*newQty;
        cell.value = newVal.toFixed();
        cell.textContent = newVal.toFixed();
        $(cell).attr("dataVal", newVal);
      }

      // update totals
      if (oldVal >-1)
      {
        var sum = parseFloat($("#log tfoot").find('td').eq(i+2).attr("dataVal"));
        sum -= oldVal;
        sum += (unitVal*newQty);
        sum = sum.toFixed();
        $("#log tfoot").find('td').eq(i+2).val(sum);
        $("#log tfoot").find('td').eq(i+2).text(sum);
        $("#log tfoot").find('td').eq(i+2).attr("dataVal", sum);
       }
    });

    // change "remove" button to "save"
    row.find('td').eq(OPTION_BTN).html('<button type="button" class="saveButton">Save</button>');
    incDirty();
  }
/***********************/

  $("#btnDaily").click(function(event)
  // ------------------------------------
  {
    var url =  "dailyTotals.php";
    document.location.href = url;
  });

/**************
  $( "#btnLogOut" ).click(function( event )
  // ------------------------------------
  {
     $.get("../includes/logOut.php");
     alert("Your have successfully logged out.");
     document.location.href = "../../index.php";
  });
/**************/

  // save all new rows (menu bar save button)
  $( '#btnSave' ).click(function( event )
  // ------------------------------------
  {
       saveItems();
  });

  function removeRow(obj, rowIndex)   // obj: button object
  // ------------------------------
  { // delete an existing row from table & database
    row = $("#log tbody").find('tr').eq(rowIndex);
    var trackingID = row.find('td').eq(TRACK_ID).text();
    var gramsPer = parseFloat(row.find('td').eq(GRAMSPER).attr("dataVal"));
    var weight = parseFloat(row.find('td').eq(WEIGHT).attr("dataVal"));
    var qty = parseFloat(row.find('td').eq(QTY).attr("dataVal"));


    // subtract row values from totals
    // weight
    var sum = parseFloat($("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).attr("dataVal"));
    sum -= weight;
    sum = sum.toFixed();

    $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).val(sum);
    $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).text(sum);
    $("#log tfoot").find('td').eq(WEIGHT-FOOTER_OFFSET).attr("dataVal", sum);

    // nutrient columns
    row.find("td.nutriCol").each( function(i, cell)
    {
//      var oldVal = parseFloat(removeCommas(cell.textContent));
/******************************************************
Explanation of line below comment:
When not used with boolean values, the logical OR (||) operator returns
the first expression (parseInt(s)) if it can be evaluated to true, 
otherwise it returns the second expression (0). The return value of parseInt('') 
is NaN. NaN evaluates to false, so num ends up being set to 0.
/******************************************************/
      var oldVal = parseFloat(removeCommas(cell.textContent)) || 0;

      // update totals
      var sum = parseFloat($("#log tfoot").find('td').eq(i+2).attr("dataVal"));
      sum -= oldVal;
      sum = sum.toFixed();
      $("#log tfoot").find('td').eq(i+2).val(sum);
      $("#log tfoot").find('td').eq(i+2).text(sum);
      $("#log tfoot").find('td').eq(i+2).attr("dataVal", sum);
    }); 

    // remove row from database
    var arrayData = { "trackingID" : trackingID };
    var itemData = JSON.stringify(arrayData);

    $.ajax(
    {
      url: "./removeTrack.php",
      type: "post",
      data: {"data" : itemData},
      success: function( data, status)  // callback
      {
        result =  $.parseJSON(data);
        if ( result[0] == "true")
        {
           // remove row from table
           obj.closest('tr').remove();

//           --firstNewRow;
           firstNewRow = $('#log tbody tr').length;
           if (firstNewRow==0)
           {
              $("#log tfoot tr").find('td.nutriCol').each(function(i, cell)
              {
                  $(cell).attr("dataVal", 0)
                  $(cell).text("0");
              })
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
    }); // ajax
  }

  function saveRow(obj, rowIndex )
  //----------------------------------
  {
    var userID = $("#userID").val();
    var sqlDate = $("#sqlDate").val();
    var row = $("#log tbody").find('tr').eq(rowIndex);

    var qty = row.find('td').eq(QTY).attr("dataVal");
    var trackingID = row.find('td').eq(TRACK_ID).text();

    var weight = parseFloat(row.find('td').eq(WEIGHT).attr("dataVal"));
    var gramsPer = parseFloat(row.find('td').eq(GRAMSPER).attr("dataVal"));

    var arrayData=[];
    arrayData[0] =
    {
       "userID" : userID
     , "sqlDate" : sqlDate
     ,  "itemID" : row.find('td').eq(ITEMID).text()
     ,  "Qty": qty
     ,  "UOM_Desc" : row.find('td').eq(UOM_DESC).text()
     ,  "servingAmt" : weight
     ,  "Water" : row.find('td').eq(WATER).attr("dataVal")
     ,  "Calories" : row.find('td').eq(CALORIES).attr("dataVal")
     ,  "Protein" :  row.find('td').eq(PROTEIN).attr("dataVal")
     ,  "Fat" : row.find('td').eq(FAT).attr("dataVal")
     ,  "Carbs" : row.find('td').eq(CARBS).attr("dataVal")
     ,  "Fiber" : row.find('td').eq(FIBER).attr("dataVal")
     ,  "Sugars" : row.find('td').eq(SUGARS).attr("dataVal")
     ,  "Phosphorus" : row.find('td').eq(PHOS).attr("dataVal")
     ,  "Potassium" : row.find('td').eq(POT).attr("dataVal")
     ,  "Sodium" : row.find('td').eq(SODIUM).attr("dataVal")
     ,  "gramsPerUnit" : gramsPer
     ,  "trackingID" : trackingID
   }

    var itemData = JSON.stringify(arrayData);

    $.ajax(
    {
      url: "./addUpdateTrackRow.php",
      type: "post",
      data: {"data" : itemData},
      success: function( data, status)  // callback
      {
        id = $.parseJSON(data);
        if ( id == "false")
        {
          alert("Error Occurred, Unable to save data");
          return false;
        }
        else
        { // update table with new tracking ID, if is newly added row
          if (trackingID==0)
          {
             row.find('td').eq(TRACK_ID).text(id);
             row.find('td').eq(TRACK_ID).val(id);
          }

          // Change 'Save' button to 'Remove'
          row.find('td').eq(OPTION_BTN).html('<button type="button" class="delButton">Remove</button>');

          // update Quantity & Weight
          row.find('td').eq(QTY).attr("dataVal", qty);
          qty = parseFloat(qty).toFixed(1);
          row.find('td').eq(QTY).val(qty);
          row.find('td').eq(QTY).text(qty);
          row.find('td').eq(QTY).addClass("editable");

          row.find('td').eq(WEIGHT).attr("dataVal", weight);
          row.find('td').eq(WEIGHT).val(parseFloat(weight));
          row.find('td').eq(WEIGHT).text(parseFloat(weight));
          row.find('td').eq(WEIGHT).addClass("editable");
          decDirty(rowIndex);
        }
      },
      error: function(xhr)
      {
        alert( "An error occured: " + xhr.status + " " + xhr.statusText);
        return false;
      }
    }); // ajax
  }

  $('#log tbody').on('click', 'button', function()
  // ----------------------------------------------
  {
     rowIndex = $(this).closest('tr').index();
     if ( $(this).text() == "Save")
     {
         if (verifyInput(rowIndex) )
             saveRow($(this), rowIndex);
         else return;
     }

     else
     {
       removeRow($(this), rowIndex);
//       $(this).closest('tr').remove();  // remove row from table
     }
  });

  $('table').on('change', 'td', function(e)
  //---------------------------------------------------------------
  {
     var cellIndex = $(this).index();
     var cell = $(this).has('input');
//     var originalContent = $(this).attr('dataVal');
//     var newContent = $("input", cell).val();

     if (cellIndex ==QTY) qtyChanged($(this));
     else if (cellIndex == WEIGHT) wtChanged($(this));
  });


  $('table').on('dblclick', 'td.editable', function()
  // ------------------------------------
  {
    var rowIndex = $(this).closest('tr').index();
    var cellIndex = $(this).index();
    var originalContent = $(this).text();
    var data = $(this).attr('dataVal');
    var row =$("#log tbody").find('tr').eq(rowIndex);

    // change "remove" button to "save"
    row.find('td').eq(OPTION_BTN).html('<button type="button" class="saveButton">Save</button>');
    incDirty();

    var htm = '<input type="number" value="' + originalContent + '"';
    htm += ' min=".1" max="9999" step=".1"';
    htm += ' dataVal="' + data + '"/>';
    $(this).html(htm);

    $(this).children().first().focus();
/**********
    $(this).children().first().keypress(function (e)
    {
       if (e.which == 13)
       { // Enter Key pressed
         var newContent = $(this).val();
         row.find('td').eq(cellIndex).val(newContent)
         row.find('td').eq(cellIndex).attr('dataVal', newContent)

         if (cellIndex==WEIGHT)
              editWeight(rowIndex, originalContent);
         else if (cellIndex==QTY)
              editQty(rowIndex, originalContent);
       }
    });
/**********/

    $(this).children().first().blur(function()
    {
//       $(this).parent().text(originalContent);
        // remove input box, keep new value
        var x= parseFloat($(this).val()).toFixed(2);
        $(this).parent().text(x);
    });
  });
/***/


});  // end on page loaded

</script>

