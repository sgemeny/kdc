<!doctype html>
<?php
  session_start();

  $self = $_SERVER['PHP_SELF'];

  if(!isset($_SESSION['userID']))
  {
    header("Location: " . "starthere.php");
    exit();
  }
  require_once ('dbConnect.php');
  require_once ('banner.php');
  require_once ('displayButtons.php');
  require_once ('chooseFoodItem.php');
  require_once ('logError.php');

  $postItCss = "../plugins/impromptu/jQuery-Impromptu-master/dist/jquery-impromptu.css";
  echo '<link rel="stylesheet" media="all" type="text/css" href="'.$postItCss.'">';

//  showBanner("View Food List");
  $conn = dbConnect();

  if (isset($_GET["cmd"])) $cmd = $_GET["cmd"];
  else $cmd = CHOOSE;

  echo '<form id="frmInfo">';
//  echo '<input type="hidden" name="subDir" id="subDir" value="' . $subDir .'" />';
  echo '<input type="hidden" name="userID" id="userID" value="' . $_SESSION["userID"] .'" />';
  echo '</form>';  // frmInfo

  echo '<form id="frmShowItems" action="'.$self.'" method="get" >';
  switch ($cmd)
  {
      case CHOOSE:
        showBanner("View Food List");
        chooseItem($conn);
      break;

      case EDIT:
       showBanner("Edit Food Item");
       if (isset ($_GET["itemChooser"]))
           showEditItem($conn, $_GET["itemChooser"]);
       else
           showEditItem($conn, $_GET["choice"]);
      break;

      case SHOW:
       showBanner("Nutritional Values");
       if (isset ($_GET["itemChooser"]))
           showItem($conn, $_GET["itemChooser"]);
       else
           showItem($conn, $_GET["choice"]);
      break;
  }
  echo "</form>";


function chooseItem($conn)
// -----------------------
{
  $btns = array( MENU => "Main Menu"
               , SHOW =>"Show Item"
//               , ADD => "Add New Item"
               );

  echo '<input type="hidden" name="choice" id="choice" value=' . $itemNo . ' />';

  // only ADMIN can edit food
  if ( $_SESSION["MEMBER_LEVEL"] ==10 ) $btns[EDIT]="Edit Item";
  if ( $_SESSION["MEMBER_LEVEL"] > 3) $btns[ADD]="Add New Item";

  displayButtons($btns);
  echo '<div>';
    echo '<input name="btnAddFood" id="btnAddFood" class="myButton hidden" 
                 type="button" disabled value="Add to Food List" >';
    echo '<input name="btnCnclFood" id="btnCnclFood" class="myButton hidden" 
                 type="button" value="Cancel" >';
  echo '</div>';

  getGroceryItems($conn, "");

  echo '<br>';
  echo '<div class="grocHead" id="grocTitle"></div>';

  // frame for USDA food look up
  echo '<div id="frameDiv" class="hidden">';
    echo '<iframe src="" id="frame" height="100%" width="100%"></iframe>';
  echo '</div>';
}

