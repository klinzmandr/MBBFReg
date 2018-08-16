<?php
session_start();

$ks = $_REQUEST['ks'];
$skey = $_SESSION['captcha_keystring'];
if ($ks == $skey) { echo "OK sessvar: $skey"; }
else { echo "NO sessvar: $skey"; }

?>