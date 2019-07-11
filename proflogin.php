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
$agn = isset($_REQUEST['agendaname']) ? $_REQUEST['agendaname'] : 'SELF';

// echo "1. id: $id, action: $action<br>";

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/checkcred.inc.php';

// get fee roster
$feesched = readlistreturnarray('Fees');
// echo "<pre>feesched "; print_r($feesched); echo '</pre>';

// echo "1. start of proflogin.php<br>";
// only triggered by call from profnew.php
if ($action == 'new') {
  // echo '<pre>NEW '; print_r($f); echo '</pre>';
  $status = sqlupdate('regprofile', $f, "`ProfileID` = '$id'");
  
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
  // create initial agenda (assume full festival registration)
  $upda['RecKey'] = 'Reg';
  $upda['ProfName'] = $id;
  $upda['AgendaName'] = strtoupper($agn);
  sqlinsert('regeventlog', $upda);

  // echo "2. action = new in proflogin, insert into evt log, status: $status<br>";
  }

// only triggered by call from profnew.php
if ($action == 'update') {
  $f[ProfileID] = "$id";
  // echo "<pre>update f "; print_r($f); echo '</pre>';  
  $status = sqlupdate('regprofile', $f, "`ProfileID` = '$id'");
  // echo "3. action = update in proflogin, status: $status<br>";
  }

// read profile
$sql  = "SELECT * FROM `regprofile` WHERE `ProfileID` = '$id';";
// echo "4. reading profile - sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$f = $res->fetch_assoc();
if ($rc == 0) {
  echo "<h1>Profile $id invalid or timed out.</h1><p>The profile name as entered can not be accessed.  Return to the previous page and re-enter it or create a new one.</p>
<a href=\"index.php\" class=\"btn btn-primary btn-lg\"><h2>R E T U R N</h2></a>";
  exit;
  }

// get festival start date and time, setup exemption flag
$exemptionstart = date("F j, Y \a\\t H:i", strtotime(getregstart()."+ 2 weeks"));
$exemptstart = strtotime(getregstart()."+ 2 weeks");
$exemptnow = strtotime("now");
// echo "exemptnow: $exemptnow, exemptstart: $exemptstart<br>";
// echo "profileexempt: $f[Exempt]<br>";
$exemptflag = 'NO';
if ($f[Exempt] != 'NO') { 
  if ($exemptnow >= $exemptstart) {
    $exemptflag = 'YES';          // OK for vols and slbm to schedule events
    }
  }
// echo "exemptflag: $exemptflag<br>";

// check if a pay lock exists for the profile.
$lock = $f[PayLock];
// if ($f[PayLock] == 'Lock') { $lock = 'Lock'; }
  
// set up for multiple attendees if a FULL registration 
// allowing multiple agendas. Partial festival registrants
// OR those asking for or have been approved for fees exemptions
// must register as individuals limited to only 1 agenda.
unset($_SESSION['multiOK']);
// multiple profiles OK for full registration only
$_SESSION['multiOK'] = 'ON';
if ($f[Exempt] == 'YES') $_SESSION['multiOK'] = 'OFF';   
if ($f[Exempt] == 'APPROVED') $_SESSION['multiOK'] = 'OFF';   
if ($f[regType] != 'full') $_SESSION['multiOK'] = 'OFF';   

// get all rows from event log
$sql = "SELECT * FROM `regeventlog` WHERE `ProfName` = '$id';";
// echo "5. sql: $sql<br>";
$res = doSQLsubmitted($sql);
$totalfees = 0; $totpay = 0; $activitycount = array(); $paycount = 0;
$waitcount = 0; $agendacount = array();
while ($r = $res->fetch_assoc()) {
  if ($r['RecKey'] == 'Reg') {
    $agendacount[] = $r['AgendaName'];
    $totalfees += $r['FEE'];
    continue;
    }
  if ($r['RecKey'] == 'Pay') {
    $paycount += 1;
    $totpay += $r['Payment'];
    continue;
    }
  if ($r['RecKey'] == 'Evt') {
    $activitycount[] = $r['EvtRowID'];
    $totalfees += $r['FEE'];
    continue;
    }
  if ($r['RecKey'] == 'EvtWL') {
    $waitcount += 1;
    }
  }
  
$agcnt = count($agendacount);
$evtcnt = count($activitycount);
//$regdues = number_format(($agcnt * 85), 2);
//$bal = 0;
//$bal = number_format(($regdues + $totalfees - $totpay),2);
// include 'Incls/vardump.inc.php';
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
  tx { font-size: 1.25em; }
  input[type=checkbox] { transform: scale(1.5); }

.ex1 {
    /* border: 1px solid red; */ 
    padding: 5px;
    background-color: green;
    color: white;
  }
.ex2 {
    /* border: 1px solid red; */ 
    /* padding: 5px; */
    /* background-color: green; */
    color: blue;
  }
