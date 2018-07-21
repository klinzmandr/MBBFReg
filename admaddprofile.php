<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$id = isset($_REQUEST['newprofname']) ? $_REQUEST['newprofname'] : $_SESSION['profname'];
$f = isset($_REQUEST['f']) ? $_REQUEST['f'] : array();

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

unset($_SESSION['profname']);
// DB will reject entry of a duplicate profile id
$addarray['ProfileID'] = $id;
$stat = sqlinsert("regprofile", $addarray);
if ($stat < 0) {
  echo '<br><br>
  <h1>Profile name already used.</h1><br><br>
  <h3><a href="admin.php">Try again!</a></h3>';
  exit;
  }
// create initial agenda
$agarray['RecKey'] = 'Reg';
$agarray['ProfName'] = $id;
$agarray['AgendaName'] = 'SELF';
sqlinsert('regeventlog', $agarray);

$_SESSION['profname'] = $id;

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Add New Profile</title>
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<style>
  input[type=checkbox] { transform: scale(1.5); }
</style> 

</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
?>

<h2>Profile: <?=$id?> created</h2><br><br>
<h4>This profile has been successfully registered.  Use the &quot;Maintain Profiles&quot; button to update profile details and add events.</h4>
<br><br><br><br>
</body>
</html>