<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';

$maxAttendees = 4;    // max attendees allowed on a single profile

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$newagenda = $_REQUEST['newagenda'];
$profname = $_SESSION['profname'];
$ag = $_REQUEST['ag'] ? $_REQUEST['ag'] : '';

// get fee roster
$feesched = readlistreturnarray('Fees');
// echo "<pre>feesched "; print_r($feesched); echo '</pre>';

// delete agendas requested
$err = ''; $errtitle = '';
if ($action == 'delagenda') {
  // see if any records to delete
  $errtitle = 'Attendee Deletion';
  foreach ($ag as $v) {
    $delsql = "DELETE FROM `regeventlog` WHERE `ProfName` = '$profname' AND `AgendaName` = '$v' AND (`RecKey` = 'Reg' OR `RecKey` LIKE 'Evt%');";
    // echo "delsql: $delsql<br>";
    $res = doSQLsubmitted($delsql);
    $delrc = $res->num_rows;
    $err .= "<h3>Attendee &quot;$v&quot; successfully deleted including any/all scheduled events for this attendee.</h3>";
    }
  }

// count all existing agendas
$sql = "
SELECT DISTINCT `AgendaName` FROM `regeventlog` WHERE `ProfName` = '$profname' AND `RecKey` = 'Reg';";
//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rcagenda = $res->num_rows;   // $rcagenda = number of agendas
// echo "rcagenda: $rcagenda<br>";
$astr = array();

// create new attendee registration if not at max
if ($action == 'addagenda') {
  if ($rcagenda >= $maxAttendees) {
    $errtitle = 'Max Attendees Defined';
    $err = '<h3>The maximum number of attendees for a profile is '.$maxAttendees.  '. If more than number this is needed another profile must be created.</h3>';
    }
  else {
  // add new agenda if requested
    //echo "adding new agenda: $newagenda<br>";
    $agarray['RecKey'] = 'Reg';
    $agarray['ProfName'] = $profname;
    $agarray['AgendaName'] = "$newagenda";
    $agarray['FEE'] = $feesched[RegFull];
    // echo '<pre>new agenda '; print_r($agarray); echo '</pre>';
    sqlinsert('regeventlog', $agarray);
    }
  }

// create attendees list
// first get profile's registration name
$profsql = "SELECT `ProfFirstName`, `ProfLastName` FROM `regprofile` WHERE `ProfileID` = '$profname'"; 
$profres = doSQLsubmitted($profsql);
$prof = $profres->fetch_assoc();
$self = strtoupper($prof[ProfFirstName] .' '. $prof[ProfLastName]);
// echo "self: $self<br>";

while ($r = $res->fetch_assoc()) {
  // echo '<pre>log '.$rowid.' '; print_r($r); echo '</pre>';
  $astr[] = $r[AgendaName];
  }
// add new agenda to bottom of array if allowed
if ($rcagenda < $maxAttendees) { 
  $astr[] = $newagenda;   
  }
// echo '<pre>astr '; print_r($astr); echo '</pre>';
$agendas = '"' . implode('", "', $astr) . '"'; // for javascript validation
// echo "agendas: $agendas<br>";

// create checkbox list
$alist = '';
foreach ($astr as $v) {
  if ($v == '') continue;
  if ($v == $self) {
    $alist .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$v<br>";
    continue;
    }
  $alist .= "<input class=ag type=checkbox value='$v' name=ag[]>&nbsp;$v<br>
"; 
  }
if ($alist == '') $alist = '<h3>No additional agendas defined</h3>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add/Delete Attendees</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

</head>
<body>
<script>
$(function() {
  var errtitle = "<?=$errtitle?>";
  var err = "<?=$err?>";
  if (err.length > 0) {
    // alert("err: "+err);
    $("#msgdialogtitle").html("<h2 style='color: red;'>"+errtitle+"</h2>");
    $("#msgdialogcontent").html("<p>"+err+"</p>");
    $('#msgdialog').modal('toggle', { keyboard: true });

    }
});
</script>
<style>
  p, th, td, select, button, input { font-size: 1.5em; }
  input[type=checkbox] { transform: scale(1.5); }
</style> 
<h1>Add/Delete Attendees</h1>
<p>Use this page to add a new attendee to your profile or delete one or more attendees from it.  There are currently <?=$rcagenda?> attendees(s) registered.  The current list is:</p>
<script>
function del() {
  var cnt = $(".ag:checked").length;
  if (cnt == 0) return false;
  var c = confirm("This action will delete the attendee resgistration(s) for all those checked as well as any scheduled events.\n\nTHIS CAN NOT BE REVERSED.!\n\nPlease click OK to confirm");
  if (c) { return true; }
  else { return false; }
  } 
</script>
<ul>
<h2></h2>
<form action="profagendas.php" method="post"  onsubmit="return del()">
<ul><?=$alist?></ul>
<input type=hidden name=action value=delagenda><br>
<input type=submit name=submit value="Delete checked attendee(s)">
</form>
<h4>NOTE: any scheduled events for the attendee(s) will also be deleted.</h4>
</ul>

<script>
function chk() {
  var A = $("#N").val();
  if (A.length == 0) return false;
  var N = A.toUpperCase();
  $("#N").val(N);             // new agenda name
  var a = [<?=$agendas?>];    // array of existing agenda names
  var i = $.inArray(N, a); 
  if (i >= 0) {
    // alert("Name entered already used.");
    $("#msgdialogtitle").html("<h2 style='color: red;'>Agenda Name Error</h2>");
    $("#msgdialogcontent").html("<p>The name provided is already in use.  Please try another.</p>");
    $('#msgdialog').modal('toggle', { keyboard: true });
    return false;
    }
  }
</script>
<ul>
<br><h3>Add new attendee:</h3>
<form action="profagendas.php" method="post"  onsubmit="return chk()">
<input id=N type=text name=newagenda value='' autofocus placeholder="New attendee name">
<input type=hidden name=action value=addagenda>
<input id=x type=submit value="Add new attendee" name=submit>
</form></p>
</ul>
<br><br>

<a href="proflogin.php" class="btn btn-primary btn-lg">RETURN</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="register.php" class="btn btn-success">Sched Events</a>

</body>
</html>