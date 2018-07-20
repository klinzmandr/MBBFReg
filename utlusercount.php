<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Page Use Summary</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
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

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/mainmenu.inc.php';

$dbinuse = "DB in use: mbbf_mbbfdb<br>";
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$sd = isset($_REQUEST['sd']) ? $_REQUEST['sd'] : date('Y-m-d 00:00:00', strtotime(now));
$ed = isset($_REQUEST['ed']) ? $_REQUEST['ed'] : date('Y-m-d 23:59:00', strtotime(now));
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '%mbbf/%';
$testdb = isset($_REQUEST['testdb']) ? $_REQUEST['testdb'] : '';

echo '
<script>
function chkform() {
  var d1 = $("#sd").val(); var d2 = $("#ed").val();
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
  var v = $("#sd").val(); // set end date = start date
//  $("#ed").val(v);
  return true;
  }
</script>

<h3>Report on Page Usage</h3>
<br>
<!-- <h4>Page useage selected: '.$type.'</h4> -->
<form name="inform" action="utlusercount.php" method="post">

Start: <input type="text" id="sd" name="sd" value="'.$sd.'" placeholder="YYYY-MM-DD HH:MM" onchange="return setval()">
End: <input type="text" id="ed" name="ed" value="'.$ed.'" placeholder="YYYY-MM-DD HH:MM">

<input type="hidden" name="action" value="continue">
<select name="type" onchange="this.form.submit()">
<option value="%%%">ALL</option >
<option value="%evt%">Events</option>
<option value="%ldr%">Leaders</option>
<option value="%rpt%">Reports</option>
<option value="%utl%">Utilities</option>
</select>
<input type="hidden" name="testdb" value="$testdb">
&nbsp;&nbsp;<input type="submit" name= "submit" value="Submit">
</form>
<script type="text/javascript">
$("#sd").datetimepicker({
    format: "yyyy-mm-dd hh:ii:ss",
    todayHighlight: true,
    // todayBtn: true,
    // showMeridian: true,
    minuteStep: 15,
    autoclose: true
});
</script>
<script type="text/javascript">
$("#ed").datetimepicker({
    format: "yyyy-mm-dd hh:ii:ss",
    todayHighlight: true,
    // todayBtn: true,
    // showMeridian: true,
    minuteStep: 15,
    autoclose: true
});
</script>

<p>This report examines the system log and counts all web pages that have been accessed for each user and groups them by date.</p>

';

// if ($testdb != '') echo "Using Test Database<br>";
if ($type == '') echo "<a href=\"rpt.php\">No page type selected - RETRY</a><br /><br />";

//echo "sd: $sd, ed: $ed<br />";
// generate the report
$msd = date('Y-m-d H:i:s', strtotime($sd)); $med = date('Y-m-d H:i:s', strtotime($ed));
// $sql = "SELECT `DateTime`,`User`,`Page`
$sql = "SELECT *
FROM `log`
WHERE  `DateTime` BETWEEN '$msd' AND '$med'
	AND `Page` LIKE '$type'
ORDER BY `DateTime` ASC;";

echo "sql: $sql<br />";
$rc = 0;
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
// echo "Total Pages Used: $rc<br />";
$whoarray = array();
while ($r = $res->fetch_assoc()) {
	// echo '<pre> row '; print_r($r); echo '</pre>';
	if ($r[User] == '') continue;
	// if ($r[Text] == 'Page Load') continue;       // ignore page loads
	list($u, $ip) = explode('@',$r[User]);
	$date = date('Y-m-d', strtotime($r[DateTime]));
	$whoarray[$date][$u] += 1;
	}

echo '<h3>User Count by Date</h3><ul>';
// echo '<pre>'; print_r($whoarray); echo '</pre>';
foreach ($whoarray as $d => $v) {
  echo "Date: $d<ul>";
  foreach ($v as $u => $vv) {
    echo "User: $u, $vv<br>";
    }
  echo "</ul>";
  }
echo "</ul>";

echo '<br>----- END OF REPORT -----<br><br>';
?>


</body>
</html>
