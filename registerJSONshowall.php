<?php
// access evets for specific profile, agenda and day
session_start();

include 'Incls/datautils.inc.php';

$profile = $_SESSION['profname'];
$agenda = $_REQUEST['agenda'];
$day = $_REQUEST['day'];

if ($agenda == 'ALLxz') $agenda = '%';  // list ALL events for ALL attendees

// get list of registered events for given day
$sql = "SELECT `regeventlog`.*, `events`.`RowID`, `events`.`MaxAttendees`  
FROM `regeventlog`, `events` 
WHERE `regeventlog`.`EvtRowID` = `events`.`RowID`
  AND `TripStatus` = 'Retain'  
  AND `regeventlog`.`ProfName` = '$profile'
  AND `regeventlog`.`AgendaName` LIKE '$agenda'
  AND `events`.`Day` = '$day'
ORDER BY `RowNbr` ASC";

$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$rowidarray = array();
while ($r = $res->fetch_assoc()) {
  $rowidarray[$r[EvtRowID]] = $r[RecKey];
  }
// echo "<pre> rows: $rc "; print_r($rowidarray); echo '</pre>';

// get list of all events for the day 
$sql = "SELECT * FROM `events` WHERE `Day` = '$day' AND `TripStatus` = 'Retain' ORDER BY `Trip` ASC;";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
if ($rc == 0) { 
  echo "<tr><td colspan=3>No events registered. Select Add/Del Event to start.</td></tr>"; 
  exit;
  }

// get event attendance counts for those already registered 
$sql = "
SELECT `EvtRowID`, COUNT(`RecKey`) AS 'RC'
FROM `regeventlog`
WHERE `RecKey` LIKE 'Evt%' GROUP BY `EvtRowID`;
";
$evts = doSQLsubmitted($sql);
$evtcounts = array();
while ($ec = $evts->fetch_assoc()) {
  $evtcounts[$ec[EvtRowID]] = $ec[RC];
  }
// echo '<pre>evtcnts '; print_r($evtcounts); echo '</pre>';

// set check box on if selected for this profile
// plus set status as OK, WL or AO depending on RecKey
$l = ''; 
while ($r = $res->fetch_assoc()) {
  $rid = $r[RowID];
  $cb = "<input type=checkbox>";
  $stat = '';
  if (array_key_exists($rid, $rowidarray)) { 
    $cb = "<input type=checkbox checked>";
    $stat = 'OK'; 
    if ($rowidarray[$rid] == 'EvtWL') {
      echo "rid: $rid";
      $stat = 'WL'; } 
    if ($rowidarray[$rid] == 'EvtAO') {
      echo "rid: $rid";
      $stat = 'AO'; } 
    }
  $st = substr($r[StartTime],0,5); $et = substr($r[EndTime],0,5);
  // for row to be bold: <tr style='font-weight: bold'>
  $tc = isset($evtcounts[$r[RowID]]) ? $evtcounts[$r[RowID]] : '0';
  $attcnt = $r[MaxAttendees] . '/' . $tc;
  $l .= "<tr><td>$cb</td><td class=RID>$r[RowID]</td><td>$stat</td><td>$r[Trip]</td><td class=ED>$r[Event] ($attcnt)</td><td>$st</td><td>$et</td><td>$r[FEE]</td></tr>"; 
  }

echo "$l";
// echo "show ALL for $profile - $agenda - $day";

?>