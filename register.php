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
$sd = date('l, F j, Y \a\t g:i A', $start); $ed = date('l, F j, Y \a\t g:i A', $end);
$evtyr = date("Y", strtotime(geteventstart()));
$today = strtotime("now");
// echo "today: start: $sd, end: $ed<br>";
// echo "today: $today, start: $start, end: $end<br>";
// echo "formatted: sd: $sd, ed: $ed<br>";
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
  $agn = $r['AgendaName'];
  $agendastr .= "<option value='$agn'>$agn</option>";
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
  $resarray[$r['RowID']] = $r;
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
  input[type=checkbox] { zoom: 2; }
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
  $('#SEL').val('SELF');
// check for start and end time to allow registration
  var regOK = "<?=$OKFlag?>";
  var sd = "<?=$sd?>"; var ed = "<?=$ed?>"; var yr = "<?=$evtyr?>";
  // console.log("sd: "+sd+", ed: "+ed+", yr: "+yr);
  if (regOK == "OFF") {
    $("#msgdialogtitle").html("<h3 style='color: red;'>On-line Event Registration Not Available</h3>");
    $("#msgdialogcontent").html("<p>Registration for events for the "+yr+"  Bird Festival is not yet open.</p><p>On-line regisration is available between the dates of "+sd+" and "+ed+".</p><p>Please check back then.</p>");
    $('#msgdialog').modal('toggle', { keyboard: true });
    // window.location.href = "proflogin.php";
    }

