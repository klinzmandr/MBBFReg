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
'.$dbinuse.'<br>
<!-- <h4>Page useage selected: '.$type.'</h4> -->
<form name="inform" action="utlpagesum.php" method="post">

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

<p>This report peruses the system log and isolates all web pages that have been used and summaries their usage by user.</p>

';

if ($testdb != '') echo "Using Test Database<br>";
if ($type == '') echo "<a href=\"rpt.php\">No page type selected - RETRY</a><br /><br />";

//echo "sd: $sd, ed: $ed<br />";
// generate the report
$msd = date('Y-m-d H:i:s', strtotime($sd)); $med = date('Y-m-d H:i:s', strtotime($ed));
$sql = "SELECT `DateTime`,`User`,`Page`
FROM `log`
WHERE  `DateTime` BETWEEN '$msd' AND '$med'
	AND (`Text` LIKE 'Page%' OR `Text` LIKE 'INSERT%')
	AND `Page` LIKE '$type'
ORDER BY `DateTime` ASC;";

//echo "sql: $sql<br />";
$rc = 0;
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
// echo "Total Pages Used: $rc<br />";
$whatarray = array(); $whoarray = array(); $whotimemin = array(); $whotimemax = array();
while ($r = $res->fetch_assoc()) {
//	echo '<pre> rows '; print_r($r); echo '</pre>';
	$exprpt = explode('/', $r[Page]);
	$exprpt[0] = $r[User];			// debug
//	echo '<pre> exprpt '; print_r($exprpt); echo '</pre>';
	if ($r[User] == '') continue;
	$rpt = end($exprpt);
	$rpt = strtolower($rpt);
//	echo "rpt: $rpt<br>";
	$whatarray[$rpt] += 1;
	$whoarray[$r[User]] += 1;
	if (($r[DateTime] < $whotimemin[$r[User]]) OR (!isset($whotimemin[$r[User]]))) 
		$whotimemin[$r[User]] = $r[DateTime]; 
	if ($r[DateTime] > $whotimemax[$r[User]]) $whotimemax[$r[User]] = $r[DateTime];
	$comboarray[$rpt] [$r[User]] += 1;
	$combotimearray[$rpt][$r[User]] = $r[DateTime];
	$usercountarray[$r[User]] += 1;
	$userarray[$r[User]] [$rpt] += 1;
//	echo '<pre> reports '; print_r($whoarray); echo '</pre>';
	}
// echo "Total Pages: " . count($whoarray) . "$rc<br />";
// echo '<pre> User start '; print_r($whotimemin); echo '</pre>';
// echo '<pre> User end '; print_r($whotimemax); echo '</pre>';	
echo '<table><tr><td valign="top"><h4>Pages Most Used</h4><ul>';
ksort($whatarray);
foreach ($whatarray as $k => $v) {
	echo "$k: $v<br />";
	}
echo '</ul>';
echo '</td><td valign="top"><h4>Page Users</h4><ul>';
ksort($whoarray);
foreach ($whoarray as $k => $v) {
	echo "$k: $v<br />&nbsp;&nbsp;(first: $whotimemin[$k], last: $whotimemax[$k])<br />";
	}
echo '</ul>';
echo '</td></tr><tr><td valign="top"><h4>Pages By User</h4><ul>';
if (count($comboarray) > 0) ksort($comboarray);
if (count($comboarray) > 0) foreach ($comboarray as $k => $v) {
	echo "$k<br /><ul>";
	ksort($v);
	foreach ($v as $kk => $vv) {
		echo "$kk -> $vv<br />";
		}
	echo '</ul>';
	}
echo '</ul>';
echo '</td><td valign="top"><h4>Users By Page:</h4><ul>';
if (count($userarray) > 0) ksort($userarray);
if (count($userarray) > 0) foreach ($userarray as $k => $v) {
	echo "$k->$usercountarray[$k]<br /><ul>";
	ksort($v);
	foreach ($v as $kk => $vv) {
		echo "$kk -> $vv, Last @ " . $combotimearray[$kk][$k] . "<br />";
		}
	echo '</ul>';
	}
echo '</td></tr></table>';

// create list of all users logged in
$sql = "SELECT *
FROM `log`
WHERE  `DateTime` BETWEEN '$msd' AND '$med'
	AND `Text` LIKE 'Logg%'
	AND `Page` LIKE '%mbbf%' 
ORDER BY `DateTime` ASC;";

// echo "sql: $sql<br />";
$rc = 0;
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
// echo "Total Pages Used: $rc<br />";

while ($r = $res->fetch_assoc()) {
//	echo '<pre> logged in '; print_r($r); echo '</pre>';
	if ($r[User] == '') continue;	
	$usr = $r[User];
//	echo '<pre> whotimemax '; print_r($whotimemax); echo '</pre>';
	$lasttimedate = $whotimemax[$usr];
	$lasttime = strtotime($whotimemax[$usr]) + 30*60;
	$now = strtotime(now);
//	echo "lasttimedate: $lasttimedate, lasttime: $lasttime, NOW: $now<br>";
//	echo "lasttime: $lasttime, lastdate: $whotimemax[$r[User]]<br>";
//	echo "now: $now, last: $lasttime<br>";
	if ($now > $lasttime) continue;
	
	if ($r[Text] == "Logged In") {
		$user[$r[User]] = $r;
		}
	if ($r[Text] == "Logged Out") {
		unset($user[$r[User]]);		
		}
	}
	
//	echo '<pre> user '; print_r($user); echo '</pre>';
//	echo '<pre> addr '; print_r($addr); echo '</pre>';
if (count($user) > 0) {
//	echo '<pre> addr '; print_r($addr); echo '</pre>';
	echo '<h4>Current Active Users:</h4>';
	foreach ($user as $k => $v) {
		$u = $v[User]; $s = $v[SecLevel]; $d = $v[DateTime];
		echo "
		$u at $d on $a<br>
		";
		}
	echo '</ul>';
	}
echo '<br>----- END OF REPORT -----<br><br>';
?>


</body>
</html>
