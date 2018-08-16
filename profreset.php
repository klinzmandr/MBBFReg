<?php 
session_start(); 
// error_reporting(E_ERROR | E_WARNING | E_PARSE); 

//include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';

// get fee roster
$feesched = readlistreturnarray('Fees');
// echo "<pre>feesched "; print_r($feesched); echo '</pre>';

$profid = $_SESSION['profname'];
$continue = isset($_REQUEST['continue']) ? 'reset' : '';
if ($continue == '') 
  $bod = "<p>This function will delete all attendee agendas and scheduled events for the profile of <b>$profid </b>.</p><p>CAUTION:  this action can NOT BE REVERSED.</p>
<a href='proflogin.php' class='btn btn-primary'>CANCEL</a></h1>
<a href='profreset.php?continue' class='btn btn-danger'>CONTINUE</a></h1>";
else {
  // delete the existing REG and EVT rec's leaving PAY ones
  $delsql = "DELETE FROM `regeventlog` WHERE `ProfName` = '$profid' AND `RecKey` <> 'Pay';";
  // echo "delsql: $delsql<br>"; 
  $res = doSQLsubmitted($delsql);
  $delrc = $res->num_rows;
  
  // read the existing profile record and replace it with an updated one
  $rdsql = "SELECT * FROM `regprofile` WHERE `ProfileID` = '$profid'";
  $rdres = doSQLsubmitted($rdsql);
  $rdr = $rdres->fetch_assoc();
  $rdr[Exempt] = 'NO';
  $rdr[regType] = 'full'; 
  $rdr[mealFrM] = $rdr[mealFrV] = $rdr[mealSaM] = $rdr[mealSaV] = 0; 
  $rdr[mealSuM] = $rdr[mealSuV] = 0;
  $rdr[shirtwS] = $rdr[shirtwM] = $rdr[shirtwL] = $rdr[shirtwXL] = 0;    
  $rdr[shirtmS] = $rdr[shirtmM] = $rdr[shirtmL] = 0;
  $rdr[shirtmXL] = $rdr[shirtmXXL] = 0; 
  
  // the profile record can now be written back
  // echo "rdsql: $rdsql<br>";
  // echo '<pre>new profile '; print_r($rdr); echo '</pre>';
  $status = sqlupdate('regprofile', $rdr, "`ProfileID` = '$profid'");
  
  // now we insert a new REG row back into the regeventlog table for this profile
  $ag['RecKey'] = 'Reg';
  $ag['ProfName'] = $profid;
  $ag['AgendaName'] = "SELF";
  $ag['FEE'] = $feesched[RegFull];
  // echo '<pre>new agenda '; print_r($ag); echo '</pre>';
  sqlinsert('regeventlog', $ag);
  
  $bod = "<p>The profile for <b>$profid</b> has been reset by deleting of all scheduled events.</p>
<p>By default, a non-exempt, FULL Festival registration has been created with NO added attendees and NO scheduled events.</p>  
  <p>Select the DONE button to display this profile and begin the registration process again.  Be sure to modify the default registration adding attendees and schedduled events if needed.</p><br>
  <a href='profnew.php?action=update&newxmpt' class='btn btn-primary'>D O N E</a></h1>";
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile Reset</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

</head>
<body>
<style>
  p, th, td, select, .btn { font-size: 1.5em; }
  tx { font-size: 1.75em; }
  input[type=checkbox] { transform: scale(1.5); }
</style> 

<div class=container>
<h1>Profile Reset for <?=$profid?></h1>&nbsp;&nbsp;&nbsp;&nbsp;
<?=$bod?>
</div> <!-- container -->
</body>
</html>