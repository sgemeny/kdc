<?php

  // <!-- Modal -->';
  echo '<div id="dateModal" class="modal fade" role="dialog">';
    echo '<div class="modal-dialog">';

      // <!-- Modal content-->
      echo '<div class="modal-content">';
        echo '<div class="modal-header">';
          echo '<h4 class="modal-title">WARNING!</h4>';
        echo '</div>'; // modal header

        echo '<div class="modal-body">';
//          echo '<p><br>Changes have been made<br> Do you want save your changes?</p>';
          echo '<h2>Changes have been made</h2> <h2>Do you want to go back & save your changes?</h2>';
        echo '</div>'; // modal body

        echo '<div class="modal-footer">';
          echo '<button type="submit" class="btn btn-danger btn-default pull-left" id="btnLose" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> NO, Lose Changes</button>';
          echo '<button type="button" class="btn btn-success" id="btnContinue" data-dismiss="modal">Yes, Save Changes</button>';
        echo '</div>'; // modal footer

      echo '</div>';  //  <!-- modal content -->
    echo '</div>';  //  <!-- modal dialog -->
  echo '</div>';  //  <!-- dateModal -->
?>


<script>
  // One of these variables must be set
  // and one must be empty for function to work
  var formName="";
  var pageName="";


  $(document).ready( function()
  // ----------------------------
  {
  });
</script>

