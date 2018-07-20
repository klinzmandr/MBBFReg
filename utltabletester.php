<?php
include 'Incls/tabledef.inc.php';

echo 'Testing table include:<br>';
foreach ($tblcols as $k => $v) {
  echo "key: $k, value: $v<br>";
}

?>