function showEditItem($conn, $itemNo)
// -----------------------------------
{ // Display table of grocery nutrients and values
  // Allow user to edit values

  $btns = array( MENU => "Main Menu"
               , CHOOSE =>  "Choose New"
               , SHOW =>"Show Item"
               , ADD => "Add New Item"
               , SAVE => "Save Item"
               , CANCEL => "Cancel Item"
               );
  displayButtons($btns);

  // save in the form to make available to jquery code
  echo '<input type="hidden" name="choice" id="choice" value=' . $itemNo . ' />';

  $sql =  "SELECT GroceryName, groceryTypeID, NDB_No, Water, Calories, ";
  $sql .= "Protein, Fat, Carbs, Fiber, Sugars, Phosphorus, Potassium, Sodium, ";
  $sql .= "gramsPerUnit, groc_UOM, gramsPerCup ";
  $sql .= "FROM GROCERIES ";
  $sql .= "WHERE GroceryNameID=?";
  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  { // bind input
    if ( mysqli_stmt_bind_param($stmt, 'i', $itemNo))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (mysqli_stmt_bind_result( $stmt
                                   , $grocName
                                   , $grocTypeID
                                   , $NDB_No
                                   , $water
                                   , $calories
                                   , $protein
                                   , $fat
                                   , $carbs
                                   , $fiber
                                   , $sugars
                                   , $phos
                                   , $potas
                                   , $sodium
                                   , $gramsPer
                                   , $groc_UOM
                                   , $gramsPerCup
                                   ) )
        mysqli_stmt_store_result($stmt);
        $sts = true;
      } // execute
    } // bind input
  } // prepare


  if ($sts)
  {
    mysqli_stmt_fetch($stmt);

    // save these in the form to make available to jquery code
    echo '<input type="hidden" name="ndbNo" id="ndbNo" value="' . $NDB_No . '" onchange="setDirty(this)" />';
    echo '<input type="hidden" name="groceryName" id="groceryName" value="' . $grocName .'"  onchange="setDirty(this)"/>';

    // display item name id and usda ndb
    echo '<div class="myHead grocHead>';
      echo $grocName;
      echo '<div id="grocery">';
        echo 'ID: '  . $itemNo;
      echo '</div>';   // grocery
    echo '</div>';     // myHead

//    echo "<table id='myTable'>";
    echo "<table id='myTable1'>";
     echo "<tr>";
      $fld_name = '<input type="text" id="GroceryName" ';
      $fld_name .= 'value="'.$grocName.'" ';
      $fld_name .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Item Name" . "</td>";
      echo "<td>" . $fld_name . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_ndb = '<input type="number" id="ndbNo" step=1 min="0" ';
      $fld_ndb .= 'value="'.$NDB_No.'" ';
      $fld_ndb .= 'onchange="setDirty(this)" >';
      echo "<td>" . "NDB Number" . "</td>";
      echo "<td>" . $fld_ndb . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_water = '<input type="number" id="water" step=.0001 min="0" ';
      $fld_water .= 'value="'.$water.'" ';
      $fld_water .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Water" . "</td>";
      echo "<td>" . $fld_water . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_calories = '<input type="number" id="calories" step=.0001 min="0" ';
      $fld_calories .= 'value="'.$calories.'" ';
      $fld_calories .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Calories" . "</td>";
      echo "<td>" . $fld_calories . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_protein = '<input type="number" id="protein" step=.0001 min="0" ';
      $fld_protein .= 'value="'.$protein.'" ';
      $fld_protein .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Protein" . "</td>";
      echo "<td>" . $fld_protein . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_fat = '<input type="number" id="fat" step=.0001 min="0" ';
      $fld_fat .= 'value="'.$fat.'" ';
      $fld_fat .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Fat" . "</td>";
      echo "<td>" . $fld_fat . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_carbs = '<input type="number" id="carbs" step=.0001 min="0" ';
      $fld_carbs .= 'value="'.$carbs.'" ';
      $fld_carbs .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Carbs" . "</td>";
      echo "<td>" . $fld_carbs . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_fiber = '<input type="number" id="fiber" step=.0001 min="0" ';
      $fld_fiber .= 'value="'.$fiber.'" ';
      $fld_fiber .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Fiber" . "</td>";
      echo "<td>" . $fld_fiber . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_sugars = '<input type="number" id="sugars" step=.0001 min="0" ';
      $fld_sugars .= 'value="'.$sugars.'" ';
      $fld_sugars .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Sugars" . "</td>";
      echo "<td>" . $fld_sugars . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_phos = '<input type="number" id="phos" step=.0001 min="0" ';
      $fld_phos .= 'value="'.$phos.'" ';
      $fld_phos .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Phosphorus" . "<sup>*</sup></td>";
      echo "<td>" . $fld_phos . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_potas = '<input type="number" id="potas" step=.0001 min="0" ';
      $fld_potas .= 'value="'.$potas.'" ';
      $fld_potas .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Potassium" . "<sup>*</sup></td>";
      echo "<td>" . $fld_potas . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_sodium = '<input type="number" id="sodium" step=.0001 min="0" ';
      $fld_sodium .= 'value="'.$sodium.'" ';
      $fld_sodium .= 'onchange="setDirty(this)" >';
      echo "<td>" . "Sodium" . "<sup>*</sup></td>";
      echo "<td>" . $fld_sodium . "</td>";
     echo "</tr>";

     echo "<tr>";
      $fld_grams = '<input type="number" id="grams" step=.01 min="0" ';
      $fld_grams .= 'value="'.$gramsPer.'" ';
      $fld_grams .= 'onchange="setDirty(this)" >';
      echo "<td>" . "gramsPerUnit" . "</td>";
      echo "<td>" . $fld_grams . "</td>";
     echo "</tr>";

    echo "<tr>";
      $fld_gramsPerCup = '<input type="number" id="gramsPerCup" step=.0001 min="0" ';
      $fld_gramsPerCup .= 'value="'.$gramsPerCup.'" ';
      $fld_gramsPerCup .= 'onchange="setDirty(this)" >';
      echo "<td>" . "gramsPerCup" . "</td>";
      echo "<td>" . $fld_gramsPerCup . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "groc_UOM" . "</td>";
      $UOM = buildUOM_dropDown($conn, $groc_UOM);
      echo $UOM;
     echo "</tr>";
    echo "</table>";

    echo '<div id="info2">';
      echo '<sup>*</sup> Values are in mg.  All others are in grams';
    echo '</div>';   // info2


    mysqli_stmt_close($stmt);
  }
  else error_log("mysql ERROR: " . mysqli_error($conn) . "\n", 3, "/tmp/myErrors.log");
}

