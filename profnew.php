<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$id = isset($_REQUEST['profname']) ? $_REQUEST['profname'] : $_SESSION['profname'];
$f = isset($_REQUEST['f']) ? $_REQUEST['f'] : array();
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

$_SESSION['profname'] = $id;  // set profile name for session

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

// used when called by the register Upd Profile button 
// or index page's "access existing profile" button
if ($action == 'update') {
  // read profile for current values
  $f = array();  
  // echo "id: $id<br>";
  $res = doSQLsubmitted("SELECT * FROM `regprofile` WHERE `ProfileID` = '$id';");
  $f = $res->fetch_assoc();
//  echo '<pre> update '; print_r($f); echo '</pre>';
  $action = 'update';   // set action for form
  // echo "set action: $action in profnew request = update <br>";
  }

// triggered by create new button in index.php
if ($action == 'new') {
  unset($_SESSION['profname']);
  // DB will reject entry of a duplicate profile id
  $addarray['ProfileID'] = $id;
  $addarray['Exempt'] = 'NO';
  $addarray['regType'] = 'full';
  $f[regType] = 'full';
  $stat = sqlinsert("regprofile", $addarray); // register new profile
  if ($stat < 0) {
    echo '<br><br>
    <h3><a href="index.php" class="btn btn-danger">Try again.</a></h3>';
    exit;
    }
  // create initial agenda for SELF (assume full festival registration)
  $agarray['RecKey'] = 'Reg';
  $agarray['ProfName'] = $id;
  $agarray['AgendaName'] = 'SELF';
  $agarray['FEE'] = $feesched[RegFull];
  sqlinsert('regeventlog', $agarray);
  $action = 'update';   // set action for form because we just added it!
  // echo "set action: $action in profnew request = new";
  $_SESSION['profname'] = $id;
  }

// setup first time flag if update and newxmpt 
$ftflag = isset($_REQUEST['newxmpt']) ? 'new' : 'not'; 

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

<img src="http://morrobaybirdfestival.net/wp-content/uploads/2016/08/LOGO3.png" alt="bird festival logo" >
<h2>Profile: <?=$id?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class="btn btn-primary btn-lg" href="proflogin.php">D O N E</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="register.php" class="btn btn-success">Sched Events</a>
</h2>
<script>
$(function() {
  // alert("doc load");
  var ftflag = "<?=$ftflag?>";        // firt time flag for new or reset profile
  var exempt = "<?=$f[Exempt]?>";     // exemption flag
  var regtype = "<?=$f[regType]?>";
  if (regtype == 'full') $("#regdone").text('Full Festival Registration');
  if (regtype != 'full') $("#regdone").text(regtype+' Only');
  if (ftflag == 'new') { 
    $("#REGONE").show(); $("#REGDONE").hide(); 
    $("#EXPONE").show(); $("#EXPDONE").hide(); 
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

// exemption checked/unchecked 
$("#EXEMPT").click(function() {
  // alert("exemption check box changed");
  var exempt = "<?=$f[Exempt]?>";    // exemption flag
  if (exempt == '') exempt = 'NO';
  if ($("#EXEMPT").is(":checked")) {
    // alert("box checked");
    $("#regdd").val('full');
    $("#REGONE").hide();
    }
  else {
    // alert ("box unchecked");
    $("#REGONE").show();
    }
});

$("#pform").submit(function(event){
  // alert("form submitted validations");
  var reason = "";
	reason += validateUserfname($("#FN").val());
	reason += validateUserlname($("#LN").val());
	reason += validatePhone($("#PN").val());
	if (reason.length != 0) {
	  event.preventDefault();
  	// alert("Error: "+reason);
  	$("#msgdialogtitle").html("<h3 style='color: red;'>Field validation errors </h3>"); 
    $("#msgdialogcontent").html('<p>The following error(s) were detected:</p><p><ol>'+reason+'</ol></p>');
    $('#msgdialog').modal('toggle', { keyboard: true });
  	return;
		}
  });

});
</script>
<script>
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
  var st = fld.replace(/[\(\)\.\-\ ]/g, '');    
  if (fld.length == 0) {
  	error = "<li>You didn't enter a phone number.</li>";
    } 
  else if (isNaN(parseInt(st))) {
    error = "<li>The phone number contains illegal characters.</li>";
    } 
  else if (st.length != 10) {
    error = "<li>The phone number be 10 digits. Make sure you included an area code.</li>";
    }
  if (error.length > 0) return error;
  var pn = st.substring(0,3)+'-'+st.substring(3,6)+'-'+st.substring(6);
  $("#PN").val(pn);
  return "";  
	}

