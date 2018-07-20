<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Report Page Template</title>
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

<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/mainmenu.inc.php';
//include 'Incls/listutils.inc.php';
//include "Incls/letter_print_css.inc.php";

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$active = isset($_REQUEST['Active']) ? $_REQUEST['Active'] : "";

echo '
<h3>Leader Info Report</h3>';

if ($action == '') {
  print <<<formPart
<div class="container">
<p>This report is created from all leaders registered on the database.  The choice is available to list all or limit the listing to those that are &quot;active&quot; or not.</p>
<p>The export file does not exactly mirror the page output and contains all fields.</p>  
  
<script>
$(document).ready ( function () {
  
  $("#SV").change ( function() {
  var sv = $("#SV").val();
  if (sv == "") { return false; }
  $("form").submit();
  return true;
  });
});
</script>

<form action="rptleaderinfo.php">
List Active: <select id="SV" name="Active">
<option value=""></option><option value="%">All</option><option value="Yes">Yes</option><option value="No">No</option>
</select>
<input type="hidden" name="action" value="genreport">
<!-- <input type="submit" name="submit" value="Create Report"> -->
</form>
</div>
formPart;

exit;
  }

$sql = 'SELECT * FROM `leaders` 
WHERE `Active` LIKE "'.$active.'" ORDER BY `LastName` ASC;';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
if ($rc == 0) {
  echo '<h4>Row '.$rowid.' not found</h4>';
  exit;
  }

echo '
<script>  
$(function(){
   $("#btnMORE").click(function() {
    $(".RH").toggle();
   });
});
</script>
<div class="hidden-print">
<button id="em" onclick=\'javascript:$("#tab").toggle();$("#emaddrs").toggle();\'>Show/Hide Email Addresses</button> 
<button id="btnMORE">Hide/Show Bio Info</button>&nbsp;&nbsp;
<a href="downloads/leaderinfo.csv">DOWN LOAD RESULTS</a><span title="Download file with quoted values and comma separated fields" class="glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px;"></span>
</div>   <!-- hidden-print -->
<table id="tab">
<tr><th>Full Name</th><th>Pri Phone</th><th>Sec Phone</th><th>Email Address</th><th>Address 1</th><th>Address 2</th><th>City</th><th>State</th><th>Zip</th><th>Active</th></tr>
';
$emstr = ''; $noemstr = '';
$csv = '"First Name","Last Name","Pri Phone","Sec Phone","Email Address","Address 1","Address 2","City","State","Zip","Active","Bio"'."\n";
while ($r = $res->fetch_assoc()) {
  //echo '<pre> full record for '.$r[RowID].' '; print_r($r); echo '</pre>';
if ($r[Bio] == "") $r[Bio] = "None provided";
$csv .= '"'.$r[FirstName].'","'.$r[LastName].'","'.$r[PrimaryPhone].'","'.$r[SecondaryPhone].'","'.$r[Email].'","'.$r[Address1].'","'.$r[Address2].'","'.$r[City].'","'.$r[State].'","'.$r[Zip].'","'.$r[Active].'","'.$r[Bio].'"'."\n";
echo '
<tr>
<td>'.$r[FirstName].'&nbsp;'.$r[LastName].'</td><td>'.$r[PrimaryPhone].'</td><td>'.$r[SecondaryPhone].'</td><td>'.$r[Email].'</td><td>'.$r[Address1].'</td><td>'.$r[Address2].'</td><td>'.$r[City].'</td><td>'.$r[State].'</td><td>'.$r[Zip].'</td><td align="center">'.$r[Active].'</td>   
</tr>
<tr class="RH">
<td>&nbsp;</td><td colspan="9"><b>BIO: </b>'.$r[Bio].'</td>
</tr>
<tr><td colspan="10">&nbsp;</td></tr>
';
if (strlen($r[Email]) > 0) {
  $emstr .= $r[FirstName].' '.$r[LastName]." &lt;".$r[Email] . "&gt;\n";   }
else {
  $noemstr .= $r[FirstName].' '.$r[LastName]."\n";   }
}
echo '</table>';

echo '
<div class="container" style="display: none; " id="emaddrs">
<pre>'.$emstr.'</pre>

<h3>Leaders without email addresses</h3>
<pre>'.$noemstr.'</pre>
</div>
';

file_put_contents("downloads/leaderinfo.csv", $csv);
// echo '<pre> CSV'; print_r($csv); echo '</pre>';
?>

</body>
</html>