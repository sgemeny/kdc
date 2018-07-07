<!DOCTYPE html>
<?php
   session_start();

  $subDir = $_SESSION["SUBDIR"];

  require_once ('jquery.php');
  $postIt = "../plugins/impromptu/jQuery-Impromptu-master/dist/jquery-impromptu.js";
  $validate = "../plugins/jquery-validate/dist/jquery.validate.js";
  echo '<script src="'.$postIt.'"></script>';
  echo '<script src="'.$validate.'"></script>';

  require_once ('dbConnect.php');
  require_once ('banner.php');
  require_once ('displayButtons.php');

  $conn = dbConnect();
  showBanner("");
 echo '<input type="hidden" name="subDir" id="subDir" value="' . $subDir .'" />';

 echo '<div id="page">';
  echo '<div id="content">';
    echo '<form action="" method="post" id="frmSignUp">';
      echo '<fieldset>';
        echo '<p id="waitBox" name="waitBox" class="hidden">Please Wait...Checking Password<br></p>';
        echo '<legend>User details</legend>';
        echo '<ul>';
          echo '<li>';
            echo '<label for="firstname">First Name</label>';
            echo '<input id="firstname" name="firstname" type="text" value="" maxlength="100">';
          echo '</li>';

          echo '<li>';
            echo '<label for="lastname">Last Name</label>';
            echo '<input id="lastname" name="lastname" type="text" value="" maxlength="100">';
          echo '</li>';

          echo '<li>';
            echo '<label for="username">User Name</label>';
            echo '<input id="username" name="username" type="text" value="" maxlength="50">';
          echo '</li>';

          echo '<li>';
            echo '<label for="password">Password </label>';
            echo '<input name="password" type="password" id="password" minlength="8" maxlength="50">';
          echo '</li>';

          echo '<li>';
            echo '<label for="password_confirm">Confirm Password</label>';
            echo '<input name="password_confirm" type="password" id="password_confirm" minlength="8" maxlength="50">';
          echo '</li>';

          echo '<li>';
            echo '<label for="email">Email address </label>';
            echo '<input id="email" id="email" name="email" type="text" value="" maxlength="150">';
          echo '</li>';

        echo '</ul>';
      echo '</fieldset>';

      echo '<br>';
      echo '<input id="signupsubmit" name="signup" type="submit" class="myButton" value="Sign Up...">';
      echo '<input id="btnGotoLogIn" name="btnGotoLogIn" type="button" class="myButton" value="Go To Log In">';
    echo '</form>';
  echo '</div>'; // content
echo '</div>';  // page
?>

<script>

$(document).ready( function()
{
  $.validator.setDefaults(    // this needs to be first! why?
  // ----------------------------
  {
    submitHandler: function()
    {
      $("#waitBox").removeClass("hidden");
      var pword = JSON.stringify($("#signUpPword").val());
      var myData=[];
      myData ={ "firstName" : $("#firstname").val()
              , "lastName" : $("#lastname").val()
              , "userName" : $("#username").val()
              , "password" : $("#password").val()
              , "email" : $("#email").val()
              }
//console.log(myData);

      $.ajax (
      {
       url: "./addUser.php",
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
               var userID =  $.parseJSON(data);
               var url = "logIn.php";
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
   var validator = $("#frmSignUp").validate(
   {
     rules:
     {
        firstname: { required: true, minlength: 2 }
     ,  lastname: { required: true, minlength: 2 }
     ,  username: { required: true, minlength: 2 }
     ,  password: { required: true, minlength: 8, pwcheck: true }
     ,  password_confirm: { required: true, minlength: 8, equalTo: "#password" }
     ,  email: { required: true, email: true }
     }    // rules

   , messages:
     {
       firstname: { required: "Enter your firstname"}
     , lastname: {required: "Enter your lastname"}
     , username:
         { required: "Enter a username"
         , minlength: jQuery.validator.format("Enter at least {0} characters")
         , remote: jQuery.validator.format("{0} is already in use")
         }
     , password:
         { required: "Provide a password"
         , minlength: jQuery.validator.format("Enter at least {0} characters")
         , pwcheck: "Must contain at least 1 Upper, 1 Lower, 1 number and 1 Special Character"
         }
     , password_confirm:
         { required: "Repeat your password"
         , minlength: jQuery.validator.format("Enter at least {0} characters")
         , equalTo: "Enter the same password as above"
         }
     , email:
         { required: "Please enter a valid email address"
         , minlength: "Please enter a valid email address"
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


/********************
  $( "#btnSignUp" ).click(function( event )
  // ------------------------------------
  {
//alert("signup clicked");
    $("#page").removeClass("hidden");
    $("#btnLine").addClass("hidden");
  });


  $( "#btnLogIn" ).click(function( event )
  // ------------------------------------
  {
//    var url = "includes/logIn.php";
    var url = "logIn.php";
    document.location.href = url;
  });
********************/

  $( "#btnGotoLogIn" ).click(function( event )
  // ------------------------------------
  {
    var url = "../logIn.php";
    document.location.href = url;
  });

  $(btnLogOut).addClass("hidden");
  $("#frmSignUp").validate()

 }); // doc ready
</script>


  </div> <!-- end of container (started in banner.php) -->
</body>
</html>;




