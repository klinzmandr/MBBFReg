<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$sql = "SELECT * FROM `regprofile` WHERE 1 = 1;";

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

$tr = '';
while ($r = $res->fetch_assoc()) {
  // echo '<pre>profile '; print_r($r); echo '</pre>';
    $emlink = "<a href='emailsend.php?rowid=$r[RowID]'>$r[ProfileID]</a>";
  $tr .= "<tr><td>$r[RowID]</td><td>$emlink</td><td>$r[ProfFirstName]</td><td>$r[ProfLastName]</td><td>$r[ProfAddress]</td><td>$r[ProfCity]</td><td>$r[ProfState]</td><td>$r[ProfZip]</td></tr>";
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile Admin</title>
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

<h1>Profile Index</h1>
<input id=filter placeholder='Filter'><button id=filterbtn1>Apply</button><button id=filterbtn2>Reset</button>
<table class=table>
<tr id=head><th>RowID</th><th>ProfileID</th><th>FIrstName</th><th>LastName</th><th>Address</th><th>City</th><th>ST</th><th>Zip</th><th>PhoneNbr</th></tr>
<?=$tr?>
</table>

<a href="admin.php" class="btn btn-primary">RETURN</a></h1>

</body>
</html>