<?php session_start(); 
date_default_timezone_set('America/Los_Angeles');?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; -->
<!-- any other head content must come *after* these tags -->
<title>Event Update</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<link rel="stylesheet" href="css/jquery.timepicker.css" type="text/css"/>
<link rel="stylesheet" href="css/bootstrap-multiselect.css" type="text/css"/>
<link href="css/bs3dropdownsubmenus.css" rel="stylesheet">

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
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/mainmenu.inc.php';

// Process listing based on selected criteria
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "";
$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : "1";

//echo '<pre> REQUEST '; print_r($_REQUEST); echo '</pre>';
$navarray = $_SESSION['navarray'];  // array of record numbers from last search
$nav = $_SESSION['nav'];            // array first, prev, curr, next and last
$ptr = $_REQUEST['ptr'];            // index of record number array 

//echo '<pre> navarray '; print_r($navarray); echo '</pre>';
//echo '<pre> BEFORE '; print_r($nav); echo '</pre>';
$nav['curr'] = $ptr;
$nav['prev'] = $nav['curr'] - 1; if ($nav['prev'] < 0) $nav['prev'] = 0;
$nav['next'] = $nav['curr'] + 1; if ($nav['next'] > $nav['last']) 
$nav['next'] = $nav['last'];
//echo '<pre> AFTER '; print_r($nav); echo '</pre>';

// PROCESS UPDATE ACTION IF INDICATED
if ($action == 'update') {
  $flds = array();
  $flds = $_REQUEST['flds'];
  $flds[StartTime] = date("H:i:s", strtotime($flds[StartTime]));
  $flds[EndTime] = date("H:i:s", strtotime($flds[EndTime]));
  if ($flds[Day] == "Friday") $flds[Dnbr] = 1; 
  if ($flds[Day] == "Saturday") $flds[Dnbr] = 2;
  if ($flds[Day] == "Sunday") $flds[Dnbr] = 3; 
  if ($flds[Day] == "Monday") $flds[Dnbr] = 4; 
  if ($flds[TripStatus] == 'Delete') { 
    $flds[Trip] = '999';
    $flds[Leader1] = ''; $flds[Leader2] = '';
    $flds[Leader3] = ''; $flds[Leader4] = '';
    } 
// handle multiselect Event Codes field
  $codes = isset($_REQUEST['Codes']) ? $_REQUEST['Codes'] : '';
//  print_r($codes);
  if ($codes != "") $flds[Level] = implode(",", $codes);
  else $flds[Level] = "";
// echo "<br>flds[Level]: ".$flds[Level]."<br>";
// handle site:sitecode split - ONLY place site portion into db field
// the SiteCode field is already initialized
  if (isset($flds[Site])) {
    list($s, $sc) = explode(':',$flds[Site]); 
    $flds[Site] = $s;
    }
	$rowid = $flds[RowID]; unset($flds[RowID]);
  sqlupdate('events', $flds, '`RowID` = "'.$rowid.'";');

  echo '
<script>
$(document).ready(function() {
  $("#X").fadeOut(2000);
});
</script>
<h3 style="color: red; " id="X">Update Completed.</h3>
'; 
  }   // END UPDATE ACTION 

// ----------------- display event info --------------------- 
$rowid = $navarray[$ptr];  
//echo "ptr: $ptr, rowid: $rowid<br>";
$sql = 'SELECT * FROM `events` WHERE `RowID` = "'.$rowid.'";';

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

