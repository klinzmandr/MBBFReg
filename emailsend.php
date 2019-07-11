<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 
?>
<!DOCTYPE html>
<html>
<head>
<title>Send Email</title>
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

$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';


if ($action == '') {
  if ($rowid != '') {
    $res = doSQLsubmitted("SELECT * FROM `regprofile` where `RowID` = '$rowid';");
    $p = $res->fetch_assoc();
    // echo '<pre>Profile '; print_r($p); echo '</pre>';
    $res1 = doSQLsubmitted("SELECT `ProfName`, SUM(`FEE`) AS 'totfees', SUM(`Payment`) AS 'totpayments' FROM `regeventlog` WHERE `ProfName` = '$p[ProfileID]';");
    $a = $res1->fetch_assoc();
    if ($a[totfees] == '') $a[totfees] = '0';
    if ($a[totpayments] == '') $a[totpayments] = '0';
    $a[profbal] = $a[totfees] - $a[totpayments];
    // echo '<pre>agenda totals '; print_r($a); echo '</pre>';
  }
  $to = $p[ProfileID];
  
?>
<div class="container">
<h3>Send Email Message</h3>
<p>This email will be sent FROM the registrar@morrobaybirdfestival.net.</p>
<script>
function setup(rep) {
  var val = rep;
  var tar = '#'+rep;
  $("#area1").html($(tar).html());
  var strNewString =  $('#area1').html().replace(/\[firstname\]/g, "<?=$p[ProfFirstName]?>");
  strNewString = strNewString.replace(/\[lastname\]/g, "<?=$p[ProfLastName]?>");
  strNewString = strNewString.replace(/\[contactnbr\]/g, "<?=$p[ProfContactNumber]?>");
  strNewString = strNewString.replace(/\[totfees\]/g, "<?=$a[totfees]?>");
  strNewString = strNewString.replace(/\[totpayments\]/g, "<?=$a[totpayments]?>");
  strNewString = strNewString.replace(/\[profbal\]/g, "<?=$a[profbal]?>");
	$('#area1').html(strNewString);
	if (strNewString.length < 10) {
	  alert("Reply has not been created.")
	  return false;
    }
  return true;
  }
</script>
<b>Choose and edit a predefined message or compose your own.</b><br>
<a class="btn btn-info" onclick=setup("Reply1")>Reply 1</a>&nbsp;&nbsp;
<a class="btn btn-info" onclick=setup("Reply2")>Reply 2</a>&nbsp;&nbsp;
<a class="btn btn-info" onclick=setup("Reply3")>Reply 3</a>&nbsp;&nbsp;
<a class="btn btn-info" onclick=setup("Reply4")>Reply 4</a>&nbsp;&nbsp;
<a class="btn btn-info" onclick=setup("Reply5")>Reply 5</a>&nbsp;&nbsp;
<a class="btn btn-info" onclick=setup("Reply6")>Reply 6</a><br><br>

<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
bkLib.onDomLoaded(function() {
  var notes = new nicEditor({fullPanel:true}).panelInstance("area1");
  });    

</script>

<script type="text/javascript">
function chkemail(form) {
	// var subj = form.subject.value.length;
	var subj = $("[name=subject]").val();
	if (subj.length == 0) {
		alert("Subject line is empty.");
		return false;
		}
	var div_val=document.getElementById("area1").innerHTML;
	var body = $("#area1").html();
  // if(div_val=='<br>'){
  if (body.length <= 4) {
    alert("Nothing entered in the email message.");
    return false;
    }
  document.getElementById("ta").value = div_val;
  //alert("OK to send");
  return true;
	}
</script>

<form name="emf" class="form" action="emailsend.php" method="post" onsubmit="return chkemail(this)">
To: <input autofocus type=email name=toaddr value='<?=$to?>'><br>
From: registrar@morrobaybirdfestival.net<br>
<br />
Subject:<br />
<input type="text" name="subject" size="90" style="width: 500; "  placeholder="Subject"><br />
Message:<br />
<div style="font-size: 16px; background-color:#FFF; padding: 3px; border: 1px solid #000;" id="area1"></div>
<textarea style="display:none;" id="ta" name="body"></textarea><br />
<input type="hidden" name="action" value="send">
<input type="submit" name="Submit" value="Send"><br />
</form>
<br>
</div>  <!-- container -->

<div style="visibility: hidden; " id="Reply1">
<?php include 'emreplys/emailReply1.inc.php'; ?>
</div>
<div style="visibility: hidden; " id="Reply2">
<?php include 'emreplys/emailReply2.inc.php'; ?>
</div>
<div style="visibility: hidden; " id="Reply3">
<?php include 'emreplys/emailReply3.inc.php'; ?>
</div>
<div style="visibility: hidden; " id="Reply4">
<?php include 'emreplys/emailReply4.inc.php'; ?>
</div>
<div style="visibility: hidden; " id="Reply5">';
<?php include 'emreplys/emailReply5.inc.php'; ?>
</div>
<div style="visibility: hidden; " id="Reply6">
<?php include 'emreplys/emailReply6.inc.php'; ?>
</div>
</body></html>

<?php
  exit;
  }

