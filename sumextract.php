<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>SignUp Masters Extract</title>
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
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/checkcred.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$sdate = isset($_REQUEST['sdate']) ? $_REQUEST['sdate'] : "";

echo '
<div class="container">
<h1>SignUp Masters Spreadsheet Extract and Download</h1>
';

if ($_REQUEST['submit'] == 'LOGIN') {
  $pw = $_REQUEST['pw'];
  $pwds = file('../.MBBFSecFile.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  $combo = "SAMUser:$pw";
  if (in_array($combo, $pwds)) {
    $_SESSION['sumlogin'] = 'OK';
    addlogentry('Logged In'); 
    }
  else {
    unset($_SESSION['sumlogin']);
    echo '<a href="sumextract.php" class="btn btn-danger">Login invalid. Try again.</a>';
    exit;
    }
  }
else {
  if (!isset($_SESSION['sumlogin'])) {
    unset($_SESSION['sumlogin']);
    // check for userid and password for sumuser
    echo 'Please provide usage password:
    <form action="sumextract.php" method="post">
    <input autofocus type="text" name="pw">
    <input type="submit" name="submit" value="LOGIN">
    </form>';
    exit;
    }
  }

if ($action == '') {
  echo '
<p>Produces an extract of the "active" events in a download CSV file using the vertical bar (&apos;pipe&apos;) character as the field separator.</p>
<a class="btn btn-primary" href="sumextract.php?action=createextract">Create Downlod File</a>
<br><br>
Output columns are:<br>
<ul>
<li>Track  (Type of event - nature, birding, etc.)</li>
<li>Trip Type (Type - presentation, field trip, event, etc.)</li>
<li>Trip#  (Trip number - a sequential number for each day.</li>
<li>Day (Day of week for event)</li>
<li>TimeSpan (Event date plus start time - Event date plus end time as YYYY-MM-DD HH:MM)</li>
<li>Site</li>
<li>Site Address</li>
<li>Max Attendees</li>
<li>CODE (Event "Level" field)</li>
<li>FEE (Event "FEE" field)</li>
<li>Event (Event name)</li>
<li>Leaders (all event leaders identified separated with comma&apos;s)</li>
<li>Program (description of program)</li>
</ul>
</div> <!-- container -->
</body>
</html>
';

exit;
  }
$start = geteventstart();
//echo "start: $start<br>";

// create extract
$sdatefmt = date("Y-m-d", strtotime("$start - 1 day"));

echo '
<h4>Using event start date of: '.$start.'</h4>

<h3>Extract ready for download.</h3>
<br>
<br>
<a class="hidden-print" href="downloads/sumextract.csv" title="Download file with fields separated by vertical bar character (&apos;|&apos;)">DOWN LOAD RESULTS</a><span title="Download file with fields separated by vertical bar character (&apos;|&apos;)" class="hidden-print glyphicon glyphicon-info-sign" style="color: blue; font-size: 20px;"></span>
<br>';

//Track	Trip	Start	End	TimeSpan	CODE	FEE	Event	Leaders
// where

$sql = '
SELECT * FROM `events` 
WHERE `TripStatus` NOT LIKE "Delete"
ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC;';

//echo "<br>sql: $sql<br>";   2016-01-15 06:45	2016-01-15 14:00

$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

$csv = "Track|EventType|Trip#|Day|Start|End|TimeSpan|Site|SiteAddr|MaxAtt|CODE|FEE|Event|Leaders|Program\n";
$seqno = 1;
while ($r = $res->fetch_assoc()) {
  list($toe,$whocares) = preg_split('/ /', $r[TypeOfEvent]);
  $st = date("H:i", strtotime($r[StartTime]));
  $et = date("H:i", strtotime($r[EndTime]));
  $sd = date("Y-m-d", strtotime("$sdatefmt + $r[Dnbr] day"));
  $starttime = $sd.' '.$st;
  $endtime   = $sd.' '.$et;
  $stts = date("g:i A", strtotime($r[StartTime])).'-'.date("g:i A", strtotime($r[EndTime]));
  $seqno = sprintf("%03s",$seqno);
  $ldr = $r[Leader1];
  if ($r[Leader2] != '') $ldr .= ", $r[Leader2]";
  if ($r[Leader3] != '') $ldr .= ", $r[Leader3]";
  if ($r[Leader4] != '') $ldr .= ", $r[Leader4]";
  $newldr = convertchrs($ldr);
  $newprog = convertchrs($r[Program]);
  $newevent = convertchrs($r[Event]);
  $newlvl = convertchrs($r[Level]);
  $newsite = convertchrs($r[Site]);
  $newsiteaddr = convertchrs($r[SiteAddr]);
  $csv .= "$r[TypeOfEvent]|$r[Type]|$r[Trip]|$r[Day]|$starttime|$endtime|$stts|\"$newsite\"|\"$newsiteaddr\"|$r[MaxAttendees]|\"$newlvl\"|$r[FEE]|\"$newevent\"|\"$newldr\"|\"$newprog\"\n";
  $seqno += 1;
  }

// echo '<pre> csv '; print_r($csv); echo '</pre>';
file_put_contents("downloads/sumextract.csv", $csv);
exit;

function convertchrs($str) {
  $chr_map = array(
   // Windows codepage 1252
   "\xC2\x82" => "'", // U+0082⇒U+201A single low-9 quotation mark
   "\xC2\x84" => '"', // U+0084⇒U+201E double low-9 quotation mark
   "\xC2\x8B" => "'", // U+008B⇒U+2039 single left-pointing angle quotation mark
   "\xC2\x91" => "'", // U+0091⇒U+2018 left single quotation mark
   "\xC2\x92" => "'", // U+0092⇒U+2019 right single quotation mark
   "\xC2\x93" => '"', // U+0093⇒U+201C left double quotation mark
   "\xC2\x94" => '"', // U+0094⇒U+201D right double quotation mark
   "\xC2\x9B" => "'", // U+009B⇒U+203A single right-pointing angle quotation mark

   // Regular Unicode     // U+0022 quotation mark (")
                          // U+0027 apostrophe     (')
   "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
   "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
   "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
   "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
   "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
   "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
   "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
   "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
   "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
   "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
   "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
   "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
  );
  $chr = array_keys  ($chr_map); // but: for efficiency you should
  $rpl = array_values($chr_map); // pre-calculate these two arrays
  $str = str_replace($chr, $rpl, html_entity_decode($str, ENT_QUOTES, "UTF-8"));
  $str = str_replace("|", "", $str);  // kill vert bar/pipe char used as fld sep
  $str = str_replace("\n", " ", $str);  // kill return char
  return($str);
  }
?>
</div>  <!-- container -->
</body>
</html>