  function verifyNumber(num, callback)
  // ------------------------------------
  { // check for valid number
    if ($.isNumeric(num)) return true;
    alert("ERROR  Please enter a valid number");
    return false;
/********************
      $.prompt( "Please enter a valid number",
               {
                 html: "hello",
                 title: "ERROR!!",
                 persistent: false,
                 focus: 1,
//                 submit:function(e,v,m,f)
//                 {
//                   verifyDone(callback);
//                 }

               }
              );
********************/
  }

  function verifyDone(obj)
  // --------------------
  {
//    obj.focus();
//    return false;
  }

  function numberWithCommas(num)
  // ----------------------------
    {
    var parts = num.toString().split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
  }

  function removeCommas(num)
  // ----------------------------
  {
    if (num === undefined || num === null) 
    {
       alert("num is undefined or null");
       var obj = {};
       Error.captureStackTrace(obj);
       console.log(obj);
    }
    return num.replace(/,/g, '');
  }




/***********************************************
function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}

pad("3", 3);    // => "003"
pad("123", 3);  // => "123"
pad("1234", 3); // => "1234"

var test = "MR 2";
var parts = test.split(" ");
parts[1] = pad(parts[1], 3);
parts.join(" "); // => "MR 002"
***********************************************/
