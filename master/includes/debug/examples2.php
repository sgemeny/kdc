<?php
  dumpCookies();

  define("CHOOSE", 1);
  define("SHOW", 1<<1);
  define("EDIT", 1<<2);
  define("ADD", 1<<3);
  define("SAVE", 1<<4);
  define("CANCEL", 1<<5);

  echo "<br>";
  echo "CHOOSE=" . CHOOSE . "<br>";
  echo "SHOW=" . SHOW . "<br>";
  echo "EDIT=" . EDIT . "<br>";
  echo "ADD=" . ADD . "<br>";
  echo "SAVE=" . SAVE . "<br>";
  echo "CANCEL=" . CANCEL . "<br>";
  echo "<br>";

  echo "---------The Following is all PHP--------------";
  $json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

  var_dump(json_decode($json));
  echo "<br>";
  echo "<br>";
  var_dump(json_decode($json, true));
  echo "<br>";
  echo "<br>";

  $arr = '{ "ID" : 121, "Seq" : 234, "Qty" :  345 }';
  $detail = json_decode($arr);
  var_dump($detail);
  echo "<br>";

  $id = $detail->ID;
  $seq = $detail->Seq;
  echo "id= " . $id . "<br>";
  echo "seq= " . $seq;
  echo "<br>";
  echo "<br>";

  echo '<input id=btnDo type="button" value="Do It">';
  echo "<br>";
  echo "<br>";

  $arr = array("one", "two", "three"); 
  foreach ($arr as $key => $value) 
  {
    echo "Key: $key; Value: $value<br />\n";
  }
  echo "<br>";

  echo '<select id="state">';
  echo '<option value="state1">Nevada</option>';
  echo '<option value="state2">New Jersey</option>';
  echo '<option value="state3">New York</option>';
  echo '<option value="state4">North Carolina</option>';
  echo '</select>';

  echo '<input type="text" id="newState" value="New Mexico"></input>';
  echo '<input id=btnDoAgain type="button" value="Insert New State">';
  echo "<br>";


  // dump $_COOKIE Variables
function dumpCookies()
//--------------------
{
  echo "<br>------------------<br>";
  echo "<pre>";
    echo "_COOKIE Array<br>";
    print_r($_COOKIE);
  echo "</pre>";
echo "<br>------------------<br>";
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>

$(document).ready( function() {
// ----------------------------

  $("#btnDoAgain").click(function(event)
  // ------------------------------------
  {
     var newItem = $("#newState").val();

     $("#state option").each(function(ndx, option)
     {
          alert (ndx + ": " + option.text + "\n");
        if ( option.text >= newItem)
        { // insert new item here
   newOption = $('<option value="newState">' + newItem + '</option>');
   $("#state option").eq(ndx).before(newOption);
//  var newOption = $('<option value="' + ndx + '">' + newItem + '</option>');
   z=1;
            return false;
        }
   y=1;
            
     });
  });

  $("#btnDo").click(function(event)
  // ------------------------------------
  {
    // this works
    var arr = { "ID" : 121, "Seq" : 234, "Qty" :  345 };
    var myData = JSON.stringify(arr);
    console.log(arr);
    console.log(myData);
    var myData =  {items: [ ]};

    myData.items.push( {id: "1", name: "Snatch", type: "crime"});
    myData.items.push( {id: "2", name: "Witches of Eastwick", type: "comedy"});
    myData.items.push( {id: "3", name: "X-Men", type: "action"});
    myData.items.push( {id: "4", name: "Ordinary People", type: "drama"});
    myData.items.push( {id: "5", name: "Billy Elliot", type: "drama"});
    myData.items.push( {id: "6", name: "Toy Story", type: "children"});
    myData.items.push({ id: x, name: z, type: "comedy"}); 
     var mData = JSON.stringify(myData.items);
    console.log(mData);

    var data = {items: [
    {id: "1", name: "Snatch", type: "crime"},
    {id: "2", name: "Witches of Eastwick", type: "comedy"},
    {id: "3", name: "X-Men", type: "action"},
    {id: "4", name: "Ordinary People", type: "drama"},
    {id: "5", name: "Billy Elliot", type: "drama"},
    {id: "6", name: "Toy Story", type: "children"} ]};

    data.items.push({id: "7", name: "Douglas Adams", type: "comedy"}); 
     var jData = JSON.stringify(data);
    console.log(jData);

    var data2= JSON.stringify(data.items);
    console.log(data2);
  });

});  // end on page loaded
</script>
