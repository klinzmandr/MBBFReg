<?php 
session_start(); 
// error_reporting(E_ERROR | E_WARNING | E_PARSE); 

$now = date('M d, Y \a\t H:i', strtotime("now"));
include 'Incls/datautils.inc.php';
// get wait list counts (if any) for each event
$res=doSQLsubmitted("SELECT `regeventlog`.`EvtRowID`, count(`regeventlog`.`RowNbr`) AS 'cntwl' FROM `regeventlog` WHERE `regeventlog`.`RecKey`='EvtWL' GROUP BY `regeventlog`.`EvtRowID`;
");
while ($r = $res->fetch_assoc()) {
  $evtwl[$r[EvtRowID]] = $r[cntwl];
  }
// echo '<pre>WL '; print_r($evtwl); echo '</pre>';

// get admin override counts (if any) for each event
$res=doSQLsubmitted("SELECT `regeventlog`.`EvtRowID`, count(`regeventlog`.`RowNbr`) AS 'cntao' FROM `regeventlog` WHERE `regeventlog`.`RecKey`='EvtAO' GROUP BY `regeventlog`.`EvtRowID`;
");
while ($r = $res->fetch_assoc()) {
  $evtao[$r[EvtRowID]] = $r[cntao];
  }
// echo '<pre>AO '; print_r($evtao); echo '</pre>';

// get attendance count from regeventlog
$sql = "
SELECT `events`.`Trip`, `events`.`Event`, `events`.`MaxAttendees`, `regeventlog`.`EvtRowID`, count(`regeventlog`.`RowNbr`) AS 'cnt' 
FROM `regeventlog`, `events`
WHERE `regeventlog`.`RecKey`='Evt'
  AND `regeventlog`.`EvtRowID` = `events`.`RowID`
GROUP BY `regeventlog`.`EvtRowID`
";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
while ($r = $res->fetch_assoc()) {
  // echo '<pre>'; print_r($r); echo '</pre>';
  $evtattendance[$r[EvtRowID]][cnt] = $r[cnt];
  $evtattendance[$r[EvtRowID]][trip] = $r[Trip];
  $evtattendance[$r[EvtRowID]][event] = $r[Event];
  $evtattendance[$r[EvtRowID]][maxcap] = $r[MaxAttendees];
  $evtattendance[$r[EvtRowID]][wl] = 0;
  if ($evtwl[$r[EvtRowID]]) 
    $evtattendance[$r[EvtRowID]][wl] = $evtwl[$r[EvtRowID]];
  $evtattendance[$r[EvtRowID]][ao] = 0;
  if ($evtao[$r[EvtRowID]]) 
    $evtattendance[$r[EvtRowID]][ao] = $evtao[$r[EvtRowID]];
  }
// echo '<pre>evtattendance '; print_r($evtattendance); echo '</pre>';
$tr = '';
foreach ($evtattendance as $k=>$v) {
  // echo '<pre>v '; print_r($v); echo '</pre>';
  $tr .= "<tr style='cursor: pointer;'><td>$k</td><td>$v[trip]</td><td>$v[event]</td><td>$v[maxcap]</td><td>$v[cnt]</td><td>$v[ao]</td><td>$v[wl]</td></tr>";
  }
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Capacity Report</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
?>
<div class="container">

<h1>Event Capacity Drilldown Report</h1>
<a class="btn btn-primary btn-xs hidden-print" id="helpbtn">HELP</a>
<div id=help>
<p>This report will provide registered attendee counts all events that have been regstered. Counts presented are the maximum capacity designated for the event, the number registered, the number admitted by administrative override and the number wait listed for each event.  All counts presented are current as of date and time the report was run.</p><p>NOTE: those events that have admission override counts are actually over booked and should be carefully reviewed to ensure that paying attendees have been cater for before those that have applied for and been approved as being eligible for event attendance as a non-paying volunteer or leader.</p>
<p>Clicking on a event row will provide a complete detail listing for the event.</p>
<p>Use the filter to enter an event number or name to quickly narrow the number of rows listed.</p>
</div>
Report as of <?=$now?><br>

<script>
$(function() {
  $("#filter").focus();
  $("#xMsg").fadeOut(5000);
  $('td:nth-child(1),th:nth-child(1)').hide();  // hide first col
$("tr").click(function() {
  // get profile row nbr from col 1
  var p = $(this).find('td').first().text(); 
  if (!p.length) return;
  $("#INp").val(p);
  $("#proform").submit();
  });
});
</script>
<div class=hidden-print>
<form action="admeventattendance.php" method="post"  id="proform">
<input id="INp" name=evtrowid type=hidden value=''>
<input type=hidden name=action value="">
</form>

<input id=filter autofocus placeholder='Filter'>&nbsp;&nbsp;<button id=filterbtn2>Reset</button>
</div>
<table class=table>
<tr><th>EvtRowID</th><th title='Event number'>EvtNbr</th><th title='Name of the event'>Event Name</th><th title='Maximum capacity set for the event'>MaxCap</th><th title='Total number of attendees registered'>Reg</th><th title='Total number regsitered by the Registrar after the maximum capacity of the event was exceeded.'>AO</th><th title='Total number of attendees wait listed due to the maximum capacity being exceeded.'>WL</th></tr>
<?=$tr?>
</table>

</div> <!-- container -->
</body>
</html>