<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

$profname = $_REQUEST['newprofname'];
$_SESSION['profname'] = $profname;

// used in profnew.php and register.php
$_SESSION['admMode'] = 'ON';

$frameurl = "profnew.php?action=new&profname=$profname&newxmpt";
$frame = "<iframe src=$frameurl style='height:550px;width:800px;'></iframe>";
// include 'Incls/vardump.inc.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile/Agenda Maint</title>
<!-- Bootstrap -->
<script src="js/jquery.min.js"></script>
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jsutils.js"></script>

<script src="js/bootstrap-session-timeout.js"></script> <!-- no menu bar -->

</head>
<body>
<a href="admin.php" class="btn btn-success btn-lg">RETURN TO ADMIN</a>
<div class="container">
<?php echo $frame; ?>
</div> <!-- container -->
</body>
</html>