function showItem($conn, $itemNo)
// -----------------------------
{ // Display table of grocery nutrients and values

  $btns = array( MENU => "Main Menu"
               , CHOOSE =>"Choose New"
               );

  // only ADMIN can edit food
  if ( $_SESSION["MEMBER_LEVEL"] ==10 ) $btns[EDIT]="Edit Item";
  displayButtons($btns);

  echo '<input type="hidden" name="choice" id="choice" value=' . $itemNo . ' />';

  $sql =  "SELECT GroceryName, groceryTypeID, NDB_No, Water, Calories, ";
  $sql .= "Protein, Fat, Carbs, Fiber, Sugars, Phosphorus, Potassium, Sodium, ";
  $sql .= "gramsPerUnit, groc_UOM, gramsPerCup ";
  $sql .= "FROM GROCERIES ";
  $sql .= "WHERE GroceryNameID=?";

  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  { // bind input
    if ( mysqli_stmt_bind_param($stmt, 'i', $itemNo))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (mysqli_stmt_bind_result( $stmt
                                   , $grocName
                                   , $grocTypeID
                                   , $NDB_No
                                   , $water
                                   , $calories
                                   , $protein
                                   , $fat
                                   , $carbs
                                   , $fiber
                                   , $sugars
                                   , $phos
                                   , $potas
                                   , $sodium
                                   , $gramsPer
                                   , $groc_UOM
                                   , $gramsPerCup
                                   ) )
        mysqli_stmt_store_result($stmt);
        $sts = true;
      } // execute
    } // bind input
  } // prepare

  if ($sts)
  {
    mysqli_stmt_fetch($stmt);

    // display item name id and usda ndb
    echo '<div class = "myHead grocHead">';
      echo $grocName;
      echo '<div id="grocery">';
        echo 'ID: '  . $itemNo;
      echo '</div>';   // grocery

      echo '<div id = "itemIDs">';
        echo 'NDB: ' . $NDB_No;
      echo '</div>';   // item ids
    echo '</div>';     // myHead

    echo "<table id='myTable'>";
     echo "<tr>";
      echo "<td>" . "Water" . "</td>";
      echo "<td>" . $water . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "Calories" . "</td>";
      echo "<td>" . $calories . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "Protein" . "</td>";
      echo "<td>" . $protein . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "Fat" . "</td>";
      echo "<td>" . $fat . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "Carbs" . "</td>";
      echo "<td>" . $carbs . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "Fiber" . "</td>";
      echo "<td>" . $fiber . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "Sugars" . "</td>";
      echo "<td>" . $sugars . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "Phosphorus" . "<sup>*</sup></td>";
      echo "<td>" . $phos . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "Potassium" . "<sup>*</sup></td>";
      echo "<td>" . $potas . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "Sodium" . "<sup>*</sup></td>";
      echo "<td>" . $sodium . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "gramsPerUnit" . "</td>";
      echo "<td>" . $gramsPer . "</td>";
     echo "</tr>";

     echo "<tr>";
      echo "<td>" . "gramsPerCup" . "</td>";
      $gms = ($gramsPerCup==1 ? "N/A" : $gramsPerCup);
      echo "<td>" . $gms . "</td>";
     echo "</tr>";

     echo "<tr>";
      $descr = getUOM_Desc($conn, $groc_UOM);
      echo "<td>" . "groc_UOM" . "</td>";
      echo "<td>" . $descr . "</td>";
     echo "</tr>";
    echo "</table>";

    echo '<div id="info">';
      echo '<sup>*</sup> Values are in mg.  All others are in grams';
    echo '</div>';   // info

  }
  else echo(mysqli_error($conn));
}

