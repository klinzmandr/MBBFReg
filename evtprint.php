<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Event Display</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet">
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

// form to allow callback parameters using form method 'post'
echo '
<form id="RETURN" action="evtlister.php" method="post">
<input type="hidden" name="day" value="'.$day.'">
<input type="hidden" name="et" value="'.$et.'">
<input type="hidden" name="ss" value="'.$ss.'">
';

echo '
<h1>Event Display
<button class="btn btn-primary" type="submit" form="RETURN">RETURN</button>
</h1>
';

$sql = '
SELECT * FROM `events` WHERE `RowID` = '.$rowid.';';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

//echo '<h2>Column Names dump</h2>';
//$cols = $res->fetch_fields();
//colcount = $res->fetch_count;
//echo '<pre> all fields records '; print_r($cols); echo '</pre>';
//while ($c = $res->fetch_fields()) {
//  echo '<pre> field record '; print_r($c); echo '</pre>';
//  echo 'name: '.$c[name].'<br>';
//}

echo '<h2>Record dump</h2>';
$r = $res->fetch_assoc();
echo '<pre> full record '; print_r($r); echo '</pre>';


?>
</div> <!-- container -->
</body>
</html>