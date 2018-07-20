<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lead Query</title>
<!-- Bootstrap -->
<link href="../css/bootstrap.min.css " rel="stylesheet" media="all">
</head>
<body>

<script src="../js/jquery.min.js"></script>
<script src="../js/bootstrap.min.js"></script>

<div class="container">
<img src="http://morrobaybirdfestival.net/wp-content/uploads/2016/08/LOGO3.png" alt="bird festival logo" >

<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start(); 

$em = isset($_REQUEST['em']) ? $_REQUEST['em'] : '';

//include "../Incls/vardump.inc.php";

if ($em == '') {
  print <<<formpart
<script>
function validatekfld() {
// keystring?
ks = new String(document.kapform.kkeystring.value);
if (ks.length < 1) {
  alert("Please enter the letters on the left.");
  document.kapform.kkeystring.focus();
  return false; }
	}

</script>

formpart;
?>
<form name="kapform" action="index.php" onsubmit="return validatekfld();">
<table border=0><tr><td>
<img src="./captcha/index.php?<?php echo session_name()?>=<?php echo session_id()?>" style="vertical-align:middle" />
</td><td>Security character string<br>(Reload page for new letters)</td></tr>
<tr><td><input autofocus type="text" name="kkeystring"></td><td>Enter security string</td></tr>
<tr><td><input type="text" name="em" value=""></td>
<td>Enter registered email address</td></tr>
<tr><td align=center><input type="submit" name="submit" value="Submit"></form></td>
<td align=center><a class="btn btn-default" href="index.php">Reload Page</a></td></tr>
</table>
<?php
  exit;
  }

if (isset($_REQUEST['kkeystring'])) {
	echo "checking keystring<br>";
	if (isset($_SESSION['captcha_keystring']) && 
				$_SESSION['captcha_keystring'] ==  $_REQUEST['kkeystring']) {
    // echo "<br>MATCHED";
		}
	else {
		echo '<head><meta http-equiv="refresh" content="2; URL=index.php"></head>
		      <h1>Verification Failed.  Try again!</h1>'; 
		exit(0);
		}	
	}

// call the query page
echo '
<form action="../leaderquery.php" method="post"  name="lq">
<input type="hidden" name="eaddr" value="'.$em.'">
</form>

<SCRIPT TYPE="text/JavaScript">document.forms["lq"].submit();</SCRIPT> 

'; 

?>
</div> <!-- container -->
</body>
</html>