function getUOM_Desc($conn, $UOM_ID)
// ----------------------------
{
  $sql  = "SELECT ID, Descr ";
  $sql .= "FROM UOM_Tbl ";
  $sql .= "WHERE ID = ?";

  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  { 
    if ( mysqli_stmt_bind_param($stmt, 'i', $UOM_ID))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (mysqli_stmt_bind_result( $stmt
                                   , $id
                                   , $descr
                                   ) )
         $sts = mysqli_stmt_store_result($stmt);
        $numUOMrows = mysqli_stmt_num_rows($stmt);
//      echo "Number of rows: " .  mysqli_stmt_num_rows($stmt) . "<br>";
      } // execute
    } // bind
  } // prepare

  if ($sts)
  {
     mysqli_stmt_fetch($stmt);
     mysqli_stmt_close($stmt);
  }
  return $descr;
}

function buildUOM_dropDown($conn, $UOM_ID)
// ----------------------------
{
  $sql  = "SELECT ID, Descr ";
  $sql .= "FROM UOM_Tbl ";

  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  { 
    if (mysqli_stmt_execute($stmt) )
    {
      if (mysqli_stmt_bind_result( $stmt
                                 , $id
                                 , $descr
                                 ) )
      mysqli_stmt_store_result($stmt);
      $numUOMrows = mysqli_stmt_num_rows($stmt);
//      echo "Number of rows: " .  mysqli_stmt_num_rows($stmt) . "<br>";
      $sts = true;
    } // execute
  } // prepare

  if ($sts)
  {
    $idx = 0;
    $UOM = array(array());
    while (mysqli_stmt_fetch($stmt))
    {
      $UOM[$idx]["ID"]= $id;
      $UOM[$idx]["Descr"]= $descr;
      ++$idx;
    }
    mysqli_stmt_close($stmt);

    $i=0;
    $selectFlag="";
    $fld_UOM = '<select id="UOM" onchange="setDirty(this)">';
    for ($i=0; $i<$numUOMrows; $i++)
    {
       $uom = $UOM[$i]["Descr"];
       $UOM_ID==$UOM[$i]["ID"] ? $selectFlag = " selected=selected" : $selectFlag = "";

       $fld_UOM .= '<option value="'.$UOM[$i]["ID"].'"';
       $fld_UOM .= $selectFlag.' " >'.$uom.'</option>';
    }
    $fld_UOM .= '</select>';
    $UOMrow = '<td>'.$fld_UOM.'</td>'; 
    return $UOMrow;
  }
  else error_log("mysql ERROR: " . mysqli_error($conn), 3, "/tmp/myErrors.log");
}

  // <!-- Placed at the end of the document so the pages load faster -->
  $buttons= "../scripts/buttons.js";
  $postIt = "../plugins/impromptu/jQuery-Impromptu-master/dist/jquery-impromptu.js";
  require_once ( 'jquery.php' );
  require_once ( 'analyticstracking.php' );
  echo '<script src="'.$buttons.'"></script>';
  echo '<script type="text/javascript" src="'.$postIt.'"></script>';
