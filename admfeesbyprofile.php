<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

$now = date('M d, Y \a\t H:i', strtotime("now"));
include 'Incls/datautils.inc.php';
$sql="
SELECT `regeventlog`.*
FROM `regeventlog`
WHERE `regeventlog`.`RecKey` = 'Pay';
";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
// echo "row count: $rc<br>";
// $tbl = "<table class=table><tr><td>Profile: $k</td><td>Payment DateTime</td></tr>";
$tbl = "<table class=table>";
while ($r = $res->fetch_assoc()) {
  // echo '<pre>row '; print_r($r); echo '</pre>';
  $bldarray[$r[ProfName]][$r[RowNbr]] = $r;
  }

// echo '<pre>bld '; print_r($bldarray); echo '</pre>';

foreach ($bldarray as $k => $v) {
  $tbl .= "<tr><td>$k</td></tr>";
  foreach ($v as $kk => $vv) {
    asort($vv);
    $p = number_format($vv[Payment],2);
    if ($vv[Payment] >= 0) $tbl .= "<tr><td><ul>$vv[DateTime]</ul></td><td>Payment</td><td align=right>$$p</td><td>$vv[ProfNotes]</td></tr>";
    else $tbl .= "<tr><td><ul>$vv[DateTime]</ul></td><td>Refund</td><td align=right>$$p</td><td>$vv[ProfNotes]</td></tr>";
    }
  }
  $tbl .= "</table>"; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fees by Profile Report</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
?>
<div class="container">

<h1>Payments History by Profile Report</h1>
<a class="btn btn-primary btn-xs hidden-print" id="helpbtn">HELP</a>
<div id=help>
This report provides all details for all payments and refunds gouped by profile id.  The profile id is created when the registration process starts and used through out the registration and payment process.  Payments are made using the &quot;Financial -&gt; Payments&quot; menu item which also provides an individual payment history for the profile.
</div>
Report as of <?=$now?><br>
<ul>
<?=$tbl?>
</ul>

</div> <!-- container -->
</body>
</html>