$r = $res->fetch_assoc();
//echo '<pre> full record '; print_r($r); echo '</pre>';
// set up Site field for multiselect initialization
if ($r[Site] != '') {
  $r[Site] = $r[Site] . ':' . $r[SiteCode];
}
echo '
<table border="0" class="hidden-print table table-condensed">
<tr>
<td width="33%" valign="top">
<h2>Event Update</h2></td>
<td align="center"><br>
<a class="clk" href="evtupdateevent.php?ptr='.$nav['start'].'"><span title="START" class="glyphicon glyphicon-fast-backward" style="color: blue; font-size: 20px;"></span></a>&nbsp;&nbsp;
<a class="clk" href="evtupdateevent.php?ptr='.$nav['prev'].'"><span title="PREV" class="glyphicon glyphicon-step-backward" style="color: blue; font-size: 20px;"></span></a>&nbsp;&nbsp;
<a href="evtlister.php" class="clk btn btn-primary">SEARCH</a>&nbsp;&nbsp;
<a class="clk" href="evtupdateevent.php?ptr='.$nav['next'].'"><span title="NEXT" class="glyphicon glyphicon-step-forward" style="color: blue; font-size: 20px;"></span></a>&nbsp;&nbsp;
<a class="clk" href="evtupdateevent.php?ptr='.$nav['last'].'"><span title="LAST" class="glyphicon glyphicon-fast-forward" style="color: blue; font-size: 20px;"></span></a><br>
</td>
<script>
function confirmContinue() {
	var r=confirm("This action cannot be reversed.\\n\\nConfirm this action by clicking OK or CANCEL"); 
	if (r==true) { return true; }
	return false;
	}
</script>

<td width="33%" align="right" valign="center">
<br>
<a class="clk" onclick="return confirmContinue()" href="evtlister.php?rowid='.$r[RowID].'&action=delete"><span title="Delete THIS Event" class="glyphicon glyphicon-trash" style="color: blue; font-size: 30px;"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a class="clk" href="evtduplicateevent.php?rowid='.$r[RowID].'"><span title="Duplicate THIS Event" class="glyphicon glyphicon-duplicate" style="color: blue; font-size: 30px;"></span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;
<a class="clk" href="evtaddevent.php"><span title="Add NEW Event" class="glyphicon glyphicon-plus" style="color: blue; font-size: 30px"></span></a>

</td></tr></table>
';

echo '
<script>
function validate() {
  var error = "";
  
  var tp1 = Date.parse("1/1/2016 "+$("#StartTime").val());
  var tp2 = Date.parse("1/1/2016 "+$("#EndTime").val());
  // console.log("starttime: "+tp1+", endtime: "+tp2);
  if (tp1 >= tp2) {
    error += "End time is before or same as start time\\n";
    }

  var d = $("#Trip").val();
  if (d.length < 3) {
    error += "New Trip number must be at least 3 digits.\\n";
    }
    
  if (error.length > 0) {
    alert("Please correct the following:\\n\\n"+error);
    return false;
    }
  var v = new String($("#Program").val());
  v = v.replace(/\<|\>/g, "");
  $("#Program").val(v);
  var v = new String($("#Event").val());
  v = v.replace(/\<|\>/g, "");
  $("#Event").val(v);
  return true;
  }
</script>';

// SELECT FIELD ON-LOAD SETUPS
echo '
<script>
$(document).ready(function() {
  $("#Day").val("'.$r[Day].'");
  $("#Type").val("'.$r[Type].'");
  $("#TripStatus").val("'.$r[TripStatus].'");
  $("#Transportation").val("'.$r[Transportation].'");
  $("#TransportNeeded").val("'.$r[TransportNeeded].'");
  $("#FeeRequired").val("'.$r[FeeRequired].'");
  $("#MultiEvent").val("'.$r[MultiEvent].'");
  $("#TypeOfEvent").val("'.$r[TypeOfEvent].'");
  $("#SC").text("'.$r[SiteCode].'");
  $("#Site").val("'.$r[Site].'");
  $("#SiteCode").val("'.$r[SiteCode].'");  
});
</script>';

$ldrlist = setupta();     // set up type ahead for lead name input fields
//echo "ldrlist: $ldrlist<br>";

// FORM FIELD DEF's
$t = sprintf("%03s",$r[Trip]);
$diff = timediff($r[StartTime],$r[EndTime]);
$stime = ($r[StartTime] != '') ? date("g:i A", strtotime($r[StartTime])) : ''; 
$etime = ($r[EndTime]   != '') ? date("g:i A", strtotime($r[EndTime])) : '';
// set up multi select init string for Level code field
// echo '<pre> codes '; print_r($codes); echo '</pre>';
if ($r[Level] != "") {
  $valarray = explode(',',$r[Level]);
  $vals = "['" . implode("','", $valarray) . "']";
  }
else $vals = "[]";
// echo "<br>vals: $vals<br>";

