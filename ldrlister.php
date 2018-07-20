<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Update Lister</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/bs3dropdownsubmenus.css" rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/mainmenu.inc.php';

echo '
<h3>Leader List</h3>
';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$ss = isset($_REQUEST['ss']) ? $_REQUEST['ss'] : "";
$active = isset($_REQUEST['Active']) ? $_REQUEST['Active'] : "";

if ($ss == '%') { $ssflag = '%'; $ss = ''; }

// process delete action
if ($action == 'delete') {
  $rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
	//echo "delete $rowid requested<br>";
	$sql = "DELETE FROM `leaders` WHERE `RowID` = '$rowid';";
	$rc = doSQLsubmitted($sql);		// returns affected_rows for delete
	if ($rc > 0) 
		echo "Deletion of leader successful.<br>";
	else
		echo "Error on delete of event $rowid<br>";
	}

echo'
<script type="text/javascript">
// set up select lists
$(document).ready(function () { 
	//alert("first the inline function");
	$("#SS").val("'.$ss.'");
	$("#ACTIVE").val("'.$active.'");
	});
</script>
<script type="text/javascript">
// reset all select lists to default
function resetflds() { 
	$("#SS").val("");
	$("#ACTIVE").val("");
	return false;
}
</script>

<h4 class="hidden-print">Select one or more selection criteria and continue: </h4>
<form id="F1" action="ldrlister.php" method="post" class="hidden-print ">
Leader Active?:
<select  id="ACTIVE" name="Active">
<option value=""></option>
<option value="Yes">Yes</option>
<option value="No">No</option></select>
<input id="SS" type=text value="" name="ss" placeholder="SEARCH FILTER" title="Enter a single word or short character string to search leader fields.">&nbsp;
<input type=hidden name=action value="list">
<button class="btn btn-primary" type="submit" form="F1" data-toggle="tooltip" data-placement="left" title="Search for % to list all">SEARCH</button>
<button class="btn btn-warning" onclick="return resetflds()">Reset</button>
</form>
';

// Process listing based on selected criteria
$sql = '
SELECT * FROM `leaders` WHERE ';
$sqllen = strlen($sql);
if (strlen($active) > 0) {
  $sql .= '`Active` = "'.$active.'" AND '; }
if ($ssflag == '%') {
  $sql .= '1 AND '; } 
if (strlen($ss) > 0) {
  $sql .= '
  (`FirstName` LIKE "%'.$ss.'%" 
  OR `LastName` LIKE "%'.$ss.'%" 
  OR `Bio` LIKE "%'.$ss.'%") AND ';
  }

if (strlen($sql) == $sqllen) {      // no criteria entered
  echo '
<h4 style="color: red; ">No criteria entered for search.</h4>
</div> <!-- contianer -->
</body>
</html>';
  exit;
  }

$sql = basename($sql, " AND ");
$sql .= ' ORDER BY `RowID` ASC';
$sql .= ';';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

echo '
<h4>Leaders Selected Listed&nbsp;&nbsp;(Listed: '.$rc.')
<table border=1 class="table table-condensed table-hover">
<tr class="hidden-print"><th>Active?</th><th>Name</th><th>PriPhone</th><th>SecPhone</th><th>Email</th></tr>
<script>
function confirmContinue() {
	var r=confirm("This action cannot be reversed.\\n\\nConfirm this action by clicking OK or CANCEL"); 
	if (r==true) { return true; }
	return false;
	}
</script>

';
$lnavarray = array(); $lvar = array(); $lptr = 0;
while ($r = $res->fetch_assoc()) {
  //if ($r['FirstName'] == '**New**') continue;
  $lnavarray[] = $r[RowID];
//  echo '<pre> full record '; print_r($r); echo '</pre>';

echo "<tr onclick=\"window.location='ldrupdate.php?lptr=$lptr&ss=$ss&Active=$active'\" style='cursor: pointer;'>";
/*
  echo '<td class="hidden-print">
<div align="center">
<a href="ldrupdate.php?rowid='.$r[RowID].'&ss='.$ss.'&Active='.$active.'"><span class="glyphicon glyphicon-pencil" style="color: blue; font-size: 20px; " title="Update Leader"></span></a>&nbsp;&nbsp;
<a onclick="return confirmContinue()" 
href="ldrlister.php?rowid='.$r[RowID].'&ss='.$ss.'&action=delete&Active='.$active.'">
<span title="Delete Leader" class="glyphicon glyphicon-trash" style="color: blue; font-size: 20px;"></span></a>&nbsp;&nbsp;
</div>
</td>';
*/
echo '
<td><font size="0">'.$r[Active].'</font></td>
<td><font size="0">'.$r[FirstName].'&nbsp;'.$r[LastName].'</font></td>
<td><font size="0">'.$r[PrimaryPhone].'</font></td>
<td><font size="0">'.$r[SecondaryPhone].'</font></td>
<td><font size="0">'.$r[Email].'</font></td>
</tr>
';

$lnav['start'] = 0; $lnav['prev'] = ''; $lnav['curr'] = '';
$lnav['next'] = ''; $lnav['last'] = count($lnavarray) - 1;

$_SESSION['lnavarray'] = $lnavarray;
$_SESSION['lnav'] = $lnav;

$lptr += 1;

}
echo '</table>';
?>
</div> <!-- contianer -->
</body>
</html>