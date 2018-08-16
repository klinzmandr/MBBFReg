<?php 
error_reporting(E_ERROR | E_WARNING | E_PARSE);

if (session_id() !== "") { 
  session_unset();
  session_destroy();
  }

session_start();
 
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/checkcred.inc.php';
// include 'Incls/vardump.inc.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Event Registration</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

</head>
<body>
<div class="container">
<img src="http://morrobaybirdfestival.net/wp-content/uploads/2016/08/LOGO3.png" alt="bird festival logo" >
<script>
$(function() {
  $("#CRbtn").attr("disabled", "disabled");
  $("#LIbtn").attr("disabled", "disabled");
  // ${".btn").attr("disabled", true);
  // alert ("loading alert");
  $("#CR").hide();
  $("#LI").hide();
$("#CRbtn").click(function() { 
  $("#CR").toggle(); 
  $("#LI").hide(); 
  $("#EM1").focus();
  });
$("#LIbtn").click(function() { 
  $("#LI").toggle(); 
  $("#CR").hide(); 
  $("#EM2").focus();
  });
});

function chkem(form) {
  // console.log(form.val());
  var email = form.profname.value;
  if (!email.length) {
    alert("no profile name entered.");
    return false;
    }
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  if (!re.test(String(email).toLowerCase())) { 
    alert("Invalid email address format entered.");
    return false;
    }
  }

</script>
<div clas="container">
<h2>Registration and Event Scheduling</h2>
<script src="js/captchascript.js"></script>
<div id=captbl>
<table border="0" align="left" width=100%>
<tr>
<td align=right width=20%>
<img src='captchabase/kcaptchagen.php?<?php echo session_name()?>=<?php echo session_id()?>'/>
</td>
<td>Enter validation string.<br>
<input id=KS type="text" name="keystring" autofocus val=''><br>
<button id=cap>Validate String</button>&nbsp;&nbsp;
<a class="btn btn-default btn-sm" href="index.php">New String</a></td>
</tr>
</table>
<br><br><br><br>
</div>

<h3><button id=CRbtn class="btn btn-primary btn-lg">Create a registration profile</button></h3>
<div id="CR">
<p>A new profile is created using a unique email address.  This address may be used to communicate with the registrant and will only be used by the Morro Bay Bird Festival.</p>
<form name=f1 action="profnew.php" method="post" onsubmit="return chkem(this)">
New Profile ID:
<input id=EM1 name=profname type=email value=''>
<input type=hidden name=action value=new>
<input type=hidden name=newxmpt>
<input type=submit name=submit value="Create new registration profile">
</form>
<hr>
</div>
<h3><button id=LIbtn class="btn btn-primary btn-lg">Sign into an existing registration profile</button></h3>
<div id=LI>
<p>A profile is used access to an exiting profile using a unique email address.  This address may be used to communicate with the registrant and will only be used by the Morro Bay Bird Festival regarding festival information.</p>
<form id=IN name=f2 action="proflogin.php" method=post onsubmit="return chkem(this)">
Profile ID: <input id=EM2 name=profname type=email value=''>
<input type=submit name=submit value='Access Reg. Profile'>
</form>
</div>
<br>

<b>NOTES:</b>
<ol>
  <li>An introductory video is available at <a href="https://youtu.be/r3or80VVyzo" target=_blank>Event Registration System</a> to allow review of this system before starting.</li>
  <li>A Festival profile is used to register on or more attendees and schedule attendance to all festival events.</li>
	<li>Your email address is used to uniquely identify your Festival schedule.</li>
	<li>Each Full Festival registration profile may have multiple attendee agendas associated with it.  Single day registrations are limited to a single attendee./li>
	<li>All profiles and associated attendee agendas are deleted at after the Festival has concluded.</li>
</ol>
<div class="well">
<h4>GPL License</h4>
<p>Event Planning System -- Copyright (C) 2013 by Pragmatic Computing, Morro Bay, CA</p>
    <p>This program comes with ABSOLUTELY NO WARRANTY.  This is free software.  It may be redistributed under certain conditions.  See <a href="LICENSE.pdf" target="_blank" title="Software License">this PDF of the GNU Public License</a> for more information.</p>
</div>
</div> <!-- container -->
</body>
</html>