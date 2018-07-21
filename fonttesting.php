<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Font Testing</title>
<link href="css/font-awesome.min.css" rel="stylesheet">
<link href="css/bootstrap.css" rel="stylesheet" media="screen">
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script> 

</head>
<body>
<div class="container">
<h3>Using font-awsome icons</h3>
Getting Started: <a href="http://fontawesome.io/get-started/">http://fontawesome.io/get-started/</a><br>
Reference: <a href="http://fontawesome.io/examples/">http://fontawesome.io/examples/</a><br>
Icons: <a href="http://fontawesome.io/icons/">http://fontawesome.io/icons/</a>
<br>
<h4>Notes</h4>
<p><ol>
<li>Used in a div or span tag, class=&apos;sr-only&apos; is used to identify verbiage to screen reader hardware.</li>
<li>Used in an i tag the class of aria-hidden="true" is used to EXCLUDE the icon from screen readers.</li>
<li>Characters defined inside the i and /i tags will take on the same size and the icon size.</li>
</ol></p>

<h4>Header Link</h4>
<p>The following link must be defined in the header section of the document.  Bootstrap is not required to get the font icons unless its functions are needed or wanted.  The link provides the identification of the css file but the fonts folder is also required.  Easier just to leave everything in the distro folder and move the whole folder into the project root.</p>
Link: <br>
<?php
$str = htmlentities('<link href="font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">');
echo "<code>$str</code>";
?>
<p>Copy the sample code and replace the fa- icon name with one from the page at the <a href="http://fontawesome.io/icons/">Icons page</a>.  Change the class parameters for size and other attributes as illustrated on the <a href="http://fontawesome.io/examples/">examples page</a> of the documentation.</a> </p>

<h4>Basic Icon</h4>
<i class="fa fa-camera-retro"></i> fa-camera-retro<br>
Sample code:<br> 
<?php
$str = htmlentities('<i class="fa fa-camera-retro"></i> fa-camera-retro');
echo "<code>$str</code>";
?>

<h4>Spiners</h4>
<i class="fa fa-spinner fa-spin fa-3x fa-fw\"></i>
<i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>
<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><br>
Sample code: <br>
<?php
$str = htmlentities('<i class="fa fa-spinner fa-spin fa-3x fa-fw\"></i>');
echo "<code>$str</code>";
?>

<h4>Different sizes</h4>
<i class="fa fa-camera-retro fa-lg">ABC xyz</i> fa-lg
<i class="fa fa-camera-retro fa-2x">ABC xyz</i> fa-2x
<i class="fa fa-camera-retro fa-3x"></i> fa-3x
<i class="fa fa-camera-retro fa-4x"></i> fa-4x
<i class="fa fa-camera-retro fa-5x"></i> fa-5x<br>
Sample code: <br>
<?php
$str = htmlentities('<i class="fa fa-camera-retro fa-5x"></i> fa-5x');
echo "<code>$str</code>";
?>

</div>   <!-- container -->
<br><br><br><br><br><br><br>

</body>
</html>