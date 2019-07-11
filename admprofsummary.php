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
  $emlink = "<a href='emailsend.php?rowid=$r[RowID]'><i class='fa fa-envelope fa-2x' aria-hidden='true' title='Create and send email to registrant'></i></a>";
  $tr .= "<tr style='cursor: pointer;'><td>$r[ProfileID]</td><td>$emlink</td><td>$r[regType]</td><td>$r[PayLock]</td><td>$r[Exempt]</td><td>$r[SLBM]</td><td>$r[Vol]</td><td>$r[ProfFirstName]</td><td>$r[ProfLastName]</td></tr>";
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile Event Summary</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<link href="css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<?php
include 'Incls/mainmenu.inc.php';
?>

<h1>Profile Event Summary</h1>
<script>
$(function() {
  $("#filter").focus();
  $("#xMsg").fadeOut(5000);
$("tr").click(function() {
  var p = $(this).find('td').first().text(); // get profile name from col 1
  if (!p.length) return;
  $("#INp").val(p);
  // console.log($(this).find('td').first().text());
  // console.log(p);
  $("#proform").submit();
  });
});
</script>
<form action="admprofsummaryform.php" method="post"  id="proform">
<input id="INp" name=profname type=hidden value=''>
<input type=hidden name=action value="">
</form>

<input id=filter autofocus placeholder='Filter'>&nbsp;&nbsp;<button id=filterbtn2>Reset</button>
<table class=table>
<tr id=head><th>ProfileID</th><th title='Create and send an email to registrant'>Email</th><th title="Indicates the type of registration for the profile.">RegType</th><th title="Indicates if the profile has been locked due to a payment or by the Registrar.">Lock</th><th title="Indicates if an exemption of fees was requested. Marked &apos;appoved&apos; only by Festival Registrar using this utility.">Exempt</th><th>SLBM</th><th>Vol</th><th>FIrstName</th><th>LastName</th></tr>
<?=$tr?>
</table>
=== END LIST ===<br>
</body>
</html>