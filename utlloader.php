<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Spreadsheet File Uploader</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/datepicker3.css" rel="stylesheet">
</head>
<body>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

echo '
<div class="container">
<h3>Event Listing Update Utility <a href="utlindex.php" class="btn btn-primary">Utility Menu</a></h3>
';
if ($action == '') {
// setup of initial input parameters
echo '

<p>This page is designed to upload a CSV spreadsheet and use it to replace all the records in the events table of the database.</p>
<p>The prerequisites of the spreadsheet file to be uploaded are:
<ol>
<li>The spreadsheet file must be a csv formatted file.</li>
<li>Only the FIRST worksheet tab of a spreadsheet file is imported.</li>
<li>Row 1 must contain column names.</li>
</ol></p>
';

echo '
<script>
function chkuser() {
  var r = prompt("Please enter useage security code.");
  if (r == "raptor") {
    document.getElementById("GIF").style.visibility = "visible";
    return true;
    }
  return false;
}
</script>
';
echo '
<form id=ulf action="utlloader.php" method="post" enctype="multipart/form-data">
<br>Select spreadsheet file:&nbsp;
<input size=50 type="file" name="file" id="file" /><br>
<input type=hidden name=action value=upload>
<button onclick="return chkuser()" class="btn btn-success" type=submit form=ulf>CONTINUE</button><br><br>
<div  id="GIF" style="visibility: hidden;">
Processing ........<br>
<img src="img/progressbar.gif" width="226" height="26" alt="">
</div>
';
exit;
}

// process upload and validate file
if ($action == 'upload') {
  $fn = ($_FILES["file"]["name"]); $fninfo = pathinfo($fn); $fnext = $fninfo['extension'];
  switch($fnext) {
    case 'csv': case 'CSV':
      continue;
    case 'ods': case 'xls': case 'xlsx': default:
      echo '<h4 style="color: red; ">File type is invalid.  Please select a spreadsheet file of type csv.<h4>
    <a class="btn btn-danger" href=utlloader.php>CONTINUE</a>';
      exit;
    }

	if ($_FILES["file"]["size"] > 20000000) {
		echo '<h4 style="color: red; ">File size exceeds maximum allowed of 20 mBytes.</h4>';
		exit;
	}
  if ($_FILES["file"]["error"] > 0)  {		// check for upload error
    echo "ERROR: " . $_FILES["file"]["error"] . "<br />";
    echo '<h4 style="color: red; ">Correct error and try again</hr>';
    exit;
    }
else {
//    	echo '<pre> file array: '; print_r($_FILES); echo '</pre>';
  move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/" . $_FILES["file"]["name"]);
  }
}

// now read the newly uploaded file and validate that 
//		it has only 1 spreadsheet 
//		a column heading containing MCID

$Filepath = "uploads/" . $_FILES["file"]["name"];
require('sslib/php-excel-reader/excel_reader2.php');
require('sslib/SpreadsheetReader.php');

try {
	$Spreadsheet = new SpreadsheetReader($Filepath);
//	$BaseMem = memory_get_usage();
//	echo "Filepath: $Filepath<br>";
	$errs = "";
	$Sheets = $Spreadsheet -> Sheets();
//	echo '<pre> Sheets: '; print_r($Sheets); echo '</pre>';

	$Index = 0;			// first (only?) sheet tab
	$Spreadsheet -> ChangeSheet($Sheets[$Index]);
//	echo '<pre> spreadsheet array: '; print_r($Spreadsheet); echo '</pre>';
	$curritem = $Spreadsheet -> current();
//	echo '<pre> curritem: '; print_r($curritem); echo '</pre>'; 
	if (count($curritem) == 0) $errs .= 'Spreadsheet is empty!<br>';
	$colidx++;		
	}

catch (Exception $E)	{
  echo '<h3>Import error:</h3>';
	echo $E -> getMessage();
  }

echo "<h4>Upload successful. File stored as: " . "&apos;" . $_FILES["file"]["name"] . '&apos;</h4>';
// report errors or continue with what is entered	
if (strlen($errs) > 0) { 
	echo "$errs<br>";
	echo '
  <h4 style="color: red; ">Spreadsheet NOT valid.</h4>
<br><br>
<a class="btn btn-primary btn-danger" href="utlloader.php">CANCEL</a>
</div>  <!-- container -->
</body>
</html>
';
exit;
}

// Spreadsheet is loaded and read to be applied

echo '<h4>Uploaded spreadsheet '. $_FILES["file"]["name"] . ' ready for import.</h4>';

doSQLsubmitted('TRUNCATE TABLE `events`;');
echo '<h4>All data rows deleted from events table.</h4>';

// read and apply rows to truncated events table.
include 'Incls/tabledef.inc.php';  // $tblcols array definition
$updarray = array(); $updcount = 0;
// echo '<pre> Spreadsheet: '; print_r($Spreadsheet); echo '</pre>';
foreach ($Spreadsheet as $Key => $Row) {
  if (substr($Row[0],0,6) == 'Trip #') continue;        // drop header row in row 1
//	echo '<pre> key: '; print_r($Key); echo '</pre>';	echo '<pre> row: '; print_r($Row); echo '</pre>';
	$colcount = count($tblcols);   // use $tblcols to load update array
	foreach ($tblcols as $colname => $coloffset) {
	  $updarray[$colname] = $Row[$coloffset];
    }
//  echo '<pre> updarray: '; print_r($updarray); echo '</pre>';
  sqlinsert('events', $updarray);
  $updcount++;
	}

//echo '<h4 style="color: red; ">Test Mode Enabled - no database update performed.</h4>';

echo '
<h4>New rows imported: '.$updcount.'</h4>';
unlink($Filepath);
echo '<h4>Upload file '.$Filepath.' deleted</h4>
<h3>Update Processing Complete!</h3>
<a href="utlloader.php" class="btn btn-success">CONTINUE</a><br><br>';
?>
</div>  <!-- container -->
</body>
</html>
