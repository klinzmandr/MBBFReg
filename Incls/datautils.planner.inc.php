<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
// connect to the database for all pages
date_default_timezone_set('America/Los_Angeles');

include '../.MBBFDBParamInfo';
$mysqli = new mysqli("localhost", DBUserName, DBPassword, ProdDBName);

if ($mysqli->connect_errno) {
		$errno = $mysqli->connect_errno;
    echo "Failed to connect to MySQL: (" . $errno . ") " . $mysqli->connect_error . "<br>
    for database $db<br>";
    exit;
    }
// echo "Initial Connection Info: ".$mysqli->host_info . "<br><br>";
addlogentry('Page Load');
// auto returns to code following the 'include' statement

// ---- submit sql statement provided by calling script ----
// submit sql statement provided in call
function doSQLsubmitted($sql) { 
global $mysqli;

if (isset($_SESSION['DB_ERROR'])) return(FALSE);
// echo "sql: ".$sql."<br>";
$res = $mysqli->query($sql);
if (substr_compare($sql,"DELETE",0,6,TRUE) == 0) {
	//echo "<br>Delete command seen - return affected_rows<br>";
	$rowsdeleted = $mysqli->affected_rows;
	//echo "delete count: $rowsdeleted<br>";	
	addlogentry($sql);
	return($rowsdeleted);
	}
// NOTE:  could do a check to see if DELETE or REPLACE was done and 
//        return 'affected_rows' instead of select results 
if (!$res) {
    showError($res);
		}
addlogentry($sql);
return($res);
}

// --- update existing row in table from assoc array provided ----
function sqlupdate($table, $fields, $where) {
global $mysqli;

$nowdate = date('Y-m-d');					// now date if needed
$sql = "UPDATE `$table` SET ";
$f = ""; 
foreach ($fields as $k => $v) {
	if (strlen($v) > 0) {
		$vv = urldecode($v);
		$vv = addslashes($vv);
		$f .= "`$k`='$vv', ";
		}
	else {
		$f .= "`$k`=NULL, ";
		}
 	}
$f = rtrim($f, ', ');
$sql .= $f . ' WHERE ' . $where;
// echo "Update SQL: $sql<br>";
addlogentry($sql);
$res = $mysqli->query($sql);
$rows = $mysqli->affected_rows;
if (!$res) {
 	showError($res);	
	}
return($rows);
}

// ----------- add new row into table from assoc array-------------
function sqlinsert($table,$fields) {
global $mysqli;

$nowdate = date('Y-m-d');					// now date if needed
$fieldnames = ''; $fieldvalues = '';
$sql = "INSERT INTO $table (";
foreach ($fields as $k => $v) {		// field names for sql statement
	$fieldnames .= "`$k`, ";
	}
foreach ($fields as $k => $v) {		// field values for sql statement
	if (strlen($v) == 0) {
		$fieldvalues .= "NULL, ";
		}
	else {	
		$vv = urldecode($v);
		$vv = addslashes($vv);
		$fieldvalues .= "'$vv', ";
		}
	}
$sql .= rtrim($fieldnames, ', ');
$sql .= ") VALUES (";
$sql .= rtrim($fieldvalues,', ');
$sql .= ");";

$res = $mysqli->query($sql);
$rows = $mysqli->affected_rows;
if (!$res) {
	$err = showError($res);
	return($err);
	}
addlogentry($sql);
//echo "Insert SQL: $sql<br>";
//echo "affected rows: $rows<br>";
return($rows);
}

// ----- generalized error display for all DB functions -----
function showError($res) {
global $mysqli;
	$errno = $mysqli->errno;
	$errmsg = $mysqli->error;
	addlogentry('DB ERROR '.$errno.': '.$errmsg);
	if ($errno == 1049) {
		$db = $_SESSION['DB_ERROR'];
		print <<<errNoDB
<div class="alert">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<h4 style="color: red; ">DB ERROR: database $db is not available</h4>
</div>
errNoDB;
  return(FALSE);
  }
	if ($errno == 1062) {
		$errmsg .= '<br><h4 style="color: red; ">A record already exists for the unique key provided.</h4>';
		}
	print <<<errMsg
<div class="alert">
<button type="button" class="close" data-dismiss="alert">&times;</button>
<h4 style="color: red; ">DB ERROR $errno</h4>: $errmsg
</div>
errMsg;
  return(FALSE);
	}	

// ------------------------- add new log entry ----------------------------
function addlogentry($text) {
	global $mysqli; $errno = '';
	if (isset($_SESSION['DB_ERROR'])) { echo 'Error!<br>'; return(FALSE); }
	$loc = $_SERVER['REMOTE_ADDR'];
	$user = isset($_SESSION['SessionUser']) ? $_SESSION['SessionUser']: "Web User@$loc";
	$seclevel = isset($_SESSION['SecLevel']) ? $_SESSION['SecLevel'] : 'Normal';
	$page = $_SERVER['PHP_SELF'];
	$txt = addslashes($text);
	$sql = "INSERT INTO `log` (`User`, `SecLevel`, `Page`, `Text`) VALUES ('$user', '$seclevel', '$page', '$txt');";
//	echo "Log: $sql<br>";
	$res = $mysqli->query($sql);
	if (!$res) {
		$errno = $mysqli->errno;
		$errmsg = $mysqli->error;
		echo "LOGGING ERROR: $errno -> $errmsg<br>";
		}
	return($errno);
	}

?>