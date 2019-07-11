<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$id = isset($_REQUEST['profname']) ? $_REQUEST['profname'] : $_SESSION['profname'];
$f = isset($_REQUEST['f']) ? $_REQUEST['f'] : array();
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// setup new flag if via index.php vs proflogin.php
$ftflag = isset($_REQUEST['newxmpt']) ? 'new' : 'not'; 
// adm flag for new via index.php vs admaddprofile.php
$adm = $_SESSION['admMode'];

$_SESSION['profname'] = $id;  // set profile name for session

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/checkcred.inc.php';

// get festival start date and time
$exemptionstart = date("F j, Y \a\\t H:i", strtotime(getregstart()."+ 2 weeks"));
// echo "exemptionstart: $exemptionstart<br>";

// used when called by the register Upd Profile button 
// or index page's "access existing profile" button
if ($action == 'update') {
  // read profile for current values
  $f = array();  
  // echo "id: $id<br>";
  $res = doSQLsubmitted("SELECT * FROM `regprofile` WHERE `ProfileID` = '$id';");
  $f = $res->fetch_assoc();
  // echo '<pre> update '; print_r($f); echo '</pre>';
  // echo "set action: $action in profnew request = update <br>";
  }

// triggered by create new button in index.php
if ($action == 'new') {
  unset($_SESSION['profname']);
  // DB will reject entry of a duplicate profile id
  $f['ProfileID'] = $id;
  $f['Exempt'] = 'NO';
  $f['SLBM'] = 'NO';
  $f['Vol'] = 'NO';
  $f['regType'] = 'full';
  $stat = sqlinsert("regprofile", $f); // register new profile
  if ($stat < 0) {
    echo '<br><br>
    <h3><a href="index.php" class="btn btn-danger">Try again.</a></h3>';
    exit;
    }
  // echo "set action: $action in profnew request should be NEW";
  $_SESSION['profname'] = $id;
  }

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Profile</title>
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<style>
  p, th, td, .xbtn { font-size: 1.5em; }
  input[type=checkbox] { transform: scale(2.0); }
a.disabled {
  opacity: 0.5;
  pointer-events: none;
  cursor: default;
}
</style> 

</head>
<body>
<script src="js/jsutils.js"></script>
<script src="js/jsresetform.js"></script>

<img src="https://morrobaybirdfestival.org/wp-content/uploads/2016/08/LOGO3.png" alt="bird festival logo" >
<h2>Profile: <?=$id?></h2>

