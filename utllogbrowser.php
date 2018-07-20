<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Log Viewer</title>
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bootstrap-datetimepicker.min.css" rel="stylesheet"> 
<link href="css/bs3dropdownsubmenus.css" rel="stylesheet">
</head>
<body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datetimepicker.min.js"></script>

<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
date_default_timezone_set('America/Los_Angeles');

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/mainmenu.inc.php';

//echo "<pre>"; print_r($_REQUEST); echo '</pre>';
$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-d 00:00:00', strtotime("now"));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d 23:59:59', strtotime("now"));

$filter = $_REQUEST['filter'];
$dbx = $_REQUEST['db'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

print <<<scriptForm
<script>
function chkform() {
  var d1 = $('#sd').val(); var d2 = $('#ed').val();
  if ((d1.length == 0) || (d2.length == 0)) {
    alert("Missing a start or end date.");
    return false;
    }
  var d1val = Date.parse(d1); var d2val = Date.parse(d2);
  if (d2val <= d1val) {
    alert("Invalid date range. End date/time before or same as start date/time.");
    return false;
    }
  return true;
  }
</script>
<script>
function setval() {
  //alert("setval entered");
  var v = $('#sd').val(); // set end date = start date
//  $('#ed').val(v);
  return true;
  }
</script>
<h3>Database Log Inspector</h3>
<form action="utllogbrowser.php" method="post" onsubmit="return chkform()">
Start: <input type="text" id="sd" name="sd" value="$sd" placeholder="YYYY-MM-DD HH:MM" onchange="return setval()">
End: <input type="text" id="ed" name="ed" value="$ed" placeholder="YYYY-MM-DD HH:MM">
<input autofocus type="text" id="filter" name="filter" value="" placeholder="Filter">
<input type="hidden" name="action" value="go">
<input type="submit" name="submit" value="submit">
</form>
<script type="text/javascript">
$('#sd').datetimepicker({
    format: 'yyyy-mm-dd hh:ii:ss',
    todayHighlight: true,
    // todayBtn: true,
    // showMeridian: true,
    minuteStep: 15,
    autoclose: true
});
</script>
<script type="text/javascript">
$('#ed').datetimepicker({
    format: 'yyyy-mm-dd hh:ii:ss',
    todayHighlight: true,
    // todayBtn: true,
    // showMeridian: true,
    minuteStep: 15,
    autoclose: true
});
</script>

scriptForm;

if ($action == '') {
  echo "<h3>Database Activity Log Viewer</h3>
<p>This utility allows the activity log of the either the mbr/vol database or the cts2 database to be examined.  These logs are where all database requests are recorded along with the date/time, the originating page address and userid performing the action.</p>
<p>All actions within the date/time range are listed.  If there is a search string entered, it will be used to filter those actions listing only those that have matching strings in the userid and log activity fields.</p>
<p>The date format will default to the system standard of &apos;YYYY-MM-DD HH:MM:SS&apos; for any date entered.  This will allow very narrow date/time ranges to be specific - right down to the second.</p>
<p>Default date range is from midnight of the current date to midnight of the date 30 days prior.</p>
</div>";  
exit;
  }
// do log lookup using db indicated
if (strlen($filter) > 0) $filter = "%$filter%";
else $filter = '%';
//echo "sd: $sd, ed: $ed<br>";
    
$sql = "SELECT * FROM `log` WHERE ( `DateTime` BETWEEN '$sd' AND '$ed' ) AND ( `User` LIKE '$filter' OR `Text` LIKE '$filter' OR `Page` LIKE '$filter' ) LIMIT 0,2000;";
//echo "sql: $sql<br />";
$res = doSQLsubmitted($sql);
$rowcount = $res->num_rows;
echo "Rows returned: $rowcount<br />";
if (!$rowcount) {
  echo "<br><b>No rows match criteria</b><br><br>===== END REPORT =====<br>";
  exit;
  }
$idx = 1;
while ($x = $res->fetch_assoc()) {
	//echo '<pre> Log record'; print_r($r); echo '</pre>';
	$resultrows[$x[LogID]] = $x; 
	$idx++;
	}
ksort($resultrows);
echo '<table width="100%" border="1" >';
echo '<tr><th>LogID</th><th>&nbsp;Date/Time&nbsp;</th><th>User Login</th><th>Ref Page</th><th>SQL submitted</th></tr>';
foreach ($resultrows as $k => $r) {
  echo "<tr><td>$r[LogID]</td><td>$r[DateTime]</td><td>$r[User]</td><td>$r[Page]</td><td>$r[Text]</td></tr>";
  }

echo '</table>===== END REPORT =====';
?>
<!-- </div> -->
</body>
</html>