</style> 
</head>
<body>
<script>
$(function() {
  // alert("on load");
  var am = "<?=$admmode?>";
  if (am.length) $("#LObtn").hide();    // set admin mode flag
  if ('<?=$_SESSION['multiOK']?>' == 'OFF') $("#ada").hide(); // hide add/delete agenda button

  var lk = "<?=$lock?>";
  if (lk == 'Lock') {
    $("#msgdialogtitle").html("<h3 style='color: red;'>Profile Update Prohibited.</h3>"); 
    $("#msgdialogcontent").html("<p>This profile can only be changed by the Festival Registrar.</p><p>Please contact the registrar by emailing registrar@morrobaybirdfestival.org or phone at 805-555-1212 between 8 and 5 weekdays.</p>");
    $('#msgdialog').modal('toggle', { keyboard: true });
    }
  
  if ('<?=$exemptflag?>' != 'NO') {
    $("#msgdialogtitle").html("<h2 style='color: red;'>Event Scheduling Blocked</h2>"); 
    $("#msgdialogcontent").html("<p><b>Volunteers, Leaders, Speakers and Board Members can not schedule events prior to <?=$exemptionstart?></b></p><p>Policy prohibits events being scheduled by those exempted from festival event fees until 2 weeks after the announced public registration date.</p>");
    $('#msgdialog').modal('toggle', { keyboard: true });
  }
  
});

</script>
<!-- <img src="http://morrobaybirdfestival.net/wp-content/uploads/2016/08/LOGO3.png" width="400" height="100" alt="bird festival logo"> -->
<h1>Profile for <?=$id?></h1>
<?php 
// include 'Incls/vardump.inc.php';
// if ($admmode == 'ON') { exit; }

// block event scheduleing by exempted attendees
if ($exemptflag != 'NO') {
  echo "<h2 style='color: red;'>Event Scheduling Blocked</h2><ul><p><b>Volunteers, Leaders, Speakers and Board Members can not schedule events prior to $exemptionstart</b></p><br><a href='index.php' class='btn btn-primary btn-lg'>E X I T</a></ul></body></html>";
  exit;
  }
  
// if ((($lock == 'Lock') OR ($admmode != 'ON'))) {
if (($lock == 'Lock')) {
  echo '<a href="profsummary.php" class="btn btn-success">Click for a complete listing of scheduled events.</a><br><br>
  <a href="profregister.php" class="btn btn-success">Click for a copy of the completed invoice.</a><br><br>
  <ul><a href="index.php" class="btn btn-primary btn-lg">E X I T</a></ul>
  </body></html>';
  exit; } 
?>
<script>
$(function() {
  $("#infobtn").click(function() {
    // alert ("info button clicked");
      $.post("profloginJSONhelp.php",
    {
    },
    function(data, status) {
      // alert(data);      
      $("#msgdialogtitle").html("<h3 style='color: red;'>Profile Information</h3>"); 
      $("#msgdialogcontent").html(data);
      $('#msgdialog').modal('toggle', { keyboard: true });
      }
    );  // end $.post logic 
  });
});
</script>
<table class=table><tr><td width="30%">
<a href="register.php" class="btn btn-primary btn-lg">Schedule Events</a></td>
<td align=center>
<a title="Add/Delete Attendees" href="profagendas.php" id=ada class="btn btn-success"><i class="fa fa-users" aria-hidden="true"></i></a>
<!-- <a title="Add or delete attendees to the profile" href="profagendas.php" id=ada class="ex1 btn btn-default btn-xs">Add Attendee(s)</a> -->
<td align="right">
<!-- <i id=helpbtn class="fa fa-bars fa-3x">&nbsp;&nbsp;</i> --> 
<i id=infobtn title="Help information" class="ex2 fa fa-info-circle fa-3x">&nbsp;&nbsp;</i>
</td></tr></table>

<ul>
<a title="Exit the scheduling system and return to login page." href="index.php" id=LObtn class="btn btn-primary btn-lg">E X I T</a>&nbsp;&nbsp;
<a title="List of any/all scheduled events for each attendee registered on the profile." href="profsummary.php" class="btn btn-primary btn-lg">Events Scheduled</a>&nbsp;&nbsp;
<a title="Update all profile information EXCEPT registration type, event exemption and the profile name/email address." href="profnew.php?action=update" class="btn btn-primary btn-lg">Update Profile</a>
</ul>
<?php
if ($admmode == 'ON') exit;
?>
<br>

<ul>
<table border="0" class="table" width="80%">
<thead></thead>
<tbody>
<tr><td><tx>Agendas currently defined:</tx></td>
<td align=right><tx><?=$agcnt?></tx></tx></tr>
<tr><td><tx>Total events scheduled (all attendees):</tx></td>
<td align=right><tx><?=$evtcnt?></tx></tx></tr>
<tr><td><tx>Total events Wait Listed (all attendees):</tx></td>
<td align=right><tx><?=$waitcount?></tx></td></tr>
<tr><td><tx>Total Accumulated Festival Fees:</tx></td>
<td align=right><tx>$<?=$totalfees?></tx></tx></tr>
</tbody>
</table>
</ul>
<table border=0 class=table>
<tr><td><a class="btn btn-primary" href="profregister.php">Invoice</a></td>
<td align=right>
<a title="Confirmation of festval registration and all scheduled events." class="btn btn-success" href="profpayment.php?pay=<?=$totalfees?>"><b>Confirmation<br>and Payment</b></a>
</td></tr>
</table>
<br><br>
</body>
</html>