<script>
$(function() {
  // alert("doc load");
  $("#LUN").hide();   // hide optional form sections
  $("#SHI").hide();
  $("#RAF").hide();
  
  var ftflag = "<?=$ftflag?>";  // firt time flag for new or upd
  if (('<?=$adm?>' == 'ON') || (ftflag == 'new'))
    $("a.btn").hide();
    
  var exempt = "<?=$f[Exempt]?>";     // exemption flag from db
  $("#EXEMPT").val(exempt);           // set hidden form value 
  var slbm = "<?=$f[SLBM]?>";     // speaker, leader, Board Mbr flag
  if (slbm == 'YES') $("#slbmflag").text('YES');
  var vol    = "<?=$f[Vol]?>";        // volunteer flag
  if (vol == 'YES') $("#volflag").text('YES');
  var regtype = "<?=$f[regType]?>";
  $("#regdd").val(regtype);           // set reg type drop down
  if (regtype == 'full') $("#regdone").text('Full Festival Registration');
  if (regtype != 'full') $("#regdone").text(regtype+' Only');
  if (ftflag == 'new') { 
    $("#REGONE").show(); $("#REGDONE").hide();    // registration option
    $("#EXPONE").show(); $("#EXPDONE").hide();    // registration input field
    }
  else { 
    $("#REGONE").hide(); $("#REGDONE").show(); 
    $("#EXPONE").hide(); $("#EXPDONE").show(); 
    }
  
  // memorize whole form on initial load to check for changes
  $form = $('form');
  origForm = $form.serialize();
  

// check if anything on the form has changed
$('form :input').on('change input', function() {
  if ($form.serialize() !== origForm) {
    // alert("form has been updated");
    $("a.btn").addClass('disabled');
    return;
    }
});

// vol flag checked/unchecked
$("#VOL").click(function() {
  // alert("VOL cb checked");
  $("#SLBM").prop("checked", false);
  if ($("#VOL").is(":checked")) {
    // alert("box checked");
    $("#regdd").val('full');    // set registration type = full
    $("#REGONE").hide();        // hide registration option
    $("#EXEMPT").val('YES');    // set hidden field for db
    showmodal();
    }  
  else {
    // alert ("box unchecked");
    $("#EXEMPT").val('NO');     // set hidden field for db
    $("#REGONE").show();        // show registraton option
    }
});

function showmodal() {
  // alert("show modal on click of either slbm or vol checkboxes");
  $("#msgdialogtitle").html("<h3 style='color: red;'>Festival Exemption Notice</h3>"); 
  $("#msgdialogcontent").html('<p>Selection of festival events for those asking for exemptions from paying festival registration fee starts 2 weeks after the start of the normal registration period.</p><p>Currently this date is <?=$exemptionstart?></p><p>Event selection before this date and time will be blocked.</p>');
  $('#msgdialog').modal('toggle', { keyboard: true });
  return;
}

// SLBM checked/unchecked 
$("#SLBM").click(function() {
  // alert("slbm check box changed");
  $("#VOL").prop("checked", false);
  if ($("#SLBM").is(":checked")) {
    // alert("box checked");
    $("#regdd").val('full');    // set registration type = full
    $("#REGONE").hide();        // hide registration option
    $("#EXEMPT").val('YES');    // set hidden field for db
    showmodal();
    }
  else {
    // alert ("box unchecked");
    $("#EXEMPT").val('NO');     // set hidden field for db
    $("#REGONE").show();        // show registraton option
    }
  });

$("#PN").change(function() {
	var reason = validatePhone($("#PN").val());
	if (reason.length != 0) {
  	// alert("Error: "+reason);
  	$("#msgdialogtitle").html("<h3 style='color: red;'>Field validation errors </h3>"); 
    $("#msgdialogcontent").html('<p>The following error(s) were detected:</p><p><ol>'+reason+'</ol></p>');
    $('#msgdialog').modal('toggle', { keyboard: true });
  	return false;
		}
  });

$("#LUNbtn").click(function(evt) {
  evt.preventDefault();
  $("#LUN").toggle();
  $("#SHI").hide();
  $("#RAF").hide();
  });
  
$("#SHIbtn").click(function(evt) {
  evt.preventDefault();
  $("#LUN").hide();
  $("#SHI").toggle();
  $("#RAF").hide();
  });
  
$("#RAFbtn").click(function(evt) {
  evt.preventDefault();
  $("#LUN").hide();
  $("#SHI").hide();
  $("#RAF").toggle();
  });
  
});

</script>
<script>
function validateform() {
  // alert("form submitted validations");
  var reason = "";
	reason += validateUserfname($("#FN").val());
	reason += validateUserlname($("#LN").val());
	reason += validatePhone($("#PN").val());
	if (reason.length != 0) {
  	// alert("Error: "+reason);
  	$("#msgdialogtitle").html("<h3 style='color: red;'>Field validation errors </h3>"); 
    $("#msgdialogcontent").html('<p>The following error(s) were detected:</p><p><ol>'+reason+'</ol></p>');
    $('#msgdialog').modal('toggle', { keyboard: true });
  	return false;
		}
  $("#AGN").val($("#FN").val()+' '+$("#LN").val());   // passoff agenda name
  }

