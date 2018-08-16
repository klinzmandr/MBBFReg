<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$f = $_REQUEST['f'];
$id = $_SESSION['profname'];  // get profile name for session

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$sql = "SELECT * FROM `regprofile` WHERE 1 = 1;";
//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

$tr = '';
while ($r = $res->fetch_assoc()) {
  // echo '<pre>profile '; print_r($r); echo '</pre>';
  $tr .= "<tr style='cursor: pointer;'><td>$r[ProfileID]</td><td>$r[PayLock]</td><td>$r[Exempt]</td><td>$r[ProfFirstName]</td><td>$r[ProfLastName]</td><td>$r[ProfAddress]</td><td>$r[ProfCity]</td><td>$r[ProfState]</td><td>$r[ProfZip]</td></tr>";
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile Maintenance</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
</head>
<body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<?php
include 'Incls/mainmenu.inc.php';
?>

<h1>Profile Maintenance</h1>
<script>
$(function() {
  $("#filter").focus();
  $("#xMsg").fadeOut(5000);
$("tr").click(function() {
  var p = $(this).find('td').first().text(); // get profile name from col 1
  if (!p.length) return;
  $("#INp").val(p);
  console.log($(this).find('td').first().text());
  console.log(p);
  $("#proform").submit();
  });
});
</script>
<form action="admprofileupdateform.php" method="post"  id="proform">
<input id="INp" name=profname type=hidden value=''>
<input type=hidden name=action value="">
</form>
</script>
<input id=filter autofocus placeholder='Filter'>&nbsp;&nbsp;<button id=filterbtn2>Reset</button>
<table class=table>
<tr id=head><th>ProfileID</th><th title="Locked status indicates payment(s) have been made.  Use this update to change this status if necessary.">Locked?</th><th title="Indicates if an exemption of fees was requested. Marked &apos;appoved&apos; only by Festival Registrar using this utility.">Exempt</th><th>FIrstName</th><th>LastName</th><th>Address</th><th>City</th><th>ST</th><th>Zip</th><th>PhoneNbr</th></tr>
<?=$tr?>
</table>
=== END LIST ===<br>
</body>
</html>