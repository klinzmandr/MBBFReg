<?php

function checkcred($uid) {
$pw = isset($_REQUEST['pw']) ? $_REQUEST['pw'] : $_SESSION[$uid];
$combo = $uid .':'. $pw;
// echo "combo: $combo<br>";
if (strlen($pw) == 0) { 
  // echo "pw: $pw<br>";
  echo '
  <script>
  function dopw() {
    var pw = ""; 
   	while (pw.length == 0) {
      pw = prompt("Please enter password: "); 
   	  //alert("Please enter a password");
   	  }
   	$("#IF").val(pw);     // save the prompt input to an input field
   	$("#PW").submit();    // submit the input form
  	return false;
  }
  </script>
  
  <form id="PW" method="post">
  <input id="IF" type="hidden" name="pw" value="">
  </form>
  <button class="btn btn-warning" onclick="return dopw()">Click to log In</button> <br><br>
  <!-- <a href="utlindex.php" class="btn btn-primary">RETURN</a> --> 
   
  ';
  exit;
  }
// echo "reading passwords<br>";
$pwds = readlistarray('Users');
// echo '<pre> pwds '; print_r($pwds); echo '</pre>';
// echo '<pre> combo '; print_r($combo); echo '</pre>';

if (!in_array($combo, $pwds)) {
  unset($_SESSION[$uid]);
  echo 'Userid and Password not registered.<br>';
  return(false);
  } 
// echo 'Found userid and password';
$_SESSION[$uid] = $pw;
return(true);
}

function geteventstart() {
  $pwds = readlistarray('Users');
//echo '<pre> pwds '; print_r($pwds); echo '</pre>';
//echo '<pre> combo '; print_r($combo); echo '</pre>';
// format: eventstart:YYYY-MM-DD HH:MM
  foreach ($pwds as $l) {
    list($part1, $starttime) = explode(" ", $l);    // isolate start time
    list($key, $startdate) = explode(':', $part1);  // isolate key and start date
    if ($key == 'eventstart') break;
  }
  return($startdate.' '.$starttime);
  }
  
function getregend() {
  $pwds = readlistarray('Users');
//echo '<pre> pwds '; print_r($pwds); echo '</pre>';
//echo '<pre> combo '; print_r($combo); echo '</pre>';
// format: regend:YYYY-MM-DD HH:MM

  foreach ($pwds as $l) {
    list($part1, $starttime) = explode(" ",$l);
    list($key, $startdate) = explode(':', $part1);
    if ($key == 'regend') break;
    }
  return($startdate.' '.$starttime);
  }
  
function getregstart() {
  $pwds = readlistarray('Users');
//echo '<pre> pwds '; print_r($pwds); echo '</pre>';
//echo '<pre> combo '; print_r($combo); echo '</pre>';
// format: regend:YYYY-MM-DD HH:MM
  foreach ($pwds as $l) {
    list($part1, $starttime) = explode(" ",$l);
    list($key, $startdate) = explode(':', $part1);
    if ($key == 'regstart') break;
  }
  return($startdate.' '.$starttime);
  }
?>