function validateUserfname(fld) {
  var error = "";
  var illegalChars = /\W/; // allow letters, numbers, and underscores
  if (fld.length == 0) {
    error += "<li>You didn't enter any first name.</li>";
  	} 
  else if (illegalChars.test(fld)) {
    error += "<li>The name may only contain letters and numbers.</li>";
   	} 
  return error;
	}

function validateUserlname(fld) {
  var error = "";
  var illegalChars = /\W/; // allow letters, numbers, and underscores
  if (fld.length == 0) {
    error += "<li>You didn't enter any last name.</li>";
  	} 
  else if (illegalChars.test(fld)) {
    error += "<li>The name may only contain letters and numbers.</li>";
   	} 
  return error;
	}

function validatePhone(fld) {
  var error = "";
  var st = fld.replace(/\D/g, '');    
  if (fld.length == 0) {
  	error += "<li>You didn't enter a phone number.</li>";
    }
  var i = parseInt(st);
  if (isNaN(i)) {
    error += "<li>The phone number contains illegal characters.</li>";
    } 
  if (st.length != 10) {
    error += "<li>The phone number be 10 digits. Make sure you included an area code.</li>";
    }
  if (error.length > 0) return error;
  var pn = st.substring(0,3)+'-'+st.substring(3,6)+'-'+st.substring(6);
  $("#PN").val(pn);
  return "";  
	}

</script>
<form action="proflogin.php" method="post" onsubmit="return validateform()">
<table class=table>
<input type=hidden name=f[ProfileID] type=text value="<?=$id?>";
<tr><td>First name*: <input  id=FN title="Required entry" name=f[ProfFirstName] type=text value="<?=$f[ProfFirstName]?>">
Last name*: <input  id=LN title="Required entry" name=f[ProfLastName] type=text value="<?=$f[ProfLastName]?>">
</td></tr>

<input type=hidden name=agendaname id=AGN value=''>

<tr><td>Mailing address: <input name=f[ProfAddress] type=text value="<?=$f[ProfAddress]?>"></td></tr>
<tr><td>City <input name=f[ProfCity] type=text value="<?=$f[ProfCity]?>">
ST <input name=f[ProfState] type=text value="<?=$f[ProfState]?>">
ZIP <input name=f[ProfZip] type=number value="<?=$f[ProfZip]?>" style="width: 75px;" min=0 max=99999></td></tr>
<tr><td>Contact phone number*: <input id=PN title="Required entry" name=f[ProfContactNumber] type=tel value="<?=$f[ProfContactNumber]?>"></td></tr></table>

<input type=hidden name=action value="<?=$action?>">

<div id=EXPONE>
Do you qualify for a festival fees exemption as a speaker, leader or Board member?&nbsp;YES: &nbsp;
  <input type=checkbox id=SLBM name=f[SLBM] value=YES><br>
Do you qualify for a festival fees exemption as volunteer?&nbsp;YES: &nbsp;
  <input type=checkbox id=VOL name=f[Vol] value=YES><br>
NOTE 1: those seeking fee exemptions are limited to registration for themselves only.<br>
NOTE 2: an exemption must be applied for, reviewed and approved by Festival Registrar before it will be applied and available only to an individual.<br>
</div>  <!-- EXPONE -->

<input id=EXEMPT type="hidden" name="f[Exempt]" value="NO">

<div id=EXPDONE>
Exemption as a leader, speaker or Board member applied for: <b><span id=slbmflag>NO</span></b><br>
Exemption as a volunteer applied for: <b><span id=volflag>NO</span></b><br>
NOTE 1: those seeking fee exemptions are limited to registration for themselves only.<br>
NOTE 2: an exemption must be applied for, reviewed and approved by Festival Registrar before it will be applied and available only to an individual.<br>
</div>  <!-- EXPDONE -->

