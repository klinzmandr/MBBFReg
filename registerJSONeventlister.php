<?php
session_start();

include 'Incls/datautils.inc.php';

$profile = $_SESSION['profname'];

// read data from events and registration log
$sql = "
SELECT
    `events`.`Trip`,
    `events`.`Event`,
    `regeventlog`.`ProfName`,
    `regeventlog`.`AgendaName`    
FROM
    `regeventlog`, `events`
WHERE
    `regeventlog`.`EvtRowID` = `events`.`RowID`
 AND `regeventlog`.`ProfName` = '$profile' 
ORDER BY `events`.`Trip` ASC, `regeventlog`.`ProfName` ASC, `regeventlog`.`AgendaName` ASC;
";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
if ($rc == 0) {
  echo '<table><tr><td>No Events Registered!</td></tr></table>';
  exit;
  }
while ($r = $res->fetch_assoc()) {
  $tarray[$r['Trip']] = $r['Event'];
  $parray[$r['Trip']][$r['AgendaName']] = $r['Trip'].' '.$r['Event'];
  }

$tr = '<table border=0>';
foreach ($parray as $k => $v) {
  //echo "key: $k, value: $v<br>\n";
  $tr .= "<tr><td><b>$k $tarray[$k]</b></td></tr>\n";
  $tr .= '<tr><td style="padding-left: 20px;">';
  foreach ($v as $kk => $vv) {
    $tr .= "$kk, ";
    }
  $tr = rtrim($tr, ", ");
  $tr .= "</td></tr>\n";
  }
$tr .= '</table>';
echo $tr;

?>