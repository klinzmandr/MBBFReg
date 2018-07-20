<?php
// --------------------- db configtable utilities ----------------------------------------
// read named file and pass contents back to caller as a string
// without any comments or blank lines
  function readlist($listname) {
  $str = ''; $l = ''; $buffer = array();
  $listname = $listname . '.txt';
  $buffer = file('cfg/' . $listname);
  foreach ($buffer as $l) {
    if (substr($l,0,2) == '//') continue;
    if (strlen($l) == 0) continue;
    $str .= $l;
    }
	//echo "<pre> cfg "; print_r($str); echo '</pre>';
	return($str);
	}

// read named file and pass contents back as an array
// file must be in param1:param2 format 
// array is param1 => param2
  function readlistreturnarray($listname) {
  $str = ''; $l = ''; $buffer = array(); $retarray = array();
  $listname = $listname . '.txt';
  $buffer = file('cfg/' . $listname, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($buffer as $l) {
    if (substr($l,0,2) == '//') continue;
    if (strlen($l) == 0) continue;
    list($k, $v) = explode(':', $l);
    $retarray[$k] = $v;
    }
	//echo "<pre> cfg "; print_r($retarray); echo '</pre>';
	return($retarray);
	}

// read named file and pass contents back to caller as a string
// including comments and blank lines
  function readfulllist($listname) {
  $str = ''; $l = ''; $buffer = array();
  $listname = $listname . '.txt';
  $buffer = file('cfg/' . $listname);
  foreach ($buffer as $l) {
    $str .= $l;
    }
	//echo "<pre> cfg "; print_r($str); echo '</pre>';
	return($str);
	}

// update named table file with text provided
function updatelist($listname,$text) {
  // do file_put_contents to write string back into named file
  $fn = 'cfg/' . $listname . '.txt';
  $status = file_put_contents($fn,$text);
	return($status);
	}
?>