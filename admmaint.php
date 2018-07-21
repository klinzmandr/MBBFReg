<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$f = $_REQUEST['f'];

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$sql = "SELECT * FROM `regprofile` WHERE 1 = 1;";

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

$tr = '';
while ($r = $res->fetch_assoc()) {
  // echo '<pre>profile '; print_r($r); echo '</pre>';
  $tr .= "<tr style='cursor: pointer;'><td>$r[ProfileID]</td><td>$r[ProfFirstName]</td><td>$r[ProfLastName]</td><td>$r[ProfAddress]</td><td>$r[ProfCity]</td><td>$r[ProfState]</td><td>$r[ProfZip]</td></tr>";
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Payments</title>
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

<h1>Agenda/Event Maintenance</h1>
<script>
$(function() {
  $("#filter").focus();
  $("#xMsg").fadeOut(5000);
$("tr").click(function() {
  var p = $(this).find('td').first().text(); // get profile name from col 1
  $("#INp").val(p);
  console.log($(this).find('td').first().text());
  console.log(p);
  $("#proform").submit();
  });
$("#CAN").click( function(event) {
  event.preventDefault();
  $("#filter").focus();
  });
});
</script>
<form id="proform" action="admmaintframe.php">
<input id="INp" name=profname type=hidden value=''>
</form>
</script>
<?=$updmsg?>
<input id=filter autofocus placeholder='Filter'><button id=filterbtn2>Reset</button>
<table class=table>
<tr id=head><th>ProfileID</th><th>FIrstName</th><th>LastName</th><th>Address</th><th>City</th><th>ST</th><th>Zip</th><th>PhoneNbr</th></tr>
<?=$tr?>
</table>
=== END LIST ===<br>
</body>
</html>