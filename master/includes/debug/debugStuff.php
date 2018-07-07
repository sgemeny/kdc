<?php
  require_once ($baseDir . '/includes/debug/dumpIt.php');

  // Open Debug File
  function openDebugFile($caller)
  // ----------------------------------------
  {
    if (DEBUG==1)
    {
      date_default_timezone_set('US/Eastern');

      $fd=fopen("/tmp/myErrors.log", 'a');  
      if (!$fd)  exit("Error Opening Error Log file\n");
      fwrite($fd, "**** " . date('Y-m-d') . "  " . date('g:i A') . "  " . $caller . " ****\n"); 
      return ($fd);
    }
  }


  // Close debug file
  function closeDebug($fd)
  // ---------------------------
  {
    if (DEBUG==1)
    {
      fclose($fd);
    }
  }

  // write debug stuff to my debug file
  function debugWrite($fd, $msg)
  // ----------------------------------------
  {
    if (DEBUG==1)
    {
      fwrite($fd, $msg . "\n");
    }
  }


// php error log stuff
// error_log("hello " 3, "/home/sue/myErrors.log");  // debug

?>

 
