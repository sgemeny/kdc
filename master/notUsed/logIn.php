<!doctype html>
<?php
   session_start();

  $postIt = "../plugins/impromptu/jQuery-Impromptu-master/dist/jquery-impromptu.js";
  $validate = "../plugins/jquery-validate/dist/jquery.validate.js";

  // these must be at top of file for plugins to work
  require_once ( 'jquery.php' );
  echo '<script type="text/javascript" src="'.$validate.'"></script>';
  echo '<script type="text/javascript" src="'.$postIt.'"></script>';

  require_once ('dbConnect.php');
  require_once ('banner.php');
  require_once ('displayButtons.php');

  $conn = dbConnect();

  showBanner("");
  
  echo '<input type="hidden" name="subDir" id="subDir" value="' . $subDir .'" />';

?>
 <div id="page"> 
  <div id="content">
    <form action="" method="post" id="frmLogIn">   <!-- change this to POST, add a URL to action -->
      </div>
      <fieldset id="logInInfo">
        <legend>Please Log In</legend>
        <p id="waitBox" name="waitBox" class="hidden">Please Wait...Checking Password<br></p>
        <ul>
          <li>
            <label for="username">User Name</label>
            <input id="username" name="username" type="text" value="" maxlength="50">
          </li>

          <li>
            <label for="password">Password </label>
            <input name="password" type="password" id="password" minlength="8" maxlength="50">
          </li>

        </ul>
      </fieldset>

      <br>
      <input id="loginsubmit" name="loginup" type="submit" class="myButton" value="Log In...">
      <input id="btnGotoSignUp" name="btnGotoSignUp" type="button" class="myButton" value="Go To Sign Up">
    </form>
  </div> <!-- content -->
</div>  <!-- page -->

<script>
$(document).ready( function() 
{
  $.validator.setDefaults(    // this needs to be first! why?
  // ----------------------------
  {
    submitHandler: function()
    {
//      alert("submitted!");
      $("#waitBox").removeClass("hidden");
      var pword = JSON.stringify($("#signUpPword").val());
      var myData=[];
      myData ={ "userName" : $("#username").val()
              , "password" : $("#password").val()
              }
//console.log(myData);

      $.ajax (
      {
       url: "./verifyUser.php",
       type: "post",
       data: {"data" : JSON.stringify(myData)},
       success: function( data, status)  // callback
         {
           if (status == "success")
           {
             ndx = data.indexOf("false");
             if ( ndx > 0)
             {
                alert (data.substr(ndx+6));
                $("#waitBox").addClass("hidden");
             }
             else
             {
               var url = "../../starthere.php";
               document.location.href = url;
//               alert("success");
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
  });


  $.validator.addMethod("pwcheck", function(value)
  {
    if (/[~!@#$%^&*(()_+<>?]/.test(value))
    {
       if (/[A-Z]/.test(value))
       {
          if (/[a-z]/.test(value))
          {
            if (/\d/.test(value))
              return true;
          }
       }
    }
    return false;
  });



   // validate signup form on keyup and submit
   var validator = $("#frmLogIn").validate(
   {
     rules: 
     {  username: { required: true, minlength: 2 }
     ,  password: { required: true, minlength: 8, pwcheck: true }
     }    // rules

   , messages: 
     { username: 
         { required: "Enter a username"
         , minlength: jQuery.validator.format("Enter at least {0} characters")
         , remote: jQuery.validator.format("{0} is already in use")
         }
     , password: 
         { required: "Provide a password"
         , minlength: jQuery.validator.format("Enter at least {0} characters")
         , pwcheck: "Must contain at least 1 Upper, 1 Lower, 1 number and 1 Special Character"
         }
     } // messages

   , errorPlacement: function(error, element) 
     {
       error.appendTo(element.parent());
     }

     // set this class to error-labels to indicate valid fields
   , success: function(label) 
     {
       // set &nbsp; as text for IE
       label.html("&nbsp;").addClass("checked");
     }

   , highlight: function(element, errorClass) 
   {
     $(element).parent().next().find("." + errorClass).removeClass("checked");
   }
 });

                // propose username by combining first- and lastname
                $("#username").focus(function() {
                        var firstname = $("#firstname").val();
                        var lastname = $("#lastname").val();
                        if (firstname && lastname && !this.value) {
                                this.value = (firstname + "." + lastname).toLowerCase();
                        }
                });


  $( "#btnGotoSignUp" ).click(function( event )
  // ------------------------------------
  {
     var url = $("#subDir").val() + "includes/signUp.php"
      document.location.href = url;
  });

  $(btnLogOut).addClass("hidden");
  $("#frmLogIn").validate()

 }); // doc ready

</script>
</body>
</html>
