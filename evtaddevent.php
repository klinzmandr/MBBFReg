<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Add New Event</title>
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
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/mainmenu.inc.php';

echo '
<h1>New Event Added</h1>
<p>A new event record has been added into the database.  This new record will be presented for updating all specific fields.</p>
<p>By default, the "Trip Status" field is set to "Delete" and the "Trip Number" field is set to 999 to facilitate searching for an event that has not yet been fully completed.</p>
';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

$updarray = array();
$updarray['Event'] = '**New Event**';
$updarray['TripStatus'] = 'Delete';
$updarray['Trip'] = '999';

// check if one already exists
$sql = "SELECT * FROM `events` WHERE `Event` LIKE '**New%';";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
$rc = $res->num_rows;
if ($rc == 1) {
//  echo 'new one exists<br>';
//  echo '<pre> NEW '; print_r($r); echo '</pre>';
  $rowid = $r[RowID];
//  echo '<h3>Search for the character string &quot;**New&quot; to display the new record for updates.</h3>
//  <a href="evtlister.php?ss=**New" class="btn btn-success">CONTINUE TO UPDATE NEW EVENT</a>';
echo '<a href="evtupdateevent.php?ptr=0" class="btn btn-success">CONTINUE TO UPDATE NEW EVENT</a>';

$navarray[] = $rowid;
$nav['start'] = 0; $nav['prev'] = ''; $nav['curr'] = '';
$nav['next'] = ''; $nav['last'] = count($navarray) - 1;

$_SESSION['navarray'] = $navarray;
$_SESSION['nav'] = $nav;
  
  
  exit;
  }

// one doesn't exist so create one now
sqlinsert('events', $updarray);

// read the new one to get the new row number
$sql = "SELECT * FROM `events` WHERE `Type` LIKE '**New%';";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
$rc = $res->num_rows;
//  echo '<pre> NEW '; print_r($r); echo '</pre>';
$rowid = $r[RowID];
//echo '<h3>Search for the character string &quot;**New&quot; to display the new record for updates.</h3>
//<a href="evtlister.php?ss=**New" class="btn btn-success">CONTINUE TO UPDATE NEW EVENT</a>';
echo '<a href="evtupdateevent.php?ptr=0" class="btn btn-success">CONTINUE TO ADD A NEW EVENT</a>';

$navarray[] = $rowid;
$nav['start'] = 0; $nav['prev'] = ''; $nav['curr'] = '';
$nav['next'] = ''; $nav['last'] = count($navarray) - 1;

$_SESSION['navarray'] = $navarray;
$_SESSION['nav'] = $nav;

?>
</body>
</html>