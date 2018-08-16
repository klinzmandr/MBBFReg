<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$evtrowid = $_REQUEST['evtrowid'];
// get all info about event
$res = doSQLsubmitted("SELECT * FROM `events` WHERE `RowID` = '$evtrowid'");
$rc = $res->num_rows;
if (!rc) $evt[] = '';
else $evt = $res->fetch_assoc();
// echo '<pre>evt '; print_r($evt); echo '</pre>';

$sql = "
SELECT `regeventlog`.*, `regprofile`.`ProfFirstName`, `regprofile`.`ProfLastName` 
FROM `regeventlog`, `regprofile`
WHERE `regeventlog`.`EvtRowID` = '$evtrowid' 
  AND `regeventlog`.`RecKey` LIKE 'Evt%' 
  AND `regeventlog`.`ProfName` = `regprofile`.`ProfileID`
ORDER BY `EvtRowID` ASC;
";
// echo "sql: $sql<br>";

$res = doSQLsubmitted($sql);
while ($r = $res->fetch_assoc()) {
  // echo '<pre>regevt '; print_r($r); echo '</pre>';
  $aa[$r[ProfName]][$r['AgendaName']] = $r[RecKey];
  $aa[$r[ProfName]][fname] = $r[ProfFirstName];
  $aa[$r[ProfName]][lname] = $r[ProfLastName];
  }
// echo '<pre>aa '; print_r($aa); echo '</pre>';
$evtcnt = 0; $aocnt = 0; $wlcnt = 0; $tr = '';
foreach ($aa as $k => $v) {
  $tr .= "<tr><td colspan=1>$k (Name: $v[fname] $v[lname])</td></tr>";
  unset($v[fname]); unset($v[lname]);
    foreach ($v as $kk => $vv) {
      $tr .= "<tr><td><ul>$kk</ul></td>";
      if ($vv == 'Evt') { 
        $tr .= "<td>Registered</td></tr>"; 
        $evtcnt += 1; 
        continue; }
      if ($vv == 'EvtAO') { 
        $tr .= "<td>AdminOverride</td></tr>"; 
        $evtcnt += 1;
        $aocnt += 1; 
        continue; }
      if ($vv == 'EvtWL') { 
        $tr .= "<td>Waitlisted</td></tr>";
        $wlcnt += 1;  
        }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Event Attendance</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
?>

<h1>Event Attendance Roster</h1>
<h2>Event Number: <?=$evt[Trip]?> <?=$evt[Event]?></h2>
<h4>
Event Max Attendees: <?=$evt[MaxAttendees]?>, Attendees: <?=$evtcnt?>, AdminOverride: <?=$aocnt?>, Waitlisted: <?=$wlcnt?></h4>
<table class=table>
<tr><th>Profile</th><th>Status</th></tr>
<?=$tr?>
</table>
</div> <!-- container -->
</body>
</html>