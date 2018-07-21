<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$newagenda = $_REQUEST['newagenda'];
$profname = $_SESSION['profname'];
$ag = $_REQUEST['ag'] ? $_REQUEST['ag'] : '';

// get fee roster
$feesched = readlistreturnarray('Fees');
// echo "<pre>feesched "; print_r($feesched); echo '</pre>';

// delete agendas requested
$err = '';
if ($action == 'delagenda') {
  // see if any records to delete
  foreach ($ag as $v) {
    $sql = "SELECT * FROM `regeventlog` WHERE `ProfName` = '$profname' AND `AgendaName` = '$v' AND `RecKey` = 'Evt';";
    // echo "sql: $sql<br>";
    $res = doSQLsubmitted($sql);
    $rc = $res->num_rows;
    // echo "rc: $rc<br>";
    // advise user if agenda has events that must be deleted
    if ($rc) {
      $err .= "<h3 style='color: red;'>ERROR: the agenda &quot;$v&quot; has $rc registered event(s).</h3><p><b>All scheduled events must be deleted before the agenda can be deleted.</b></p>";
      }
    else {
      $delsql = "DELETE FROM `regeventlog` WHERE `ProfName` = '$profname' AND `AgendaName` = '$v' AND `RecKey` = 'Reg';";
      // echo "delsql: $delsql<br>";
      $res = doSQLsubmitted($delsql);
      $delrc = $res->num_rows;
      $err .= "<h3>Agenda named &quot;$v&quot; successfully deleted.</h3>";
      }
    }
  }
// if (strlen($err)) $err = '<div id=ERR>' . $err . '</div>';

// query profile to get the number of full registrations agendas entered
$sql = "SELECT `regType` FROM `regprofile` WHERE `ProfileID` = '$profname';";
$pres = doSQLsubmitted($sql);
$profile = $pres->fetch_assoc();
// echo '<pre>profile '; print_r($profile); echo '</pre>';

// add new agenda if requested
if ($action == 'addagenda') {
  //echo "adding new agenda: $newagenda<br>";
  $agarray['RecKey'] = 'Reg';
  $agarray['ProfName'] = $profname;
  $agarray['AgendaName'] = "$newagenda";
  $agarray['FEE'] = $feesched[RegFull];
  // echo '<pre>new agenda '; print_r($agarray); echo '</pre>';
  sqlinsert('regeventlog', $agarray);
  }

// list all existing agenda (including new one)
$sql = "
SELECT DISTINCT `AgendaName` FROM `regeventlog` WHERE `ProfName` = '$profname' AND `RecKey` = 'Reg';";

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rcagenda = ($res->num_rows) -1 ;   // $rcagenda = number of ADDED agendas

$astr = array();
while ($r = $res->fetch_assoc()) {
  // echo '<pre>log '.$rowid.' '; print_r($r); echo '</pre>';
  $astr[] = $r[AgendaName];
  }
// echo '<pre>astr '; print_r($astr); echo '</pre>';
$agendas = '"' . implode('", "', $astr) . '"'; // for jquery validation
// echo "agendas: $agendas<br>";

// create checkbox list
$alist = '';
foreach ($astr as $v) {
  if ($v == '') continue;
  if ($v == 'SELF') {
    $alist .= "&nbsp;&nbsp;&nbsp;&nbsp;$v<br>";
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
<title>Agenda Add/Delete</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

</head>
<body>
<script>
$(function() {
  var err = "<?=$err?>";
  if (err.length > 0) {
    // alert("err: "+err);
    $("#msgdialogtitle").html("<h2 style='color: red;'>Deletion of Attendee</h2>");
    $("#msgdialogcontent").html("<p>"+err+"</p>");
    $('#msgdialog').modal('toggle', { keyboard: true });

    }
});
</script>
<style>
  p, th, td, select, button, input { font-size: 1.5em; }
  input[type=checkbox] { transform: scale(1.5); }
</style> 
<h1>Agenda Maintenance</h1>
<p>Use this page to add a new attendee to your profile or delete one or more attendees from it.  There are currently <?=$rcagenda?> attendees(s) added in addition to &apos;SELF&apos;.  The current list is:</p>
<script>
function del() {
  var cnt = $(".ag:checked").length;
  if (cnt == 0) return false;
  var c = confirm("This action can not be reversed!\n\nPlease click OK to confirm");
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

<a href="proflogin.php" class="btn btn-primary btn-lg">D O N E</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="register.php" class="btn btn-success">Sched Events</a>

</body>
</html>