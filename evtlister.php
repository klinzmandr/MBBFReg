<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Update Lister</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
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
include 'Incls/listutils.inc.php';
include 'Incls/mainmenu.inc.php';

echo ' 
<h3>Event List</h3>
';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$day = isset($_REQUEST['day']) ? $_REQUEST['day'] : ""; // event day of week
$et = isset($_REQUEST['et']) ? $_REQUEST['et'] : "";    // event type
$ss = isset($_REQUEST['ss']) ? $_REQUEST['ss'] : '';    // event search string
$site = isset($_REQUEST['Site']) ? $_REQUEST['Site'] : '';
$transportneeded = isset($_REQUEST['TransportNeeded']) ? $_REQUEST['TransportNeeded'] : '';
$tripstatus = isset($_REQUEST['TripStatus']) ? $_REQUEST['TripStatus'] : '';
$typeofevent = isset($_REQUEST['TypeOfEvent']) ? $_REQUEST['TypeOfEvent'] : '';

// process delete action
if ($action == 'delete') {
  $rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
	// echo "delete $rowid requested<br>";
	$sql = "DELETE FROM `events` WHERE `RowID` = '$rowid';";
	$rc = doSQLsubmitted($sql);		// returns affected_rows for delete
	if ($rc > 0) {
		  echo '
<script>
$(document).ready(function() {
  $("#X").fadeOut(2000);
});
</script>
<h3 style="color: red; " id="X">Event successfully deleted.</h3>
'; 
  }

	else 
		echo "Error on delete of event $rowid<br>";
	}

echo'
<script>  
$(function(){
   $("#btnMORE").click(function() {
    $("#btnMORE").toggle();
    $(".HIDE").toggle();
   });
   $("#btnLESS").click(function() {
    $(".HIDE").toggle();
    $("#btnMORE").toggle();
   });

});
</script>
<script type="text/javascript">
// set up select lists
$(document).ready(function () { 
	//alert("start field initialization");
	$("#S2").val("'.$et.'");
	var et = "'.$et.'";
	if (et == "%") $("#S2").val("");
	$("#S1").val("'.$day.'");
	$("#SS").val("'.$ss.'");
	$("#Site").val("'.$site.'");
	$("#TransportNeeded").val("'.$transportneeded.'");
	$("#TripStatus").val("'.$tripstatus.'");
	$("#TypeOfEvent").val("'.$typeofevent.'");
  $(".HIDE").hide();
  //alert("end field initialization");
  });
</script>
<script type="text/javascript">
// reset all select input fields to null
function resetflds() { 
	$(":input").val("");
	return false;
}
</script>

<h4 class="hidden-print">Select from list or enter selection criteria to search: </h4>
<form class="hidden-print" id="F1" action="evtlister.php" method="post">
<select id="S1" name=day>';
echo readlist('Day');
echo '</select>&nbsp;

<select id="TripStatus" name="TripStatus">';
echo readlist('TripStatus');
echo '</select>

<input id="SS" type=text value="" name=ss placeholder="Search Filter" title="Enter a single word or short character string to search all program descriptions.">&nbsp;
<input type=hidden name=action value="list">
<button class="btn btn-primary" type="submit" form="F1" data-toggle="tooltip" data-placement="left" title="Search for % to list all">SEARCH</button>
<button class="btn btn-warning" onclick="return resetflds()">Reset</button>

<div class="HIDE">

<select id="Site" name="Site">';
echo readlist('Site');
echo '</select>

<select id="TypeOfEvent" name="TypeOfEvent">';
echo readlist('TypeOfEvent');
echo '</select>

<br><button id="btnLESS">LESS</button>
</form>
</div>
<button class="hidden-print" id="btnMORE">MORE</button>
';
// Process listing based on selected criteria
$sql = '
SELECT * FROM `events` WHERE ';
$sqllen = strlen($sql);
if (strlen($day) > 0) { 
  $sql .= '`Day` LIKE "%'.$day.'%" AND '; }
