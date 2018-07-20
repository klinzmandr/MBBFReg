<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Event Listing</title>
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<link href="css/bs3dropdownsubmenus.css" rel="stylesheet">
</head>
<body>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/mainmenu.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$day = isset($_REQUEST['Day']) ? $_REQUEST['Day'] : "";

echo '
<div class="container">
<h3>Starter Event Listing (Download only)</h3>
<p>All scheduled events are listed in a download CSV spreadsheet file.</p>
';
// create report
echo '
<br>
<a class="hidden-print" href="downloads/starterlisting.csv">DOWN LOAD RESULTS</a><span title="Download file with quoted values and comma separated fields" class="hidden-print glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px;"></span>
</div>
';

//Type	Trip	TypeOfEvent	Day	StartTime	EndTime	Event	Site	SiteRoom	MaxAttendees	Leader1	Leader2	Leader3	Leader4

$sql = '
SELECT * FROM `events` 
WHERE 1=1
  AND `TripStatus` NOT LIKE "Delete" 
ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC;';
$day = 'ALL';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
//echo '<h3>Start Event Listing</h3>Event count: '.$rc.'<br>';
// Trip, Start, End, Type, Event, Site, Leader1, Leader2, Leader3, Travel, Attend

$mask = '
<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>';
$csvmask = '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s",%s'."\n";
$csv = 'Trip, Start, End, Type, Event, Site, SiteRoom, Leader1, Leader2, Leader3, Leader4, Travel, Attend'."\n";

//echo '<table class="table" border=0>
//<tr><th>Trip</th><th>Start</th><th>End</th><th>Type</th><th>Event</th><th>Site</th><th>SiteRoom</th><th>Leader1
//</th><th>Leader2</th><th>Leader3</th><th>Leader4</th><th>Travel</th><th>Attend</th></tr>';

// convert trip type to equivalent code
$sc = readlistreturnarray("TripTypeCodes");

while ($r = $res->fetch_assoc()) {
  $st = date("g:iA", strtotime($r[StartTime]));
  $et = date("g:iA", strtotime($r[EndTime]));
  // printf($mask,$r[Trip],$st,$et,$sc[$r[Type]],$r[Event],$r[SiteCode],$r[Leader1],$r[Leader2],$r[Leader3],$r[Leader4],$r[Transportation],$r[MaxAttendees]);
  $csv .= sprintf($csvmask,$r[Trip],$st,$et,$sc[$r[Type]],$r[Event],$r[SiteCode],$r[SiteRoom],$r[Leader1],$r[Leader2],$r[Leader3],$r[Leader4],$r[Transportation],$r[MaxAttendees]);
  
  //echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
  }
//echo '</table>';

// echo "<pre> csv \n"; print_r($csv); echo '</pre>';
file_put_contents("downloads/starterlisting.csv", $csv);

?>
</body>
</html>