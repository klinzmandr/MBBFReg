<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Program Extract</title>
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

echo '<h3>Program Extract</h3>';

if ($action == '') {
  echo '
<p>Extract of specific information from all registered events.</p>
<p>Rows of this report are available to download as a CSV file for spreadsheet import.</p>
<p>By default all events will be listed but events for a specific day can also optionally be selected from the selection list.</p>
<script>
$(document).ready (function() {
  $("#Day").change( function() {
    $("#FF").submit();
    });
  });

</script>
<form id="FF" action="rptprogramextract.php" method="post">
Day: 
<select id="Day" name="Day">
<option value=""></option>';
echo readlist('Day');
echo '</select>
<input type="hidden" name="action" value="genreport">
<!-- <button form="FF" class="btn btn-primary" type="submit">Generate Report</button> -->
</form>
</body>
</html>';
exit;
  }

if ($day == '') $day = "%";  
$sql = '
SELECT * FROM `events` 
WHERE `Day` LIKE "'.$day.'" 
  AND `TripStatus` NOT LIKE "Delete" 
ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC;';

// echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

// Day	Trip	TimeSpan	CODE	TypeOfEvent  Event	
$mask = '<tr><td>%s</td><td>%s</td><td>%s-%s</td><td>%s</td><td>%s</td><td>%s</td></tr>
';

$csv = "Day,Trip,StartTime,EndTime,Codes,TypeOfEvent,Event\n";
$csvmask .= '"%s","%s","%s","%s","%s","%s","%s"' ."\n";

echo '<a class="hidden-print" href="downloads/leaderinfo.csv">DOWN LOAD RESULTS</a><span title="Download file with quoted values and comma separated fields" class="hidden-print glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px;"></span>';
echo '<table border="1" class="table">';
echo '<tr><th>Day</th><th>Trip</th><th>Time Span</th><th>Code(s)</th><th>TypeOfEvent</th><th>Event</th></tr>';

while ($r = $res->fetch_assoc()) {
  //echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
  $st = date("g:i A", strtotime($r[StartTime]));
  $et = date("g:i A", strtotime($r[EndTime]));
      
  printf($mask,$r[Day],$r[Trip],$st,$et,$r[Level],$r[TypeOfEvent],$r[Event]);
  
  $csv .= sprintf($csvmask,$r[Day],$r[Trip],$st,$et,$r[Level],$r[TypeOfEvent],$r[Event]);
  }
echo '</table>';
file_put_contents("downloads/programextract.csv", $csv);

// echo '<pre> csv '; print_r($csv);
?>
</body>
</html>