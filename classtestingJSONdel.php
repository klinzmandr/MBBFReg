<?php
session_start();

//print_r($_REQUEST);
$agenda = $_REQUEST['agenda'];
$rid = $_REQUEST['rid'];

echo "$agenda - $rid";


?>