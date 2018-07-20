<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Reset Trip Status</title>
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
include 'Incls/listutils.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/checkcred.inc.php';

if ( !checkcred('ReSet') ) {
//  echo "pw passed<br>";
  echo 'Incorrect password entered for administrative access.<br>
  <a href="utlindex.php" class="btn btn-danger">RETURN</a>';
  exit;
  }

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$day = isset($_REQUEST['Day']) ? $_REQUEST['Day'] : "";

echo '<h3>Reset of Event Status</h3>';

$dayarray[Friday] = 1;$dayarray[Saturday] = 2; $dayarray[Sunday] = 3; $dayarray[Monday] = 4; 

if ($action == '') {
  echo '
  <p>This utility will set the &quot;Trip Status&quot; field on all events to a value of &quot;Under Consideration&quot;.  In addition, the &quot;Secondary Status&quot; field used for production notes is also cleared.</p>
  <p>NOTE: those events with the status of &quot;Delete&quot; are NOT CHANGED and so they still remain available for inclusion into the new event calendar.</p>
  <p>This action is usually ONLY done at the start of planning of the new seasons event calendar.  As events are confirmed and finalized, their status is changed to another setting.</p>

<a href="utlresetstatus.php?action=reseq" class="btn btn-primary">Reset all trip status.</a>

';

exit;
  }

if ($action == 'reseq') {
$sql = '
SELECT * FROM `events` WHERE 1=1 AND `TripStatus` NOT LIKE "Delete";';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

echo "
<p>There are $rc events that will have their status chnged to &quot;Under Consideration&quot;.</p>

";
echo '
<script>
$(document).ready(function() {
  $("#AP").click(function() {
    $("#IMG").attr("style","visibility-visible");
    });
});
</script>

<a href="utlresetstatus.php" class="btn btn-primary">Re-start process</a><br><br>';

echo '
<a id="AP" href="utlresetstatus.php?action=apply" class="btn btn-primary">Apply</a>
<br><br>
<img id="IMG" style="visibility: hidden;" src="img/progressbar.gif" width="226" height="26" alt="">
';
exit;
}

//echo 'apply changes<br>';

$sql = '
SELECT * FROM `events` 
WHERE 1=1 AND `TripStatus` NOT LIKE "Delete";';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

$updarray = array();
while ($r = $res->fetch_assoc()) {
  // echo '<pre> full record for '.$rowid.' '; print_r($r[Trip]); echo '</pre>';
  $rowid = $r[RowID];
  $results .= "$r[Trip] => $seqstart, ";
  $updarray[TripStatus] = 'Under Consideration';
  $updarray[SecondaryStatus]  = '';
  sqlupdate('events', $updarray, '`RowID` = "'.$rowid.'";');
  $seqstart += 1;
  }

addlogentry('Status of all events: Under Consideration');

echo '
<h3>Status updates are complete.</h3>';

?>
</div> <!-- container -->
</body>
</html>