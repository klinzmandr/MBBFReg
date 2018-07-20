<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Duplicate Event</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet">
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

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : "";
$newtrip = isset($_REQUEST['newtrip']) ? $_REQUEST['newtrip'] : "";

echo '
<h1>Duplicate Event&nbsp;&nbsp;
<a class="btn btn-primary" href="evtlister.php">RETURN</a>

</h1>';

// check if one already exists
$sql = 'SELECT * FROM `events` WHERE `RowID` = "'.$rowid.'";';
//echo "sql: $sql<br>";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
$rc = $res->num_rows;
//echo "rowcount: $rc<br>";

if ($rc == 0) {
//  echo 'old one does not exists<br>';
  $rowid = $r[RowID];
  echo '<h3 style="color: red; ">Error on read of Row ID '. $rowid.'</h3><br>';
  echo '<button class="btn btn-primary" type="submit" href="evtlister.php">RETURN</button>
  </div> <!-- container -->
  </body>
  </html>';
  exit;
  }

//  get the new trip number for the duplicate record
if ($action == '') {
  echo'
<script>
function chktrip() {
  var l = $("#T1").val().length
  if (l <> 3) {
    alert("Trip number must be 3 digits.\\n\\nPlease re-enter.");
    return false;
    }
  return true;  
  }
</script>
<h3>Duplication of Event</h3>
<p>Duplication of an existing Trip numbered "'.$r[Trip].'" with the description of "'.$r[Event].'" has been requested.</p>
<h4>Please enter the new trip number to use for the duplicate record.</h4>
<p>Please note that the trip number must be 3 digits long. Numbering scheme is for 100 series numbers are used for day 1 events, 200 series numbers for day 2 and so on.  Renumbering of events may be necessary if numbers are duplicated.</p>
<form action="evtduplicateevent.php" method="post" onsubmit="return chktrip()">
<input id="T1" type="text" name="newtrip" value="'.$newtrip.'">
<input type="hidden" name="action" value="addnew">
<input type="hidden" name="rowid" value="'.$rowid.'">
<button class="btn btn-primary" type="submit">DUPLICATE EVENT</button>
';
exit;
}

// echo '<pre> Existing '; print_r($r); echo '</pre>';

$updarray = array();
$updarray = $r;  
//echo '<pre> updarray before '; print_r($updarray); echo '</pre>';

unset($updarray[RowID]);          // dump old row number and
$updarray[Trip] = $newtrip;       // insert the new trip number into update array
//echo '<pre> Duplicate '; print_r($updarray); echo '</pre>';

// create dup now
$err = sqlinsert('events', $updarray);
if ($err == FALSE) {
  echo '
Existing row: '.$rowid.', duplicate key entered: '.$newtrip.'<br>
<a href="evtlister.php" class="btn btn-danger">Return</a>
</div> <!-- container -->
</body>
</html>';
  exit;
  }

$sql = 'SELECT * FROM `events` WHERE `Trip` = "'.$newtrip.'" ORDER BY `RowID` DESC;';
//echo "sql after insert: $sql<br>";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
$rc = $res->num_rows;
//echo "row count after insert: $rc<br>";
//echo '<pre> DUPLICATE '; print_r($r); echo '</pre>';

$rowid = $r[RowID];
echo "rowid: $rowid<br>";
$navarray[] = $rowid;
$nav['start'] = 0; $nav['prev'] = ''; $nav['curr'] = '';
$nav['next'] = ''; $nav['last'] = count($navarray) - 1;

$_SESSION['navarray'] = $navarray;
$_SESSION['nav'] = $nav;

echo '
<h3>A duplicate record of event "'.$r[Trip].'" with the description of "'.$r[Event].'" has been completed.</h3>
<a href="evtupdateevent.php?ptr=0" class="btn btn-success">CONTINUE TO UPDATE THE DUPLICATE EVENT</a>';

?>
</div> <!-- container -->
</body>
</html>