<table id=REGONE class=table><tr>
<tr><td><h3>Registration Type*
<select class=reg id=regdd name="f[regType]">
<option class=sel value='full'>Full Festival Registration</option>
<option class=sel value=Friday>Friday only</option>
<option class=sel value=Saturday>Saturday only</option>
<option class=sel value=Sunday>Sunday only</option>
<option class=sel value=Monday>Monday only</option>
</select></h3></td></tr></table>

<div id=REGDONE>
<h3>Registration Type*:&nbsp;<span id=regdone></span></h3>
NOTE 1: Only full registrations are permitted to register multiple agendas.<br>
NOTE 2: Only the Registrar can change the registration type.
</div>  <!-- REGDONE -->

<hr>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<button class="btn btn-success" id=LUNbtn>Lunches</button>
&nbsp;&nbsp;&nbsp;&nbsp;
<button class="btn btn-success" id=SHIbtn>Shirts</button>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<button class="btn btn-success" id=RAFbtn>Raffle Tickets</button><br><br>
<div id=LUN>
<h3>Lunches</h3>
<h4>Lunch ($8.00 per person)</h4>
<table class=table><tr><td>
<b>Friday:</b> Meat <input type=number name="f[mealFrM]" value="<?=$f[mealFrM]?>" style="width: 35px;" min=0 max=9> or Veg <input type=number name="f[mealFrV]" value="<?=$f[mealFrV]?>" style="width: 35px;" min=0 max=9></td><td>
<b>Saturday:</b>  Meat <input type=number name="f[mealSaM]" value="<?=$f[mealSaM]?>" style="width: 35px;" min=0 max=9> or Veg <input type=number name="f[mealSaV]" value="<?=$f[mealSaV]?>" style="width: 35px;" min=0 max=9></td><td>
<b>Sunday:</b> Meat <input type=number name="f[mealSuM]" value="<?=$f[mealSuM]?>" style="width: 35px;" min=0 max=9> or Veg <input type=number name="f[mealSuV]" value="<?=$f[mealSuV]?>" style="width: 35px;" min=0 max=9></td><tr></table>
</div>   <!-- lunches div -->
<div id=RAF>
<h3>Raffle ticket purchases</h3>
</div>  <!-- raffle ticket div -->
<div id=SHI>
<hr>
<h3>Shirts</h3>
<h4>Shirt Size (full registration only)</h4>
<table class=table border=0><tr><td>
<b>Women</b></td><td>S:<input type=number name="f[shirtwS]" value="<?=$f[shirtwS]?>" style="width: 35px;" min=0 max=9></td><td>
M:<input type=number name="f[shirtwM]" value="<?=$f[shirtwM]?>" style="width: 35px;" min=0 max=9></td><td>
L:<input type=number name="f[shirtwL]" value="<?=$f[shirtwL]?>" style="width: 35px;" min=0 max=9></td><td>
XL:<input type=number name="f[shirtwXL]" value="<?=$f[shirtwXL]?>" style="width: 35px;" min=0 max=9></td><td>&nbsp;</td></tr>

<tr><td><b>Men</b></td><td>S:<input type=number name="f[shirtmS]" value="<?=$f[shirtmS]?>" style="width: 35px;" min=0 max=9></td><td>
M:<input type=number name="f[shirtmM]" value="<?=$f[shirtmM]?>" style="width: 35px;" min=0 max=9></td><td>
L:<input type=number name="f[shirtmL]" value="<?=$f[shirtmL]?>" style="width: 35px;" min=0 max=9></td><td>
XL:<input type=number name="f[shirtmXL]" value="<?=$f[shirtmXL]?>" style="width: 35px;" min=0 max=9></td><td>
XXL:<input type=number name="f[shirtmXXL]" value="<?=$f[shirtmXXL]?>" style="width: 35px;" min=0 max=9></td></tr></table>
</div>  <!-- shirts div -->
<input class=xbtn type=submit name=submit value="Update Profile">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a class="btn btn-danger" href="proflogin.php">RETURN</a>
</form>
<br><br><br>
</body>
</html>