<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$sql = "
SELECT `regeventlog`.*, `events`.`Trip`, `events`.`Event`, `events`.`FEE`  
 FROM `regeventlog`, `events` 
WHERE `regeventlog`.`EvtRowID` = `events`.`RowID`
ORDER BY `regeventlog`.`ProfName` ASC, `regeventlog`.`AgendaName` ASC;";

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

$tr = '';
while ($r = $res->fetch_assoc()) {
  // echo '<pre>profile '; print_r($r); echo '</pre>';
  $tr .= "<tr><td>$r[RowNbr]</td><td>$r[ProfName]</td><td>$r[AgendaName]</td><td>$r[FEE]</td><td>$r[Payment]</td><td>$r[Trip]</td><td>$r[Event]</td></tr>";
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Agenda Admin</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
</head>
<body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<?php
include 'Incls/mainmenu.inc.php';
?>

<h1>Agenda Index</h1>
<input id=filter placeholder='Filter'><button id=filterbtn1>Apply</button><button id=filterbtn2>Reset</button>
<table class=table>
<tr id=head><th>RowNbr</th><th>ProfileID</th><th>AgendaName</th><th>FEE</th><th>Payment</th><th>Trip</th><th>Event</th></tr>
<?=$tr?>
</table>

<a href="admin.php" class="btn btn-primary">RETURN</a></h1>

</body>
</html>