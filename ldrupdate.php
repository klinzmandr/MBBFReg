<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Leader Update</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
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

<div class="container">
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/mainmenu.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : "1";
$ss = isset($_REQUEST['ss']) ? $_REQUEST['ss'] : "";
$active = isset($_REQUEST['Active']) ? $_REQUEST['Active'] : "";

//echo '<pre> REQUEST '; print_r($_REQUEST); echo '</pre>';
$lnavarray = $_SESSION['lnavarray'];  // array of record numbers from last search
$lnav = $_SESSION['lnav'];            // array first, prev, curr, next and last
$lptr = $_REQUEST['lptr'];            // index of record number array 

//echo '<pre> navarray '; print_r($navarray); echo '</pre>';
//echo '<pre> BEFORE '; print_r($nav); echo '</pre>';
$lnav['curr'] = $lptr;
$lnav['prev'] = $lnav['curr'] - 1; if ($nav['lprev'] < 0) $lnav['prev'] = 0;
$lnav['next'] = $lnav['curr'] + 1; if ($nav['lnext'] > $lnav['last']) 
$lnav['next'] = $lnav['last'];
//echo '<pre> AFTER '; print_r($nav); echo '</pre>';

// PROCESS UPDATE ACTION IF INDICATED
if ($action == 'update') {
  $flds = array();
  $flds = $_REQUEST['flds'];
//	echo '<pre> full update '; print_r($flds); echo '</pre>';

  echo '
<script>
$(document).ready(function() {
  $("#X").fadeOut(2000);
});
</script>
<h3 style="color: red; " id="X">Update Completed.</h3>
'; 
  sqlupdate('leaders', $flds, '`RowID` = "'.$rowid.'";');
  }

// ----------------- display event info ---------------------   
$rowid = $lnavarray[$lptr];
$sql = '
SELECT * FROM `leaders` WHERE `RowID` = '.$rowid.';';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

$r = $res->fetch_assoc();
//echo '<pre> full record '; print_r($r); echo '</pre>';
if ($r[FirstName] == '**New**') $r[FirstName] = '';
echo '
<script>
function confirmContinue() {
	var r=confirm("This action cannot be reversed.\\n\\nConfirm this action by clicking OK or CANCEL"); 
	if (r==true) { return true; }
	return false;
	}
</script>

<table border="0" class="table hidden-print">
<tr><td width="33%">
<h2 class="hidden-print">Leader Update</h2></td>

<td align="right"><br><a onclick="return confirmContinue()" 
href="ldrlister.php?rowid='.$r[RowID].'&ss='.$ss.'&action=delete&Active='.$active.'">
<span title="Delete Leader" class="glyphicon glyphicon-trash" style="color: blue; font-size: 40px;"></span></a>&nbsp;&nbsp;

</td></tr>
</table>
<div class="hidden-print" align="center"><button form="F1" class="btn btn-success" type="submit">UPDATE LEADER</button></div>


<font size="+2"><b>'.$r[FirstName].' '.$r[LastName].'</b></font>
';

echo '
<script>
function chkupd() {
  if (updcnt > 0) {
    if (confirm("Updates made without saving saving them.\\n\\nCancel action or OK to continue.")) 
      { return true; }
    return false;
    }
  }
</script>
<script>
function validate() {
  var v = new String($("#Notes").val());
  v = v.replace(/\<|\>/g, "");
  $("#Notes").val(v);
  var v = new String($("#Bio").val());
  v = v.replace(/\<|\>/g, "");
  $("#Bio").val(v);
  return true;
  }
</script>';

// SELECT FIELD ON-LOAD SETUPS
echo '
<script>
$(document).ready(function() {
  $("#ACTIVE").val("'.$r[Active].'");
});
</script>';

// FORM FIELD DEF's
echo '
<form id="F1" action="ldrupdate.php" method="post" onsubmit="return validate()">
<table border="0">
<tr><td>
Leader Active?:
<select id="ACTIVE" name="flds[Active]">
<option value=""></option><option value="Yes">Yes</option><option value="No">No</option>
</select>
</td><td>
First Name: 
<input type="text" name="flds[FirstName]" value="'.$r[FirstName].'" autofocus>&nbsp;
</td><td>
Last Name: 
<input type="text" name="flds[LastName]" value="'.$r[LastName].'">
</td></tr>
</table>
<table>
<tr><td>
Primary Phone: 
<input type="text" name="flds[PrimaryPhone]" value="'.$r[PrimaryPhone].'">&nbsp;
</td><td>
Secondary Phone: 
<input type="text" name="flds[SecondaryPhone]" value="'.$r[SecondaryPhone].'"><br>
</td><td></td></tr>
</table>
<table>
<tr><td colspan="3">
Email Address: 
<input type="text" name="flds[Email]" value="'.$r[Email].'" size="40" id="Event">
</td></tr>
</table>
<table>
<tr><td>
Address Line 1: 
<input type="text" name="flds[Address1]" value="'.$r[Address1].'">&nbsp;
</td><td>
Address Line 2: 
<input type="text" name="flds[Address2]" value="'.$r[Address2].'">
</td></tr>
</table>
<table>
<tr><td>
City: 
<input type="text" name="flds[City]" value="'.$r[City].'">
</td><td>
State: 
<input type="text" name="flds[State]" value="'.$r[State].'">
</td><td>
Zip: 
<input type="text" name="flds[Zip]" value="'.$r[Zip].'">
</td></tr>
</table>
<table border=0>
<tr><td>
Biography:<br>
<textarea name="flds[Bio]" rows="10" cols="40">'.$r[Bio].'</textarea>
</td><td>
Notes:<br>
<textarea name="flds[Notes]" rows="10" cols="40">'.$r[Notes].'</textarea>
</td</tr></table>
';

// HIDDEN FORM FIELDS
echo '
<input type="hidden" name="action" value="update">
<input type="hidden" name="lptr" value="'.$lptr.'">
<input type="hidden" name="rowid" value="'.$rowid.'">
<input type="hidden" name="ss" value="'.$ss.'">
<input type="hidden" name="Active" value="'.$active.'">
</form>
<div class="hidden-print" align="center"><button form="F1" class="btn btn-success" type="submit">UPDATE LEADER</button></div>
<br><br><br><br>
';
?>

</div> <!-- container -->

</body>
</html>