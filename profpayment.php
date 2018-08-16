<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

// include 'Incls/vardump.inc.php';

$pay = $_REQUEST['pay'];
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
<script src="js/chksession.js"></script>
<style>
  p { font-size: 1.25em; }
  tx { font-size: 1.75em; }
  input[type=checkbox] { transform: scale(1.5); }
</style> 

</head>
<body>
<h1>Payments</h1>
<p>Use this page to choose how you would like to pay for the Festival registration and event fees.</p>
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

</body>
</html>