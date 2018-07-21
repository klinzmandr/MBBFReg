<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 


$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$profname = isset($_REQUEST['profname']) ? $_REQUEST['profname'] : '';

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

if ($action == 'wipe') {
  // do actual sql deletes in this block
  $xMsg = "<h4 id=xMsg>Wipe action completed for profile $profname.</h4>";  
  }

$Msg = '';
if ($action == 'apply') {
  // rcpro is count of profiles deleted
  $res = doSQLsubmitted("SELECT * FROM `regprofile` WHERE `ProfileID` = '$profname';");
  $rcpro = $res->num_rows;
  // rcag is count of agendas
  $res = doSQLsubmitted("SELECT DISTINCT `AgendaName` FROM `regeventlog` WHERE `ProfName` ='$profname'");
  $rcag = $res->num_rows;
  // rcevt is count of events
  $res = doSQLsubmitted("SELECT * FROM `regeventlog` WHERE `RecKey` ='Evt' AND `ProfName` = '$profname'");
  $rcevt = $res->num_rows;
  // rcpay is count of payments
  $res = doSQLsubmitted("SELECT * FROM `regeventlog` WHERE `RecKey` ='Pay' AND `ProfName` = '$profname'");
  $rcpay = $res->num_rows;
  $Msg = "<h3 id=Msg style='color: red;'>Delete action for profile $profname will be that $rcpro profile, $rcag agenda(s), $rcevt event(s) and $rcpay payment(s) will be deleted.<br><br>
  <a href='admprofilewipeout.php?action=wipe' class='btn btn-danger'>CONTINUE</a><br><br></h3>";
  
  }

$sql = "SELECT * FROM `regprofile` WHERE 1 = 1;";
//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

$tr = '';
while ($r = $res->fetch_assoc()) {
  // echo '<pre>profile '; print_r($r); echo '</pre>';
  $tr .= "<tr style='cursor: pointer;'><td>$r[ProfileID]</td><td>$r[Exempt]</td><td>$r[ProfFirstName]</td><td>$r[ProfLastName]</td><td>$r[ProfAddress]</td><td>$r[ProfCity]</td><td>$r[ProfState]</td><td>$r[ProfZip]</td></tr>";
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile Wipout</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<script src="js/chksession.js"></script>

</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
?>

<h1>Profile Wipeout</h1>&nbsp;&nbsp;&nbsp;&nbsp;
<p>Thif function will completely delete a registered profile incuding all agendas defined and assocated scheduled events.</p>
<p>Any scheduled events will immediately become available for others to select.</p>
<h4>CAUTION:  this action can NOT BE RECOVERED.  Once the profile and its assoicated elements are deleted they can not be restored.</h4>

<p>Select the target profile from the following list:</p>

<script>
$(function() {
  $("#filter").focus();
  $("#xMsg").fadeOut(5000);
$("tr").click(function() {
  var p = $(this).find('td').first().text(); // get profile name from col 1
  if (!p.length) return;
  var c = confirm("This action for profile "+p+" can not be recovered.\n\nPlease click OK to confirm.");
  if (!c) { return; }   // action cancelled
  $("#INp").val(p);
  // console.log($(this).find('td').first().text());
  // console.log(p);
  $("#proform").submit();
  });
});
</script>
<form action="admprofilewipeout.php" method="post"  id="proform">
<input id="INp" name=profname type=hidden value=''>
<input type=hidden name=action value="apply">
</form>
</script>
<?=$xMsg?>
<?=$Msg?>
<input id=filter autofocus placeholder='Filter'>&nbsp;&nbsp;<button id=filterbtn2>Reset</button>
<table class=table>
<tr id=head><th>ProfileID</th><th title="Indicates if an exemption of fees was requested. Marked &apos;appoved&apos; only by Festival Registrar using this utility.">Exempt</th><th>FIrstName</th><th>LastName</th><th>Address</th><th>City</th><th>ST</th><th>Zip</th><th>PhoneNbr</th></tr>
<?=$tr?>
</table>
=== END LIST ===<br>

</div> <!-- container -->
</body>
</html>