echo '
<button form="F1" class="btn btn-success hidden-print" type="submit">APPLY UPDATES TO EVENT: </button>&nbsp;<font size="+2">'.$r[Event].'</font>

<form id="F1" action="evtupdateevent.php" method="post" onsubmit="return validate()">
<table border="0">
<input type="hidden" name="flds[RowID]" value="'.$r[RowID].'">
<tr><td>
Trip Number: 
<input autofocus type="text" name="flds[Trip]" value="'.$t.'" size="5" id="Trip">
</td><td>
Day: 
<select id="Day" name="flds[Day]">';
echo readlist('Day');
echo '</select>
</td><td>
Trip Status: 
<select id="TripStatus" name="flds[TripStatus]">';
echo readlist('TripStatus');
echo '</select>
</td></tr>
<tr><td>
Start Time: 
<input type="text" name="flds[StartTime]" value="'.$stime.'" size="15" class="tpick" id="StartTime">
</td><td>
End Time: 
<input type="text" name="flds[EndTime]" value="'.$etime.'" size="15" class="tpick" id="EndTime">
</td><td>
Duration: <span id="DUR">'.$diff.'</span>
</td></tr>
<tr><td colspan="3">
Event Name: 
<input type="text" name="flds[Event]" value="'.$r[Event].'" size="60" id="Event">
</td>
</tr>
<tr>
<td>
Trip Type:
<select id="Type" name="flds[Type]">';
echo readlist('TripType');
echo '</select>
</td>
<td>
Event Type: 
<select id="TypeOfEvent" name="flds[TypeOfEvent]">';
echo readlist('TypeOfEvent');
echo '</select>
</td>
<script type="text/javascript">
$(document).ready(function () {

  var initValues = '.$vals.';
  $("#Codes").val(initValues);
  $("#Codes").multiselect({
    numberDisplayed: 5,
    delimiterText: ",",
    nonSelectedText: "None Selected"
    });
  $("#Codes").multiselect("refresh");

});
</script>
<td>
Event Level: 
<select id="Codes" name="Codes[]" multiple>';
echo readlist('EventLevels');
echo '</select>
</td></tr>
<script>
// get site code from Site drop down list and update site code fields
$(document).ready(function() {
  $("#Site").change(function() {
    var x = $("#Site").val();
    var parts = x.split(":");
    $("#SC").text(parts[1]);
    $("#SiteCode").val(parts[1]);
  });
});
</script>
<tr><td>
Site:
<select id="Site" name="flds[Site]">';
echo readlist('Site');
echo '</select>
</td>
<td>
Site Code: <span id="SC"></span>
<input id="SiteCode" type="hidden" name="flds[SiteCode]" value="">
</td>
<td> 
Site Room: 
<input id="SiteRoom" type="text" name="flds[SiteRoom]" value="'.$r[SiteRoom].'">
</td>
</tr>
<tr>
<td valign="top">Site Address or Directions:</td>
<td id="sa" colspan="2">
<textarea name="flds[SiteAddr]" cols="50"  colid="SiteAddr">'.$r[SiteAddr].'</textarea>
</td>
</tr>
</table>

