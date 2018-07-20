<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>User Administration</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet">
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
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include 'Incls/datautils.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/checkcred.inc.php';

if ( !checkcred('Admin') ) {
//  echo "pw passed<br>";
  echo 'Incorrect password entered for administrative access.<br>
  <a href="utlindex.php" class="btn btn-danger">RETURN</a>';
  exit;
  }

echo '
<h3>Administer Users and Passwords</h3>
';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$ta = isset($_REQUEST['ta']) ? $_REQUEST['ta'] : "";

if ($action == 'update') {
  echo '
<script>
$(document).ready(function() {
  $("#X").fadeOut(2000);
});
</script>
<h3 style="color: red; " id="X">Update Completed.</h3>
';
  file_put_contents('../.MBBFSecFile.txt', $ta);
  $log = file('../.MBBFSecFile.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  $logstr = implode(', ',$log);
  addlogentry('Admin file updated: '.$logstr);
  }

$pwds = file_get_contents('../.MBBFSecFile.txt');

echo '
<form id="UPD" action="utladmin.php" method="post">
<textarea name="ta" rows="20" cols="80">'.$pwds.'</textarea><br>
<input type="hidden" name="action" value="update">
<button form= "UPD" class="btn btn-success" type="submit">Apply Update(s)</button>
</form>
';
?>
</div> <!-- container -->
</body>
</html>