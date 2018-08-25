<?php
session_start();
date_default_timezone_set('Etc/UTC');

?>
<!DOCTYPE html>
<html>
<head>
<title>SMTP Test</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

<?php

// include 'Incls/vardump.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/datautils.inc.php';

if (!isset($_REQUEST['run'])) {
?>

<div class=container>
<h2>SMPT Mail Connection Test</h2>
<p>This uses the SMTP class alone to check that a connection can be made to an SMTP server, authenticate, then disconnect.</p>
<p>This has to be successful before any email can be sent.</p>
<p>SMTP needs accurate times, and the PHP time zone MUST be set This should be done in your php.ini, but this is how to do it if you don\'t have access to that.</p>

<a id=BTN href="adm_smtp_tester.php?run" class="btn btn-success">CONTINUE</a>
<br><br>
<img style="visibility: hidden;" src="img/progressbar.gif" width="250" height="29" alt="" id="PB">
</div>
<script>
$("#BTN").click(function() {
  $("#PB").css("visibility", "visible");
  });
</script>';
<?php
exit;
}

echo '<h3>Results:</h3><div class=well><pre>';
echo "check starts ....\n";
require './PHPMailer/PHPMailerAutoload.php';

//Create a new SMTP instance
$smtp = new SMTP;
$err = '';

//Enable connection-level debug output
$smtp->do_debug = SMTP::DEBUG_CONNECTION;
try {
    $err .= 'Connecting to SMTP Server<br>';
    //Connect to an SMTP server
    // if (!$smtp->connect('pacificwildlifecare.org', 25)) {
    // if (!$smtp->connect('morrobaybirdfestival.org', 25)) {
    // if (!$smtp->connect('morrobaybirdfestival.org', 465)) {
    // if (!$smtp->connect('morrobaybirdfestival.org', 587)) {
    // if (!$smtp->connect('cpanel01.digitalwest.net', 25)) {    // works!
    // if (!$smtp->connect('cpanel01.digitalwest.net', 465)) {
    if (!$smtp->connect('cpanel01.digitalwest.net', 587)) {      // works better!
        throw new Exception('Connect failed');
        $err .= 'Connection attempt failed!<br>';
    }
    $err .= 'Saying hello to server.<br>';
    //Say hello
    if (!$smtp->hello(gethostname())) {
        throw new Exception('EHLO failed: ' . $smtp->getError()['error']);
        $err .= 'EHLO failed!';
    }
    //Get the list of ESMTP services the server offers
    $e = $smtp->getServerExtList();
    //If server can do TLS encryption, use it
    if (array_key_exists('STARTTLS', $e)) {
      $tlsok = $smtp->startTLS();
      if (!$tlsok) {
        throw new Exception('Failed to start encryption: ' . $smtp->getError()['error']);
        }
      //Repeat EHLO after STARTTLS
      $err .= 'Get host name from server<br>';
      if (!$smtp->hello(gethostname())) {
        throw new Exception('EHLO (2) failed: ' . $smtp->getError()['error']);
        $err .= 'Get host name FAILED<br>';
        }
        //Get new capabilities list, which will 
        //usually now include AUTH if it didn't before
      $e = $smtp->getServerExtList();
      }
    //If server supports authentication, do it (even if no encryption)
    echo "Trying to connect with userid and password\n";
    $err .= 'Trying to connect with userid and password<br>';
    if (array_key_exists('AUTH', $e)) {
      if ($smtp->authenticate('mbbf', 'Ci1xkPyOadOr')) {
        echo "Connected ok!\n";
        $err .= 'Connected ok!<br>';
        } 
     else {
            throw new Exception('Authentication failed: ' . $smtp->getError()['error']);
            $err .= 'Authentication failed<br>';
        } 
      }
    echo "authentication test SUCCESSFUL!\n";
    }  
  catch (Exception $e) {
    echo 'SMTP error: ' . $e->getMessage(), "\n";
    $err .= 'SMTP error: ' . $e->getMessage() . '<br>';;
    }
//Whatever happened, close the connection.
$smtp->quit(true);

echo "\ncheck ends.....\n";

$err .= "SMTP test completed.<br>";
echo '</pre><br>';
echo $err;
addlogentry($err);
?>

<br>==== END OF TEST ====<br><br>
<a href="admin.php" class="btn btn-primary">RETURN</a>
</div>
</body>
</html>
