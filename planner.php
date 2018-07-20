<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>MBWBF Demo</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>
<h2>Plan Your Trip: Search Event Listings</h2>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.planner.inc.php';
include 'Incls/listutils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$day = isset($_REQUEST['Day']) ? $_REQUEST['Day'] : "";
$et = isset($_REQUEST['Type']) ? $_REQUEST['Type'] : "";
$ss = isset($_REQUEST['ss']) ? $_REQUEST['ss'] : '';   // event search string

echo'
<script type="text/javascript">
// set up select lists
$(document).ready(function () { 
	//alert("first the inline function");
//	$("#SS").val("'.$ss.'");
	$("#Day").val("'.$day.'");
	$("#Type").val("'.$et.'");
	});
</script>
<script type="text/javascript">
function resetflds() { 
	$(":input").val("");
	return false;
}
</script>

<h3>Select one or more selection criteria and continue:</h3> 
<form id="f1" action="planner.php" method="post">
Day: 
<select id="Day" name="Day">';
echo readlist('Day');
echo '</select>
&nbsp;

Trip Type: 
<select id="Type" name="Type">';
echo readlist('TripType');
echo '</select>
<input id="SS" type=text value="'.$ss.'" name="ss" placeholder="Search" title="Enter a single word or short character string to search all program descriptions.">&nbsp;
<input type=hidden name=action value="list">
<button class="btn btn-primary" type="submit" form="f1">SEARCH EVENTS</button>
<button class="btn btn-warning" onclick="return resetflds()">Clear Form</button>
';

// Process listing based on selected criteria
$sql = '
SELECT * FROM `events` 
WHERE `TripStatus` NOT LIKE "Delete" AND ';
$sqllen = strlen($sql);
if (strlen($day) > 0) { 
  $sql .= '`Day` LIKE "%'.$day.'%" AND '; }
if (strlen($et) > 0) {
  $sql .= '`Type` LIKE "%'.$et.'%" AND '; }
if (strlen($ss) > 0) {
  $sql .= '
    (`Program` LIKE "%'.$ss.'%" 
    OR `Type` LIKE "%'.$ss.'%" 
    OR `Event` LIKE "%'.$ss.'%" 
    OR `Trip` LIKE "%'.$ss.'%" 
    OR `Leader1` LIKE "%'.$ss.'%" 
    OR `Leader2` LIKE "%'.$ss.'%"
    OR `Leader3` LIKE "%'.$ss.'%"
    OR `Leader4` LIKE "%'.$ss.'%") AND '; }

if (strlen($sql) == $sqllen) {      // no criteria entered
  echo '
<h4 style="color: red; ">Please provide criteria for search.</h4>
</div> <!-- contianer -->
</body>
</html>';
  exit;
  }

$sql = substr($sql,0,-5);       // trip trailing 5 char's
$sql .= ' ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC';
$sql .= ';';

// echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

echo '<h3>Events meeting selected criteria</h3>
<p>Events selected: '.$rc.'.  Click on the title for more details regarding that event.</p>
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';


while ($r = $res->fetch_assoc()) {
//  echo '<pre> full record '; print_r($r); echo '</pre>';
  if ($r[FEE] == '') $r[FEE] = 'No Charge';
  else $r[FEE] = '$'.$r[FEE];  
  $r[StartTime] = date("g:i A", strtotime($r[StartTime]));
  $r[EndTime] = date("g:i A", strtotime($r[EndTime]));
  echo '
<div class="panel panel-default">
<div class="panel-heading" role="tab" id="heading'.$r[RowID].'">
<h4 class="panel-title">
<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$r[RowID].'" aria-expanded="false" aria-controls="collapse'.$r[RowID].'">
  Event '.$r[Trip].' '.$r[Event].'
</a>
</h4>
</div> <!-- panel-eading -->
<div id="collapse'.$r[RowID].'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading'.$r[RowID].'">
<div class="panel-body">
<table>
<tr>
<td>Event Type: '.$r[Type].'</td>
<td>Event Day: '.$r[Day].'</td>
<td>Event Hours: '.$r[StartTime].' to '.$r[EndTime].'</td>
</tr>
<tr>
<td>Guide/Speaker: '.$r[Leader1].'</td>
<td>FEE: '.$r[FEE].'</td>
<td>Site: '.$r[Site].'</td>
</tr>
<tr><td colspan=3 border=1>'.$r[Program].'</td></tr>
</table>
</div> <!-- panel-body -->
</div> <!-- panel-collapse collapse -->
</div> <!-- panel panel-default -->
';
}

echo '
</div> <!-- panel panel-default -->
';

?>
</div> <!-- panel-group -->
</body>
</html>