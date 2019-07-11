<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

//include 'Incls/vardump.inc.php';

include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

if (file_exists('./reg.evt.monitor.LOCK')) 
  $time = file_get_contents('./reg.evt.monitor.LOCK');
else $time = '';
switch($action) {
  case 'status':
    $status = 'Background monitor process NOT active.<br>';
    if (strlen($time) > 0) {
      $status = "admevtmonitor running.  $time<br>";
      }
    $res = doSQLsubmitted("SELECT * FROM `log` WHERE `User` = 'EvtMon' ORDER BY `DateTime` DESC LIMIT 0,5");
    $loglist = '<h3>Status of background monitor process:</h3><br><b>Last 5 event montior entries:</b><br><ul><table border=1>';
    while ($r = $res->fetch_assoc()) {
      $loglist .=  "<tr><td valign=top>$r[DateTime]</td><td>$r[Text]</td></tr>";
      }
    $loglist .=  '</table></ul>==== END LIST ====<br>';
    echo '';
    break;
    
  case 'start':
    if ($time == '') {
      addlogentry("admevtmon starting");
      // exec('./reg.evt.monitor > /dev/null &');

      $cmd = 'php ./reg.evt.monitor';
      $outputfile = 'reg.evt.output';
      $pidfile = 'reg.evt.pidfile';
      exec(sprintf("%s > %s 2>&1 & echo $! > %s", $cmd, $outputfile, $pidfile));
      // echo sprintf("%s > %s 2>&1 & echo $! > %s", $cmd, $outputfile, $pidfile);

      $time = "Started on ".date("M d, Y \a\\t H:i", strtotime("now"))."\n";
      file_put_contents('./reg.evt.monitor.LOCK', $time);
      $status = "Monitor $time<br>";
      }
    else $status = '<h3>Monitor already running</h3><br>';
    break;
    
  case 'stop':
    if ($time != '') {
      addlogentry("admevtmonitor stopping");
      if (file_exists('./reg.evt.pidfile')) {         // kill monitor
        $pid = file_get_contents('./reg.evt.pidfile');
        exec("kill -9 $pid");
        unlink('./reg.evt.pidfile');
        }
      unlink('./reg.evt.monitor.LOCK');
      $status = '<h3>Monitor run flag deleted</h3><br>';
      }
    else $status = '<h3>Monitor not running</h3><br>';
    break;
    
  }

$mon = '<i title="backbround monitor NOT running" style="color: red;" class="fa fa-ban fa-2x fa-fw"></i>';
// $monpid = `pgrep reg.evt.monitor`;    // returns pid if running
// if (strlen($monpid) != 0) {
$monlock = file_exists('./reg.evt.monitor.LOCK');
if ($monlock) {
  $mon = '<i title="background monitor in operation" style="color: green;" class="fa fa-cog fa-spin fa-2x fa-fw"></i>';
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Event Monitor</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<link href="css/font-awesome.min.css" rel="stylesheet">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

</head>
<body>
<?php include 'Incls/mainmenu.inc.php'; ?>
<div class=container>
<!-- <div align=center><img src="img/Under_Construction.gif" width="500" height="300" alt="Under Construction"></div> -->

<h1>Scheduled Event Monitor&nbsp;&nbsp;<i id=helpbtn title="Event Monitor information" class="fa fa-info-circle fa-1x" style="color: blue;"></i></h1>
<p>Check the status, start and stop the background Event Monnitor used to check the expiration of scheduled events on all registraton profiles.</p>
<p>The Event Monitor will drop all events for those profiles that have not had any updates within the last 30 minutes.</p>
<script>
$(function() {
  $("#helpbtn").click(function() {
    $("#msgdialogtitle").html("<h3>Event Monior</h3>"); 
    $("#msgdialogcontent").html('<p>The Event Monitor is a background process used to monitor events selected on all profiles that have not been confirmed.  If a profile has not had an event added during the expiration period all scheduled activities are deleted from the profile.</p><p>Currently the monitor runs every 10 minutes and the expiration period is 30 minutes.</p><p>Practically this means that a registrant has 30 minutes from the time of their last event selection to confirm the registration and lock in all the events.</p>');
    $('#msgdialog').modal('toggle', { keyboard: true });
  });
  $(".TM").click(function() {
    alert("EVENT MONITOR IS IN TEST MODE.\n\nEvents listed will not actually be deleted until testing is completed.  All other actions work as expected.");
  });
});
</script>
<h3>Background monitor status: <?=$mon?></h3><br>
<a href="admevtmon.php?action=status" class="btn btn-primary">Status</a>
<a href="admevtmon.php?action=start" class="btn btn-success">Start</a>
<a href="admevtmon.php?action=stop" class="btn btn-danger">Stop</a>
<br>

<?=$status?>
<?=$loglist?>

<br><br>

</div> <!-- container -->
</body>
</html>