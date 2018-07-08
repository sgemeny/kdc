  var selectList = [];
  var inputList = [];
  var origSelectValues = [];
  var origInputValues = [];

  // When document ready
  // ----------------------
  $(document).ready(function(){
     // Remember objects' original values

alert("Doc Ready");
     selectList = document.getElementsByTagName("select");
     inputList = document.getElementsByTagName("input");

     for (var i=0; i<selectList.length; i++)
     {
       origSelectValues[i]= selectList[i].value;
     }

     for (var i=0; i<inputList.length; i++)
     {
       if (inputList[i].type == "text")
       {
           origInputValues[i] = inputList[i].value;
       }
       else
       {
	  origInputValues[i] = null;
       }
     }

  alert("hello");
     for (var i=0; i<origSelectValues.length; i++)
     {
	alert("select " + i + ": " + origSelectValues[i]);
     };

/*************** 
     experimenting with ways to reference objs within a div section
     currently doesn't work
     var container = getElementById("divInput");
     var list = conatiner.document.getElementsByTagName("input");
     alert("list length= " + list.length);
***************/

  } );  // function on page loaded

/*******		need to make this work yet
  function checkDirty()
  {
     var dirtyList = document.getElementsByClassName("data-dirty");
     var dirtyValues = new Array();
     alert("dirty list length= " + dirtyList.length);

     for (var i=0; i<dirtyList.length; i++)
     {
       if(dirtyList.type== "text")
       {
          dirtyValues[i]= dirtyList[i].value;
     }

     for (var i=0; i<inputList.length; i++)
     {
       if (inputList[i].type == "text")
       {
           origInputValues[i] = inputList[i].value;
       }
       else
       {
	  origInputValues[i] = null;
       }
     }
   }
  }
**************/

  // JavaScript func for "On Change" event
  // Set object as "dirty"
  // ----------------------
  function setDirty(obj)
  {
   alert("Got HERE");
     obj.className = obj.className + " data-dirty";
     dirty = $(obj).hasClass("data-dirty");

   alert("obj dirty: " + dirty + " type: " obj.type);
  }

  // Check select object as "dirty"
  // ----------------------
  function checkSelected(sel)
  {
      var value = sel.value;
     dirty = $(sel).hasClass("data-dirty");
      alert("Selected value= " + value + dirty);
  }

  // Reset Button clicked
  // ---------------------
  function resetIt()
  {
    alert("reset!!");
  }

  // Save button clicked
  //-----------------
  function saveIt()
  {
    // The data rows have a class name, use that to get num rows
    var numRows = document.getElementsByClassName("rowData");  // 8
    alert("numRows  has " + numRows.length + " rows");

  /***********
  This works, but it includes the header row as well.
    var cells=document.getElementById("tblRecipe").rows[0].cells.length;  // 4
    var rows2 = document.getElementById("tblRecipe").rows.length;	  // 9
    alert("There are " + cells + " data cells  and " + rows2 + "rows" );
  ***********/
  }

