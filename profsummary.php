<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';

$profile = $_SESSION['profname'];

$sql = "
SELECT `regeventlog`.*, `events`.`RowID`, `events`.`Trip` ,`events`.`Event` , `events`.`StartTime`, `events`.`EndTime`,`events`.`FEE`, `events`.`Day` FROM `regeventlog`, `events` 
WHERE `regeventlog`.`EvtRowID` = `events`.`RowID` 
  AND `regeventlog`.`ProfName` = '$profile'
  AND `events`.`TripStatus` = 'Retain' 
ORDER BY `Trip` ASC;";

//echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
if ($rc == 0) {
  $tbod = "<h3>No events are scheduled for profile $profile.</h3><br><br>
  <p>The profile itself may be reviewed and/or updated using the <b>&apos;<a href='http://localhost/www/dev/mbbfreg/profnew.php?action=update'>Upd Profile</a>&apos;</b> button.</p>";
  }
else {
while ($r = $res->fetch_assoc()) {
  $agendas[$r[AgendaName]][$r[EvtRowID]] = $r;
  }
// echo "<pre> agenda "; print_r($agendas); echo '</pre>';


foreach ($agendas as $k => $v) {
  $tbod .= "<h3>Agenda: $k</h3>";
  $tbod .= "<ul><table class=table>";
  $tbod .= '<thead><tr><th>EvtRID</th><th>ST</th><th>EvtNbr</th><th>Event Title</th><th>Day</th><th>Start</th><th>End</th><th>Fee</th><th></th></tr></thead><tbody>';
  foreach ($v as $kk => $vv) {
    $st = substr($vv[StartTime],0,5); $et = substr($vv[EndTime],0,5);
    $stat = 'OK'; 
    if ($vv[RecKey] == 'EvtWL') $stat = 'WL';
    if ($vv[RecKey] == 'EvtAO') $stat = 'AO';
    $fee = number_format($vv[FEE],2);
    $tbod .= "<tr><td class=RID>$vv[EvtRowID]</td><td>$stat</td><td>$vv[Trip]</td><td class=ED>$vv[Event]</td><td>$vv[Day]</td><td>$st</td><td>$et</td><td align=right>$fee</td></tr>";
    }
  $tbod .= "</tbody></table></ul>";
  }
}
// echo "$tbod";

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile Summary</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

<style>
  p, th, td, select { font-size: 1.25em; }
  input[type=checkbox] { transform: scale(1.5); }
.ED { 
  color: blue; 
  /* font-weight: bold; */ 
  /* text-decoration: underline; */ 
  cursor: pointer;  
  }
</style> 

</head>
<body>
<script>
$(function() {
  //alert("doc load");
  $('td:nth-child(1),th:nth-child(1)').hide();  // hide first col
$(".ED").click(function() {
  // alert("desc clicked");
  var rid = $(this).parent().find("td.RID").text(); // read RID
  // console.log("desc RID: "+rid);
  $.post("registerJSONeventdescription.php",
      {
      rid: rid
      },
    function(data, status) {
      // alert("response: "+data);
      $("#msgdialogtitle").html("<h3 style='color: red;'>Event Description</h3>");
      var b = data.substring(4);
      $("#msgdialogcontent").html(b);
      $('#msgdialog').modal('toggle', { keyboard: true });
      return;
  });
  
});
  
});
</script>
<h1>Profile Event Summary: <?=$profile?>&nbsp;&nbsp;
<a href="proflogin.php" class="hidden-print btn btn-primary btn-lg">D O N E</a></h1>
<?=$tbod?>

</body>
</html>