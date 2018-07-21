<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$now = date('M d, Y \a\t H:i', strtotime("now"));
include 'Incls/datautils.inc.php';
$sql = "
SELECT SUM(`mealFrM`) as 'frm', SUM(`mealFrV`) as 'frv', SUM(`mealSaM`) as 'sam', SUM(`mealSaV`) as 'sav', SUM(`mealSuM`) as 'sum', SUM(`mealSuV`) as 'suv'
FROM `regprofile`
WHERE 1=1; 
";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
//echo '<pre>'; print_r($r); echo '</pre>';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Lunches Report</title>
<!-- Bootstrap -->
<script src="js/jquery.min.js"></script>
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
?>
<div class="container">

<h1>Lunches Order Report</h1>
<a class="btn btn-primary btn-xs hidden-print" id="helpbtn">HELP</a>
<div id=help>
This report will provide ordering information for the types and quatities quantities for the lunches that have been ordered during registration.  All counts presented are current from the data base as of date and time the report was run.
</div>
Report as of <?=$now?><br>

<h3>Friday</h3>
<ul>
<table class=table>
<tr><th>Type</th><th>Qty</th></tr>
<tr><td>Meat</td><td><?=$r[frm]?></td></tr>
<tr><td>Vegitarian</td><td><?=$r[frv]?></td></tr>
</table>
</ul>
<h3>Saturday</h3>
<ul>
<table class=table>
<tr><th>Type</th><th>Qty</th></tr>
<tr><td>Meat</td><td><?=$r[sam]?></td></tr>
<tr><td>Vegitarian</td><td><?=$r[sav]?></td></tr>
</table>
</ul>
<h3>Sunday</h3>
<ul>
<table class=table>
<tr><th>Type</th><th>Qty</th></tr>
<tr><td>Meat</td><td><?=$r[sum]?></td></tr>
<tr><td>Vegitarian</td><td><?=$r[suv]?></td></tr>
</table>
</ul>

</ul>


</div> <!-- container -->
</body>
</html>