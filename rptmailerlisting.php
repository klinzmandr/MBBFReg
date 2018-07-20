<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Mailer Listing</title>
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
include 'Incls/mainmenu.inc.php';
//include 'Incls/listutils.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

echo '

';

if ($action == '') {
  echo '
<div class="container">
<h3>Event Mailer Listing and Extract</h3>
<p>This report duplicates the columns and layout contained in the event mailer.</p>
<p>A download CSV file is created and is available with the same results as shown on the page except that the venue name is in column 1 of each row of the result.</p>
<p>Printing of the report is possible but should be done after doing a print preview and adjusting the print settings appropriately.</p>
<a href="rptmailerlisting.php?action=genreport" class="btn btn-primary">Generate Report</a>
</div>';

exit;
  }
// create report
echo '
<h3>Event Mailer Listing and Extract</h3>
<a class="hidden-print" href="downloads/mailerlisting.csv">DOWN LOAD RESULTS</a><span title="Download file with quoted values and comma separated fields" class="hidden-print glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px;"></span>';

//Trip	StartTime-EndTime	Codes  Events


//$sql = 'SELECT * FROM `events` WHERE 1=1 LIMIT 0,10;';
//$sql = 'SELECT * FROM `events` WHERE 1=1;';
$sql = '
SELECT * FROM `events` 
WHERE 1=1  
  AND `TripStatus` NOT LIKE "Delete" 
ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC;
';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$mask = "<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>";
$html = '';
$csv[] = "Trip, TypeOfEvent, Time, CODES, Event\n";
echo '<table class="table">
<tr><th>#</th><th>TypeOfEvent</th><th>Time</th><th>CODES</th><th>Event</th></tr>';

while ($r = $res->fetch_assoc()) {
  //echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
  $codes = array();
  if (stripos($r[Type], 'n/f') > 0) $codes[] = 'P-F';
  else $codes[] = substr($r[Type],0,1);
  if (strlen($r[SiteCode]) > 0) $codes[] = $r[SiteCode];
  if (strlen($r[Level]) > 0) $codes[] = str_replace(",","",$r[Level]); 
  if (strlen($r[FEE]) > 0)  $codes[] = '$'.$r[FEE];
  $codeout = implode('/',$codes);
  $st = date('h:ia', strtotime($r[StartTime]));
  $et = date('h:ia', strtotime($r[EndTime]));
  $timerange = $st.'-'.$et; 
  printf($mask, $r[Trip], $r[TypeOfEvent], $timerange, $codeout, $r[Event]);
  $csv[] .= "$r[Trip], $r[TypeOfEvent], $timerange, $codeout, \"$r[Event]\"\n";
  }
echo '</table>';
// echo "<pre> csv \n"; print_r($csv); echo '</pre>';
file_put_contents("downloads/mailerlisting.csv", $csv);
// echo '<a href="rptmailerlisting.php">HOLD ON</a>';
?>
</body>
</html>