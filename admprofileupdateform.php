<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$id = isset($_REQUEST['profname']) ? $_REQUEST['profname'] : $_SESSION['profname'];
$_SESSION['profname'] = $id;  // set profile name for session
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$f = $_REQUEST['f'];
$paylock = isset($_REQUEST["PayLock"]) ? 'Lock' : '';
$f[PayLock] = $paylock;

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

// update profile if requested
if ($action == 'update') {
  sqlupdate('regprofile', $f, "`ProfileID` = '$id';");
  // echo 'update action requested<br>';
  }
 
// read profile for current/new field values
$f = array();  
// echo "id: $id<br>";
$res = doSQLsubmitted("SELECT * FROM `regprofile` WHERE `ProfileID` = '$id';");
$f = $res->fetch_assoc();
// echo '<pre> update '; print_r($f); echo '</pre>';
$f['Exempt'] = isset($f['Exempt']) ? $f['Exempt'] : 'NO';
$exempt = $f['Exempt'];


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Attendee Invoice</title>
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<style>
  input[type=checkbox] { transform: scale(1.5); }
</style> 

</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
echo $xMsg;
?>
<h2>Attendee Profile: <?=$id?></h2>
<script>
$(function() {
  // alert("doc load");
  $("#xMsg").fadeOut(5000);
  $("#EX").val('<?=$exempt?>');
  $("#regdd").val("<?=$f[regType]?>");
  var cb = "<?=$f[PayLock]?>";
  if (cb == 'Lock') $("#CB").prop('checked', true);
  $("#VOL").val("<?=$f[Vol]?>");
  $("#SLBM").val("<?=$f[SLBM]?>");
  });
</script>
<form action="admprofileupdateform.php" method="post">
<table class=table border=1>
<input type=hidden name=f[ProfileID] type=text value="<?=$id?>";
<tr><td>First name: <input name=f[ProfFirstName] type=text value="<?=$f[ProfFirstName]?>">
Last name: <input name=f[ProfLastName] type=text value="<?=$f[ProfLastName]?>">
</td></tr>
<tr><td>Mailing address: <input name=f[ProfAddress] type=text value="<?=$f[ProfAddress]?>"></td></tr>
<tr><td>City <input name=f[ProfCity] type=text value="<?=$f[ProfCity]?>">
ST <input name=f[ProfState] type=text value="<?=$f[ProfState]?>">
ZIP <input name=f[ProfZip] type=number value="<?=$f[ProfZip]?>" style="width: 75px;" min=0 max=99999></td></tr>
<tr><td>Contact phone number: <input name=f[ProfContactNumber] type=tel value="<?=$f[ProfContactNumber]?>">
</td></tr>
<input type=hidden name=action value="update">
<tr><td><input type="submit" name="submit" value="Update Profile" class="btn btn-primary"></td></tr>
</table>
<script>
$(function() {
  $("#SLBM").change(function() {
    // alert("smbl changed");
    $("#VOL").val('NO');
    $("#EX").val('YES');
    if (($("#SLBM").val() == 'NO') && ($("#VOL").val() == 'NO'))
      $("#EX").val('NO');
    });
  $("#VOL").change(function() {
    // alert("vol changed");
    $("#SLBM").val('NO');
    $("#EX").val('YES');
    if (($("#SLBM").val() == 'NO') && ($("#VOL").val() == 'NO'))
      $("#EX").val('NO');
    });
});
</script>
<h3>Registration Info</h3>
Fees Exemption: <select id=EX name="f[Exempt]">
<option value=NO>NO</option>
<option value=YES>YES</option>
<option value=APPROVED>APPROVED</option>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;
SLBM: <select id=SLBM name="f[SLBM]" title="Speaker, Leader, Board Member?">
<option value=NO>NO</option>
<option value=YES>YES</option>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;
Volunteer:<select id=VOL name="f[Vol]" title="Volunteer?">
<option value=NO>NO</option>
<option value=YES>YES</option>
</select>
&nbsp;&nbsp;&nbsp;&nbsp;
Profile locked: <b><input id=CB title="Profile lock when payment(s) entered and blocks further changes by registrant unless unlocked by Registrar." type=checkbox name=PayLock value='Lock'></b>

<!-- <table class=table><tr><td> -->
<h3>Registration Type:
<select id=regdd name="f[regType]" class=reg>
<option value='full'>Full Festival Registration</option>
<option class=sel value=Friday>Friday only</option>
<option class=sel value=Saturday>Saturday only</option>
<option class=sel value=Sunday>Sunday only</option>
<option class=sel value=Monday>Monday only</option>
</select></h3>
<!-- </td></tr></table> -->

<h3>Lunch ($8.00 per person)</h3>
<table class=table><tr><td>
<b>Friday:</b> Meat <input type=number name="f[mealFrM]" value="<?=$f[mealFrM]?>" style="width: 35px;" min=0 max=9> or Veg <input type=number name="f[mealFrV]" value="<?=$f[mealFrV]?>" style="width: 35px;" min=0 max=9></td><td>
<b>Saturday:</b>  Meat <input type=number name="f[mealSaM]" value="<?=$f[mealSaM]?>" style="width: 35px;" min=0 max=9> or Veg <input type=number name="f[mealSaV]" value="<?=$f[mealSaV]?>" style="width: 35px;" min=0 max=9></td><td>
<b>Sunday:</b> Meat <input type=number name="f[mealSuM]" value="<?=$f[mealSuM]?>" style="width: 35px;" min=0 max=9> or Veg <input type=number name="f[mealSuV]" value="<?=$f[mealSuV]?>" style="width: 35px;" min=0 max=9></td><tr></table>

<h3>Shirt Size (full registration only)</h3>
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
<input type="submit" name="submit" value="Update Profile" class="btn btn-primary">
</form>
<br>
<br><br><br>
</body>
</html>