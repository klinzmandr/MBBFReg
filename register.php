<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$id = $_SESSION['profname'];
// $selstring = isset($_SESSION['selstring']) ? $_SESSION['selstring'] : '';
$day = isset($_REQUEST['day']) ? $_REQUEST['day'] : '';

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';
include 'Incls/checkcred.inc.php';

// check and setup event registration start and end
$start = strtotime(getregstart());  $end = strtotime(getregend()); 
$sd = date("M d, Y", $start); $ed = date("M d, Y", $end);
$evtyr = date("Y", strtotime(geteventstart()));
$today = strtotime("now");
// echo "today: start: $sd, end: $ed<br>";
// echo "today: $today, start: $start, end: $end<br>";
$OKFlag = 'OFF'; 
if (($today >= $start) AND ($today <= $end)) {
  $OKFlag = 'ON';
  // echo "Date range check passed<br>";
  }
// echo "OKFlag: $OKFlag<br>";

// read profile to get partial frestival day, if any
$profres = doSQLsubmitted("Select `regType` FROM `regprofile` WHERE `ProfileID` = '$id';");
$profile = $profres->fetch_assoc();
// echo '<pre>profile >'; print_r($profile); echo '</pre>';
$regType = $profile['regType']; 
// echo "regType: $regType<br>";

// create the day drop down selections  
// create single day agenda for partial and exempt profiles
if ($regType != 'full') {  
  $selstring = "<option class=SL value=$regType>$regType</option>";
  } 
// else get day names from config list if full registration
else {
  $selstring = readlist('Day');
  }

// create the agenda drop down
$sql  = "
SELECT DISTINCT `AgendaName` FROM `regeventlog` WHERE `RecKey` = 'Reg' AND `ProfName` = '$id';
";
$agendastr = '';
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
while ($r = $res->fetch_assoc()) {
  // echo "<pre>rc: $rc agenda "; print_r($r); echo '</pre>';
  $agendastr .= "<option value='$r[AgendaName]'>$r[AgendaName]</option>";
  }
$_SESSION['agendastr'] = $agendastr;
// echo '<pre> agendastr '; print_r(htmlentities($agendastr)); echo '</pre>';

// get list of approved events for given day
$sql = "
SELECT * FROM `events` 
WHERE `Day` = '$day' AND `TripStatus` = 'Retain'  
ORDER BY `RowID` ASC";
// echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$tbl = '';
while ($r = $res->fetch_assoc()) {
  $resarray[$r[RowID]] = $r;
  $tbl .= "<tr><td><input type=checkbox></td><td>$r[RowID]</td><td>$r[Trip]</td><td>$r[Event]</td><td>".substr($r[StartTime],0,5)."</td><td>".substr($r[EndTime],0,5)."</td></tr>";
  }
// echo '<pre> resarray '; print_r($resarray); echo '</pre>';

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Schedule</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>

<style>
  form { display: inline; }
  p, th, td, select, button { font-size: 1.5em; }
  input[type=checkbox] { transform: scale(1.5); }
.default {
  cursor: default;
  }
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
  // alert("page load event");
  $('#DAY').val('Friday');
// check for start and end time to allow registration
  var regOK = "<?=$OKFlag?>";
  var sd = "<?=$sd?>"; var ed = "<?=$ed?>"; var yr = "<?=$evtyr?>";
  // console.log("sd: "+sd+", ed: "+ed+", yr: "+yr);
  if (regOK == "OFF") {
    $("#msgdialogtitle").html("<h3 style='color: red;'>On-line Event Registration Not Available</h3>");
    $("#msgdialogcontent").html("<p>Registration for events for the "+yr+"  Bird Festival is not yet open.</p><p>On-line regisration is available between the dates of "+sd+" and "+ed+".  Please check back then.</p>");
    $('#msgdialog').modal('toggle', { keyboard: true });
    // window.location.href = "proflogin.php";
    }

