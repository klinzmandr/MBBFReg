<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registration Admin</title>
<!-- Bootstrap -->
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<link href="css/bs3dropdownsubmenus.css" rel="stylesheet">
</head>
<body>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

<img src="https://morrobaybirdfestival.org/wp-content/uploads/2016/08/LOGO3.png" width="400" height="100" alt="bird festival logo" >
<?php
// echo '<pre>Server '; print_r($_SERVER); echo '</pre>';
// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/checkcred.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

$res1 = doSQLsubmitted("SELECT DISTINCT `ProfName` FROM `regeventlog` WHERE 1;");
$profilecount = $res1->num_rows;
//echo "profilecount: $profilecount<br>";

$res2 = doSQLsubmitted("SELECT `ProfName`, `AgendaName` FROM `regeventlog` WHERE 1 GROUP BY `ProfName`, `AgendaName`;");
$attendeecount = $res2 ->num_rows;
// echo "attendeecount: $attendeecount<br>";

$sql = "SELECT `events`.`Trip`, `events`.`Event`, `events`.`MaxAttendees`, `regeventlog`.`EvtRowID`, count(`regeventlog`.`RowNbr`) AS 'cnt' 
FROM `regeventlog`, `events`
WHERE `regeventlog`.`RecKey`='Evt'
  AND `regeventlog`.`EvtRowID` = `events`.`RowID`
GROUP BY `regeventlog`.`EvtRowID`";
$res3 = doSQLsubmitted($sql);
$evtcount = 0;
while ($r = $res3->fetch_assoc()) {
  if ($r[cnt] >= $r[MaxAttendees]) $evtcount++;
  } 
// echo "evtcocunt: $evtcount<br>";

$res4 = doSQLsubmitted("SELECT DISTINCT `ProfileID` FROM `regprofile` WHERE `PayLock` = 'Lock';");
$confirmedcount = $res4->num_rows;
// echo "PayLockcount: $profilecount<br>";

$res5 = doSQLsubmitted("SELECT DISTINCT `ProfileID` FROM `regprofile` WHERE `Exempt` = 'Approved';");
$exemptcount = $res5->num_rows;
// echo "exemptcount: $exemptcount<br>";

$mon = '<i style="color: red;" class="fa fa-ban fa-2x fa-fw"></i>';
if (file_exists('reg.evt.monitor.LOCK'))
  $mon = '<i style="color: green;" class="fa fa-cog fa-spin fa-2x fa-fw"></i>';

// process login info validation
if ($action == 'auth') {
  $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : "";
  $pw = isset($_REQUEST['pw']) ? $_REQUEST['pw'] : "";
  $combo = $uid . ':' . $pw;
  $pwds = readlistarray('Users');
  if (!in_array($combo, $pwds)) {
    echo '<h3 style="color: red; ">Invalid user id and/or password.</h3>';
    session_unset();    
    //session_destroy();
    //session_start(); 
    $action = '';
    }
  else {
    // echo 'user id and password valid<br>';
    $_SESSION['REGSessionUser'] = $uid;
    $_SESSION['REGSessionActive'] = date("Y-m-d H:i:s");
    addlogentry("Logged In");
    $action = '';
    }
  }   

// output menu if session var is loaded
if (isset($_SESSION['REGSessionUser'])) {
  $start = date('l, F j, Y \a\t g:i A', strtotime(geteventstart()));
  // echo "event start: $start<br>";

include 'Incls/mainmenu.inc.php';

  echo '
<h3>Registration Administration&nbsp;&nbsp;<a href="adminsto.php?lo=lo" class="btn btn-danger">Log Out</a>
</h3>

<h3>Dashboard</h3><ul>
';
showstats();
echo '</ul><br><br>

<div class="well">
<h4>GPL License</h4>
<p>Registration Admin - Copyright (C) 2017 by Pragmatic Computing, Morro Bay, CA</p>
    <p>This program comes with ABSOLUTELY NO WARRANTY.  This is free software.  It may be redistributed under certain conditions.  See &apos;Help->About Event Admin&apos; for more information.</p>
</div>
</body>
</html>';
exit;
}

// display login page to get userid and pw
if ($action == '') {
  echo '
<div class="container">
<h3>Registration Administration</h3>
<h4>Please provide login information:</h4>
<form action="admin.php" method="post"  id="login">
<input type="text" name="uid"  placeholder="User Name" autofocus>
<input type="text" name="pw"  placeholder="Password">
<input type="hidden" name="action" value="auth">
<button name="sb" type="submit" form="login">LOG IN</button>
</form>
<br><br>
<h3>Dashboard</h3><ul>';
showstats();
echo '</ul><br><br>
<div class="well">
<h4>GPL License</h4>
<p>Registration Admin -- Copyright (C) 2013 by Pragmatic Computing, Morro Bay, CA</p>
    <p>This program comes with ABSOLUTELY NO WARRANTY.  This is free software.  It may be redistributed under certain conditions.  See <a href="LICENSE.pdf" target="_blank" title="Software License">this PDF of the GNU Public License</a> for more information.</p>
</div>
';
}

function showstats() {
  global $mon, $profilecount, $confirmedcount;
  global $attendeecount, $exemptcount,$evtcount;
  echo '
<h4>Event Monitor: '.$mon.'</h4>
<h4>Profiles Registered: '.$profilecount.'</h4>
<h4>Registrations Confirmed: '.$confirmedcount.'</h4>
<h4>Attendees Registered: '.$attendeecount.'</h4>
<h4>Approved Exemptions: '.$exemptcount.'</h4>
<h4>Events at Capacity: '.$evtcount.'</h4>
';
}
?>
</body>
</html>