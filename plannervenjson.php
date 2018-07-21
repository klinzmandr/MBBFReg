<?php
// AJAX response code - bootstrap is implemented in the receiving page.
// list contents of pictures folder for modal.
include 'Incls/datautils.planner.inc.php';

$venname = $_REQUEST['name'];
$sql  = "
SELECT * FROM `venues` WHERE `VenName` = '$venname';";  

$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$r = $res->fetch_assoc();

//$bio = preg_replace('/(?<!href="|">)(?<!src=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/is', '<a href="\\1" target="_blank">\\1</a>', $r[Bio]);

if ($rc == 0) {
  echo '<table class="table" border=1><tr><td>';
  echo "$venname<br>NOT FOUND";
  echo '</td></tr></table>';
  }
else {
  if ($r[VenGmapURL] == '') {
    echo "<table class='table' border=1>
          <tr><td colspan=2><h4>$r[VenName]</h4></td></tr>
          <tr><td>$r[VenAddr]<br>
          $r[VenCity], $r[VenState]. $r[VenZip]</td></tr>
          </table>"; }
  else {
    echo "$r[VenGmapURL]"; }
  }
?>