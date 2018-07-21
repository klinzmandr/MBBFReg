<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$f = $_REQUEST['f'];

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

if ($action == 'pay') {
  sqlinsert("regeventlog", $f);
  $updmsg = '<h3 id="xMsg" style="color: red;">Update complete</h3>';
  // echo 'payment processing<br>';
  }

$res = doSQLsubmitted("SELECT `ProfName`, SUM(`FEE`) AS 'totfee', SUM(`Payment`) AS 'totpay' FROM `regeventlog` WHERE 1=1 GROUP BY `ProfName`;");
$rc = $res->num_rows;
// echo "rowcount: $rc<br>";
while ($r = $res->fetch_assoc()) {
  $paytot[$r[ProfName]] += $r[totpay];
  $feetot[$r[ProfName]] += $r[totfee];
  // $paytot[$r[ProfName]] -= $r[totpay];
  }

$sql = "SELECT * FROM `regprofile` WHERE 1 = 1;";
//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$tr = '';
while ($r = $res->fetch_assoc()) {
  // echo '<pre>profile '; print_r($r); echo '</pre>';
  $id = $r[ProfileID];
  $p = number_format($paytot[$id], 2);
  $f = number_format($feetot[$id], 2);
  $baldue = number_format(($f - $p), 2);
  if (!isset($paytot[$id])) $p = '0.00';
  if (!isset($feetot[$id])) $p = '0.00';
  $tr .= "<tr style='cursor: pointer;'><td>$id</td><td align=right>$$f</td><td align=right>$$p</td><td align=right>$$baldue</td><td>$r[ProfFirstName]</td><td>$r[ProfLastName]</td><td>$r[ProfAddress]</td><td>$r[ProfCity]</td><td>$r[ProfState]</td><td>$r[ProfZip]</td><td>$r[ProfContactNumber]</td></tr>";
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

<h1>Payments</h1>
<script>
var p = '';
$(function() {
  $("#payform").hide();
  $("#filter").focus();
  $("#xMsg").fadeOut(5000);

$("tr").click(function() {
  $("#payform").toggle();
  p = $(this).find('td').first().text();
  // console.log($(this).find('td').first().text());
  $("#PN").val(p);
  $("#PI").text(p);
  $("#AMT").focus();
  });

$("#CAN").click( function(event) {
  event.preventDefault();
  $("#payform").hide();
  $("#filter").focus();
  });

$("#HIST").click(function() {
  $.post("admpaymentsJSON.php",
  {
    profile: p
  },
  function(data, status){
    // alert("Data: " + data + "\nStatus: " + status);
    $("#msgdialogtitle").html("<h3>Payment History for "+p+"</h3>"); 
    $("#msgdialogcontent").html(data);
    $('#msgdialog').modal('toggle', { keyboard: true });
    });  // end $.post logic 

  });
});
</script>

<div id=payform>

<h3>New payment for profile: <span id=PI></span></h3>
<p>NOTE: enter a negative amount for a refund.</p>
<form action="admpayments.php" method="post">
Amount: <input id=AMT type=number name=f[Payment] value=''><br>
Notes: <input type=text name=f[ProfNotes] style="width: 350px;" value=''><br>
<input type="hidden" name="f[ProfName]" id="PN" value=''>
<input type="hidden" name="f[RecKey]" value='Pay'>
<input type="hidden" name="action" value='pay'>
<input type=submit name=submit value=Submit>&nbsp;&nbsp;<button id=CAN>CANCEL</button>
</form><br>
<button id=HIST class="btn btn-success">Show Payment History</button>
<br><br>
</div>  <!-- payform -->
<?=$updmsg?>
<input id=filter autofocus placeholder='Filter'>&nbsp;&nbsp;<button id=filterbtn2>Reset</button>
<table class=table>
<tr id=head><th>ProfileID</th><th>TotFees</th><th>TotPay</th><th>BalDue</th><th>FIrstName</th><th>LastName</th><th>Address</th><th>City</th><th>ST</th><th>Zip</th><th>PhoneNbr</th></tr>
<?=$tr?>
</table>

</body>
</html>