<?php
  session_start();

  // required for PHP < 5.5
  require ("bcrypt.php");

  require_once ('dbConnect.php');
  $conn = dbConnect();

  if (isset($_POST["data"]))
  {
    $stripped = stripslashes($_POST["data"]);
    $data = json_decode($_POST["data"]) ;

    $firstName = $data->firstName;
    $lastName = $data->lastName;
    $userName = $data->userName;
    $email = $data->email;

    $bcrypt = new Bcrypt(15);
    $hashAndSalt = $bcrypt->hash($data->password);

    date_default_timezone_set('US/Eastern');

    // Is user already in use?
    $sql = "SELECT ID FROM users WHERE userName = ? LIMIT 1;";
    if (($stmt = $conn->prepare($sql)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmt, 's', $userName))
      { // execute
        if (mysqli_stmt_execute($stmt) )
        {
          if (mysqli_stmt_bind_result($stmt, $id) )
          {
            if (mysqli_stmt_fetch($stmt))
            {  // user already exists
               echo "false This user name is in use";
               return false;
            } // fetch
            // fetch failed: user name does not exist
          } // bind_result
        } // execute
      }  // bind_param
      // close stmt
      mysqli_stmt_close($stmt);
    } // stmt prepare

    // User name is avaialble, Is email already in use?
    $sql = "SELECT email FROM users WHERE email = ? LIMIT 1;";
    if (($stmt = $conn->prepare($sql)))
    { // bind input
      if ( mysqli_stmt_bind_param($stmt, 's', $email))
      { // execute
        if (mysqli_stmt_execute($stmt) )
        { // bind output
          $mail="";
          if (mysqli_stmt_bind_result($stmt, $mail) )
          {
            if (mysqli_stmt_fetch($stmt))
            {  // email already exists
               echo "false This email is in use";
               return false;
            } // fetch
            // fetch failed: email name does not exist, is available to use
          } // bind_result
        } // execute
      }  // bind_param
      // close stmt
      mysqli_stmt_close($stmt);
    } // stmt prepare

   // userName & email are available, so add the user
   $timestamp = date('Y-m-d G:i:s');
   $endDate = date_create($timestamp);
   date_add($endDate, date_interval_create_from_date_string('1 year'));
   $endDate = date_format($endDate, 'Y-m-d H:i:s');

   $sql = "INSERT INTO users VALUES (NULL,?,?,?,?,?,'1','$timestamp', '$endDate')";
   if (($stmt = $conn->prepare($sql)))
    { // bind input 
      if ( mysqli_stmt_bind_param($stmt, 'sssss', $userName, $hashAndSalt, $firstName, $lastName, $email))
      { // execute
        if (mysqli_stmt_execute($stmt) )
        { 
          $newId = mysqli_insert_id($conn);
          echo json_encode($newId);
          return true;
        } //execute
      } // bind_param
    // close stmt
    mysqli_stmt_close($stmt);
    } // prepare
    return false;
  } // post data


/*************
  $password="hello";
  $hashAndSalt = password_hash($password, PASSWORD_DEFAULT );
  echo "hashAndSalt is: " . $hashAndSalt;
  if (password_verify($password, $hashAndSalt))
  {
     echo "<br>password verified!";
     // is verified password
  }
*************/
     
?>

