<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Event Log Viewer</title>
<!-- Bootstrap -->
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<link href="css/bs3dropdownsubmenus.css" rel="stylesheet">
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet"> 
</head>
<body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datetimepicker.min.js"></script>
<script src="js/jsutils.js"></script>

<?php
// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/checkcred.inc.php';
include 'Incls/mainmenu.inc.php';

$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-d H:i', strtotime('-12 hours'));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d 23:59', strtotime("now"));
$search = isset($_REQUEST['search']) ? $_REQUEST['search'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
?>
<h3>Event Monitor Log Viewer&nbsp;&nbsp;<i id=helpbtn title="Help information" class="fa fa-info-circle fa-1x" style="color: blue;"></i></h3>
<div id=help>
<p>This utility allows the event monior activity to be examined.  This log is where all event monitor requests are recorded along with the date/time.</p>
<p>All actions within the date/time range are listed.  If there is a search string entered, it will be used to filter those actions listing only those that have matching strings in the userid and log activity fields.</p>
<p>The date format will default to the system standard of 'YYYY-MM-DD HH:MM' for any date entered.  This will allow very narrow date/time ranges to be specific - right down to the minute.</p>
<p>Default date range is the last hour from the current date and time.</p>
</div>  <!-- help -->

<form action="admevtlogviewer.php">
Start: <input type="text" id="sd" name="sd" value="<?=$sd?>" placeholder="YYYY-MM-DD HH:MM">
End: <input type="text" id="ed" name="ed" value="<?=$ed?>" placeholder="YYYY-MM-DD HH:MM">
Search (optional)<input type="text" name="search" value="<?=$search?>">
<input type="hidden" name="action" value="search">
<input type="submit" name="submit" value="Submit">
</form>

<script type="text/javascript">
$('#sd').datetimepicker({
    format: 'yyyy-mm-dd hh:ii',
    todayHighlight: true,
    // todayBtn: true,
    // showMeridian: true,
    minuteStep: 15,
    autoclose: true
});

$('#ed').datetimepicker({
    format: 'yyyy-mm-dd hh:ii',
    todayHighlight: true,
    // todayBtn: true,
    // showMeridian: true,
    minuteStep: 15,
    autoclose: true
});
</script>

<?php
if ($action == 'search') {
	$search = $_REQUEST['search'];
	if (strlen($search) > 0) $searchstr = "AND `Text` LIKE '%$search%'";
	else $searchstr = '';

	$sql = "
SELECT * FROM `log` 
WHERE ( `DateTime` BETWEEN '$sd' AND '$ed' )
  AND ( `Text` NOT LIKE 'Page Load' )
  AND ( `User` LIKE 'EvtMon' $searchstr )
ORDER BY `DateTime` DESC LIMIT 0,1000;
";
	echo "sql: $sql<br />";
	$res = doSQLsubmitted($sql);
	$rowcount = $res->num_rows;
	echo "Rows returned: $rowcount<br />";
	echo '<table class="table table-condensed" border="1" >';
	echo '<tr><th>Date/Time</th><th>EvtMon Action</th></tr>';
	while ($r = $res->fetch_assoc()) {
		//echo '<pre> Log record'; print_r($r); echo '</pre>';
		$seclevel = $r[SecLevel];
		//echo "seclevel: $seclevel<br />";
		echo "<tr><td width='20%'>$r[DateTime]</td><td>$r[Text]</td></tr>";
		}
	echo '</table>';
	}
?>
<a class='btn btn-primary btn-success' href='admevtlogviewer.php?sd=<?=$sd?>&ed=<?=$ed?>'>New Search</a>
</body>
</html>
