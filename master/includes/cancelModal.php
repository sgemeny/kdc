<?php

  // <!-- Modal -->';
  echo '<div id="cancelModal" class="modal fade" role="dialog">';
    echo '<div class="modal-dialog">';

      // <!-- Modal content-->
      echo '<div class="modal-content">';
        echo '<div class="modal-header">';
          echo '<h4 class="modal-title">WARNING!</h4>';
        echo '</div>'; // modal header

        echo '<div class="modal-body">';
          echo '<p>Are You Sure You Want to Leave This Page?<br><br>Your Changes Will Be Lost</p>';
        echo '</div>'; // modal body

        echo '<div class="modal-footer">';
          echo '<button type="submit" class="btn btn-danger btn-default pull-left" id="btnNo" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Yes, Lose Changes</button>';
          echo '<button type="button" class="btn btn-success" id="btnYes" data-dismiss="modal">No, Stay on Page</button>';
        echo '</div>'; // modal footer

      echo '</div>';  //  <!-- modal content -->
    echo '</div>';  //  <!-- modal dialog -->
  echo '</div>';  //  <!-- cancelModal -->
?>


<script>
  // One of these variables must be set
  // and one must be empty for function to work
  var formName="";
  var pageName="";


  $(document).ready( function()
  // ----------------------------
  {
    $('#btnNo').click(function(e)
    // ------------------------------------
    { // lose changes, leave without saving
      $('#canclModal').modal('toggle');
      e.preventDefault();
      zeroDirty();
      if (pageName=="")
//          formName.submit();
         document.location.href = pageName;
      else 
         document.location.href = pageName;
      a=1;
    });

    $('#btnYes').click(function()
    // ------------------------------------
    { // stay on page, keep changes
      $('#cancelModal').modal('toggle');
    });
  });
	</script>

