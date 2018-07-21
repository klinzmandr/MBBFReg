<?php
// ------- db configtable utilities ------
// read named db record and pass contents back to caller as a string
// without any comments or blank lines
function readlist($listname) {
  // echo "function loaded and active<br>"; 
  $str = ''; $l = ''; $buffer = array();
	$sqldb = "SELECT * FROM `config` WHERE `CfgName` = '$listname';";
	// echo "sql: $sqldb\n<br>";
	$res = doSQLsubmitted($sqldb);
	$r = $res->fetch_assoc();
	$rc = $res->num_rows;
	// echo "rc: $rc<br>"; exit;
  $buffer = explode("\n", $r[cfgtext]);
  foreach ($buffer as $l) {
    $l = rtrim($l, "\r\n");
    if (substr($l,0,2) == '//') continue;
    if (strlen($l) == 0) continue;
    $str .= $l;
    }
	// echo "<pre> cfg: "; print_r($str); echo '</pre>'; exit;
	return($str);
	}

// read named vendor table and format/pass row contents 
// back to caller as a string without any comments or blank lines
// in the same format as readlist().
function readvenlist($listname) {
  // echo "function loaded and active<br>"; 
  $str = ''; $l = ''; $buffer = array();
	$sqldb = "SELECT * FROM `venues` WHERE 1=1 ORDER BY `VenCode` ASC;";
	// echo "sql: $sqldb\n<br>";
	$res = doSQLsubmitted($sqldb);
	$rc = $res->num_rows;
	$r = $res->fetch_assoc();
	$str = '';
  while ($r = $res -> fetch_assoc()) {
  	// echo '<pre>'; print_r($r); echo '</pre>';
    $l = '<option value="' . $r[VenName] . ':' . $r[VenCode] . '">';
    $l .= $r[VenName] . '</option>';
    $str .= $l;
    }
	// echo "<pre> cfg: "; print_r($str); echo '</pre>'; exit;
	return($str);
	}

// read named db record and pass contents back to caller as an simple array
// without any comments or blank lines
function readlistarray($listname) {
  $str = array(); $l = ''; $buffer = array();
	$sqldb = "SELECT * FROM `config` WHERE `CfgName` = '$listname';";
	//echo "sql: $sqldb\n<br>";
	$res = doSQLsubmitted($sqldb);
	$r = $res->fetch_assoc();
	$rc = $res->num_rows;
  $buffer = explode("\n", $r[cfgtext]);
  foreach ($buffer as $l) {
    $l = rtrim($l, "\n");
    $l = rtrim($l, "\r");
    if (substr($l,0,2) == '//') continue;
    if (strlen($l) == 0) continue;
    if ($l == '') continue;
    $str[] = $l;
    }
	//echo "<pre> cfg "; print_r($str); echo '</pre>';
	return($str);
	}
	
// read named db table and pass rows back to caller as an simple array
// without any comments or blank lines
function readvenlistarray($listname) {
  $str = array(); $l = ''; $buffer = array();
	$sqldb = "SELECT * FROM `venues` WHERE 1=1 ORDER BY `VenCode` ASC;";
	//echo "sql: $sqldb\n<br>";
	$res = doSQLsubmitted($sqldb);
	$rc = $res->num_rows;
	$str = array();
	while ($r = $res->fetch_assoc()) {
    $l = '<option value="' . $r[VenName] . ':' . $r[VenCode] . '">';
    $l .= $r[VenName] . '</option>';
    $str[] = $l;
    }
	//echo "<pre> cfg "; print_r($str); echo '</pre>';
	return($str);
	}

// read named file and pass contents back as an keyed array
// file must be in param1:param2 format 
// array is param1 => param2
  function readlistreturnarray($listname) {
  $str = ''; $l = ''; $buffer = array(); $retarray = array();
  $sqldb = "SELECT * FROM `config` WHERE `CfgName` = '$listname';";
//	echo "sql: $sqldb\n<br>";
	$res = doSQLsubmitted($sqldb);
	$r = $res->fetch_assoc();
	$rc = $res->num_rows;
  $buffer = explode("\n", $r[cfgtext]);
//  echo '<pre>'; print_r($buffer); echo '</pre>';
  foreach ($buffer as $l) {
    $l = rtrim($l, "\r\n");
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
  $sqldb = "SELECT * FROM `config` WHERE `CfgName` = '$listname';";
	//echo "sql: $sqldb\n<br>";
	$res = doSQLsubmitted($sqldb);
	$r = $res->fetch_assoc();
	$rc = $res->num_rows;
  $buffer = explode("\n", $r[cfgtext]);
  foreach ($buffer as $l) {
    $str .= $l;
    }
	//echo "<pre> cfg "; print_r($str); echo '</pre>';
	return($str);
	}

// update named table file with text provided
function updatelist($listname,$text) {
 	$flds = array();
	$flds[CfgText] = $text;
	// echo '<pre> upd '; print_r($flds); echo '</pre>';
	$rows = sqlupdate('config', $flds, "`cfgName` = '$listname'");
	return($rows);
	}
?>