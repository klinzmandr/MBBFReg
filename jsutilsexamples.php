<?php
$text = isset($_REQUEST['text']) ? $_REQUEST['text'] : '';
$ta = isset($_REQUEST['ta']) ? $_REQUEST['ta'] : '';
$select = isset($_REQUEST['select']) ? $_REQUEST['select'] : '';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>JS Utilities</title>
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
</head>
<body>
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<div class="container">
<div id="Xmsg"><h3></h3></div>

<h3>JS Utility Examples and Documentation</h3>
<button class="btn btn-xs" id="helpbtn">Help</button>
<a class="btn btn-default btn-xs" href="jsutilsexamples.php">Reload Page</a>
<p>The help button functionality allows information embedded in the page to be optionally displayed and hidden.  Usually this information pertains to the contents of the page.</p>

<div id="help">
<p>Lorem ipsum dolor sit. Lacinia, leo faucibus malesuada lacus venenatis mus vivamus. Molestie. Urna quisque eget, ad. Velit congue consequat in, ipsum ve fringilla. Ut aptent at, commodo cras bibendum velit massa ante. Morbi pellentesque inceptos proin dis arcu elit. Imperdiet malesuada etiam sed amet facilisis lectus, ve fames dictumst at, suspendisse cras. Ligula aliquet id in. Magnis, ante aenean cum, commodo habitant urna posuere vulputate diam conubia. Tempor. Dictumst cubilia cursus lacus, in senectus natoque nonummy consequat rutrum parturient. Curae eu consectetuer potenti, proin vestibulum interdum quisque viverra. Nulla. Libero porta felis lacus a, mauris amet. Vel senectus vivamus nullam eget amet.</p>
</div>

<script>
$(document).ready(function() {
  $("[name=select]").val("<?=$select?>");
  $("#errmsg").click(function() {
    $("#Xmsg").html("<h3>Error Message From Button</h3>").toggle().fadeOut(2000);
    });
  $("#showmodal").click(function() {
    $('#msgdialogtitle').html('TITLE OF MODAL');
    $('#msgdialogcontent').html('Content of the message dialog.');
    $('#msgdialog').modal({ keyboard: true });
  });
});  
</script>

<button id="errmsg">Show error message</button>
<p>This button merely displays a message in the area designated for status and/or error messages on the page.  It fades out after a 5 second timeout.</p>
<h3>Form submission</h3>
<p>The following form is used to demonstrate the detection of any changes to the form and asks confirmation before allowing navigation away from the page.</p>
<p>Navigation away from the page is determined by clicking any object (link, button or menu item with a class name of &quot;dropdown&quot;.</p>
<ul><div style="border: thin solid black"><h4>Form:</h4>
<form action="jsutilsexamples.php">
Text: <input type="text" name="text" value="<?=$text?>"><br>
Checkbox: <input name="cb1" type="checkbox"><input name="cb2" type="checkbox">&nbsp;
Radio: cb1:<input type="radio" name="a">
       cb2:<input type="radio" name="a">&nbsp;
Select: <select name=select>
<option></option>
<option value=1>Option1</option>
<option value=2>Option2</option>
</select><br>
Textarea<br><textarea name=ta><?=$ta?></textarea><br>
<input class="updb" type="submit" value="Apply Updates">&nbsp;&nbsp;
<input id=reset type="reset" value="Reset Form">
</form>
</div></ul>
<p>Use this button to test if navigation away from the page is working.</p>
<button class=dropdown>Navigate away from page</button>
<br><br>
<p>Use this button to show the modal. The title and message is defined by the calling javascript/jquery code.  Examine source code for more detail.</p>
<button id=showmodal>Show modal</button>
<br><br>
<h3>Table Row Filer Example</h3>
<p>The following example show how to apply a filter to selectively display table rows based on a search string.</p>
Filter: <input id="filter"> + <button id=filterbtn1>Apply Filter</button><button id=filterbtn2>Show All</button>
<table>
<table border="1">
<tbody>
<tr id="head"><th>Role</th><th>User ID</th><th>MCID</th><th>Password</th><th>Full Name</th><th>Email</th></tr><tr><td>admin</td><td>carla</td><td></td><td>raptor</td><td>Carla Flanders</td><td>carlanewmail@yahoo.com</td></tr><tr><td>admin</td><td>davek</td><td></td><td>fresno</td><td></td><td>dave.klinzman@yahoo.com</td></tr><tr><td>admin</td><td>gregg</td><td></td><td>jeep</td><td>Greg Gallo</td><td>slowildrsq@gmail.com</td></tr><tr><td>admin</td><td>heatherc</td><td></td><td>raptor</td><td>Heather Craig</td><td>mtnlovr@outlook.com</td></tr><tr><td>admin</td><td>kelly</td><td></td><td>raptor</td><td></td><td>kellywcherry@yahoo.com</td></tr><tr><td>admin</td><td>lauriee</td><td></td><td>raptor</td><td>Laurie Edwards</td><td>slostar2003@att.net</td></tr><tr><td>admin</td><td>markg</td><td></td><td>pelican</td><td>Mark Garman</td><td>thegarmans@aol.com</td></tr><tr><td>admin</td><td>randyd</td><td></td><td>raptor</td><td>Randy Derhammer</td><td>drderhammer@sbcglobal.net</td></tr><tr><td>admin</td><td>susang</td><td></td><td>allie</td><td>Susan Garman</td><td>thegarmans@aol.com</td></tr><tr><td>guest</td><td>abel</td><td></td><td>raptor</td><td></td><td>abe_lincoln1212@hotmail.com</td></tr><tr><td>guest</td><td>andream</td><td></td><td>raptor</td><td></td><td>aimslo@charter.net</td></tr><tr><td>guest</td><td>annieg</td><td></td><td>raptor</td><td></td><td>aanyka@yahoo.com</td></tr><tr><td>guest</td><td>arianel</td><td></td><td>raptor</td><td></td><td>chuparrosa@mac.com</td></tr><tr><td>guest</td><td>beckyb</td><td></td><td>raptor</td><td></td><td>bowenhome@aol.com</td></tr><tr><td>guest</td><td>beckye</td><td></td><td>raptor</td><td></td><td>lilpickles69@yahoo.com</td></tr><tr><td>guest</td><td>berniep</td><td></td><td>raptor</td><td></td><td>indianwells333@aol.com</td></tr>
</tbody>
</table>
<br><br><br><br><br><br>
</div>
</body>
</html>