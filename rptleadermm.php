<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Leader Mail Merge</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
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
//include 'Incls/listutils.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

echo '
<h1>Leader Activity
<a href="rptindex.php" class="btn btn-primary hidden-print">RETURN</a></h1>
';

//if ($action == '') {
  echo '
<p>This creates a spreadsheet to use as the source database for performing a mail merge process with work processing tools.  </p>
<!-- <a class="btn btn-primary" href="rptleadermm.php?action=genreport"> CONTINUE</a> -->
';
//exit; 
//  }

echo '
<a class="hidden-print" href="downloads/leadermm.csv">DOWN LOAD RESULTS</a><span title="Download file with quoted values and comma separated fields" class="hidden-print glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px;"></span>';

// fill the array with leader address info
$sql = '
SELECT * FROM `leaders` WHERE 1=1;';
//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$l = array();
while ($r = $res->fetch_assoc()) {
    // echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
    $key = $r[FirstName] . ' ' . $r[LastName];
    $l[$key] [addr1] = $r[Address1];
    $l[$key] [addr2] = $r[Address2];
    $l[$key] [city] = $r[City];
    $l[$key] [st] = $r[State];
    $l[$key] [zip] = $r[Zip];
    $l[$key] [priphone] = $r[PrimaryPhone];
    $l[$key] [secphone] = $r[SecondaryPhone];
    $l[$key] [email] = $r[Email];
    $l[$key] [bio] = $r[Bio];
  }

// add the event info to the array
$sql = '
SELECT * FROM `events` WHERE 1=1;';
//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
while ($r = $res->fetch_assoc()) {
  // echo '<pre> full record for '.$rowid.' '; print_r($r); echo '</pre>';
  if ($r[Leader1] != '') {
    $l[$r[Leader1]] [event] [$r[Event]] += 1; }
  if ($r[Leader2] != '') {
    $l[$r[Leader2]] [event] [$r[Event]] += 1; }
  if ($r[Leader3] != '') {
    $l[$r[Leader3]] [event] [$r[Event]] += 1; }
  if ($r[Leader4] != '') {
    $l[$r[Leader4]] [event] [$r[Event]] += 1; }
  }
ksort($l);
//echo '<pre>full '; print_r($l); echo '</pre>';

// now unpack the array and create the CSV file
// this is to whole csv string
$str = '"Name","Address1","Address2","City","St","Zip",';
$str .= '"PriPhone", "SecPhone", "Email", "Bio", ';
$str .= '"Event1","Event2","Event3","Event4","Event5","Event6"' . "\n";  
// this is to whole csv string
foreach ($l as $k => $v) {
  //echo "count of v: " . count($v) . '<br>';
  //echo '<pre>'; print_r($v); echo '</pre>';
  if (count($v) <= 9) continue;         // no events for this leader
  //echo "$k, $v[addr1], $v[addr2], $v[city], $v[state], $v[zip],";
  $str .= "\"$k\", \"$v[addr1]\", \"$v[addr2]\", \"$v[city]\", \"$v[st]\", \"$v[zip]\",";
  $str .= "\"$v[priphone]\", \"$v[secphone]\", \"$v[email]\", \"$v[bio]\",";
  // echo "<pre> leader $k "; print_r($v); echo '</pre>';
  foreach ($v[event] as $kk => $vv) {
    //echo "<pre>"; print_r($kk); echo '</pre>';
    //echo "\"$kk\", ";
    $str .= "\"$kk\", ";
    }
  $str = rtrim($str, ", ") . "\n";
  //echo '<br>';
  }
//echo "<pre>"; print_r($str); echo '</pre>';
file_put_contents("downloads/leadermm.csv", $str);

?>
</div> <!-- container -->
</body>
</html>