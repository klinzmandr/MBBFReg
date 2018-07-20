<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Comm Ctr Schedule</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
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
include 'Incls/listutils.inc.php';
include 'Incls/mainmenu.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$site = isset($_REQUEST['Site']) ? $_REQUEST['Site'] : "";

echo '<h3>Venue Schedule of Events</h3>';

if ($action == '') {
echo '
<script>
$(document).ready(function() {
  $("#Site").change ( function() {
    $("#FF").submit();
  });
});
</script>

<p>This report provides the scheduled actvities for the venue seleted.</p>
<p>Selection of the first selection item (a blank) will select all venues and list all events for each.</p>
<p>A download CSV file is created and is available with the same results as shown on the page except that the venue name is in column 1 of each row of the result.</p>
<p>Printing of the report is possible but should be done after doing a print preview and adjusting the print settings appropriately.</p>

<form id="FF" action="rptsitesched.php" method="post">
Site: 
<select id="Site" name="Site">';
echo '<option value=""></option>';
echo readlist('Site');
echo '</select>
<input type="hidden" name="action" value="genreport">
<!-- <button form="FF" class="btn btn-primary" type="submit">Generate Report</button> -->
</form>
</div> <!-- container -->
</body>
</html>
';
exit;
}
// echo '<h3>'.$site.'</h3>';
if ($site == '') $site = '%';
else list($site, $code) = explode(':', $site);
$sql = '
SELECT * FROM `events` 
WHERE `Site` LIKE "'.$site.'%" 
  AND `TripStatus` NOT LIKE "Delete" 
ORDER BY `Site` ASC, `Dnbr` ASC, `StartTime` ASC;';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

if ($rc == 0) {
  echo '<h3>No events found for site: '.$site.'</h3>';
  exit;
  }
// Day	Start Time	Event (count)
//  second line (hidden): Site Event  1 line per event
$venuearray = array();
while ($r = $res->fetch_assoc()) {
//  echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
  $venuecount[$r[Site]] += 1;
  $venuearray[$r[Site]][] = $r;
  }

//echo '<pre> venue '; print_r($venuearray); echo '</pre>';
$mask = '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>';
$csvmask = '"%s","%s","%s","%s","%s","%s"'."\n";
$csv = 'Site,Day,StartTime,Duration,Event,SiteRoom'."\n";

echo '
<a class="hidden-print" href="downloads/siteschedule.csv">DOWN LOAD RESULTS</a><span title="Download file with quoted values and comma separated fields" class="hidden-print glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px;"></span>';


foreach ($venuearray as $k => $v) {
  //echo "<pre> venue $k "; print_r($v); echo '</pre>';
  $ec = $venuecount[$k];
  echo '<h3>'.$k.' (Event Count: '.$ec.')</h3>
  <table class="table">
  <tr><th>Day</th><th>StartTime</th><th>Duration</th><th>Event</th><th>SiteRoom</th>';
  foreach ($v as $kk => $vv) {
    //echo "<pre> xxx $kk "; print_r($vv); echo '</pre>';
    //echo "Day: $vv[Day]<br>";
    $st = date("g:i A", strtotime($vv[StartTime]));
    $dur = timediff($vv[StartTime], $vv[EndTime]);
    printf($mask,$vv[Day],$st,$dur,$vv[Event],$vv[SiteRoom]);
    $csv .= sprintf($csvmask,$k,$vv[Day],$st,$dur,$vv[Event],$vv[SiteRoom]);
    }
  echo '</table>';
  }

//echo '<pre> csv file<br>'; print_r($csv); echo '</pre>';
file_put_contents("downloads/siteschedule.csv", $csv);

function timediff($start, $end) {
  $tp1val = strtotime($start);
  $tp2val = strtotime($end);
  $diff = $tp2val - $tp1val;
  $hrs = sprintf("%s", floor($diff/3600));   // diff in hours
  $mins = (($tp2val - $tp1val) - ($hrs * (60*60)))/60;   // diff in min
  if ($mins == 0) $fmtdiff = sprintf("%2d Hour(s)", $hrs); 
  else $fmtdiff = sprintf("%2d Hour(s) %2d Min", $hrs, $mins);
  return($fmtdiff);
  }

?>
</div> <!-- container -->
</body>
</html>