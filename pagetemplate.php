<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Page Template</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
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

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : "1";
$day = isset($_REQUEST['day']) ? $_REQUEST['day'] : ""; // event day of week
$et = isset($_REQUEST['et']) ? $_REQUEST['et'] : "";    // event type
$ss = isset($_REQUEST['ss']) ? $_REQUEST['ss'] : '';    // event search string

echo '
<h1>Event Display
<a href="evtlister.php?$day='.$day.'&et='.$et.'&ss='.$ss.'" class="btn btn-primary">RETURN</a></h1>
';

$sql = '
SELECT * FROM `events` WHERE `RowID` = '.$rowid.';';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
if ($rc == 0) {
  echo '<h4>Row '.$rowid.' not found</h4>';
  exit;
  }

$r = $res->fetch_assoc();
echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';

?>
</div> <!-- container -->
</body>
</html>