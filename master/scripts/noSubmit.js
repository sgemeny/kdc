
// jQuery- Stop the "Enter" key from submitting a form.
//
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>

$(document).ready( function() {
// ----------------------------
  function noSubmit(field, event){
      var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
    if (keyCode == 13) {
      var i;
      for (i = 0; i < field.form.elements.length; i++)
        if (field == field.form.elements[i])
          break;
        i = (i + 1) % field.form.elements.length;
        field.form.elements[i].focus();
        return false;
    }
    else
      return true;
  }
});  // end on page loaded
</script>