// sending the message starts here
// this has been tested and working as of 2018-09-22
// NOTE:  actual send currently DISABLED at line 188
// include 'Incls/vardump.inc.php';
include '../MBBFDBParamInfo';

$from = 'registrar@morrobaybirdfestival.org';
$addr = $_REQUEST['toaddr'];
$specto = htmlentities($_REQUEST['toaddr']);
$subject = $_REQUEST['subject'];
$body = stripslashes($_REQUEST['body']);

// create and log message to call history
$notes = 'Email message sent to caller as follows:<br>';
$notes .= 'To: ' . $specto . '<br>';

$trans = array("\\" => '', "\n" => '', "\t"=>'', "\r"=>'');
$subject = strtr($subject, $trans);
$message  = strtr($body, $trans);

$notes .= 'Subject: ' . $subject . '<br>';
$notes .= 'Message: ' . $body . '<br>';

//  echo '<pre>NOTE '; print_r($notes); echo '</pre>';

// establish and set up smtp mail object
require 'PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use), 1 = client messages, 2 = client and server messages
$mail->SMTPDebug = 0;
//Ask for text-friendly debug output
$mail->Debugoutput = 'echo';
$mail->Host = 'cpanel01.digitalwest.net';
$mail->SMTPAuth = true;
$mail->SMTPKeepAlive = false;
$mail->Port = 587;
$mail->Username = EmailUserID;
$mail->Password = EmailPassWord;
$mail->setFrom($from, '');
$mail->addReplyTo($from, '');
$mail->Subject = $subject;
// echo '<pre>'; print_r($mail); echo '</pre>';
$mail->msgHTML($message);
$mail->AltBody = 'To view this message, please use an HTML compatible email viewer!';
$mail->addAddress($addr, '');

//echo '<pre>'; print_r($mail); echo '</pre>';

// set up done - send it
if (!$mail->send()) {
// if (1 == 2) {  // force error for debugging
	echo "<br>MAILER ERROR (" . str_replace("@", "&#64;", $addr) . ') ' . $mail->ErrorInfo . "<br />\n";
  $errmsg .= "<br>MAILER ERROR (" . str_replace("@", "&#64;", $addr) . ') ' . $mail->ErrorInfo . '<br />';
  addlogentry($errmsg);
  }
addlogentry($notes);

// done!
echo '<h2>Email message sent</h2>
<p>The message will be able to be reviewed by using the <a href="admlogviewer.php" target=_blank>Log Viewer</a> which will also available in the Reports menu</p><br>
<!-- <img src="img/Under_Construction.gif" width="310" height="186" alt=""> -->
<br><br>

</body>
</html>';

// ====================== functions ==============
function clickable($string) {     // convert URLs to clickable links
	// if anchors already exist - don't translate
	if (stripos($string,'<a ') !== FALSE) return($string); 
  // make sure there is an http:// on all URLs
  $string = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2",$string);
  // make all URLs links
  $string = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<A target=\"_blank\" href=\"$1\">$1</A>",$string);
  // make all emails hot links
  $string = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<A HREF=\"mailto:$1\">$1</A>",$string);
  return $string;
	}

?>

