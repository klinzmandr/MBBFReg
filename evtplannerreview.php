<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Event Planer Review</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet">
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
</head>
<body style="background: grey; background-color: #8D766E; ">



<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<div class="container">
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include 'Incls/datautils.inc.php';
echo '
<h1 style="color: white; ">Event Planner Review
<a href="index.php" class="btn btn-primary">Main Menu</a></h1>
';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";

echo '
<p style="color: white; ">This window is how the event planner will appear on the web site.</p>
 <!-- <iframe src="planner.php" width="700" height="500" frameborder="1"></iframe>  -->
 <iframe class="embed-responsive-item" src="planner.php" height="500" width="100%"></iframe>
';


?>
</div> <!-- container -->
</body>
</html>