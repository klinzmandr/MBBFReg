<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MBWBF Planner</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css" rel="stylesheet"  media="all">
</head>
<body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<h2>Plan Your Trip: Search Event Listings</h2>
<?php
// error_reporting(E_ERROR | E_WARNING | E_PARSE);

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.planner.inc.php';
include 'Incls/listutils.inc.php';

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$day = isset($_REQUEST['Day']) ? $_REQUEST['Day'] : '';
$et = isset($_REQUEST['Type']) ? $_REQUEST['Type'] : '';
$ss = isset($_REQUEST['ss']) ? $_REQUEST['ss'] : '';   // event search string

$caller = $_SERVER['REQUEST_URI'];

?>

<script type="text/javascript">
// set up select lists
$(document).ready(function () { 
	//alert("first the inline function");
//	$("#SS").val("$ss");
	$("#Day").val("<?=$day?>");
	$("#Type").val("<?=$et?>");
	
$(".mod").click(function(){
  var ldrname = $(this).text();
  ldrname = ldrname.replace(/[,\s]/g, "");
  // alert("Modal button clicked: " + ldrname);
  $.post("plannerldrjson.php",
    {
        name: ldrname
    },
    function(data, status){
      // alert("Data: " + data + "\\nStatus: " + status);
      $("#title").html("Leader Information");
      $("#content").html(data); 
      $('#ldrModal').modal('toggle', { keyboard: true });
    });  // end $.post logic
  });

$(".ven").click(function() {
  var venname = $(this).text();
  // alert ("venue link clicked: "+ venname);
  $.post("plannervenjson.php",
    {
        name: venname
    },
    function(data, status){
      // alert("Data: " + data + "\\nStatus: " + status);
      $("#title").html("Venue Information");
      $("#content").html(data); 
      $('#ldrModal').modal('toggle', { keyboard: true });
    });  // end $.post logic
  }); 
});

</script>
<script type="text/javascript">
function resetflds() { 
	$(":input").val("");
	return false;
}
</script>

<h3>Select one or more selection criteria and continue:</h3> 
<form id="f1" action="<?=$caller?>" method="post">
<select id="Day" name="Day">
<?php echo readlist('Day'); ?>
</select>&nbsp;
<select id="Type" name="Type">
<?php echo readlist('TripType'); ?>
</select>
<input id="SS" type=text value="<?=$ss?>" name="ss" placeholder="Search" title="Enter a single word or short character string to search all program descriptions.">&nbsp;
<input type=hidden name=action value="list">

<button class="btn btn-primary" type="submit" form="f1">SEARCH EVENTS</button>
<button class="btn btn-warning" onclick="return resetflds()">Clear Form</button>
</form>

<?php
// Process listing based on selected criteria
$sql = '
SELECT * FROM `events` 
WHERE `TripStatus` NOT LIKE "Delete" AND `TripStatus` = "Retain" AND ';
$sqllen = strlen($sql);
if (strlen($day) > 0) { 
  $sql .= '`Day` LIKE "%'.$day.'%" AND '; }
if (strlen($et) > 0) {
  $sql .= '`Type` LIKE "%'.$et.'%" AND '; }
if (strlen($ss) > 0) {
  $sql .= '
    (`Program` LIKE "%'.$ss.'%" 
    OR `Type` LIKE "%'.$ss.'%" 
    OR `Event` LIKE "%'.$ss.'%" 
    OR `Trip` LIKE "%'.$ss.'%" 
    OR `Leader1` LIKE "%'.$ss.'%" 
    OR `Leader2` LIKE "%'.$ss.'%"
    OR `Leader3` LIKE "%'.$ss.'%"
    OR `Leader4` LIKE "%'.$ss.'%") AND '; }

if (strlen($sql) == $sqllen) {      // no criteria entered
  echo '
<h4 style="color: red; ">Please provide criteria for search.</h4>
</div> <!-- contianer -->
</body>
</html>';
  exit;
  }

$sql = substr($sql,0,-5);       // trip trailing 5 char's
$sql .= ' ORDER BY `Dnbr` ASC, `StartTime` ASC, `EndTime` ASC';
$sql .= ';';

// echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;

echo '<h3>Events meeting selected criteria</h3>
<p>Events selected: '.$rc.'.  Click on the title for more details regarding that event.</p>
<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

<style>
.default {
  cursor: default;
  }
.mod, .ven { 
  color: blue; 
  font-weight: 
  bold; text-decoration: 
  underline; 
  cursor: pointer;  
  }
</style>';

$rarray = array(); 
while ($r = $res->fetch_assoc()) {
  $rarray[$r[RowID]] = $r;
  }
// echo '<pre> results '; print_r($rarray); echo '</pre>';
// echo '<pre> leader '; print_r($ldrarray); echo '</pre>';

foreach ($rarray as $k => $r) {
//  echo '<pre> full record '; print_r($r); echo '</pre>';
  $ldrstr = !empty($r[Leader1]) ? "<b><span class=mod>$r[Leader1]</span></b>" : '';
  $ldrstr .= !empty($r[Leader2]) ? ", <b><span class=mod>$r[Leader2]</span></b>" : '';
  $ldrstr .= !empty($r[Leader3]) ? ", <b><span class=mod>$r[Leader3]</span></b>" : '';
  $ldrstr .= !empty($r[Leader4]) ? ", <b><span class=mod>$r[Leader4]</span></b>" : '';
// echo '<pre> leaders '; echo $ldrs; echo '</pre>';
  if ($r[FEE] == '') $r[FEE] = 'No Charge';
  else $r[FEE] = '$'.$r[FEE];  
  $r[StartTime] = date("g:i A", strtotime($r[StartTime]));
  $r[EndTime] = date("g:i A", strtotime($r[EndTime]));
  echo '
<div class="panel panel-default">
<div class="panel-heading" role="tab" id="heading'.$r[RowID].'">
<h4 class="panel-title">
<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$r[RowID].'" aria-expanded="false" aria-controls="collapse'.$r[RowID].'">
  Event '.$r[Trip].' '.$r[Event].'
</a>
</h4>
</div> <!-- panel-eading -->
<div id="collapse'.$r[RowID].'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading'.$r[RowID].'">
<div class="panel-body">
<table>
<tr>
<td>Event Type: '.$r[Type].'</td>
<td>Event Day: '.$r[Day].'</td>
<td>Event Hours: '.$r[StartTime].' to '.$r[EndTime].'</td>
</tr>
<tr>
<td>Guide/Speaker: '.
$ldrstr
.'</td>
<td>FEE: '.$r[FEE].'</td>
<td>Site: <b><span class=ven>'.$r[Site].'</span></b></td>
</tr>
<tr><td colspan=3 border=1>'.$r[Program].'</td></tr>
</table>
</div> <!-- panel-body -->
</div> <!-- panel-collapse collapse -->
</div> <!-- panel panel-default -->
';
}

echo '
</div> <!-- panel panel-default -->
';
?>

<div class="modal fade" id="ldrModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h4 id=title class="modal-title" id="myModalLabel"></h4>
</div>  <!-- modal header -->
<div class="modal-body">
<div id="content" style="overflow-y:scroll; height:400px;">
Test content.
</div>
</div>  <!-- modal body -->
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">CLOSE</button>
</div>  <!-- modal footer -->
</div><!-- modal-content -->
</div><!-- modal-dialog -->
</div><!-- modal -->
<!-- end of modal -->

</div> <!-- panel-group -->
</body>
</html>
