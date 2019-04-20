<!doctype html>
<?php

$embedded = 0;
if ( isset($_GET["embed"]))
{
   if ($_GET["embed"]==1)
       $embedded = 1;
}
else $embedded = 0;

echo '<html>';
echo '<head>';
  echo ' <meta charset="utf-8">';
  echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">';
  echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
  echo '<title>USDA Food LookUp</title>';

  echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>';
  echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" ></script>';

  // Bootstrap core CSS
  echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >';

  // Custom styles for this template -->';
  echo '<link href="css/custom.css" rel="stylesheet"> ';
  echo '<link href="css/foodList.css" rel="stylesheet"> ';
  echo '<link href="css/style.css" rel="stylesheet"> ';
echo '</head>';

if ($embedded == 0)
{
  echo '<body>';
    echo '<nav class="navbar navbar-inverse navbar-fixed-top"> ';
      echo '<div class="container">';
        echo '<div class="navbar-header">';
          echo '<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>';

          echo '<div id="img">';
            echo '<a href="../" class="pull-left"><img src="images/kdcLogo.png" alt="KDC" ></a>';
          echo '</div>'; // img
        echo '</div>';   // navbar header -->

      echo '<div id="navbar" class="collapse navbar-collapse">';
        echo '<ul class="nav navbar-nav pull-left">';
          echo '<li><a id="home" href="../");">Home</a></li>';
          echo '<li><a id="home" href="../member-home">Member Area</a></li>';
        echo '</ul>';
      echo '</div>'; // nav-collapse -->
    echo '</div>';  // container
  echo '</nav>';

  echo '<section>';
    echo '<br><br>';
    echo '<div class="container">';
    echo '<h2 class="leftJustify">USDA Food Search</h2>';
    echo '<div iD="inputDiv">';
      echo '<input type="text" id="searchItem" placeholder="Search for food">';
      echo '<input name="btnSearch" class="myButton" id="btnSearch" type="button" value="Search">';
    echo '</div>';
}
else
{
  echo '<section>';
    echo '<div iD="inputDiv" class="embedded">';
      echo '<input type="text" id="searchItem" placeholder="Search for food">';
      echo '<input name="btnSearch" class="myButton" id="btnSearch" type="button" value="Search">';
    echo '</div>';
}

    echo '<input type="hidden" name="embedded" id="embedded" value="' . $embedded .'" />';

    // create drop down box  
    echo '<div id="chooseItem" class="hidden">';
       echo '<select name="itemChooser" id="itemChooser"> </select>';
     echo '<input name="btnSelectItem" class="myButton" id="btnChoose" type="button" value="Choose">';
    echo '</div>'; // end of chooser Item 

    // create list of nutrient items
    echo '<div id="itemDiv" class="hidden">';  // table div
      echo '<h4 id="itemTitle"></h4>';
      echo '<table id="itemTable">';
        echo '<thead></thead>';
        echo '<tbody></tbody>';
      echo '</table>';
    echo '</div>';  // end itemDiv

    echo '<br>';

    if ($embedded==1)
    { // create drop down list of measures
      echo '<div id="measures" class="hidden">';
        echo '<label for "weightChoice">Choose Unit of Measure </label>';
        echo '<select name="weightChoice" id="weightChoice"></select>';
      echo '</div>';
    }
    else  // NOT embedded
    {  // create list of measures   
      echo '<div id="measures">';
        echo '<h4 id="measure"></h4>';
        echo '<table id="tblMeasures" class="hidden">';
          echo '<thead><th>Item</th><th>Grams</th></thead>';
            echo '<tbody> </tbody>';
        echo '</table>';
      echo '</div>';  // end measures div

      echo "<br>";
      $servingSize=10;
      echo '<div id="enterServing" class="hidden">';
        echo '<label for "serving">Enter Serving Size in Grams</label>';
        $fld_serving = '<input type="number" id="serving"';
        $fld_serving .= 'value="'.number_format($servingSize, 1, '.', '').'" ';
        $fld_serving .= ' min="1" max="999" size=5 length=5 step=".1"/>';
        echo $fld_serving;
        echo '<input name="btnCalc" class="myButton" id="btnCalc" type="button" value="Calculate">';

      echo '</div>';  // end of enterServing

      // show nutrients for serving size
      echo '<div id="testDiv">';   // for table
        echo '<table class="totals hidden" id="tbl_perServing">';
          echo '<thead>';
            echo '<th>Water</th>';
            echo '<th>Calories</th>';
            echo '<th>Protein</th>';
            echo '<th>Fat</th>';
            echo '<th>Carbs</th>';
            echo '<th>Fiber</th>';
            echo '<th>Sugars</th>';
            echo '<th>Phos.</th>';
            echo '<th>Pot.</th>';
            echo '<th>Sodium</th>';
          echo '</thead>';

          echo "<tbody>";
            echo "<tr>";
              echo "<td></td>"; // water
              echo "<td></td>"; // calories
              echo "<td></td>"; // protein
              echo "<td></td>"; // fat
              echo "<td></td>"; // carbs
              echo "<td></td>"; // fiber
              echo "<td></td>"; // sugars
              echo "<td></td>"; // phosphorus
              echo "<td></td>"; // potassium
              echo "<td></td>"; // sodium
            echo "</tr>";
          echo "</tbody>";
        echo '</table>';
      echo '</div>';  // end table div
    } // if embedded

  echo '</section>';
  echo '</body>';