?>


  <script>
  var pageDirty = false;

  function setDirty(obj)
  //-------------------
  {
    txt = obj.value;
    $(obj).addClass("dataDirty");
    pageDirty = true;
    $("#btnSave").prop('disabled', false);
    $("#btnCancel").prop('disabled', false);
  }

$(document).ready( function() {
// ----------------------------
  var title = 'WARNING';
  var txt = 'Are you sure you want to leave this page?<br><br>';
  var txt = txt + 'Your changes will be lost?';
  var myButtons = { "Yes, Your Changes will NOT be saved!": true, "No, Stay On Page": false };

  /*************************/


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
        var url =  "../starthere.php";
        document.location.href = url;
     }
  }

  $("#btnMenu").click(function(event)
  // ------------------------------------
  {
    if (pageDirty)
    {
      jConfirm(txt,title, myButtons, btnMenuCallBack);
    }
    else
    {
      var url =  "../starthere.php";
      document.location.href = url;
    }
  });


  $("#btnShow").click(function(event)
  // ------------------------------------
  {
    if (!pageDirty)
    {
//      var myItem = $("#itemChooser").val();
//      $("#choice").prop('value', myItem);
      var myItem = $("#choice").val();
      $("#btnCmd").prop('value', SHOW);
      $("#frmShowItems").submit();
    }
    else
      jConfirm(txt,title, myButtons, btnMenuCallBack);
  })

  function btnShowCallBack(v)
  //------------------------
  {
    if (v)
    {
      var myItem = $("#itemChooser").val();
      $("#choice").prop('value', myItem);
      $("#btnCmd").prop('value', SHOW);
      $("#frmShowItems").submit();
    }
  };

  $("#btnEdit").click(function(event)
  // ------------------------------------
  { // can get here when itemChooser not on screen
    // it will be undefined, but choice will be defined
    var myItem = $("#itemChooser").val();
    if (typeof myItem === "undefined")
         myItem = $("#choice").val();
    editItem(myItem);
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

//    pageDirty = false;
//    $("#btnCmd").prop('value', CHOOSE);
//    $("#frmShowItems").submit();
  });

