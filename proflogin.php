<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$id = isset($_REQUEST['profname']) ? $_REQUEST['profname'] : $_SESSION['profname'];
$_SESSION['profname'] = $id;

// hide logout button in admin mode 
$admmode = isset($_SESSION['admMode']) ? 'ON' : '';

$f = isset($_REQUEST['f']) ? $_REQUEST['f'] : array();  // row fields
// echo '<pre> field '; print_r($f); echo '</pre>';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// echo "1. id: $id, action: $action<br>";

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';

// get fee roster
$feesched = readlistreturnarray('Fees');
// echo "<pre>feesched "; print_r($feesched); echo '</pre>';


// only triggered by call from profnew.php
if ($action == 'new') {
  echo '<pre>new '; print_r($f); echo '</pre>';
  $status = sqlinsert('regprofile', $f);
  // echo "2. action = new in profnew, insert status: $status<br>";
  }

// only triggered by call from profnew.php
if ($action == 'update') {
  $f[ProfileID] = "$id";
  // echo "<pre>update f "; print_r($f); echo '</pre>';  
  $status = sqlupdate('regprofile', $f, "`ProfileID` = '$id'");
  // echo "3. action = update in profnew, status: $status<br>";
  
  // update profeventlog SELF agenda with latest fees
  switch ($f[regType]) {
    case 'full':
      $upda['FEE'] = $feesched[RegFull];
      break;
    case 'Friday':
    case 'Saturday':
    case 'Sunday':
      $upda['FEE'] = $feesched[RegOne];
      break;
    case 'Monday':
      $upda['FEE'] = $feesched[RegLast];
      break;
    }  
  
  // echo '<pre>upd '; print_r($upda); echo '</pre>';
  $astatus = sqlupdate('regeventlog', $upda, "`ProfName` = '$id' AND `AgendaName` = 'SELF'");
  }

// read profile
$sql  = "SELECT * FROM `regprofile` WHERE `ProfileID` = '$id';";
// echo "4. reading profile - sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$f = $res->fetch_assoc();
if ($rc == 0) {
  echo "<h1>Profile $id does not exist.</h1><p>The profile name as entered does not yet exist.  Return to the previous page and click &quot;Create a registration profile&quot;.</p>
<a href=\"index.php\" class=\"btn btn-primary btn-lg\"><h2>R E T U R N</h2></a>";
  exit;
  }

// set up for multiple agendas if a FULL registration allowing
// multiple agendas. Partial festival registrants
// OR those asking for or have been approved for fees exemptions
// must register as individuals limited to only 1 agenda.
$multiOK = '';
// multiple profiles OK for full registration only
if ($f[regType] == 'full') $multiOK = 'ON'; 
// default: NO unless asked for (YES) or approved (APPROVED) 
if ($f[Exempt] != 'NO') $multiOK = '';  
// echo "regType: $f[regType], multiOK: $multiOK<br>";

// get all rows from event log
$sql = "SELECT * FROM `regeventlog` WHERE `ProfName` = '$id';";
// echo "5. sql: $sql<br>";
$res = doSQLsubmitted($sql);
$totalfees = 0; $totpay = 0; $activitycount = array(); $paycount = 0;
$waitcount = 0; $agendacount = array();
while ($r = $res->fetch_assoc()) {
  if ($r[RecKey] == 'Reg') {
    $agendacount[] = $r[AgendaName]; 
    continue;
    }
  if ($r[RecKey] == 'Pay') {
    $paycount += 1;
    $totpay += $r[Payment];
    continue;
    }
  if ($r[RecKey] == 'Evt') {
    $activitycount[] = $r[EvtRowID];
    $totalfees += $r[FEE];
    continue;
    }
  if ($r[RecKey] == 'EvtWL') {
    $waitcount += 1;
    }
  }
  
$agcnt = count($agendacount);
$evtcnt = count($activitycount);
//$regdues = number_format(($agcnt * 85), 2);
//$bal = 0;
//$bal = number_format(($regdues + $totalfees - $totpay),2);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Profile Login</title>
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="css/font-awesome.min.css" rel="stylesheet">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

