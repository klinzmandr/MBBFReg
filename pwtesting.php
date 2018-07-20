<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>PW Testing</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css"/>
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
<script type="text/javascript" src="js/bootstrap-multiselect.js"></script>

<div class="container">
<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//include 'Incls/vardump.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$codes = isset($_REQUEST['Codes']) ? $_REQUEST['Codes'] : '';

echo '<pre> input '; print_r($codes); echo '</pre>';
if ($codes != "") $vals = "['" . implode("','", $codes) . "']";
else $vals = "[]";
echo "<br>vals: $vals<br>";

if ($action == 'reset')  {
  unset($_SESSION['Admin']);
  //session_unset();    
  //session_destroy();
  echo 'SESSION RESET<br>';
  }

include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/checkcred.inc.php';

if ( checkcred('Admin') )
  echo "pw passed<br>";
else 
  echo "pw failed<br>";
  
echo '
<h1>Password and Select list Tester</h1>

<script type="text/javascript">
$(document).ready(function () {

  var initValues = '.$vals.';
  $("#Codes").val(initValues);
  $("#Codes").multiselect({
    numberDisplayed: 6,
    delimiterText: ", ",
    nonSelectedText: "Select Codes"
    });
  $("#Codes").multiselect("refresh");

});
</script>

<form action="pwtesting.php" method="post">
<select id="Codes" name="Codes[]" multiple>';

echo readlist('Codes');

?>
</select>
<input type="submit" name="submit" value="submit">
</form>

<a href="pwtesting.php" class="btn btn-primary">Go Again</a><br><br>
<a href="pwtesting.php?action=reset" class="btn btn-primary">RESET SESSION</a>

</div> <!-- container -->
</body>
</html>