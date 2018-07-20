<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Leader Activity</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
</head>
<body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<div class="container">
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.planner.inc.php';
//include 'Incls/listutils.inc.php';

// Process listing based on selected criteria

$eaddr = isset($_REQUEST['eaddr']) ? $_REQUEST['eaddr'] : "";
//$eaddr = 'chris@campoceanpines.org';

$sql = "SELECT * FROM `leaders` WHERE `Email`='$eaddr';";

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

if ($rc == 0) {
  echo '<h2>ERROR: email address is not regstered</h2>';
  exit;
  }  

if ($rc > 1) {
  echo '<h2>ERROR: multiple leaders have the same email address</h2>
  <h4>Please notify the event coordinator.</h4>';
  exit;
  }  

// there should be only 1 record returned so process it here
$l = $res->fetch_assoc();
$leader = $l[FirstName] . ' ' . $l[LastName];

addlogentry("ldrqry for $leader");

echo '<img src="http://morrobaybirdfestival.net/wp-content/uploads/2016/08/LOGO3.png" alt="bird festival logo" >';
echo "<h1>Scheduled Events for $leader </h1>";
 
// generate activity report
$sql = "
SELECT * FROM `events` 
WHERE (`Leader1` LIKE '$leader' 
  OR `Leader2` LIKE '$leader' 
  OR `Leader3` LIKE '$leader' 
  OR `Leader4` LIKE '$leader')
 AND `TripStatus` NOT LIKE 'Delete'
ORDER BY `Trip` ASC;";

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

echo '
<table border=1 class="table table-condensed">
<tr><th>Trip#</th><th>Day</th><th>Time</th><th>Event</th><th>Leader Group</th></tr>';
while ($r = $res->fetch_assoc()) {
  $kk = $r[Dnbr];
  if ($kk == 1) $dx='Friday '; if ($kk == 2) $dx='Saturday ';
  if ($kk == 3) $dx='Sunday '; if ($kk == 4) $dx='Monday ';
  if ($kk == '') $dx='NotSet';
  $st = date("g:iA", strtotime($r[StartTime]));
  $et = date("g:iA", strtotime($r[EndTime]));
  // echo '<pre> full record '.$rowid.' '; print_r($r); echo '</pre>';
  $ldrgrp = $r[Leader1];
  if (strlen($r[Leader2]) > 0) $ldrgrp .= ', '.$r[Leader2]; 
  if (strlen($r[Leader3]) > 0) $ldrgrp .= ',<br>'.$r[Leader3]; 
  if (strlen($r[Leader4]) > 0) $ldrgrp .= ', '.$r[Leader4]; 
  echo 
"<tr><td>$r[Trip]</td><td>$dx</td><td>$st-$et</td><td>$r[Event]</td><td>$ldrgrp</td><tr>";         
  }
echo '</table>';
echo "<h2>Information on file for $leader</h2>";
//echo '<pre> full leader record'; print_r($l); echo '</pre>';
echo "
<table border=0>
<tr><td width=\"20%\"><b>Primary Phone:</b></td><td>$l[PrimaryPhone]</td></tr>
<tr><td><b>Secondary Phone:</b></td><td>$l[SecondaryPhone]</td></tr>
<tr><td><b>Email Address:</b></td><td>$l[Email]</td></tr>
<tr><td><b>Firm:</b></td><td>$l[Address2]</td></tr>
<tr><td><b>Address:</b></td><td>$l[Address1]</td></tr>
<tr><td><b>City, State ZIP:</b></td><td>$l[City], $l[State] $l[Zip]</td></tr>
<tr><td valign='top'><b>Biography</b></td><td>$l[Bio]</td></tr>
</table>";
?>
</div> <!-- container -->
</body>
</html>