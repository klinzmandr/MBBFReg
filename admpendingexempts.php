<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

$exempt = isset($_REQUEST['exempt']) ? $_REQUEST['exempt'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$f = $_REQUEST['f'];      // profile name

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

if ($action == 'upd') {
  // echo 'profile update requested<br>';
  $updmsg = '<h3 id="xMsg" style="color: red;">Update complete</h3>';
  $updarray[Exempt] = $exempt; // update profile with exempt info
  sqlupdate('regprofile', $updarray, "`ProfileID` = '$f[ProfName]'");
  
  }

// $sql = "SELECT * FROM `regprofile` WHERE `Exempt` <> 'NO';";
$sql = "SELECT * FROM `regprofile` WHERE 1 = 1;";
//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$tr = '';
while ($r = $res->fetch_assoc()) {
  // echo '<pre>profile '; print_r($r); echo '</pre>';
  $id = $r[ProfileID];
  $emlink = "<a href='emailsend.php?rowid=$r[RowID]'><i class='fa fa-envelope fa-2x' aria-hidden='true' title='Create and send email to registrant'></i></a>";
  $tr .= "<tr class='ROW' style='cursor: pointer;'><td>$id</td><td>$emlink</td><td>$r[Exempt]</td><td>$r[ProfFirstName]</td><td>$r[ProfLastName]</td><td>$r[ProfAddress]</td><td>$r[ProfCity]</td><td>$r[ProfState]</td><td>$r[ProfZip]</td><td>$r[ProfContactNumber]</td></tr>
";
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
<link href="css/font-awesome.min.css" rel="stylesheet">
</head>
<body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<?php
include 'Incls/mainmenu.inc.php';
?>

<h1>Exemption Approvals</h1>
<script>
var p = '';
$(function() {
  $("#exemptform").hide();
  $("#filter").focus();
  $("#xMsg").fadeOut(5000);

$("tr.ROW").click(function() {
  $("#exemptform").toggle();
  p = $(this).find('td').first().text();  // profile name
  $("#PN").val(p);
  $("#PI").text(p);
  console.log(p);
  var l = $(this).find('td:nth-child(3)').text();
  console.log(l);
  $("[name=exempt]").val(l);
  });

$("#CAN").click( function(event) {
  event.preventDefault();
  $("#exemptform").hide();
  $("#filter").focus();
  });


});
</script>
<style> input[type=checkbox] { zoom: 2; } </style>
<div id=exemptform>
<h3>Update exemption setting for profile: <span id=PI></span></h3>
<form action="admpendingexempts.php" method="post">

<h4 title="Profile exemption status.">Exemption Status: 
<select name=exempt>
<option value=NO>NO</option>
<option value=YES>YES</option>
<option value=APPROVED>APPROVED</option>
</select>

<input type="hidden" name="f[ProfName]" id="PN" value=''>
<input type="hidden" name="action" value='upd'>&nbsp;&nbsp;
<input type=submit name=submit value=Submit>&nbsp;&nbsp;<button id=CAN>CANCEL</button>
</form></h4><br>
<br>
</div>  <!-- exemptform -->
<?=$updmsg?>
<input id=filter autofocus placeholder='Filter'>&nbsp;&nbsp;<button id=filterbtn2>Reset</button>
<table class=table>
<tr id=head><th>ProfileID</th><th title='Create and send an email to registrant'>Email</th><th>Status</th><th>FIrstName</th><th>LastName</th><th>Address</th><th>City</th><th>ST</th><th>Zip</th><th>PhoneNbr</th></tr>
<?=$tr?>
</table>

</body>
</html>