$(document).ready( function() {
// ----------------------------

  $("#btnLogOut").click(function(event)
  // ------------------------------------
  {
     var myData = { "userName" : $("#username").val() };

     $.ajax(
     {
       url: "./includes/logOut.php",
       type: "post",
       data: {"data" : JSON.stringify(myData)},
       success: function( data, status)  // callback
                {
                   if (status=="success")
                   {
                       alert("Your have successfully logged out.");
                       var url = $("#subDir").val() + "includes/logIn.php";
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
});  // doc ready
