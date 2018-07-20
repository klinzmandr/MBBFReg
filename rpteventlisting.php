<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Event Listing</title>
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
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/mainmenu.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$day = isset($_REQUEST['Day']) ? $_REQUEST['Day'] : "";

echo '<h3>Event Listing</h3>';

if ($action == '') {
  echo '
  <p>A listing of specific events.</p>
  <p>All scheduled events will be listed if no selection is made.</p>
  <p>A download CSV file is created and is available with the same results as shown on the page except that the venue name is in column 1 of each row of the result.</p>
<p>Printing of the report is possible but should be done after doing a print preview and adjusting the print settings appropriately.</p>

<script>
$(document).ready (function() {
  $("#Day").change (function() {
    $("#FF").submit();  
    });
  });

</script>
<form id="FF" action="rpteventlisting.php" method="post">
Day: 
<select id="Day" name="Day">
<option value=""></option>';
echo readlist('Day');
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

// create report
echo '
<a class="hidden-print" href="downloads/eventlisting.csv">DOWN LOAD RESULTS</a><span title="Download file with quoted values and comma separated fields" class="hidden-print glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px;"></span>';

//Type	Trip	TypeOfEvent	Day	StartTime	EndTime	Event	Site	SiteRoom	MaxAttendees	Leader1	Leader2	Leader3	Leader4

$sql = '
SELECT * FROM `events` 
WHERE `Day` =   "'.$day.'"
  AND `TripStatus` NOT LIKE "Delete" 
ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC;
';
if ($day == '') { 
$sql = '
SELECT * FROM `events` 
WHERE 1=1
  AND `TripStatus` NOT LIKE "Delete" 
ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC;';
$day = 'ALL';
}

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
echo '<h3>Listing for Day: '.$day.'</h3>row count: '.$rc.'<br>';
$mask = '
<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>';
$csvmask = '"%s","%s","%s","%s",%s,%s,"%s","%s","%s",%s,"%s","%s","%s","%s"'."\n";
$csv = 'Type,Trip,TypeOfEvent,Day,StartTime,EndTime,Event,Site,SiteRoom,MaxAttendees,Leader1,Leader2,Leader3,	Leader4'."\n";

echo '<table class="table">
<tr><th>Type</th><th>Trip</th><th>Type</th><th>Day</th><th>StartTime</th><th>EndTime</th><th>Event</th><th>Site</th><th>SiteRoom</th><th>MaxAttendees</th><th>Leader1</th><th>Leader2</th><th>Leader3</th><th>Leader4</th></tr>';

while ($r = $res->fetch_assoc()) {
  $st = date("g:i A", strtotime($r[StartTime]));
  $et = date("g:i A", strtotime($r[EndTime]));
  printf($mask,$r[Type],$r[Trip],$r[TypeOfEvent],$r[Day],$st,$et,$r[Event],$r[Site],$r[SiteRoom],$r[MaxAttendees],$r[Leader1],$r[Leader2],$r[Leader3],$r[Leader4]);
  $csv .= sprintf($csvmask,$r[Type],$r[Trip],$r[TypeOfEvent],$r[Day],$st,$et,$r[Event],$r[Site],$r[SiteRoom],$r[MaxAttendees],$r[Leader1],$r[Leader2],$r[Leader3],$r[Leader4]);
  
//  echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
  }
echo '</table>';

// echo "<pre> csv \n"; print_r($csv); echo '</pre>';
file_put_contents("downloads/eventlisting.csv", $csv);

?>
</body>
</html>