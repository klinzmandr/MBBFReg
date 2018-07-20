<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Mail Merge Export</title>
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
include 'Incls/listutils.inc.php';
include 'Incls/mainmenu.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$type = isset($_REQUEST['Type']) ? $_REQUEST['Type'] : "";

echo '
<div class="container">
<h3>Leader Email Merge Extract</h3>
';

echo '
<p>This extract examines all &quot;active&quot; events and creates a list of the email addresses for all the leaders that are assigned ANY leader position.</p>
<p>The report output is to be highlighted then copy/pasted into the email client.</p>
';

// create report
/* echo '
<a class="hidden-print" href="downloads/leadermailmerge.csv">DOWN LOAD RESULTS<a><span title="Download file with quoted values and comma separated fields" class="hidden-print glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px;"></span>';
*/

// create array of leader names and email addresses
$sql = '
SELECT * FROM `leaders` WHERE 1=1';
//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
while ($r = $res->fetch_assoc()) {
  $key = $r[FirstName] . ' ' . $r[LastName];
  $leaderemail[$key] = $r[Email];
  $leadername[$key] = $key;
  }
//echo '<pre> email array '; print_r($leaderemail); echo '</pre>';

// all leaders -> email in $leaderemail array
$sql = '
SELECT `events`.*
FROM `events` 
WHERE 1 = 1
  AND `TripStatus` NOT LIKE "Delete"  
ORDER BY `events`.`Dnbr` ASC, `events`.`StartTime` ASC, `events`.`EndTime` ASC;';

$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
// building this array will eliminate any dup email addresses.
while ($r = $res->fetch_assoc()) {
  $emarray[$leaderemail[$r[Leader1]]] = $leadername[$r[Leader1]]; 
  $emarray[$leaderemail[$r[Leader2]]] = $leadername[$r[Leader2]];
  $emarray[$leaderemail[$r[Leader3]]] = $leadername[$r[Leader3]]; 
  $emarray[$leaderemail[$r[Leader4]]] = $leadername[$r[Leader4]]; 
  }
echo '<br>Active event count: '.$rc.'<br>';
echo 'Leader email count: '.count($emarray).'<br>';
//echo 'results array: '.count($emarray).'<br>';
//echo '<pre> emarray '; print_r($emarray); echo '</pre>';

echo '<pre>';
foreach ($emarray as $k => $v) {
  if ($k == '') continue;
  echo "$v &lt;$k&gt;,\n";
  } 
echo '</pre>';

?>
</div>  <!-- container -->
</body>
</html>