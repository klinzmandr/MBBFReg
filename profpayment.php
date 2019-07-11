<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/checkcred.inc.php';

$pay = $_REQUEST['pay'];

// check and setup event registration start and end
$start = strtotime(getregstart());  $end = strtotime(getregend()); 
$sd = date('l, F j, Y \a\t g:i A', $start); $ed = date('l, F j, Y \a\t g:i A', $end);
$today = strtotime("now");
// echo "today: start: $sd, end: $ed<br>";
// echo "today: $today, start: $start, end: $end<br>";
// echo "formatted: sd: $sd, ed: $ed<br>";
$OKFlag = 'OFF';
if (($today >= $start) AND ($today <= $end)) {
  $OKFlag = 'ON';
  // echo "Date range check passed<br>";
  }
// echo "OKFlag: $OKFlag<br>";


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Payment</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

<style>
  p { font-size: 1.25em; }
  tx { font-size: 1.75em; }
  input[type=checkbox] { transform: scale(1.5); }
</style> 

</head>
<body>
<script>
$(function() {
  $("#PAYOPTIONS").hide();
  $("#CONFbtn").css("color", "green").css("background", "white");
  if ('<?=$OKFlag?>' == 'OFF') {
    $("#msgdialogtitle").html("<h3 style='color: red;'>Registration NOT available</h3>"); 
    $("#msgdialogcontent").html("<p><b>Confirmation and Payment</b></p><p>The period for  event registration confirmation and payments for this years festival is from <?=$sd?> to <?=$ed?>.  </p><p>Please try again within that time frame.</p>"); 
    $('#msgdialog').modal('toggle', { keyboard: true });
    }

$("#CONFbtn").click( function () {
    $.post("profpaymentJSON.php",
    {
    },
    function(data, status){
      alert("Data: " + data + "\nStatus: " + status);
      }
    );  // end $.post logic 
  $("#CONFbtn").html("Confirmation Completed");  
  $("#CONFbtn").css("color", "white").css("background", "green");  
  $("#CONFbtn").prop("disabled",true);
  $("#PAYOPTIONS").show();
  });
});
</script>
<div class=container>
<h1>Confirmation and Payment</h1>
<?php 
if ($OKFlag == 'OFF') {
  echo '<a href="proflogin.php" class="btn btn-danger">RETURN</a>';
  exit;
  }
?>
<p>Registration and confirmation of requested events is restricted to the confirmation period from <?=$sd?> to <?=$ed?>.</p>
<p>Clicking the confirmation button will mark all events as confirmed pending full payment within 7 days.  After 7 days, the Registrar reserves the right to remove all reservations from these events.</p>
<div align=center><h1><a href="proflogin.php" class="btn btn-primary">RETURN</a></h1>
<h1><button id=CONFbtn style='color: green;'>CONFIRM ALL NOW</button></h1></div>
<div id=PAYOPTIONS>
<p>Your profile has now been marked as 'CONFIRMED'.  Any changes to this profile must now be done by the Registrar.</p>
<p>Use the following options to choose how you would like to pay for Festival registration and event fees.</p>
<p>You may pay online using either VISA or MasterCard on the secure PayPal server, OR send us a check by mail to reach us within 7 days.</p>
<table class=table>
<tr><td width="50%">
<h3>Send a Check</h3>
<p>If you pay by check, make it payable to <b>Morro Bay Bird Winter Festival</b>, or <b>MBWBF</b>, and mail to:</p>
<ul>
Registrar, MBWBF<br>
P.O. Box 1175<br>
Morro Bay, CA 93443.<br>
</ul>
<p>Please note your registration profile name (email address) on the memo line.</p>
<p>Online registrations paid by mail will be held for one week pending payment.</p>
<p>Payments will only be listed after they have been processed and entered by the Event Registrar.</p>
</td>
<td>
<h3>Do an electronic payment via PayPal</h3>
<p>Click the 'Pay Now' button.  You will be directed to the secure PayPal server. Enter your total amount due of $<?=$pay?> and use your PayPal account or preferred electronic payment card information to complete the payment process.</p>
<div align=center><img src="img/PayNow.jpg" width="262" height="140" alt="PayPal Payment"></div>
<p>Payment notification will be transmitted by PayPal to the Registrar for entry into the registration system.</p>
<p>You will be able to note your payment when you next visit your profile after payment processing has been completed.</p>
</td></tr></table>
<a href="proflogin.php" class="btn btn-primary">Finished</a></h1>
</div>    <!-- payoptions -->
</div>    <!-- container -->
</body>
</html>