<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
  box-sizing: border-box;
}

#myInput {
  background-image: url('/css/searchicon.png');
  background-position: 10px 12px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myUL {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

#myUL li a {
  border: 1px solid #ddd;
  margin-top: -1px; /* Prevent double borders */
  background-color: #f6f6f6;
  padding: 12px;
  text-decoration: none;
  font-size: 18px;
  color: black;
  display: block
}

#myUL li a:hover:not(.header) {
  background-color: #eee;
}
</style>
</head>
<body>

<h2>My Phonebook</h2>

<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." title="Type in a name">
<ul id="myUL">
  <li><a href="#">Adele</a></li>
  <li><a href="#">Agnes</a></li>

  <li><a href="#">Billy</a></li>
  <li><a href="#">Bob</a></li>

  <li><a href="#">Calvin</a></li>
  <li><a href="#">Christina</a></li>
  <li><a href="#">Cindy</a></li>
</ul>


<script>
function myFunction() {
    var input, filter, ul, li, a, i;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

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
     $("#itemChooser option").each(function(ndx, optn)
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
           $("#itemChooser option").eq(ndx).hide();
     }); // each option
  }


</script>

</body>
</html>

