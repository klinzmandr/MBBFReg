<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>List Maintenance</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="css/bs3dropdownsubmenus.css" rel="stylesheet">
</head>
<body onchange="flagChange()">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<?php
//include 'Incls/vardump.inc.php';include 'Incls/datautils.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/mainmenu.inc.php';
include 'Incls/checkcred.inc.php';

if ( !checkcred('Admin') ) {
//  echo "pw passed<br>";
  echo 'Incorrect password entered for administrative access.<br>
  <a href="utlindex.php" class="btn btn-danger">RETURN</a>';
  exit;
  }


$file = isset($_REQUEST['file'])? $_REQUEST['file'] : "";
$action = isset($_REQUEST['action'])? $_REQUEST['action'] : "";
$updfile = isset($_REQUEST['updfile'])? $_REQUEST['updfile'] : "";
$ta = isset($_REQUEST['ta'])? $_REQUEST['ta'] : "";

echo "<div class=\"container\">";
//echo "database in use: ".$_SESSION['DB_InUse']."<br>";
if ($action == "update") {
	updatelist($updfile,$ta);
	$file = $updfile;
	  echo '
<script>
$(document).ready(function() {
  $("#X").fadeOut(2000);
});
</script>
<h3 style="color: red; " id="X">Update Completed.</h3>
'; 

	}

echo '<h2>List Maintenance Utility</h2>';

	echo '<p>Choose a menu option to update a specific list.</p>
	<p>All lists use a free form text file to define the list items used.  Lines that begin with a double slash (//) are provided for comments (which are encouraged.)  The comment lines as well as blank lines are ignored </p>
';
	
	echo "<p>Make sure to save your changes after performaing any updates.</p>";
	

// NOTE: 'value' parameter of option tag MUST be the filename (without .txt) in the cfg folder 
echo '
<form action="utllistmaint.php" method="post">
Choose list from the following: 
<select name="file" onchange="this.form.submit()">
<option value=""></option>
<option value="Day">Day</option>
<option value="Site">Sites</option>
<option value="Transportation">Transportation</option>
<option value="TripStatus">Trip Status</option>
<option value="TripType">Trip Type</option>
<option value="TripTypeCodes">Trip Type Codes</option>
<option value="TypeOfEvent">Type Of Event</option>
<option value="EventLevels">Event Levels</option>
</select>
</form>
';

// no file name provided so exit to allow one to be entered
if ($file == "") {
  exit;
}

// file type is there, process it
	echo '
<h3>File: '.$file.'</h3>
<form action="utllistmaint.php" method="post">
<textarea name="ta" rows="10" cols="100">';
	echo readfulllist($file);
	echo '
</textarea><br />	
<input type="hidden" name="action" value="update">
<input type="hidden" name="updfile" value="'.$file.'">	
<input type="submit" name="Submit" value="SAVE UPDATES" />
</form></div></body></html>';
	exit;

?>

</div>
</body>
</html>
