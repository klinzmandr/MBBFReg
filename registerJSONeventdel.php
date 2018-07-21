<?php
session_start();

include 'Incls/datautils.inc.php';

$profile = $_SESSION['profname'];
$agenda = $_REQUEST['agenda'];
$day = $_REQUEST['day'];
$rid = $_REQUEST['rid'];

$sql = "DELETE FROM `regeventlog` 
WHERE `ProfName` = '$profile'
  AND `AgendaName` = '$agenda'
  AND `EvtRowID` = '$rid';";
$r = doSQLsubmitted($sql);

$resp = 'OK';
if ($r != 1) {
  $resp = 'NO';
  }
echo $resp;
//echo "Event deSELECTed for $profile - $agenda - $day - $rid";

?>