if (strlen($et) > 0) {
  $sql .= '`Type` LIKE "%'.$et.'%" AND '; }
if (strlen($site) > 0) {
  list($s,$c) = explode(':', $site);
  $sql .= '`Site` LIKE "'.$s.'" AND '; }
if (strlen($transportneeded) > 0) {
  $sql .= '`TransportNeeded` = "'.$transportneeded.'" AND '; }
if (strlen($tripstatus) > 0) {
  $sql .= '`TripStatus` = "'.$tripstatus.'" AND '; }
if (strlen($typeofevent) > 0) {
  $sql .= '`TypeOfEvent` = "'.$typeofevent.'" AND '; }
if (strlen($ss) > 0) {
  $sql .= '
  (`Program` LIKE "%'.$ss.'%" 
  OR `Type` LIKE "%'.$ss.'%" 
  OR `Event` LIKE "%'.$ss.'%" 
  OR `Trip` LIKE "%'.$ss.'%" 
  OR `Leader1` LIKE "%'.$ss.'%" 
  OR `Leader2` LIKE "%'.$ss.'%"
  OR `Leader3` LIKE "%'.$ss.'%"
  OR `Leader4` LIKE "%'.$ss.'%"
  OR `MultiEventCode` LIKE "%'.$ss.'%"
  OR `SecondaryStatus` LIKE "%'.$ss.'%") AND ';
  }

if (strlen($sql) == $sqllen) {      // no criteria entered
  echo '
<h4 style="color: red; ">No criteria entered for search.</h4>
</div> <!-- contianer -->
</body>
</html>';
  exit;
  }

if (strlen($tripstatus) == 0) 
  $sql .= '`TripStatus` NOT LIKE "Delete" AND ';

$sql = substr($sql,0,-5);       // trip trailing 5 char's
$sql .= ' ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC';
$sql .= ';';

//echo "<br>sql before submit: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

echo '
<h4>Events Listed&nbsp;&nbsp;(Listed: '.$rc.')

<table border="1" class="table table-condensed table-hover">
<tr><th><font size="0">Day</font></th><th><font size="0">TripStatus</font></th><th><font size="0">Trip Type</font></th><th><font size="0">StartTime</font></th><th><font size="0">Event</font></th><th><font size="0">Site</font></th><th><font size="0">EventTitle</font></th><th><font size="0">Leader</font></th></tr>';
$navarray = array(); $var = array(); $ptr = 0;
while ($r = $res->fetch_assoc()) {
  //if ($r['Type'] == '**New**') continue;
  $navarray[] = $r[RowID];
//  echo '<pre> full record '; print_r($r); echo '</pre>';
//  echo '
//<tr onclick=window.location="evtupdateevent.php?ptr='.$ptr.'" style="cursor: pointer;">
echo "<tr onclick=\"window.location='evtupdateevent.php?ptr=$ptr'\" style='cursor: pointer;'>";
$stime = ($r[StartTime] != '') ? date("g:i A", strtotime($r[StartTime])) : '';
echo '
<td><font size="0">'.$r[Day].'</font></td>
<td><font size="0">'.$r[TripStatus].'</font></td>
<td><font size="0">'.$r[Type].'</font></td>
<td><font size="0">'.$stime.'</font></td>
<td><font size="0">'.$r[TypeOfEvent].'</font></td>
<td><font size="0">'.$r[Site].'</font></td>
<td><font size="0">'.$r[Trip].'&nbsp;'.$r[Event].'</font></td>
<td><font size="0">'.$r[Leader1].'</font></td>
</tr>
';
$ptr += 1;
}
echo '</table>';

$nav['start'] = 0; $nav['prev'] = ''; $nav['curr'] = '';
$nav['next'] = ''; $nav['last'] = count($navarray) - 1;

$_SESSION['navarray'] = $navarray;
$_SESSION['nav'] = $nav;

?>
</body>
</html>