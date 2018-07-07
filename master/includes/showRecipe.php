<?php
  session_start();
  if(!isset($_SESSION['userName']))
  {
    header("Location: " . "../../starthere.php");
    exit();
  }

  $self = $_SERVER['PHP_SELF'];

  require_once ('dbConnect.php');
  require_once ('banner.php');
  require_once ('displayButtons.php');
  require_once ('fractions.php');
  require_once ('getRecipe.php');
//  require_once ('analyticstracking.php');
  require_once ('logError.php');

  $conn = dbConnect();

  $thisScript = $_SERVER['PHP_SELF'];
  $chosenRecipe = $_GET["chosenRecipe"];;

  $sql  = "SELECT RecipeName, servingSize, Comments ";
  $sql .= "     , totWeight, Water, Calories, Protein, Fat, Carbs ";
  $sql .= "     , Fiber, Sugars, Phosphorus, Potassium, Sodium ";
  $sql .= "FROM RecipeMaster ";
  $sql .= "WHERE RecipeMaster.ID = ?  ";

  $sts = false;
  if (($stmt = $conn->prepare($sql)))
  { // bind input
    if ( mysqli_stmt_bind_param($stmt, 'i', $chosenRecipe))
    {
      if (mysqli_stmt_execute($stmt) )
      {
        if (mysqli_stmt_bind_result( $stmt
                                   , $itemName
                                   , $servSize
                                   , $comments
                                   , $totWeight
                                   , $waterPerGram
                                   , $caloriesPerGram
                                   , $proteinPerGram
                                   , $fatPerGram
                                   , $carbsPerGram
                                   , $fiberPerGram
                                   , $sugarPerGram
                                   , $phosPerGram
                                   , $potPerGram
                                   , $sodiumPerGram
                                   ) )
        mysqli_stmt_store_result($stmt);
        $sts = true;
      } // execute
    } // bind input
  } // prepare

  if (!$sts)
  {
    logError( mysqli_error($conn) );
    exit(-2);
  }
  else
  {
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
  }

  showBanner($itemName);

  $btns = array( MENU => "Main Menu"
               , CHOOSE =>"Choose New"
               , EDIT => "Edit Recipe"
               );

  displayButtons($btns);

  echo '</div>';   // end of container (started in banner.php)

  echo '<div class="recipeHolder">';
  echo '<form id="frmShowRecipe" action="'.$self.'" method="get" >';
//  echo '<input type="hidden" name="subDir" id="subDir" value="' . $subDir .'" />';
  echo '<input type="hidden" name="chosenRecipe" id="chosenRecipe" value="' . $chosenRecipe .'" />';
  echo '<input type="hidden" name="username" id="username" value="' . $_SESSION["userName"] .'" />';
  echo '<input type="hidden" name="userID" id="userID" value="' . $_SESSION["userID"] .'" />';
  echo '<input type="hidden" name="editable" id="editable" value="' . $_GET["canEdit"] .'" />';

  getRecipe($conn, $chosenRecipe, $comments);
  echo '<div class="divCaption" id="servingInfo">';

    echo '<div id="enterServing">';
      echo '<label for "serving" id="specialLabel">Serving Size(g)</label>';
      $fld_serving = '<input type="number" id="serving"'; 
      $fld_serving .= 'value="'.number_format($servSize).'" ';
      $fld_serving .= 'onchange="calcServing()"';
      $fld_serving .= 'onfocus="savePrevious()"';
      $fld_serving .= ' min="1" max="999" size=5 length=5 step="1"/>';
      echo $fld_serving;
    echo '</div>';  // end of enterServing
    echo '<div id="weight">';
      echo "Recipe Total Weight " . $totWeight . "(g)";
    echo '</div>';  // weight
  echo '</div>';  // divCaption

  showNutrients($conn, $chosenRecipe, $servSize);
  echo '</form>';

  // <!-- Placed at the end of the document so the pages load faster -->
  $buttons= "../scripts/buttons.js";
  echo '<script src="'.$buttons.'"></script>';
  require_once ( 'jquery.php' );
?>

<script>
  var prevSize = 1;

  function savePrevious()
  //--------------------
  {
     prevSize = $('#serving').val() *1.0;
  }

  function calcServing()
  // ------------------
  {
     var newSize=$('#serving').val() * 1.0;
     var tblRow = $("#tbl_perServing tr:last");
     var invPrevSize = 1.0 / prevSize;  // invert & multiply

     var water = tblRow.find('td:eq(0)').text()*1.0 *newSize * invPrevSize;
     var calories = tblRow.find('td:eq(1)').text()*1.0 *newSize * invPrevSize;
     var protein = tblRow.find('td:eq(2)').text()*1.0 *newSize * invPrevSize;
     var fat = tblRow.find('td:eq(3)').text()*1.0 *newSize * invPrevSize;
     var carbs = tblRow.find('td:eq(4)').text()*1.0 *newSize * invPrevSize;
     var fiber = tblRow.find('td:eq(5)').text()*1.0 *newSize * invPrevSize;
     var sugar = tblRow.find('td:eq(6)').text()*1.0 *newSize * invPrevSize;
     var phos = tblRow.find('td:eq(7)').text()*1.0 *newSize * invPrevSize;
     var pot = tblRow.find('td:eq(8)').text()*1.0 *newSize * invPrevSize;
     var salt = tblRow.find('td:eq(9)').text()*1.0 *newSize * invPrevSize;

     servingRow = $("#tbl_perServing tr");
     servingRow.find('td:eq(0)').text(water.toFixed(2));
     servingRow.find('td:eq(1)').text(calories.toFixed(2));
     servingRow.find('td:eq(2)').text(protein.toFixed(2));
     servingRow.find('td:eq(3)').text(fat.toFixed(2));
     servingRow.find('td:eq(4)').text(carbs.toFixed(2));
     servingRow.find('td:eq(5)').text(fiber.toFixed(2));
     servingRow.find('td:eq(6)').text(sugar.toFixed(2));
     servingRow.find('td:eq(7)').text(phos.toFixed(2));
     servingRow.find('td:eq(8)').text(pot.toFixed(2));
     servingRow.find('td:eq(9)').text(salt.toFixed(2));
 };
       

$(document).ready( function(e) {
// ----------------------------

  var editable = $("#editable").val();

  if (editable==1)  
  {
     $("#btnEdit").prop('disabled', false);
  }
  else
  { 
    $("#btnEdit").prop('disabled', true);
  }


  $("#serving").keypress(function (e)
  {
     if (e.which==13)
     {
          return false;
     }
  })

  $("#btnMenu").click(function(event)
  // ------------------------------------
  {
    var url =  "../starthere.php";
    document.location.href = url;
  });

  $("#btnEdit").click(function(event)
  // ------------------------------------
  {
    var chosenRecipe = $("#chosenRecipe").val();
    var canEdit = 1;
    $("#btnCmd").prop('value', EDIT);
    
    var url =  "../forms/editRecipe.php?cmd="+ EDIT +"&chosenRecipe="+chosenRecipe;
    document.location.href = url;
  });

  $("#btnChoose").click(function(event)
  // ------------------------------------
  {
//    var url =  $("#subDir").val() + "includes/recipes.php";
    var url =  "recipes.php";
    document.location.href = url;
  });


});  // end on page loaded

  $("#btnLogOut").click(function(event)
  // ------------------------------------
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
  });

</script>


</body>
</html>;