echo '</html>';

echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>';

?>

<script>
  var items = [];

  function calcServing()
  // ------------------
  {
     var mult=$('#serving').val() * 1.0;
     servingRow = $("#tbl_perServing tbody tr");

     $("#itemTable tbody tr").each( function(i, row)
     { // each nutrient row
        var newVal = $(row).find("td").eq(1).text()*mult;
        servingRow.find('td').eq(i).text(newVal.toFixed(2));
     });
 };


$(document).ready( function(e) {
// ----------------------------
$("#searchItem").keyup(function(event){
    if(event.keyCode == 13){
        $("#btnSearch").click();
    }
});

 $("#btnCalc").click(function()
  // ----------------------------
  { // calculate new serving size
        calcServing();
  });  // end btnCalc

 $("#btnSearch").click(function()
  // ----------------------------
  { // empty for new entries
   $("#measures").addClass("hidden");
   if ( $("#embedded").val() == 0 )
       $("#tblMeasures tbody tr").remove();

    $("#itemChooser").empty();
    $("#itemTable tbody tr").remove();
    $("#tblMeasures tbody tr").remove();
    $("h4#itemTitle").empty();
    $("h4#measure").empty();
    $("#chooseItem").removeClass("hidden");
    $("#itemDiv").addClass("hidden");
    $("#tbl_perServing").addClass("hidden");
    $("#enterServing").addClass("hidden");

    var term = $("#searchItem").val();
//    var url ="http://api.nal.usda.gov/ndb/search/?format=xml&q=" + term + "&max=70&offset=0&api_key=itAP1bNbuIZHkOVrDJow1NsRoo0xFmuZOr9QhTUm";
    var url ="https://api.nal.usda.gov/ndb/search/?format=xml&q=" + term + "&offset=0&api_key=itAP1bNbuIZHkOVrDJow1NsRoo0xFmuZOr9QhTUm";

    $.ajax({
       type: "GET",
       dataType: "xml",
       url: url,
       success: processXml,
       error: processError
    });

    function processError(xhr)
    // ----------------------
    {
       alert("There was an ERROR on the Server!\nPlease try again later." );
    }

    function processXml(xml) 
    // ----------------------
    {
      var error = true;
      $(xml).find("item[offset]").each(function() 
      {
        if ( $(this).find("group").text().indexOf("Branded") == -1)
        {
           error = false;
           var name = $(this).find("name").text();
           var ndb = $(this).find("ndbno").text();
           var offset = $(this).attr("offset");
           items.push ( {itemNum: ndb, index: offset} );
           addToChooser(ndb, name, offset);
        }
      }) // end each
      if (error) alert("Item Not Found!");
    }

  function addToChooser(id, newItem, offset)
  // ------------------------------------------
  {
     newOption = $('<option value="' + id + '">' + newItem + '</option>');

     // add to "chooser" select
    $("#itemChooser").append(newOption);  // add to end of list
  }

  });  // end btnSearch

 $("#btnChoose").click(function()
  // ----------------------------
  {
   $("#itemDiv").addClass("hidden");
   $("#itemTable tbody tr").remove();

   // remove measure options
   if ( $("#embedded").val() == 1 )
        $("#weightChoice option").remove();
   else $("#tblMeasures tbody tr").remove();

   $("#tbl_perServing tbody td").empty();
   $("h4#itemTitle").empty();
   $("h4#measure").empty();

     var ndb = $("#itemChooser option:selected").val();
     var itemName= $("#itemChooser option:selected").text();

     $.each(items, function (i, num)
     {
        if (num.itemNum == ndb)
        {
           $("h4#itemTitle").append("Nutrients per Gram of (USDA #" + " " + ndb + ")<br>" + itemName);
           return false;
        }
     });  // each item

    // item chosen, now get item nutrient info
    $("h4#measure").append("Grams per Unit of Measure");
    var url = "https://api.nal.usda.gov/ndb/reports/?ndbno="
    url += ndb + "&type=b&format=xml&api_key=itAP1bNbuIZHkOVrDJow1NsRoo0xFmuZOr9QhTUm";
    $.ajax({
       type: "GET",
       dataType: "xml",
       url: url,
       success: processXmlItem,
        error: function(xhr)
               {
                  alert("A server error occurred, could not retrieve information");
               }
    }); 
  });  // end btnChoose

    function processXmlItem(xml) 
    // --------------------------
    {
      var ndb = $(xml).find("food").attr("ndbno");
      var name = $(xml).find("food").attr("name");
      var servingSize = $("#serving").val();
      var nutrientCount =0;

      $("#itemDiv").removeClass("hidden");
      $("#tblMeasures").removeClass("hidden");
      $("#tbl_perServing").removeClass("hidden");
      $("#enterServing").removeClass("hidden");

      // get nutrients
      $(xml).find("nutrient").each(function()
      {
        var name = $(this).attr("name");
        var unit = $(this).attr("unit");
        var val = parseFloat($(this).attr("value")) * .01;
        var tot = val * servingSize;

        switch (name)
        {
          case "Water":
             ++nutrientCount;
             getMeasures($(this));  // only need to do this once
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             $("#tbl_perServing tbody tr" ).find('td:eq(0)').text(tot.toFixed(1));
             break;

          case "Energy":
             ++nutrientCount;
             name= "Calories";
             $("#tbl_perServing tbody tr" ).find('td:eq(1)').text(tot.toFixed(1));
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             break;

          case "Protein":
             ++nutrientCount;
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             $("#tbl_perServing tbody tr" ).find('td:eq(2)').text(tot.toFixed(1));
             break;

          case "Total lipid (fat)":
             ++nutrientCount;
             name= "Fat";
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             $("#tbl_perServing tbody tr" ).find('td:eq(3)').text(tot.toFixed(1));
             break;

          case "Carbohydrate, by difference":
             ++nutrientCount;
             name= "Carbs";
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             $("#tbl_perServing tbody tr" ).find('td:eq(4)').text(tot.toFixed(1));
             break;

          case "Fiber, total dietary":
             ++nutrientCount;
             name= "Fiber";
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             $("#tbl_perServing tbody tr" ).find('td:eq(5)').text(tot.toFixed(1));
             break;

          case "Sugars, total":
             ++nutrientCount;
             name= "Sugars";
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             $("#tbl_perServing tbody tr" ).find('td:eq(6)').text(tot.toFixed(1));
             break;

          case "Phosphorus, P":
             ++nutrientCount;
             name= "Phosphorus";
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             $("#tbl_perServing tbody tr" ).find('td:eq(7)').text(tot.toFixed(1));
             break;

          case "Potassium, K":
             ++nutrientCount;
             name= "Potassium";
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             $("#tbl_perServing tbody tr" ).find('td:eq(8)').text(tot.toFixed(1));
             break;

          case "Sodium, Na":
             ++nutrientCount;
             name= "Sodium";
             $("#itemTable tbody").append("<tr><td>" +name + "</td><td>" + val.toFixed(3) +"</td><td>" +unit+ "</td></tr>");
             $("#tbl_perServing tbody tr" ).find('td:eq(9)').text(tot.toFixed(1));
             break;

          default:
            break;
        }
      });  // find nutrient

     var missingStuff = false;
     if (nutrientCount < 10) missingStuff = true;

      $.each($("#tbl_perServing tbody tr").find("td"), function() 
      {
        if ($(this).text().length == 0) 
        {
           $(this).text("N/A");
           missingStuff = true;
       }
      });

      if ($("#embedded").val()==1) 
      {
         if (missingStuff) 
             alert("This item does NOT have all the nutrients available\nPlease try another item");
         else
             parent.$("#btnAddFood").prop('disabled',false)
      }
    } // processXmlItem


  function getMeasures(obj)
  //--------------------------
  {
    $("#measures").removeClass("hidden");

    $(obj).find("measure").each(function()
    {
          var label = $(this).attr("label");
          var eqv = $(this).attr("eqv");
          var qty = $(this).attr("qty");
          var measure = qty + " " + label;

          if ($("#embedded").val()==1)
          { // add option to select
            var val = eqv / qty;
            $("#weightChoice").append( '<option value=' + val + '>' + label + '</option>');
//            $("#weightChoice").append( '<option value=' + eqv + '>' + label + '</option>');
          }
          else
          { // add to table
            $("#tblMeasures tbody").append("<tr><td>" +measure + "</td><td>" + " " + eqv +"</td></tr>");
          }
    })
  }
});  // end on page loaded

</script>

