<?php
session_start();

include 'Incls/datautils.inc.php';

$rid = $_REQUEST['rid'];

// read event description from event record
$sql = "SELECT `Event`, `Program` FROM `events` 
WHERE `RowID` = '$rid';";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$r = $res->fetch_assoc();

$resp = 'OK ';
$resp .= "<p><b>$r[Event]</b></p><p>$r[Program]</p>";
if ($rc != 1) {
  $resp = 'NO';
  }
echo $resp;
//echo "Event deSELECTed for $profile - $agenda - $day - $rid";

?>