// initialize page buttons on document load  
  // $('td:nth-child(2),th:nth-child(2)').hide();  // hide second col
  showselectedevents();
});
</script>
<script>
function showselectedevents() {
  // alert ("show selected agenda events for a day");
  var a = $("#SEL").val();
  if (a == '') { return; }
  var d = $("#DAY").val();
  if (d == '') { return; }
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
  if (a == '') { return; }
  var d = $("#DAY").val();
  if (d == '') { return; }
  // console.log("ALL for Day: "+d+", Agenda: "+a);
  $.post("registerJSONshowall.php",
    {
    agenda: a,
    day: d
    },
    function(data, status){
      $("#TB").html(data);
      // console.log("Data: "+data);
      // $('th:nth-child(1)').css(transform: scale(1.5););
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
  showselectedevents();
  });

$("#SEL").change(function() {
  // alert("event attendee changed");
  $("#ADD").show();
  showselectedevents();
  });

$("#ADD").click(function() {
  // alert("add event to agenda button clicked");
  // add in ALL to selection list
  // $("#SEL option[value='ALL']").remove();
  // $('#SEL').append('<option value="ALL">ALL</option>');
  $("#ADD").hide();
  showallevents(); 
});


// bind click of event description to dynamic rows in table
$('tbody').on('click', '.ED', function() {
  var rid = $(this).parent().find("td.RID").text(); // read RID
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
$('tbody').on('click', ':checkbox', function(e) {
  var rowtr = $(this).parent().parent();            // is the row's tr parent
  var rid = rowtr.find("td.RID").text();            // read RID using class name
  var sttus = rowtr.find("td:nth-child(3)");        // cache var for status col
  var desc = rowtr.find("td:nth-child(5)").text();  // get desc in col 5
  var fee = rowtr.find("td:last").text();           // read FEE in last column
  var matchpat = /^(.*)\((\d{1,3})\/(\d{1,3})\)/;   // mask for update of desc
// capture select list and handle occurance of 'ALL'
  var a = $("#SEL").val();  // attendee
  var optArray = [];
  if (a == 'ALL') {
    var list =   $("#SEL")[0];       // get select list OBJECT
    for (var i = 0; i < list.length; i++) {
      if (list[i].value == '') continue;
      if (list[i].value == 'ALL') continue;
      if (list[i].value == 'Attendee') continue;
      optArray.push(list[i].value);    // save VALUE of all select items
      // console.log(list[i].value);
      }
    }
  else { optArray.push(a);  }   
// optArray now contains one or more items from select list
// console.log("agendaList: "+optArray);
  var optlen = optArray.length;   // remember size
  var d = $("#DAY").val();  // day
  var cb = $(this);         // checkbox 
  // console.log("cb RID: "+rid);
  if (cb.prop("checked")) {
    // console.log("agenda: "+a);
    $.post("registerJSONeventadd.php",
      {
      agenda: optArray,
      day: d,
      rid: rid,
      fee: fee
      },
    function(data, status) {
      // alert("response: "+data);
      if (data.includes('OK')) {
        // set status column value
        sttus.text('OK'); // status column
        // adjust attendee count 
        var ext = desc.match(matchpat);
        var newdesc = ext[1]+'('+ext[2]+'/'+(parseInt(ext[3])+optlen)+')';
        // console.log("desc: "+ newdesc);
        rowtr.find("td:nth-child(5)").text(newdesc);
        return;
        }
      if (data.includes('WL')) {
        // alert('WL returned: ' + data);
        sttus.text('WL');  // status column
        $("#msgdialogtitle").html("<h3>Event Capacity Exceeded</h3>"); 
        $("#msgdialogcontent").html("<p>The maximum capacity for the selected event has been exceeded.</p><p>An event is &quot;Wait Listed&quot; (indicated by a status code of &quot;WL&quot; in the status column) when the maximum capacity of the requested event has been reached.</p><p>A future request to register will be fulfilled if there is capacity available and there is still interest in the event.</p><p>To re-check if a wait listed event is available merely un-check the event and re-select it. If capacity is available, the status will change to &quot;OK&quot;. Otherwise, it will revert to &quot;WL&quot;.</p>"); 
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      if (data.includes('TE')) {
        // alert('TE returned: ' + data.substring(3));
        cb.prop("checked", false);
        $("#msgdialogtitle").html("<h3>Event Time Conflict</h3>"); 
        $("#msgdialogcontent").html("<p>This event has a time conflict with another event already selected for this day.</p><p>Carefully review all selected events and choose those whose start and end times do not conflict with other events already selected.</p>"); 
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      if (data.includes('AO')) {
        // alert("AO returned: "+data);
        sttus.text('AO');   // status column
        // adjust attendee count 
        var ext = desc.match(matchpat);
        var newdesc = ext[1]+'('+ext[2]+'/'+(parseInt(ext[3])+1)+')';
        // console.log("desc: "+ newdesc);
        rowtr.find("td:nth-child(5)").text(newdesc);
        
        var regx = /^\s*.*count:.(\d{1,3})\/cap:.(\d{1,3})\/wl:.(\d{1,3}).*\s*$/;
        var res = data.match(regx);
        // console.log("res1: "+res[1]+", res2: "+res[2]);
        $("#msgdialogtitle").html("<h3 style='color: red;'>Attendance Override</h3>"); 
        $("#msgdialogcontent").html("<p>Registration done in ADMIN mode.</p><p>ALL CHECKS REGARDING EVENT CAPACITY AND TIME OVERLAPS HAVE BEEN BYPASSED.</p><p>Currently registered: "+res[1]+", Event capacity: "+res[2]+", Wait Listed: "+res[3]+"</p>");
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      if (data.includes('TM')) {
        cb.prop("checked", false);    // clear the check box
        var regx = /^\s*.*count:.(\d{1,3})\/cap:.(\d{1,3})\/wl:.(\d{1,3}).*\s*$/;
        var res = data.match(regx);
        // console.log("res1: "+res[1]+", res2: "+res[2]);
        $("#msgdialogtitle").html("<h3 style='color: red;'>Mulitple Event Add Error</h3>"); 
        $("#msgdialogcontent").html("<p>Registration of ALL attendees for this event has failed because it exceeds the maximum capacity of the event.</p><p>NO EVENTS HAVE BEEN REGISTERED!</p><p>Currently registered: "+res[1]+", Event capacity: "+res[2]+", Wait Listed: "+res[3]+"</p>");
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      alert("ERROR: " + data);
      });  // end $.post logic 
    }
  
  else {        // handle unchecking the check box
    var d = $("#SEL").val();    // save current value
    var optArray = [];
    if (d == 'ALL') {
      var list =   $("#SEL")[0];       // get select list OBJECT
      for (var i = 0; i < list.length; i++) {
        if (list[i].value == '') continue;
        if (list[i].value == 'ALL') continue;
        if (list[i].value == 'Attendee') continue;
        optArray.push(list[i].value);    // save VALUE of all select items
        // console.log(list[i].value);
        }
      }
    else { optArray.push(d);  }   // optArray contains one or more items
    
    $.post("registerJSONeventdel.php",
      {
      agenda: optArray,
      day: d,
      rid: rid
      },
    function(data, status){
      // alert("Data: " + data + "\nStatus: " + status);
      if (data.includes('OK')) {
        // set status column to blank
        var stat = sttus.text();
        sttus.text(''); // status column
        // adjust attendee count 
        var ext = desc.match(matchpat);
        var newdesc = ext[1]+'('+ext[2]+'/'+(parseInt(ext[3])-optlen)+')';
        // console.log("desc: "+ newdesc);
        if (stat != 'WL') {
          rowtr.find("td:nth-child(5)").text(newdesc); }
        // alert("OK unclick returned:" + data);
        return;
        }
      //alert('Error on deletion of event from agenda.');
      alert(data);
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
<select id=DAY name=day>
<?=$selstring?>
</select>
</form>

<span id=filter>
<select id=SEL> 
<option value=''>Attendee</option>
<option value='ALL'>ALL</option>
<?=$agendastr?>
</select>
</span>
&nbsp;&nbsp;
<button id=ADD title="Add or delete events to agenda">Add/Del Evt</button>

<table class="table" border=1>
<thead>
<tr><th>Sel</th><th>Rowid</th><th>ST</th><th>Evt</th><th>Event Title (Max/Att)</th><th>Start</th><th>End</th><th>FEE</th></tr>
</thead>
<tbody id=TB></tbody>
</table>
<a href="proflogin.php" class="btn btn-primary btn-lg">D O N E</a></h1>

</body>
</html>