<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Validate Database</title>
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
include 'Incls/mainmenu.inc.php';
//include 'Incls/listutils.inc.php';
include 'Incls/letter_print_css.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

echo '<h3>Validate Database</h3>';

  echo '
<h4>Database Info</h4>
Connection Info: '.$mysqli->host_info.'<br>
Client Info: '.$mysqli->client_info.'<br>
Server Info: '.$mysqli->server_info.'<br /><br>
<h4>Overview</h4>
<p>This program will perform various database validations including those listed:</p>
Examine all ACTIVE event records and report:
<ol>
	<li>any with missing Site Codes.</li>
	<li>any that do not have a Leader 1 identified.</li>
	<li>any with invalid leaders not in the leaders registry.</li>
	<li>any with missing start and/or end times.</li>
	<li>any with &quot;Fee Required&quot; indicated but no &quot;FEE&quot; entered.</li>
	<li>any with &quot;Transport Required&quot; indicated but no &quot;Transportation&quot; entry.</li>
	<li>any indicated to be a &quot;Multi-code&quot; but no &quot;Multi-code&quot; entered.</li>
	<li>any missing a value for the &quot;Max Attendees&quot;.</li>
	<li>any missing a expertise level rank.</li>
</ol>
Examine all leader records and report:
<ol>
	<li>any leader not having a primary phone number registered.</li>
	<li>any leader without an email address.</li>
	<li>any leader with a duplicated email address.</li>
</ol>
<br /><br>
';

echo '<div class="page-break"></div>
<h4>Event Record Validation Report</h4>';
// ============ load up leader array
$sql = '
SELECT * FROM `leaders` WHERE 1=1 AND `Active` = "Yes";';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$leaderrc = $res->num_rows;
$ldrs = array();
while ($r = $res->fetch_assoc()) {
//  echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
  $key = $r[FirstName] . ' ' . $r[LastName];
  if ($r[LastName] == '') $key = $r[FirstName];
  $ldrs[$key] = $r;
  }
//echo '<pre> leader '; print_r($ldrs); echo '</pre>';

// ========== read and validate events
$sql = '
SELECT * FROM `events` 
-- WHERE `TripStatus` IS NULL
WHERE 1=1
ORDER BY `Trip` ASC;';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$eventrc = $res->num_rows;

$err = array();
while ($r = $res->fetch_assoc()) {
  //echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
  if ($r[SiteCode] == '') $err[$r[Trip]][] = "Missing the site code";
  if ($r[Leader1] == '' ) $err[$r[Trip]][] = "Has no Leader 1 defined";
  if (($r[Leader1] != '') AND (!array_key_exists($r[Leader1], $ldrs))) 
    $err[$r[Trip]][] = sprintf("Leader1 (%s) is not registered.", $r[Leader1]);
  if (($r[Leader2] != '') AND (!array_key_exists($r[Leader2], $ldrs))) 
    $err[$r[Trip]][] = sprintf("Leader2 (%s) is not registered.", $r[Leader2]);
  if (($r[Leader3] != '') AND (!array_key_exists($r[Leader3], $ldrs))) 
    $err[$r[Trip]][] = sprintf("Leader3 (%s) is not registered.", $r[Leader3]);
  if (($r[Leader4] != '') AND (!array_key_exists($r[Leader4], $ldrs))) 
    $err[$r[Trip]][] = sprintf("Leader4 (%s) is not registered.", $r[Leader4]);
  if ($r[StartTime] == '') $err[$r[Trip]][] = "Has no start time defined";
  if ($r[EndTime] == '') $err[$r[Trip]][] = "Has no end time defined";
  if (($r[FeeRequired] == 'Yes') AND ($r[FEE] == '')) 
    $err[$r[Trip]][] = "Has a fee requirement and no fee.";
  if (($r[MultiEvent] == 'Yes') AND ($r[MultiEventCode] == '')) 
    $err[$r[Trip]][] = "is identified as a multi-event function but has no multi-event code.";
  if (($r[TransportRequired] == 'Yes') AND ($r[Transportation] == '')) 
      $err[$r[Trip]][] = "Has a trasport requirement and no transportaton identified.";
  if ($r[MaxAttendees] == '') $err[$r[Trip]][] = "has no max attendee limit defined.";
  if ($r[Level] == '') $err[$r[Trip]][] = "has no experience levels defined.";
  }

// check out leader info
$ema = array();
foreach ($ldrs as $k => $v) {
  if ($v[Email] == '') $ldrerr[$k][] = "Leader missing email address.";
  if (!in_array($v[Email], $ema)) $ema[] = $v[Email];
  else $ldrerr[$k][] = "Leader email address is a duplicate.";  
  if ($v[PrimaryPhone] == '') $ldrerr[$k][] = "Leader missing primary phone number.";
  }
  
//echo '<pre> error '; print_r($err); echo '</pre>';
if (count($err) > 0 ) {
  //echo '<pre> error '; print_r($errors); echo '</pre>';
  foreach ($err as $k => $v) {
    echo "Trip $k<br><ul>";
    foreach ($v as $l) {
      echo $l . '<br>';
      }
    echo '</ul><br>';
    }
  echo "TOTAL EVENT ERRORS: ".count($err).'<br>';
  }
else {
  echo 'No event errors to report<br><br>';
  } 
echo '<div class="page-break"></div>
<h4>Leader Record Validation Report</h4>';
if (count($ldrerr) > 0) {
  foreach ($ldrerr as $k => $v) {
    echo "$k<br><ul>";
    foreach ($v as $l) {
      echo $l . '<br>';
      }
    echo '</ul><br>';
    }
  echo "TOTAL LEADER ERRORS: ".count($ldrerr).'<br>';
  }
else {
  echo 'No leader errors to report<br><br>';
  }
echo '<br><b>Total Rows in event table: '.$eventrc.', total ACTIVE rows in leader table: '.$leaderrc.'</b><br><br>';
echo '=== END REPORT ===<br><br>';
?>
</div> <!-- container -->
</body>
</html>