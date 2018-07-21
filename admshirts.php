<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

$now = date('M d, Y \a\t H:i', strtotime("now"));
include 'Incls/datautils.inc.php';

$sql = "
SELECT SUM(`shirtwS`) as 'ws', SUM(`shirtwM`) as 'wm', SUM(`shirtwL`) as 'wl', SUM(`shirtwXL`) as 'wxl', SUM(`shirtmS`) as 'ms', SUM(`shirtmM`) as 'mm', SUM(`shirtmL`) as 'ml', SUM(`shirtmXL`) as 'mxl', SUM(`shirtmXXL`) as 'mxxl'  
FROM `regprofile`
WHERE 1=1; 
";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
// echo '<pre>'; print_r($r); echo '</pre>';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Shirts Report</title>
<!-- Bootstrap -->
<link  href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<script src="js/chksession.js"></script>

</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
?>
<div class="container">

<h1>Shirts Order Report</h1>
<a class="btn btn-primary btn-xs hidden-print" id="helpbtn">HELP</a>
<div id=help>
This report will provide ordering information for the sizes and quantities for the shirts that have been ordered during registration.  All counts presented are current from the data base as of date and time the report was run.
</div>
Report as of <?=$now?><br>
<h3>Women</h3>
<ul>
<table class=table>
<tr><th>Size</th><th>Qty</th></tr>
<tr><td>Small</td><td><?=$r[ws]?></td></tr>
<tr><td>Medium</td><td><?=$r[wm]?></td></tr>
<tr><td>Large</td><td><?=$r[wl]?></td></tr>
<tr><td>XLarge</td><td><?=$r[wxl]?></td></tr>
</table>
</ul>
<h3>Men</h3>
<ul>
<table class=table>
<tr><th>Size</th><th>Qty</th></tr>
<tr><td>Small</td><td><?=$r[ms]?></td></tr>
<tr><td>Medium</td><td><?=$r[mm]?></td></tr>
<tr><td>Large</td><td><?=$r[ml]?></td></tr>
<tr><td>XLarge</td><td><?=$r[mxl]?></td></tr>
<tr><td>XXLarge</td><td><?=$r[mxxl]?></td></tr>
</table>

</ul>


</div> <!-- container -->
</body>
</html>