</script>
<form id="pform" action="proflogin.php" method="post">
<table class=table>
<input type=hidden name=f[ProfileID] type=text value="<?=$id?>";
<tr><td>First name*: <input  id=FN title="Required entry" name=f[ProfFirstName] type=text value="<?=$f[ProfFirstName]?>">
Last name*: <input  id=LN title="Required entry" name=f[ProfLastName] type=text value="<?=$f[ProfLastName]?>">
</td></tr>
<tr><td>Mailing address: <input name=f[ProfAddress] type=text value="<?=$f[ProfAddress]?>"></td></tr>
<tr><td>City <input name=f[ProfCity] type=text value="<?=$f[ProfCity]?>">
ST <input name=f[ProfState] type=text value="<?=$f[ProfState]?>">
ZIP <input name=f[ProfZip] type=number value="<?=$f[ProfZip]?>" style="width: 75px;" min=0 max=99999></td></tr>
<tr><td>Contact phone number*: <input id=PN title="Required entry" name=f[ProfContactNumber] type=tel value="<?=$f[ProfContactNumber]?>"></td></tr></table>
<input type=hidden name=action value="<?=$action?>">

<div id=EXPONE>
Do you qualify as a volunteer, leader or speaker for a frestival fees exemption?&nbsp;YES: &nbsp;
  <input type=checkbox id=EXEMPT name=f[Exempt] value=YES><br>
NOTE 1: the exeption is requested when initially registering and will be applied after review and approval by the Festival Registrar.<br>
NOTE 2: exemption applied to Full Festival Registrations only.  It does not apply to any other fees and charges.
</div>  <!-- EXPONE -->
<div id=EXPDONE>
Exemption as a volunteer, leader or speaker applied for: <b><?=$f[Exempt]?></b><br>
NOTE 1: an exemption must be applied for, reviewed and approved by Festival Registrar before it will be applied and available only to an individuals.<br>
NOTE 2: You must reset the profile to change the exemption type. 
</div>  <!-- EXPDONE -->
<h3><input type="submit" name="submit" value="Update Profile"></h3>

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
NOTE 2: You must reset the profile to change the registration type.
</div>  <!-- REGDONE -->

<hr>
<h3>Lunches</h3>
<h4>Lunch ($8.00 per person)</h4>
<table class=table><tr><td>
<b>Friday:</b> Meat <input type=number name="f[mealFrM]" value="<?=$f[mealFrM]?>" style="width: 35px;" min=0 max=9> or Veg <input type=number name="f[mealFrV]" value="<?=$f[mealFrV]?>" style="width: 35px;" min=0 max=9></td><td>
<b>Saturday:</b>  Meat <input type=number name="f[mealSaM]" value="<?=$f[mealSaM]?>" style="width: 35px;" min=0 max=9> or Veg <input type=number name="f[mealSaV]" value="<?=$f[mealSaV]?>" style="width: 35px;" min=0 max=9></td><td>
<b>Sunday:</b> Meat <input type=number name="f[mealSuM]" value="<?=$f[mealSuM]?>" style="width: 35px;" min=0 max=9> or Veg <input type=number name="f[mealSuV]" value="<?=$f[mealSuV]?>" style="width: 35px;" min=0 max=9></td><tr></table>
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
<input class=xbtn type=submit name=submit value="Update Profile">
</form>
<br>
<br><br><br>
</body>
</html>