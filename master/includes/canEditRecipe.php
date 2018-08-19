<?
  require_once("jquery.php");
?>

<script>

 // determine if user is allowed to edit
 // a specific recipe

 function checkIfCanEdit()
 // ----------------------
 {
    var chosenRecipe = $("#recipeChoice").val();
    var owner = $("#owner").val();
    var user = $("#userID").val();

    if (owner == user)
    {
       canEdit=1;
       $("#btnEdit").prop('disabled',false);
    }
    else
    {
      canEdit=0;
      $("#btnEdit").prop('disabled', true);
    }
    return canEdit;
 }
</script>

