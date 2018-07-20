<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start();
header( "Content-Type: application/vnd.ms-excel" );
header( "Content-disposition: attachment; filename=startersheet.xls" );
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
$sql = 'SELECT * FROM `events` WHERE 1=1  AND `TripStatus` NOT LIKE "Delete" ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC;';
//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
echo "Trip\tStart\tEnd\tType\tEvent\tSite\tLeader1\tLeader2\tLeader3\tLeader4\tTravel\tAttend\n";
$csvmask = "%s\t%s\t%s\t%s\t\"%s\"\t%s\t%s\t%s\t%s\t%s\t%s\t%s\n";
$ttc = readlistreturnarray("TripTypeCodes");
while ($r = $res->fetch_assoc()) {
  $st = date("g:iA", strtotime($r[StartTime]));
  $et = date("g:iA", strtotime($r[EndTime]));
  printf($csvmask,$r[Trip],$st,$et,$ttc[$r[Type]],$r[Event],$r[SiteCode],$r[Leader1],$r[Leader2],$r[Leader3],$r[Leader4],$r[Transportation],$r[MaxAttendees]);
  }
?>