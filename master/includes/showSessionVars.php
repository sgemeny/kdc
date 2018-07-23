<?php

  dumpSessionVars();

  echo "ALL DONE<br>";

// dump $_SESSION Variables
function dumpSessionVars()
//---------------------
{
  echo "<br>------------------<br>";
  echo "<pre>";
    echo "_SESSION Array<br>";
    print_r($_SESSION);
  echo "</pre>";
echo "<br>------------------<br>";
}

