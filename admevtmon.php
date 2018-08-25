<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

//include 'Incls/vardump.inc.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Under Construction</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<link href="css/font-awesome.min.css" rel="stylesheet">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</head>
<body>
<div class=container>
<!-- <div align=center><img src="img/Under_Construction.gif" width="500" height="300" alt="Under Construction"></div> -->
<?php
include 'Incls/datautils.inc.php';
include 'Incls/mainmenu.inc.php';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$time = file_get_contents('reg.evt.monitor.LOCK');
switch($action) {
  case 'status':
    $status = 'Background monitor process NOT active.<br>';
    if (strlen($time) > 0) {
      $status = "admevtmonitor running.  Started at $time<br>";
      }
    $res = doSQLsubmitted("SELECT * FROM `log` WHERE `Text` NOT LIKE 'Page%' AND (`Text` LIKE '%admevtmon%' OR `Page` LIKE '%admevtmon%') ORDER BY `DateTime` DESC LIMIT 0,10");
    $loglist = '<h3>Status of background monitor process:</h3><br>Last 10 event montior entries:<br><ul>';
    while ($r = $res->fetch_assoc()) {
      $loglist .=  "$r[DateTime]: $r[Text]<br>";
      }
    $loglist .=  '</ul>==== END LIST ====<br>';
    echo '';
    break;
    
  case 'start':
    if ($time == '') {
      addlogentry("admevtmon starting");
      exec('./reg.evt.monitor > /dev/null &');
      $time = "Started on ".date("M d, Y \a\\t H:i", strtotime("now"))."\n";
      file_put_contents('reg.evt.monitor.LOCK', $time);
      $status = "Monitor $time<br>";
      }
    else $status = '<h3>Monitor already running</h3><br>';
    break;
    
  case 'stop':
    if ($time != '') {
      addlogentry("admevtmonitor stopping");
      unlink('reg.evt.monitor.LOCK');
      $status = '<h3>Monitor run flag deleted</h3><br>';
      }
    else $status = '<h3>Monitor not running</h3><br>';
    break;
    
  }

$mon = '<i title="backbround monitor NOT running" style="color: red;" class="fa fa-ban fa-2x fa-fw"></i>';
// $monpid = `pgrep reg.evt.monitor`;    // returns pid if running
// if (strlen($monpid) != 0) {
$monlock = file_exists('reg.evt.monitor.LOCK');
if ($monlock) {
  $mon = '<i title="background monitor in operation" style="color: green;" class="fa fa-cog fa-spin fa-2x fa-fw"></i>';
  }

echo '<h1>Background Event Monitor</h1>
<p>Check the status, start and stop the background process used to check the expiration of scheduled events on all registraton profiles.</p>
<p>Process will delete all events for those profiles that have not had any updates since the defined expiration time.</p>
<h3>Background monitor status: '.$mon.'</h3><br>
<a href="admevtmon.php?action=status" class="btn btn-primary">Status</a>
<a href="admevtmon.php?action=start" class="btn btn-success">Start</a>
<a href="admevtmon.php?action=stop" class="btn btn-danger">Stop</a>
<br>';

echo $status;
echo $loglist;
echo 'Finished.<br>';
 
?>

<br><br>

</div> <!-- container -->
</body>
</html>