// initialize page buttons on document load  
  // $('td:nth-child(2),th:nth-child(2)').hide();  // hide second col
  $("#DONE").hide();
  showselectedevents();
});
</script>
<script>
function showselectedevents() {
  // alert ("show selected agenda events for a day");
  var a = $("#SEL").val();
  var d = $("#DAY").val();
  // console.log("Selected for Day: "+d+", Agenda: "+a);
  $.post("registerJSONshowselected.php",
    {
    agenda: a,
    day: d
    },
    function(data, status){
      $("#TB").html(data);
      $('td:nth-child(1),th:nth-child(1)').hide();  // hide first col
      $('td:nth-child(2),th:nth-child(2)').hide();  // hide second col
      // alert("Data: " + data + "\nStatus: " + status);
      }
    );  // end $.post logic 
}
</script>
<script>
function showallevents() {
  // alert("list all events for the selected day");
  var a = $("#SEL").val();
  var d = $("#DAY").val();
  // console.log("ALL for Day: "+d+", Agenda: "+a);
  $.post("registerJSONshowall.php",
    {
    agenda: a,
    day: d
    },
    function(data, status){
      $("#TB").html(data);
      // console.log("Data: "+data);
      $('th:nth-child(1)').show();                  // show check box col
      $('td:nth-child(2),th:nth-child(2)').hide();  // hide rowid col
      // alert("Data: " + data + "\nStatus: " + status);
      }
    );  // end $.post logic 

} 
</script>
<script>
// following are event handlers
$(function() {
$("#DAY").change(function() {
  // alert("Day selection changed");
  $("#ADD").show();
  $("#DONE").hide();
  showselectedevents();
  });

$("#ADD").click(function() {
  // alert("add event to agenda button clicked");
  $("#ADD").hide();
  $("#DONE").show();
  showallevents(); 
});

$("#DONE").click(function() {
  // alert("event add DONE clicked");
  $("#ADD").show();
  $("#DONE").hide();  
  showselectedevents();
});

$("#SEL").change(function() {
  // alert("event agenda changed");
  $("#ADD").show();
  $("#DONE").hide();  
  showselectedevents();
});

// bind click of event description to dynamic rows in table
$('tbody').on('click', '.ED', function(){
  var rid = $(this).parent().find("td.RID").text(); // read RID
  // console.log("desc RID: "+rid);
  // alert ("description clicked, RID: "+rid);
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

// bind click event of check box to dynamic rows in table
$('tbody').on('click', ':checkbox', function(){
  // alert ("checkbox event entered");
  // <tr><td>checkbox</td><td>RID</td> .......
  // $(this) is the input checkbox
  // $(this).parent() is the td parent
  // $(this).parent().next() is the following td containing the RID
  // $(this).parent().parent() is the tr parent
  var a = $("#SEL").val();  // agenda
  var d = $("#DAY").val();  // day
  var cb = $(this);   // checkbox 
  // var rid = $(this).parent().next().text(); // read RID
  var rid = $(this).parent().parent().find("td.RID").text(); // read RID
  // console.log("cb RID: "+rid);
  var td = $(this).parent();
  if ($(this).prop("checked")) {
    var fee = $(this).parent().parent().find("td:last").text();
    // console.log(fee);
    $.post("registerJSONeventadd.php",
      {
      agenda: a,
      day: d,
      rid: rid,
      fee: fee
      },
    function(data, status) {
      // alert("response: "+data);
      if (data.includes('OK')) {
        td.next().next().text('OK');
        // console.log(td.next().next().text('OK'));
        // alert("OK click returned:" + data);
        return;
        }
      if (data.includes('WL')) {
        // alert('WL returned: ' + data);
        td.next().next().text('WL');
        $("#msgdialogtitle").html("<h3>Event Capacity Exceeded</h3>"); 
        $("#msgdialogcontent").html("<p>The maximum capacity for the selected event has been exceeded.</p><p>An event is &quot;Wait Listed&quot; (indicated by a status code of &quot;WL&quot; in the status column) when the maximum capacity of the requested event has been reached.</p><p>A future request to register will be fulfilled if there is capacity available and there is still interest in the event.</p><p>To re-check if a wait listed event is available merely un-check the event and re-select it. If capacity is available, the status will change to &quot;OK&quot;. Otherwise, it will revert to &quot;WL&quot;.</p>"); 
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      if (data.includes('TE')) {
        // alert('TE returned: ' + data.substring(3));
        cb.prop("checked", false);
        $("#msgdialogtitle").html("<h3>Event Time Conflict</h3>"); 
        $("#msgdialogcontent").html("<p>The selected event has a time conflict with another event already selected for this day.</p><p>Carefully review all selected events and select those whose start and end times do not conflict with other events already selected.</p>"); 
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      if (data.includes('AO')) {
        // alert("AO returned: "+data);
        td.next().next().text('AO'); 
        var regx = /^\s*.*count:.(\d{1,3})\/cap:.(\d{1,3})\/wl:.(\d{1,3}).*\s*$/;
        var res = data.match(regx);
        // console.log("res1: "+res[1]+", res2: "+res[2]);
        $("#msgdialogtitle").html("<h3 style='color: red;'>Attendance Override</h3>"); 
        $("#msgdialogcontent").html("<p>Registration done in ADMIN mode.</p><p>ALL CHECKS REGARDING EVENT CAPACITY AND TIME OVERLAPS HAVE BEEN BYPASSED.</p><p>Currently registered: "+res[1]+", Event capacity: "+res[2]+", Wait Listed: "+res[3]+"</p>");
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      alert("ERROR: " + data);
      });  // end $.post logic 
    }
  
  else {        // handle unchecking the check box
    $.post("registerJSONeventdel.php",
      {
      agenda: a,
      day: d,
      rid: rid
      },
    function(data, status){
      // alert("Data: " + data + "\nStatus: " + status);
      if (data.includes('OK')) {
        td.next().next().text('');
        // console.log(td.next().next().text(''));
        // alert("OK unclick returned:" + data);
        return;
        }
      alert('Error on deletion of event from agenda.');
      });  // end $.post logic     
    }
  });
});
</script>
<?php
if ($OKFlag == 'OFF') {
  echo "<h3>Event registration not available.</h3>";
  echo "<a class='btn btn-danger btn-lg' href='proflogin.php'>RETURN</a>";
  exit;
  }
?>
<h1>Schedule Events</h1>
<h3>Profile Name: <?=$id?>&nbsp;&nbsp;<a href="proflogin.php" class="btn btn-primary btn-lg">D O N E</a></h1></h3>

<form id=doit action=register.php method=post>
<b>Day:</b> 
<select id=DAY name=day>
<?=$selstring?>
</select>
</form>

<b>Agenda:</b> <span id=filter>
<select id=SEL> 
<?=$agendastr?>
</select>
</span>
&nbsp;&nbsp;
<button id=ADD title="Add or delete events to agenda">Modify Agenda</button>
<button id=DONE title="Add or delete events to agenda">Modify Complete</button>

<table class="table" border=1>
<thead>
<tr><th>Sel</th><th>Rowid</th><th>ST</th><th>Evt</th><th>Event Title</th><th>Start</th><th>End</th><th>FEE</th></tr>
</thead>
<tbody id=TB></tbody>
</table>
<a href="proflogin.php" class="btn btn-primary btn-lg">D O N E</a></h1>

</body>
</html>