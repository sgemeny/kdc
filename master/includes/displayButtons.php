<?php
   require_once("defines.php");
   require_once("logError.php");


function displayButtons($btns)
// -------------------------------------
{  // display button bar. Only show buttons requested in $btns
   echo '<div class="btnBar navigation" id="btnLine">';

   // member level overrides initState
   if ( $_SESSION["MEMBER_LEVEL"] >=4 ) $disabledFlag = "";
   else $disabledFlag="disabled";

   foreach ($btns as $btn => $caption)
   {
      switch ($btn)
      {
        case MENU:
          echo '<input name="btnMenu"
                 id="btnMenu"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case CHOOSE:
           echo '<input name="btnChoose"
                 id="btnChoose"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case SHOW:
           echo '<input name="btnShow"
                 id="btnShow"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case EDIT:
           echo '<input name="btnEdit"
                 '.$disabledFlag.'
                 id="btnEdit"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case ADD:
           echo '<input name="btnAdd"
                 '.$disabledFlag.'
                 id="btnAdd"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case SAVE:
           echo '<input name="btnSave"
                 id="btnSave"
                 class="myButton"
                 class="Button"
                 type="button"
                 value="'.$caption.'"
                 disabled
                 >';
                 break;

        case CANCEL:
           echo '<input name="btnCancel"
                 id="btnCancel"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 disabled
                 >';
                 break;

        case SIGNUP:
           echo '<input name="btnSignUp"
                 id="btnSignUp"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case LOGOUT:
           echo '<input name="btnLogIn"
                 id="btnLogOut"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case RECIPES:
           echo '<input name="btnRecipes"
                 '.$disabledFlag.'
                 id="btnRecipes"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case GROCERIES:
           echo '<input name="btnGroceries"
                 id="btnGroceries"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case TRACK:
           echo '<input name="btnTrack"
                 id="btnTrack"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case DAILYS:
           echo '<input name="btnDaily"
                 id="btnDaily"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case CHANGE:
           echo '<input name="btnChangeName"
                 id="btnChangeName"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case GOBACK:
           echo '<input name="btnGoBack"
                 id="btnGoBack"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case USDA:
           echo '<input name="btnUSDA"
                 id="btnUSDA"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;

        case MEMBER:
           echo '<input name="btnMember"
                 id="btnMember"
                 class="myButton"
                 type="button"
                 value="'.$caption.'"
                 >';
                 break;
      }
  }

   echo '<input type="hidden" name="cmd" id="btnCmd" value=' . CHOOSE . ' />';
  echo '</div>'; // end of btnLine
}




?>
