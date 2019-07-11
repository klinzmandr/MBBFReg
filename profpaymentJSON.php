<?php
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

// AJAX response code - bootstrap is implemented in the receiving page.
// list contents of pictures folder for modal.
include 'Incls/datautils.inc.php';

$id = $_SESSION['profname'];

$updarray['PayLock'] = 'Lock';

$res = sqlupdate('regprofile', $updarray, "`ProfileID` = '$id'");

echo 'OK'; 

?>