<table border="0">
<tr><td>
Leader 1: 
<input  class="LDR" data-provide="typeahead" id="Leader1" type="text" name="flds[Leader1]" value="'.$r[Leader1].'">
</td><td colspan="2">
Leader 2: 
<input class="LDR" data-provide="typeahead" id="Leader2" type="text" name="flds[Leader2]" value="'.$r[Leader2].'">
</td></tr>
<tr><td>
Leader 3: 
<input class="LDR" data-provide="typeahead" id="Leader3" type="text" name="flds[Leader3]" value="'.$r[Leader3].'">
</td><td>
Leader 4: 
<input class="LDR" data-provide="typeahead" id="Leader4" type="text" name="flds[Leader4]" value="'.$r[Leader4].'">
</td></tr>
</table>
<table border="0">
<tr><td>
Fee Required(Y/N):
<select id="FeeRequired" name="flds[FeeRequired]">
<option value=""></option><option value="Yes">Yes</option><option value="No">No</option>
</select>
</td><td colspan="2">
FEE: 
<input id="FEE" type="text" name="flds[FEE]" value="'.$r[FEE].'" size="6" ><br>
</td></tr>
<tr><td>
Transport Needed(Y/N): 
<select id="TransportNeeded" name="flds[TransportNeeded]">
<option value=""></option><option value="Yes">Yes</option><option value="No">No</option>
</select>
</td><td colspan="2">
Transportation:
<select id="Transportation" name="flds[Transportation]">';
echo readlist('Transportation');
echo '</select><br>
</td></tr>
<tr><td>
Maximum Attendees: 
<input type="text" name="flds[MaxAttendees]" value="'.$r[MaxAttendees].'" size="5" id="MaxAttendees">
</td><td>
Multi-Event(Y/N): 
<select id="MultiEvent" name="flds[MultiEvent]">
<option value=""></option><option value="Yes">Yes</option><option value="No">No</option>
</select>
</td><td>
Multi Event Code(s): 
<input id="MultiEventCode" type="text" name="flds[MultiEventCode]" value="'.$r[MultiEventCode].'">
</td></tr>
<tr><td>
</table>
<table>
<tr><td>
Program Description: <br>
<textarea id="Program" name="flds[Program]" rows="10" cols="40">'.$r[Program].'</textarea>
</td><td valign="top">
Secondary Status (Production Notes):<br>
<textarea id="SecondaryStatus" name="flds[SecondaryStatus]" rows="10" cols="40">'.$r[SecondaryStatus].'</textarea>
</td></tr>
<tr><td align="center" colspan="3">
<button form="F1" class="btn btn-success hidden-print" type="submit">APPLY UPDATES</button>
</td></tr></table>

';
// HIDDEN FORM FIELDS
echo '
<input type="hidden" name="action" value="update">
<input type="hidden" name="ptr" value="'.$ptr.'">
</form>
</div> <!-- container -->
';
?>

<script type="text/javascript" src="js/jquery.timepicker.js"></script>
<script>
$(document).ready(function(){
    $("input.tpick").timepicker({ 
    timeFormat: "h:mm p",
    dynamic:    true,
    startTime:  "7:00 a",
    minTime:    "6:00 a",
    maxTime:    "6:00 p",
    interval:   15,
    scrollbar:  true
    });
});
</script>

<script src="js/bootstrap3-typeahead.js"></script>
<script>
 var ldrs = <?=$ldrlist?>; 
  $("input.LDR").typeahead({source: ldrs});
</script>
</body>
</html>

<?php
// set up type ahead for leader input fields
function setupta() {
  $sql = "SELECT `FirstName`,`LastName` from `leaders` 
  WHERE `Active` = 'YES'
  ORDER BY `LastName` ASC;";
$res = doSQLsubmitted($sql);
$rowcount = $res->num_rows;
// echo "rowcount: $rowcount<br>";
if ($res->num_rows == 0) {
	echo '<h2>No leaders exists to populate the typeahead fields.</h2>
	<br><br>';
	echo '<a class="btn btn-danger" href="evtlister.php">RETURN</a></body></html>';
	exit;
	}
// now create the string for the javascript arrays to download
$ldrs = '[';		// create string for form typeahead
while ($r = $res->fetch_assoc()) {
	$ldrfn = preg_replace("/[\(\)\.\-\ \/\&]/i", "", $r[FirstName]);
	$ldrln = preg_replace("/[\(\)\.\-\ \/\'\&]/i", "", $r[LastName]);
	$ldrs .= "'$ldrfn $ldrln',";
	}
$ldrs = rtrim($ldrs,',') . ']';
return($ldrs);

}

function timediff($start, $end) {
  $tp1val = strtotime($start);
  $tp2val = strtotime($end);
  $diff = $tp2val - $tp1val;
  $hrs = sprintf("%s", floor($diff/3600));   // diff in hours
  $mins = (($tp2val - $tp1val) - ($hrs * (60*60)))/60;   // diff in min
  if ($mins == 0) $fmtdiff = sprintf("%2d Hour(s)", $hrs); 
  else $fmtdiff = sprintf("%2d Hour(s) %2d Min", $hrs, $mins);
  return($fmtdiff);
  }
?>