/*************
  $("#btnUSDA").click(function(event)
  // ------------------------------------
  {
    window.open("foodList.php", "_blank","resizable=yes,top=400,left=550,width=400,height=400");
  });
*************/

  function btnChooseCallBack(v)
  // ------------------------------------
  {
     if (v) // leave page without saving
     {
        $("#btnCmd").prop('value', CHOOSE);
        $("#frmShowItems").submit();
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
      $("#btnCmd").prop('value', CHOOSE);
      $("#frmShowItems").submit();
    }
  });


  $("#btnSave").click(function(event)
  // ------------------------------------
  {
     saveItem();
  });

  $("#btnAdd").click(function(event)
  // ------------------------------------
  {
/********************
     $("#frmShowItems").addClass("hidden");
     $("#myTable").addClass("hidden");
     $("#addItemBox").removeClass("hidden");
     $("#grocName").focus();
********************/

     // show buttons
     $("#btnAddFood").removeClass("hidden");
     $("#btnCnclFood").removeClass("hidden");

     // hide chooser drop down
     $("#itemChooser").addClass("hidden");
     $("#btnLine").addClass("hidden");
     var url = "../foodList.php?embed=1";
     $('#frame').attr('src', url);
     $("#frameDiv").removeClass("hidden");
  });

  $("#btnCnclFood").click(function(event)
  // ------------------------------------
  {
     $('#frame').attr('src', "about:blank");
     $("#btnAddFood").addClass("hidden");
     $("#btnCnclFood").addClass("hidden");
     $("#frameDiv").addClass("hidden");
     $("#itemChooser").removeClass("hidden");
     $("#btnLine").removeClass("hidden");
  });

 $("#btnAddFood").click(function()
  // ----------------------------
  {
     var volNames = ['tsp', 'tbsp', 'cup', 'each', 'quart', 'oz', 'can'];
     var volIds = [1, 2, 3, 4, 7, 9, 20];
//debugger;
     // get item name and NDB number of chosen item
     var itemName = $("#frame").contents().find("#itemChooser option:selected").text() + ", ";
     var ndb = $("#frame").contents().find("#itemChooser option:selected").val();

     var measures = $("#frame").contents().find("#weightChoice");
     itemName += measures.find("option:selected").text();

     // Check to see if item already exists in database
     if (itemExists(itemName)) return;

     var options = $("#frame").contents().find("#weightChoice option");
     var multiplier = 1;
     var multVal = 1;
     var uom = measures.find("option:selected").text().toLowerCase()
     var qty = measures.find("option:selected").val();

     // See if the uom is a cup of something (ie. cup of slices like peaches)
     if (uom.indexOf("cup") > -1) uom = "cup";

     var uomIdx = volNames.indexOf(uom);
     var uomVal = uomIdx == -1 ? 4 : volIds[uomIdx];
     var uomGrams = measures.find("option:selected").val();
     var gramsPerCup = 1;
     
     for (var i=0; i<options.length; i++)
     {
        var txt = options[i].text.toLowerCase();
        if ( txt.indexOf("cup") > -1)
        {  // found it
           gramsPerCup = options[i].value;
           break;
        }
        else if (txt.indexOf("tbsp") > -1)
        {
            multiplier = 16;
            multVal = options[i].value;
        }
        else if (txt.indexOf("tsp") > -1)
        {
            multiplier = 48;
            multVal = options[i].value;
        }
     }
     
     if (gramsPerCup==1 && multiplier > 1)
            gramsPerCup = multiplier * multVal;

     // get the nutrient values table
     var food = $("#frame").contents().find("#itemTable");

     var myData = {};
     myData["GroceryName"] = itemName;
     myData["NDB_No"] = ndb;
     food.find("tbody tr").each(function(row, tr)
     {
        var name = $(tr).find('td').eq(0).text();
        var nutrient = $(tr).find('td').eq(1).text();
            switch (name)
            {
              case "Water": 
              case "Calories":
              case "Protein":
              case "Fat":
              case "Carbs":
              case "Fiber":
              case "Sugars":
              case "Phosphorus":
              case "Potassium":
              case "Sodium":
                 myData[name] = nutrient;
                break;

              default:
                break;
            }
        }); //each row

          myData["gramsPerUnit"] = uomGrams;
          myData["groc_UOM"] = uomVal;
          myData["gramsPerCup"] = gramsPerCup;

        var itemData = JSON.stringify(myData);
//console.debug(itemData);
//debugger;

     // Add grocery item to data base
     $.ajax(
     {
       url: "./addGrocItem.php",
       type: "post",
       data: {"data" : itemData},
       success: function( data, status)  // callback
                {
                   if (status=="success") 
                   {
                     if (data.indexOf("FAIL") != -1)
                     {
		       saveError();
                       return false;
                     }
                     var itemID = $.parseJSON(data);

                     if ($.isNumeric(itemID)) // check for numeric, else error occurred
                     {
                       updateChooser(itemID, itemName);
                       alert("Your Item was Successfully Added");
                       showNewItem(itemID);
                     }
                     else
                     {
		       saveError();
                       return false;
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

     $('#frame').attr('src', "about:blank");
     $("#btnAddFood").addClass("hidden");
     $("#btnCnclFood").addClass("hidden");
     $("#frameDiv").addClass("hidden");
     $("#itemChooser").removeClass("hidden");
     $("#btnLine").removeClass("hidden");
  });

function saveError()
// -----------------
{
  alert("An Error Occurred\naddGrocItem.php returned bad data!");
}

  function editItem(myItem)
  // ------------------------------------
  {
    $("#choice").prop('value', myItem);
    $("#btnCmd").prop('value', EDIT);
    $("itemChooser").prop('value', myItem);
    var url =  "showGroceries.php?cmd="+EDIT+"&itemChooser=" + myItem;
    document.location.href = url;
  }

  function showNewItem(myItem)
  // ------------------------------------
  {
    $("#choice").prop('value', myItem);
    $("#btnCmd").prop('value', SHOW);
    $("itemChooser").prop('value', myItem);
    var url =  "showGroceries.php?cmd="+SHOW+"&itemChooser=" + myItem;
    document.location.href = url;
  }

  function restoreButtons()
  // ------------------------------------
  {
     $("#frmShowItems").removeClass("hidden");
     $("#myTable").removeClass("hidden");
     $("#addItemBox").addClass("hidden");
/**************************************************
     $("#btnMenu").removeClass("hidden");
     $("#btnAdd").removeClass("hidden");
     $("#btnShow").removeClass("hidden");
     $("#btnEdit").removeClass("hidden");
     $("#chooser").removeClass("hidden");
     $("#btnChoose").removeClass("hidden");
**************************************************/
  }


  $("#btnCnclBox").click(function(event)
  // ------------------------------------
  { 
      restoreButtons();
       $("#grocName").val("");
  });

  $("#btnEnter").click(function(event)
  // ------------------------------------
  {
     if ( ($.trim( $("#grocName").val())).length > 0)
     {
       addItem();
       restoreButtons();
       $("#grocName").val("");
     }
     else
        alert("Please enter an Item Name!");
  });

  function itemExists(itemName)
  // -------------------------------
  {
     // This approach is nasty & time consuming, but I
     // couldn't get the more elegant approaches to compile correctly
     // var exists = $("#itemChooser option[value='" +itemName+"']").length
     // var exists = $("#itemChooser").find('option[value="'+itemName +'"]').length > 0

     var found = false;
     $("#itemChooser option").each(function()
     {
// console.debug( $(this).text());
       // remove all spaces for compare
       if ( $(this).text().toUpperCase().replace(/ /g,'') == itemName.toUpperCase().replace(/ /g,'') )
       {
           alert(itemName +  " Already Exists.  Please Try Another");
           found = true;
           return false;
       }

     });  // for each
     return found;
  }

  function updateChooser(id, newItem)
  // ------------------------------------
  {
     // update "chooser" select
     newOption = $('<option value="' + id + '">' + newItem + '</option>');
     done = false;
     $("#itemChooser option").each(function(ndx, option)
     {
        if ( option.text.toUpperCase() >= newItem.toUpperCase())
        { // insert new item here
          $("#itemChooser option").eq(ndx).before(newOption);
          return false;
        }
    });
    if (!done)
        $("#itemChooser").append(newOption);  // add to end of list
  }

  function addItem()
  // ----------------
  {
     var itemName = $("#grocName").val();
     var arrayData = { "GroceryName" : itemName };
//     var itemData = JSON.stringify(arrayData).replace(/'/g, "\\'")
     var itemData = JSON.stringify(arrayData);
     var itemId;

     // This approach is nasty & time consuming, but I
     // couldn't get the more elegant approaches to compile correctly
     // var exists = $("#itemChooser option[value='" +itemName+"']").length
     // var exists = $("#itemChooser").find('option[value="'+itemName +'"]').length > 0

     var found = false;
     $("#itemChooser option").each(function()
     {
       // remove all spaces for compare
       if ( $(this).text().toUpperCase().replace(/ /g,'') == itemName.toUpperCase().replace(/ /g,'') )
       {
           alert(itemName +  " Already Exists.  Please Try Another");
           found = true;
           return false;
       }

     });  // for each
     if (found)
        return false;

     $.ajax(
     {
       url: "./addGrocItem.php",
       type: "post",
       data: {"data" : itemData},
       success: function( data, status)  // callback
                {
//console.debug(status);
                   if (status=="success") 
                   {
                     var itemID = $.parseJSON(data);
                     if ($.isNumeric(itemID)) // check for numeric, else error occurred
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

  function saveItem()
  // ----------------
  {
     var ndb=  $("#ndbNo").val();
     var itemNo = $("#choice").val();
     var itemName = $("#groceryName").val();

     if (pageDirty)
     {
        var tblData="";
        var arrayRow=0;

        var myData =  {items: [{GroceryNameID: itemNo}]};
//        myData.items.push( {NDB_NO: ndb});
//        myData.items.push( {GroceryName: itemName});
        $('#myTable1 tr').each(function(row, tr)
        {
            switch (row)
            {
              case 0:  // this way works
                var grocName = $(tr).find("#GroceryName").val();
                myData.items.push( {GroceryName: grocName});
                break;

              case 1:  // this way works
                var ndb_no = $(tr).find("#ndbNo").val();
                myData.items.push( {NDB_No: ndb_no});
                break;

              case 2:  // this way works
                var water = $(tr).find("#water").val();
                myData.items.push( {Water: water});
                break;

              case 3:  // and so does this
                  myData.items.push( {"Calories" : $(tr).find("#calories").val()} );
                break;

              case 4:
                myData.items.push( {"Protein" : $(tr).find("#protein").val()} );
                break;

              case 5:
                myData.items.push( {"Fat" : $(tr).find("#fat").val()} );
                break;

              case 6:
                myData.items.push( {"Carbs" : $(tr).find("#carbs").val()} );
                break;

              case 7:
                myData.items.push( {"Fiber" : $(tr).find("#fiber").val()} );
                break;

              case 8:
                myData.items.push( {"Sugars" : $(tr).find("#sugars").val()} );
                break;

              case 9:
                myData.items.push( {"Phosphorus" : $(tr).find("#phos").val()} );
                break;

              case 10:
                myData.items.push( {"Potassium" : $(tr).find("#potas").val()} );
                break;

              case 11:
                myData.items.push( {"Sodium" : $(tr).find("#sodium").val()} );
                break;

              case 12:
                myData.items.push( {"gramsPerUnit" : $(tr).find("#grams").val()} );
                break;

              case 14:
                myData.items.push( {"groc_UOM" : $(tr).find("#UOM").val()} );
                break;


              case 13:
                myData.items.push( {"gramsPerCup" : $(tr).find("#gramsPerCup").val()} );
                break;

              default:
                break;
            }
        }); //each row

        var itemData = JSON.stringify(myData);

        $.ajax(
         {
           url: "./saveGrocery.php",
           type: "post",
           data: {"data" : itemData},
           success: function( data, status)  // callback
                    {
                      if (status=="success") 
                      {  
                         if (data.indexOf("FAIL") === -1)
                         {
                            alert("Your changes were Successfully Completed");
                            pageDirty = false;
                            $("#btnSave").prop('disabled', true);
                            $("#btnCancel").prop('disabled', true);
                         }
                         else alert("Error Occurred, Unable to save data")
                      }
                      else alert("Error Occurred, Unable to save data")
                     },  // end function
           error: function(xhr)
                    {
                      alert( "An error occured: " + xhr.status + " " + xhr.statusText);
                    }
         });
     } // pageDirty
  }

});  // end on page loaded


</script>

  </div> <!-- end of container (started in banner.php) -->
 </body>
</html>