<style>
  p, th, td, select, .btn { font-size: 1.5em; }
  tx { font-size: 1.75em; }
  input[type=checkbox] { transform: scale(1.5); }
</style> 
</head>
<body>
<script>
$(function() {
  // alert("on load");
  var am = "<?=$admmode?>";
  if (am.length) $("#LObtn").hide();    // set admin mode flag
  var ma = "<?=$multiOK?>";
  if (ma.length == 0) $("#ada").hide(); // hide add/delete agenda button
});

</script>
<!-- <img src="http://morrobaybirdfestival.net/wp-content/uploads/2016/08/LOGO3.png" width="400" height="100" alt="bird festival logo"> -->
<h1>Profile for <?=$id?></h1>
<table class=table><tr><td>
<a href="register.php" class="btn btn-primary btn-lg">Schedule Events</a></td>

<td align="right">
<i id=helpbtn class="fa fa-bars fa-3x">&nbsp;&nbsp;</i> 
</td></tr></table>

<ul><a href="profagendas.php" id=ada class="btn btn-success">Add/Delete Attendees</a></ul>

<div id=help>
<ul>
<a href="index.php" id=LObtn class="btn btn-primary btn-lg">FINISHED</a>&nbsp;&nbsp;
<a href="profsummary.php" class="btn btn-primary btn-lg">Event Summary</a>&nbsp;&nbsp;
<a href="profnew.php?action=update" class="btn btn-primary btn-lg">Upd Profile</a>

<br>
<h3>Profile Information:</h3>
<p>The profile record is used to hold information about the registrant as well as other information such as meal selections, etc.</p>
<p>A profile may have one or more attendee agendas defined for a FULL Festival registration.  By default one agenda (identified as &quot;SELF&quot;) is created.  Any number of attendee agendas may be added.</p>
<p>A <a href="profreset.php" style="background-color: red; color: white;">Profile Reset</a> may be done to delete all added attendee agendas and all associated scheduled events.  This basically starts the registration process over.  Only the basic information (name, address, contact information, etc.) of the profile is retained.</p>
<p>Additional attendee agendas are created by clicking the &quot;Add/Del Attendee&quot; button then and providing a unique identifier.  All attendee agendas and their associated events are grouped under the profile along with any/all fees applicable.</p>
<p>An single attendee agenda may be deleted as long as there are NO event scheduled.  ALL events for ALL days must be deleted before an attendee agenda can be deleted.  A list of all scheduled events for all agendas and all days may be viewed by clicking the &quot;Event Summary&quot; button</p>
<p>Event selection is done by clicking the &quot;Schedule Events&quot; button.  ALL selected events for a day are listed.  Successful registration is noted by a status code of &quot;OK&quot; in the status column.</p>
<p>An event is &quot;Wait Listed&quot; (indicated by a status code of  &quot;WL&quot; in the status column) when the maximum capacity of the requested event has been reached.  A future request to register will be fulfilled if there is capacity available and there is still interest in the event.  Un-check the event and re-select it.  If capacity is available, the status will change to &quot;OK&quot;.  Otherwise, it will revert to &quot;WL&quot;.</p>
<p>Clicking the &quot;Pro Forma Invoice and Payments&quot; button will provide an invoice detailing all the fees that have been accrued based on the number of agendas defined, the selections in the profile (lunches, shirts, etc.), events fees (if any).  Any previous payments are also noted providing a balance due amount.</p>

</ul>
</div>

<ul>
<table border="0" class="table" width="80%">
<thead></thead>
<tbody>
<tr><td><tx>Agendas currently defined:</tx></td>
<td><tx><?=$agcnt?></tx></tx></tr>
<tr><td><tx>Total events scheduled (all agendas):</tx></td>
<td><tx><?=$evtcnt?></tx></tx></tr>
<tr><td><tx>Total events Wait Listed (all agendas):</tx></td>
<td><tx><?=$waitcount?></tx></td></tr>
<tr><td><tx>Total Event Fees (Scheduled events only):</tx></td>
<td><tx>$<?=$totalfees?></tx></tx></tr>
</tbody>
</table>
</ul>
<a class="btn btn-primary btn-lg" href="profregister.php">Pro Forma Invoice and Payment</a>
<br><br>
</body>
</html>