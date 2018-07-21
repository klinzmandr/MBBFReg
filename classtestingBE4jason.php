<?php
$text = isset($_REQUEST['text']) ? $_REQUEST['text'] : '';
$ta = isset($_REQUEST['ta']) ? $_REQUEST['ta'] : '';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Class Testing</title>
<link rel="shortcut icon" href="">
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="css/font-awesome.min.css" rel="stylesheet">
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<style>
  input[type=checkbox] { transform: scale(1.5); }
</style> 
</head>
<body>
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
$(function() {
  $('td:nth-child(2),th:nth-child(2)').hide();
  $("#INP").hide();
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
<script>
$(function() {
$(":checkbox").click(function() {
  var cl = $("#SEL").val();
  $(this).parent().parent().removeClass(cl);     
  if ($(this).prop("checked")) {
    $(this).parent().parent().addClass(cl);
    }  
  });

  $("#PLUS").click(function() {
    // alert("Plus clicked");
    // $(".xxx").toggle();
    $("#INP").show();
    $("#PLUS").hide();
    // console.log($(this).val("td:nth-child(2)"));
  });
  $("#INP").blur(function() {
    // alert("Plus clicked");
    // $(".xxx").toggle();
    $("#INP").hide();
    $("#SEL").append(new Option($("#INP").val(),$("#INP").val()));
    // console.log("select value: " + $("#SEL").val());
    $("#PLUS").show();
    $("#INP").val('');
  });
  
  $("#APPLY").click(function() {
    var filter = '.'+$("#SEL").val();
    if (!filter.length == 1) return;
    $("tr").hide();
    $("#head").show();
    $(filter).show();    
    filter = "tr"+filter + " input:checkbox"; 
    // console.log("new filter: " + filter);
    $(filter).each(function(){ $(this).prop('checked',true); });  
    // $(filter).each(function(){ console.log($(this).prop("checked"); });  
  });

  $("#ALL").click(function() {
    // var filter = '.'+$("#SEL").val();
    // alert("show all clicked");
    $(":checkbox").prop("checked", false);
    $("tr").each(function() {
      var c = $(this).prop("className");
      if (c.length > 0) {
        if (c == "head") return;
        $(this).find("input:checkbox").prop("checked", true);
        // console.log("className: " + c); 
        // console.log($(this));
      }
      // $(this).prop("checked", true); 
      });
    // console.log("filter: " + filter);
    $("#SEL").val('self');
    $('tr').show();
  });
 
});  
</script>

<button id="errmsg">Show error message</button>
<br><br>
<p>Use this button to show the modal. The title and message is defined by the calling javascript/jquery code.  Examine source code for more detail.</p>
<button id=showmodal>Show modal</button>
<br><br>

<h3>Table Row Filer Example</h3>
<p>The following example show how to apply a filter to selectively display table rows based on a search string.</p>
Filter: <span id=filter><select id=SEL> 
<option value="self">Self</option></select>
</span>

+ <button id=APPLY>Apply Filter</button><button id=ALL>Show All</button>
&nbsp;&nbsp;<span id=QM><input id=INP value=""></span>
&nbsp;&nbsp;
&nbsp;&nbsp;<span id="PLUS" title="zzzzz"><i class="fa fa-plus"></i></span>

<table id=TBL>
<table border="1">
<tbody>
<tr id="head"><th>Sel</th><th>Role</th><th>User ID</th><th>MCID</th><th>Password</th><th>Full Name</th><th>Email</th></tr>

<tr><td align=center align=center><input type=checkbox></td><td>admin</td><td>carla</td><td></td><td>raptor</td><td>Carla Flanders</td><td>carlanewmail@yahoo.com</td></tr>

<tr><td align=center><input type=checkbox></td><td>admin</td><td>davek</td><td></td><td>fresno</td><td></td><td>dave.klinzman@yahoo.com</td></tr>

<tr><td align=center><input type=checkbox></td><td>admin</td><td>gregg</td><td></td><td>jeep</td><td>Greg Gallo</td><td>slowildrsq@gmail.com</td></tr>

<tr><td align=center><input type=checkbox></td><td>admin</td><td>heatherc</td><td></td><td>raptor</td><td>Heather Craig</td><td>mtnlovr@outlook.com</td></tr>
<tr><td align=center><input type=checkbox></td><td>admin</td><td>kelly</td><td></td><td>raptor</td><td></td><td>kellywcherry@yahoo.com</td></tr>
<tr><td align=center><input type=checkbox></td><td>admin</td><td>lauriee</td><td></td><td>raptor</td><td>Laurie Edwards</td><td>slostar2003@att.net</td></tr>
<tr><td align=center><input type=checkbox></td><td>admin</td><td>markg</td><td></td><td>pelican</td><td>Mark Garman</td><td>thegarmans@aol.com</td></tr>
<tr><td align=center><input type=checkbox></td><td>admin</td><td>randyd</td><td></td><td>raptor</td><td>Randy Derhammer</td><td>drderhammer@sbcglobal.net</td></tr>
<tr><td align=center><input type=checkbox></td><td>admin</td><td>susang</td><td></td><td>allie</td><td>Susan Garman</td><td>thegarmans@aol.com</td></tr>
<tr><td align=center><input type=checkbox></td><td>guest</td><td>abel</td><td></td><td>raptor</td><td></td><td>abe_lincoln1212@hotmail.com</td></tr>
<tr><td align=center><input type=checkbox></td><td>guest</td><td>andream</td><td></td><td>raptor</td><td></td><td>aimslo@charter.net</td></tr>
<tr><td align=center><input type=checkbox></td><td>guest</td><td>annieg</td><td></td><td>raptor</td><td></td><td>aanyka@yahoo.com</td></tr>
<tr><td align=center><input type=checkbox></td><td>guest</td><td>arianel</td><td></td><td>raptor</td><td></td><td>chuparrosa@mac.com</td></tr>
<tr><td align=center><input type=checkbox></td><td>guest</td><td>beckyb</td><td></td><td>raptor</td><td></td><td>bowenhome@aol.com</td></tr>
<tr><td align=center><input type=checkbox></td><td>guest</td><td>beckye</td><td></td><td>raptor</td><td></td><td>lilpickles69@yahoo.com</td></tr>
<tr><td align=center><input type=checkbox></td><td>guest</td><td>berniep</td><td></td><td>raptor</td><td></td><td>indianwells333@aol.com</td></tr>
</tbody>
</table>
<br><br><br><br><br><br>
</div>
</body>
</html>