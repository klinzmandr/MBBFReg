<?php
session_start();

include 'Incls/datautils.inc.php';

$profile = $_SESSION['profname'];
$agenda = $_REQUEST['agenda'];      // comes in as an array
$day = $_REQUEST['day'];
$rid = $_REQUEST['rid'];

$new = '';
foreach ($agenda as $v) {
  if ($v == '') continue;
  if (preg_match("/all|attendee/i", $v)) continue;
  $new .= "'$v', " ;
  $newsize++;
  }
$sql = "DELETE FROM `regeventlog` 
WHERE `ProfName` = '$profile' 
  AND `EvtRowID` = '$rid' 
  AND `AgendaName` IN " . '(' . rtrim($new, ", ") . ');';

$r = doSQLsubmitted($sql);

$resp = 'OK';
if ($r == 0) {
  $resp = "NO r: $r, sql:$sql";
  }
echo $resp;
//echo "Event deSELECTed for $profile - $agenda - $day - $rid";

?>