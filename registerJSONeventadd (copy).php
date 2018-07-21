<?php
session_start();

include 'Incls/datautils.inc.php';

$pn = $_SESSION['profname'];
$an = $_REQUEST['agenda'];
$rid = $_REQUEST['rid'];
$day = $_REQUEST['day'];
$addarray['RecKey'] = 'Evt';
$addarray['ProfName'] = "$pn";
$addarray['AgendaName'] = "$an";
$addarray['EvtRowID'] = $rid;
$addarray['FEE'] = $_REQUEST['fee'];

// admin mode flag for max capacity override 
$admmode = isset($_SESSION['admMode']) ? 'ON' : '';

// if admin mode is ON then bypass all capacity and time checks
// just approve the adddition and mark it as AO
//if ($admmode == 'ON') {
//  $addarray['RecKey'] = 'EvtAO';
//  $r = sqlinsert("regeventlog", $addarray);
//  echo "AO admin override mode enabled";
//  exit;
//  }

// get start and end times for all registered events for profile/agenda
$res = doSQLsubmitted("
SELECT `events`.`StartTime`, `events`.`EndTime` 
FROM `events`, `regeventlog` 
WHERE `events`.`RowID` = `regeventlog`.`EvtRowID`  
  AND `events`.`TripStatus` = 'Retain'
  AND `events`.`Day` = '$day' 
  AND `regeventlog`.`ProfName` = '$pn'
  AND `regeventlog`.`AgendaName` = '$an';
  ");
$rc = $res->num_rows;
// access start and end time for all scheduled event(s)
$starray = array(); $etarray = array();
if ($rc) {
  while ($r = $res->fetch_assoc()) {
    $starray[] = strtotime($r[StartTime]);
    $etarray[] = strtotime($r[EndTime]);
    }
  }

// get start/end time from requested event
$res = doSQLsubmitted("SELECT `StartTime`, `EndTime` FROM `events` WHERE `RowID` = '$rid';");
$r = $res->fetch_assoc();
$st = strtotime($r[StartTime]);  $et = strtotime($r[EndTime]);

// check start and end time for any conflicts
if (count($starray)) {
  $em = '';
  for ($i=0; $i<count($starray); $i++) {
    switch (true) {
      case (($starray[$i] < $st) AND ($st < $etarray[$i])):
        $em .= 'a start time conflict';
        break;
      case (($starray[$i] < $et) AND ($et < $etarray[$i])):
        $em .= 'an end time ('.$et.') conflict sta: '.$starray[$i].', eta: '.$etarray[$i];
        break;
      case (($starray[$i] >= $st) AND ($et >= $etarray[$i])):
        $em .= 'the same start/end times or a complete overlap';
        break;
      }
    if (strlen($em)) break;
    }
  }
// return 'TE' plus error message if any conflicts detected and exit.
if (strlen($em)) {
  $em = 'TE Event has ' . $em . ' with another scheduled event';
  echo $em;
  exit;
  }

// do capacity check for event
//  first get the max capacity for event from events table from row 'rid'
$res = doSQLsubmitted("SELECT `MaxAttendees` FROM `events` WHERE `RowID` = '$rid' AND `TripStatus` = 'Retain';");
$r = $res->fetch_assoc();
$maxcap = $r['MaxAttendees'];

// next search regeventlog for all with EvtRowID = rid and count all
$res = doSQLsubmitted("SELECT `RowNbr`, `RecKey` FROM `regeventlog` WHERE `RecKey` LIKE 'Evt%' AND `EvtRowID` = '$rid';");
$evt = 0; $evtao = 0; $evtwl = 0;
while ($r = $res->fetch_assoc()) {
  if ($r[RecKey] == 'Evt') $evt += 1;
  if ($r[RecKey] == 'EvtAO') $evtao += 1;
  if ($r[RecKey] == 'EvtWL') $evtwl += 1;
}
$evtcount = $evt + $evtao;    // total number registered for event

// if count <= max capacity add row to regeventlog with RecKey = 'Evt' 
//        and set response to browswer to 'OK'
// if count > max capacity AND admin mode OFF: 
//        add row to regeventlog with RecKey = 'EvtWL'
//        and set response to browser to 'WL' to clear check box
// if count > max capacity AND admin mode ON:
//        add row to regeventlog with RecKey = 'EvtAO'
//        and set response to browser to 'AO' to notify admin user

if ($evtcount < $maxcap) {
  $r = sqlinsert("regeventlog", $addarray);
  echo 'OK ' . 'count: ' . ($evtcount+1) . '/' . 'cap: ' . $maxcap;
  }
if (($evtcount >= $maxcap) AND ($admmode == '')) { 
  $addarray['RecKey'] = 'EvtWL';
  $addarray['FEE'] = '0';
  $r = sqlinsert("regeventlog", $addarray);
  echo 'WL ' . 'count: ' . ($evtcount+1) . '/' . 'cap: ' . $maxcap;
  }
if (($evtcount >= $maxcap) AND ($admmode == 'ON')) {
  $addarray['RecKey'] = 'EvtAO';
  $r = sqlinsert("regeventlog", $addarray);
  echo 'AO '.'count: '.($evtcount+1).'/cap: '.$maxcap.'/wl: '.$evtwl;
  }
return;
?>