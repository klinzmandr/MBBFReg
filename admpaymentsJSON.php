<?php
include 'Incls/datautils.inc.php';
// for tesing use test profile
// $id = isset($_REQUEST['profile']) ? $_REQUEST['profile'] : 'a@a.com';
$id = isset($_REQUEST['profile']) ? $_REQUEST['profile'] : '';
if ($id == '') {
  echo '<h3>ERROR: no profile received by json module.</h3>';
  exit;
  }
$sql = "SELECT * FROM `regeventlog` WHERE `ProfName` = '$id' AND `RecKey` = 'Pay' ORDER BY `DateTime` DESC;";
$res = doSQLsubmitted($sql);
$rc = $res -> num_rows; $ptr = ''; $bal = 0; $evtbal = 0;
$ptr = '<table class=table>
<tr><th>DateTime</th><th>Payment</th><th>Notes</th></tr>';
while($r = $res->fetch_assoc()) {
  $bal += $r[Payment];
  $ptr .= "<tr><td>$r[DateTime]</td><td>$$r[Payment]</td><td>$r[ProfNotes]</td></tr>";
  }
$ptr .= '</table>';
if (!$rc) 
  $ptr = "<table><tr><td><h4>No payment history for profile $id</h4></td></tr></table>";
echo $ptr;
?>



