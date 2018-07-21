<?php
// AJAX response code - bootstrap is implemented in the receiving page.
// list contents of pictures folder for modal.
include 'Incls/datautils.planner.inc.php';

$leadername = $_REQUEST['name'];

// $ldrkey = preg_match("/^(.*) (.*)$/", $leadername);

$sql  = "
SELECT * FROM `leaders`
WHERE (`FirstName` = '$leadername' AND `LastName` IS NULL) OR (`FirstName` IS NULL AND `LastName` = '$leadername') OR CONCAT(`LastName`,`FirstName`) = '$leadername'; 
"; 

$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$r = $res->fetch_assoc();
if ($rc == 0) {
  echo '<table class="table" border=1><tr><td>';
  echo "$leadername<br>NOT FOUND";
  echo '</td></tr></table>';
  }
else {
  $img = $r[ImgURL];
  $bio = $r[Bio];
  if ($img == '') $img = "./npa.png";
  if ($bio == '') $bio = "No biography information available";

  $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
  $bio = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $r[Bio]);

  echo '<table class="table" border=1>';
  echo "<tr><td colspan=2><h4>$r[FirstName] $r[LastName]</h4></td></tr>
        <tr><td><img src='$img' width='200' height='150' alt='$leadername'></td>
        <td>$bio</td></tr>";
  echo '</table>';

  }
?>