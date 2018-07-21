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
<title>Profile Wipout</title>
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

<h1>Profile Wipeout</h1>&nbsp;&nbsp;&nbsp;&nbsp;
<p>Thif function will completely delete a registered profile incuding all agendas defined and assocated scheduled events</p>
<p>CAUTION:  this action can NOT BE RECOVERED.  Once the profile and its assoicated elements are deleted and can not be restored.</p>

<p>Select the target profile from the following list:</p>




<a href="admin.php" class="btn btn-primary">RETURN</a></h1>

</div> <!-- container -->
</body>
</html>