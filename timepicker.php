<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Time Picker Tests</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link rel="stylesheet" href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link rel="stylesheet" href="css/jquery.timepicker.css" type="text/css"/>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.timepicker.js"></script>
</head>
<body>

<?php

$tp1 = isset($_REQUEST['tp1']) ? $_REQUEST['tp1'] : '';
$tp2 = isset($_REQUEST['tp2']) ? $_REQUEST['tp2'] : '';

$tp1val = strtotime($tp1);
$tp2val = strtotime($tp2);
$diff = $tp2val - $tp1val;
$hrs = sprintf("%s",$diff/3600);   // diff in hours
$mins = (($tp2val - $tp1val) - ($hrs * (60*60)))/60;   // diff in min
 
$fmtdiff = sprintf("%2d Hours %2d Min", $hrs, $mins);

echo "<br>tp1val: $tp1val, tp2val: $tp2val, diff: $diff<br>";
echo "tp1: $tp1, tp2: $tp2, diff: $hrs fmtdiff: $fmtdiff<br>";


?>
<h1>Time Picker Testing</h1>
<p>Time selected as the end time can not be a time prior to the start time.  The values of the end time are autormatically limited by the start time selected.  Parmeters of the picker function define the min and max and increments to be used for time selections.</p>
<form action="timepicker.php" method="post"  id="F1">

tp1:<input class="tpick" id="tp1" name="tp1" type="text" value="<?=$tp1?>">
<br>
tp2:<input class="tpick" id="tp2" name="tp2" type="text" value="<?=$tp2?>"/>
<br>

<input type="submit" name="submit" value="Submit">
</form>
<br>
<script>
$(document).ready(function(){
    $("input.tpick").timepicker({ 
    timeFormat: 'h:mm:ss p',
    minTime:    "7:00 AM",
    maxTime:    "8:00 PM",
    interval:   15 
    });
});
</script>

</body>
</html>
