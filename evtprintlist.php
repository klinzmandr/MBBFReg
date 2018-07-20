<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Print List</title>
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
include 'Incls/letter_print_css.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

echo '
<h3 class="hidden-print">Print Report List</h3>
';

if ($action == '') {
  echo '
  <p>This page will print the list of events that were in the last search results list.  Each event will be printed.  The result page produced, if printed, will show each event on a single page.  Use the browsers print functions to perform the actual printing.</p>

<a href="evtprintlist.php?action=genreport" class="btn btn-primary">Print Event(s)</a>
';

exit;
  }

// generate the output
echo '
<h4 class="hidden-print">User the browser &quot;File->Print&quot; function to output the event list one event per page.  If using Chrome, the page output may also be saved as a PDF file.</h4>
';

$navarray = $_SESSION['navarray'];
if (count($navarray) == 0) {
  echo '<h3>No entries in the search list</h3>';
  exit;
  }
$list = "('" . implode("', '", $navarray) . "')";

$sql = '
SELECT * FROM `events` WHERE `RowID` IN '.$list.';';

// echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

while ($r = $res->fetch_assoc()) {
// echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
// FORM FIELD DEF's
$t = sprintf("%03s",$r[Trip]);
$diff = timediff($r[StartTime],$r[EndTime]);
$stime = date("g:i A", strtotime($r[StartTime]));
$etime = date("g:i A", strtotime($r[EndTime]));
echo '
<h3>Event: '.$r[Event].'</h3>
<table class="table" border="0">
<tr><td>
Trip Number: '.$t.'
</td><td>
Day: '.$r[Day].' 
</td><td>
Trip Status: '.$r[TripStatus].'
</td></tr>
<tr><td>
Start Time: '.$stime.'
</td><td>
End Time: '.$etime.'
</td><td>
Duration: '.$diff.'
</td></tr>

<tr><td colspan="2">
Event Name: '.$r[Event].'
</td><td>
Type:  '.$r[Type].'
</td></tr>
<tr><td>
Type of Event:  '.$r[TypeOfEvent].'
</td><td colspan="2">
Level: '.$r[Level].'
</td></tr>
<tr><td>
Site:  '.$r[Site].'
</td>
<td>
Site Code: '.$r[SiteCode].'
</td>
<td> 
Site Room: '.$r[SiteRoom].'
</td>
</tr>
</table>

<table class="table" border="0">
<tr><td>
Leader 1: '.$r[Leader1].'
</td><td>
Leader 2: '.$r[Leader2].'
</td></tr>
<tr><td>
Leader 3: '.$r[Leader3].'
</td><td>
Leader 4: '.$r[Leader4].'
</td></tr>
</table>

<table class="table" border="0">
<tr><td>
Fee Required(Y/N): '.$r[FeeRequired].'
</td><td colspan="2">
FEE: '.$r[FEE].'
</td></tr>
<tr><td>
Transport Needed(Y/N): '. $r[TransportNeeded].'
</td><td colspan="2">
Transportation: '.$r[Transportation].'
</td></tr>
<tr><td>
Maximum Attendees: '.$r[MaxAttendees].'
</td><td>
Multi-Event(Y/N): '.$r[MultiEvent].'
</td><td>
Multi Event Code(s): '.$r[MultiEventCode].'
</td></tr>
<tr><td>
</table>
<table class="table" border="0">
<tr><td>
Program Description: <br>'.$r[Program].'
</td></tr>
<tr><td valign="top">
Secondary Status (Production Notes):<br>'.$r[SecondaryStatus].'
</td></tr>
</table>
<div class="page-break"></div>
';
  
  }

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
</body>
</html>