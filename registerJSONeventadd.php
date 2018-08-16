<?php
session_start();

include 'Incls/datautils.inc.php';

$pn = $_SESSION['profname'];    
$an = $_REQUEST['agenda'];    // input is an array
$rid = $_REQUEST['rid'];
$day = $_REQUEST['day'];
$fee = $_REQUEST['fee'];
if ($fee == 0) $fee = 0;
$addarray['RecKey'] = 'Evt';
$addarray['ProfName'] = "$pn";
$addarray['AgendaName'] = "$an[0]";
$addarray['EvtRowID'] = $rid;
$addarray['FEE'] = $fee;


// admin mode flag for max capacity override 
$admmode = isset($_SESSION['admMode']) ? 'ON' : '';

// create list of one or more agenda names
$size = count($an);
$insertsize = 0; $new = ''; $slist = '';
foreach ($an as $v) {
  if ($v == '') { $size--; continue; }
  if (preg_match("/all|attendee/i", $v)) { $size--; continue; }
  $new .= "('Evt', '$pn', '$v', $rid, $fee), " ;
  $slist .= "'$v', ";
  $insertsize++;
  }
$insertlist = rtrim($new, ", ");              // list of values for insert
$sellist = '(' . rtrim($slist, ", ") . ')';   // list for select

// get start and end times for all registered events for profile/agenda
$sql = "
SELECT `events`.`StartTime`, `events`.`EndTime`, `regeventlog`.`RecKey` 
FROM `events`, `regeventlog` 
WHERE `events`.`RowID` = `regeventlog`.`EvtRowID`  
  AND `events`.`TripStatus` = 'Retain'
  AND `events`.`Day` = '$day' 
  AND `regeventlog`.`ProfName` = '$pn'
  AND `regeventlog`.`AgendaName` IN $sellist;
  ";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

// access start and end time for all scheduled event(s)
$starray = array(); $etarray = array();
if ($rc) {
  while ($r = $res->fetch_assoc()) {
    if ($r[RecKey] == 'EvtWL') continue;      // ignore conflict if EvtWL
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

// next search regeventlog for all with EvtRowID = rid and count them
$res = doSQLsubmitted("SELECT `RowNbr`, `RecKey` FROM `regeventlog` WHERE `RecKey` LIKE 'Evt%' AND `EvtRowID` = '$rid';");
$evt = 0; $evtao = 0; $evtwl = 0;
while ($r = $res->fetch_assoc()) {
  if ($r[RecKey] == 'Evt') $evt += 1;
  if ($r[RecKey] == 'EvtAO') $evtao += 1;
  if ($r[RecKey] == 'EvtWL') $evtwl += 1;
  }
$evtcount = $evt + $evtao;    // total number registered for event

// if agenda name list > 1 then it is an all or nothing proposition
if (($size > 1) AND (($evtcount + $size) > $maxcap)) {
  echo 'TM '.'count: '.($evtcount+1).'/cap: '.$maxcap.'/wl: '.$evtwl;
  exit;
  } 

// if count <= max capacity add row to regeventlog with RecKey = 'Evt' 
//        and set response to browswer to 'OK'
// if count > max capacity AND admin mode OFF: 
//        add row to regeventlog with RecKey = 'EvtWL'
//        and set response to browser to 'WL' to clear check box
// if count > max capacity AND admin mode ON:
//        add row to regeventlog with RecKey = 'EvtAO'
//        and set response to browser to 'AO' to notify admin user

$sql = "INSERT INTO `regeventlog` (`RecKey`, `ProfName`, `AgendaName`, `EvtRowID`, `FEE`) VALUES $insertlist";

if ($evtcount < $maxcap) {
  $r = doSQLsubmitted($sql);
  echo 'OK ' . 'counts: ('.$maxcap.'/'.($evtcount+$size).") sql: $sql";
  exit;
  }
  
// good to use the single insert here
// there will never be a multi event schedule with a WL condition 
// since it is tested for and handled prior to this logic.
  
if (($evtcount >= $maxcap) AND ($admmode == '')) { 
  $addarray['RecKey'] = 'EvtWL';
  $addarray['FEE'] = '0';
  $r = sqlinsert("regeventlog", $addarray);
  // echo 'WL ' . 'count: ' . ($evtcount+1) . '/' . 'cap: ' . $maxcap;
  echo 'WL ' . 'counts: ('.$maxcap.'/'.($evtcount+1).')';
  }
if (($evtcount >= $maxcap) AND ($admmode == 'ON')) {
  $addarray['RecKey'] = 'EvtAO';
  $r = sqlinsert("regeventlog", $addarray);
  echo 'AO '.'count: '.($evtcount+1).'/cap: '.$maxcap.'/wl: '.$evtwl;
  // echo 'AO ' . 'counts: ('.$maxcap.'/'.($evtcount+1).')';
  }
return;
?>