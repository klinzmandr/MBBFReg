<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

//include 'Incls/vardump.inc.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Template</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<script src="js/chksession.js"></script>

</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
?>

<h1>Template</h1>&nbsp;&nbsp;&nbsp;&nbsp;
<p>Starting point for new pages and/or reports</p>

<a href="admin.php" class="btn btn-primary">RETURN</a></h1>

</div> <!-- container -->
</body>
</html>