<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Event Maintenance</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<link href="css/bs3dropdownsubmenus.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<div class="container">
<img src="http://morrobaybirdfestival.net/wp-content/uploads/2016/08/LOGO3.png" alt="bird festival logo" >
<?php
// session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/checkcred.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

// process logout
if ($action == 'logout') {
  addlogentry("Logged Out");
  unset($_SESSION['SessionUser']);
  session_unset();    
  //session_destroy();
  //session_start(); 
  $action = '';
  }
  
// process login info validation
if ($action == 'auth') {
  $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";
  $pw = isset($_REQUEST['pw']) ? $_REQUEST['pw'] : "";
  $combo = $uid . ':' . $pw;
  // echo "combined: $combo<br>";
  $pwds = file('../.MBBFSecFile.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if (!in_array($combo, $pwds)) {
    echo '<h3 style="color: red; ">Invalid user id and/or password.</h3>';
    session_unset();    
    //session_destroy();
    //session_start(); 
    $action = '';
    }
  else {
    // echo 'user id and password valid<br>';
    $_SESSION['SessionUser'] = $uid;
    addlogentry("Logged In");
    $action = '';
    }
  }   

// output menu if session var is loaded
if (isset($_SESSION['SessionUser'])) {
  $_SESSION[LAST_ACTIVITY] = $_SERVER['REQUEST_TIME'];
  $start = date('l, F j, Y', strtotime(geteventstart()));
    //echo "start: $start<br>";

include 'Incls/mainmenu.inc.php';

  echo '
<h2>Event Administraton System&nbsp;&nbsp;<a href="index.php?action=logout" class="btn btn-danger">Log Out</a>
</h2>
<div class="well">
<h4>GPL License</h4>
<p>Event Administration System - Copyright (C) 2017 by Pragmatic Computing, Morro Bay, CA</p>
    <p>This program comes with ABSOLUTELY NO WARRANTY.  This is free software.  It may be redistributed under certain conditions.  See &apos;Help->About Event Admin&apos; for more information.</p>
</div>
</body>
</html>';
exit;
} 

// display login page to get userid and pw
if ($action == '') {
  echo '
<div clas="container">
<h1>Event Maintenance System</h1>
<h3>Please provide login information:</h3>
<form action="index.php" method="post"  id="login">
<input type="text" name="uid"  placeholder="User Name" autofocus>
<input type="text" name="pw"  placeholder="Password">
<input type="hidden" name="action" value="auth">
<button name="sb" type="submit" form="login">LOG IN</button>
</form>
<br><br>
<div class="well">
<h4>GPL License</h4>
<p>Event Maintenance System -- Copyright (C) 2013 by Pragmatic Computing, Morro Bay, CA</p>
    <p>This program comes with ABSOLUTELY NO WARRANTY.  This is free software.  It may be redistributed under certain conditions.  See <a href="LICENSE.pdf" target="_blank" title="Software License">this PDF of the GNU Public License</a> for more information.</p>
</div>
';
}

?>
</div> <!-- container -->
</body>
</html>