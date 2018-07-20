<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Add New Leader</title>
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

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/mainmenu.inc.php';

echo '
<h3>New Leader Added</h3>';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

$updarray = array();
$updarray['Active'] = 'Yes';
$updarray['FirstName'] = '**New**';

// check if one already exists
$sql = "SELECT * FROM `leaders` WHERE `FirstName` = '**New**';";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
$rc = $res->num_rows;
if ($rc == 1) {
//  echo 'new one exists<br>';
//  echo '<pre> Just created '; print_r($r); echo '</pre>';
  $rowid = $r[RowID];
//  echo '<a href=ldrupdate.php?rowid='.$rowid.' class="btn btn-success">CONTINUE TO UPDATE NEW LEADER</a>';
  echo '<a href="ldrupdate.php?lptr=0" class="btn btn-success">CONTINUE TO UPDATE NEW LEADER</a>';
  $navarray[] = $rowid;
  $nav['start'] = 0; $nav['prev'] = ''; $nav['curr'] = '';
  $nav['next'] = ''; $nav['last'] = count($navarray) - 1;

  $_SESSION['lnavarray'] = $navarray;
  $_SESSION['lnav'] = $nav;
  exit;
  }

// one doesn't exist so create one now
sqlinsert('leaders', $updarray);

// read the new one to get the new row number
$sql = "SELECT * FROM `leaders` WHERE `FirstName` = '**New**';";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
$rc = $res->num_rows;
// echo '<pre> Exiting New '; print_r($r); echo '</pre>';
$rowid = $r[RowID];
//echo '<a href=ldrupdate.php?rowid='.$rowid.' class="btn btn-success">CONTINUE TO UPDATE NEW LEADER</a>';
echo '<a href="ldrupdate.php?lptr=0" class="btn btn-success">CONTINUE TO UPDATE NEW LEADER</a>';
$navarray[] = $rowid;
$nav['start'] = 0; $nav['prev'] = ''; $nav['curr'] = '';
$nav['next'] = ''; $nav['last'] = count($navarray) - 1;

$_SESSION['lnavarray'] = $navarray;
$_SESSION['lnav'] = $nav;

?>
</div> <!-- container -->
</body>
</html>