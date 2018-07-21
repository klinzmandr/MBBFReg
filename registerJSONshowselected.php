<?php
// access events for specific profile, agenda and day
session_start();

include 'Incls/datautils.inc.php';

$profile = $_SESSION['profname'];
$agenda = $_REQUEST['agenda'];
$day = $_REQUEST['day'];
$sql = "SELECT `regeventlog`.*, `events`.`RowID`, `events`.`Trip` ,`events`.`Event` , `events`.`StartTime`, `events`.`EndTime`,`events`.`FEE` FROM `regeventlog`, `events` 
WHERE `regeventlog`.`EvtRowID` = `events`.`RowID` 
  AND `TripStatus` = 'Retain' 
  AND `regeventlog`.`ProfName` = '$profile'
  AND `regeventlog`.`AgendaName` = '$agenda'
  AND `events`.`Day` = '$day'
ORDER BY `RowNbr` ASC";

$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
if ($rc == 0) { 
  echo "<tr><td>&nbsp;</td><td>&nbsp;</td><td colspan=6>No events registered</td></tr>"; 
  exit;
  }
$l = '';
while($r = $res->fetch_assoc()) {
  $stat = 'OK'; 
  if ($r[RecKey] == 'EvtWL') $stat = 'WL';
  if ($r[RecKey] == 'EvtAO') $stat = 'AO';  
  $st = substr($r[StartTime],0,5); $et = substr($r[EndTime],0,5);
  $l .= "<tr><td><input type=checkbox></td><td class=RID>$r[EvtRowID]</td><td>$stat</td><td>$r[Trip]</td><td class=ED>$r[Event]</td><td>$st</td><td>$et</td><td>$r[FEE]</td></tr>";
}
echo "$l";
// echo "Show SELECTED for $profile - $agenda - $day";

?>