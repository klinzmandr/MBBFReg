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

$evtyr = date("Y", strtotime(geteventstart()));

// read profile to get partial frestival day, if any
$profres = doSQLsubmitted("Select `regType` FROM `regprofile` WHERE `ProfileID` = '$id';");
$profile = $profres->fetch_assoc();
// echo '<pre>profile >'; print_r($profile); echo '</pre>';
$regType = $profile['regType']; 
// echo "regType: $regType<br>";

// create the day drop down selections  
// create single day agenda for partial and exempt profiles
if ($regType != 'full') {  
  $selstring = "<option value=''>Day</option><option value=$regType selected>$regType</option>";
  } 
// else get day names from config list if full registration
else {
  $selstring = readlist('Day');
  }

// create the agenda drop down
$sql  = "
SELECT DISTINCT `AgendaName` FROM `regeventlog` WHERE `RecKey` = 'Reg' AND `ProfName` = '$id';";
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
  $('#DAY').val('');    // day list choice
  $('#SEL').val('');    // attendee list choice
  $('#TB').html('<tr><td>&nbsp;</td><td>&nbsp;</td><td colspan=6>No events scheduled. Start by selecting the Day and Attendee.</td></tr>');
  
// initialize rows on document load  
  $('td:nth-child(2),th:nth-child(2)').hide();  // hide second col
  showselectedevents();
});

function showselectedevents() {
  // alert ("show selected agenda events for a day and attendee");
  var a = $("#SEL").val();  // attendee list choice
  if (a == '') { return; }
  var d = $("#DAY").val();  // day choice
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
  // alert("list all events for the selected day and attendee");
  var a = $("#SEL").val();    // attendeed list choice
  if (a == '') { return; }
  var d = $("#DAY").val();    // day list choice
  if (d == '') { return; }
  // console.log("ALL for Day: "+d+", Agenda: "+a);
  $.post("registerJSONshowall.php",
    {
    agenda: a,
    day: d
    },
    function(data, status){
      // alert("Data: " + data + "\nStatus: " + status);
      $("#TB").html(data);
      $('th:nth-child(1)').show();                  // show check box col
      $('td:nth-child(2),th:nth-child(2)').hide();  // hide rowid col
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
  // alert("event attendee selection changed");
  $("#ADD").show();
  showselectedevents();
  });

$("#ADD").click(function() {
  // alert("add/delete event clicked");
  $("#ADD").hide();
  showallevents(); 
  });

$("#LIST").click(function() {
  // alert("list all clicked");
  $.post("registerJSONeventlister.php",
  { /* no parameters */  },
  function(data, status){
    // alert("Data: " + data + "\nStatus: " + status);
    $("#msgdialogtitle").html("<h3>All Events for All Attendees</h3>"); 
    $("#msgdialogcontent").html(data); 
    $('#msgdialog').modal('toggle', { keyboard: true });
    }
  );  // end $.post logic 

  });

// bind click of event description (class ED) to dynamic rows in table
// requires 'on' operator since table rows are dynamically loaded
//    by either json routines showselectedevents or showallevents 
$('tbody').on('click', '.ED', function() {
  var rid = $(this).closest('tr').find("td.RID").text(); // read RID
  // alert("rid: "+rid);
  $.post("registerJSONeventdescription.php",
      {
      rid: rid
      },
    function(data, status) {
      // alert("response: "+data);
      $("#msgdialogtitle").html("<h3 style='color: red;'>Event Description</h3>");
      var b = data.substring(3);
      $("#msgdialogcontent").html(b);
      $('#msgdialog').modal('toggle', { keyboard: true });
      return;
  });
});

// bind click event of checkbox to dynamic rows in table
// requires 'on' operator since table rows only loaded
//    by either json routines showselectedevents or showallevents 
// handle check and uncheck status of checkbox in table row
$('tbody').on('click', ':checkbox', function(e) {
  var rowtr = $(this).closest('tr');        // is the row's tr parent
  var rid = rowtr.find("td.RID").text();    // read RID using class name
  var sttus = rowtr.find("td:eq(2)");       // cache status col
  var desc = rowtr.find("td:eq(4)").text(); // get desc in col 5
  var fee = rowtr.find("td:last").text();   // read FEE in last column
  var matchpat = /^(.*)\((\d{1,3})\/(\d{1,3})\)/;   // mask for update of desc
// capture select list and handle occurance of 'ALL'
  var a = $("#SEL").val();  // attendee
  var optArray = [];
  if (a == 'ALLxz') {               // get all attendees in selection list
    var list = $("#SEL")[0];        // get select list OBJECT
    for (var i = 0; i < list.length; i++) {
      if (list[i].value == '') continue;
      if (list[i].value == 'ALLxz') continue;
      optArray.push(list[i].value);    // save VALUE of all select items
      // console.log(list[i].value);
      }
    }
  else {  optArray.push(a);  }   // use selected attendee if not all
// optArray now contains one or more attendees from select list
// console.log("agendaList: "+optArray);
  var optlen = optArray.length;   // remember size
  var d = $("#DAY").val();        // day
  var cb = $(this);               // checkbox 
  // console.log("cb RID: "+rid);
// handle checkbox status
  if (cb.prop("checked")) {
    // console.log("event checked for: "+a);
    var data = "";
    $.post("registerJSONeventadd.php",
      {
      agenda: optArray,
      day: d,
      rid: rid,
      fee: fee
      },
    function(data, status) {
      // alert("checked response: "+data);
      var dx = data.substring(0,3);
      // console.log('>'+dx+'<');
      if (dx.includes("OK")) {    // event selected successfully
        // set status column value
        sttus.text('OK'); // status column
        // adjust attendee count 
        var ext = desc.match(matchpat);
        var newdesc = ext[1]+'('+ext[2]+'/'+(parseInt(ext[3])+optlen)+')';
        // console.log("desc: "+ newdesc);
        rowtr.find("td:eq(4)").text(newdesc);
        return;
        }
      if (dx.includes('WL')) {  // event wait listed
        // alert('WL returned: ' + data);
        sttus.text('WL');  // status column
        $("#msgdialogtitle").html("<h3>Event Capacity Exceeded</h3>"); 
        $("#msgdialogcontent").html("<p>The maximum capacity for the selected event has been exceeded.</p><p>An event is &quot;Wait Listed&quot; (indicated by a status code of &quot;WL&quot; in the status column) when the maximum capacity of the requested event has been reached.</p><p>A future request to register will be fulfilled if there is capacity available and there is still interest in the event.</p><p>To re-check if a wait listed event is available merely un-check the event and re-select it. If capacity is available, the status will change to &quot;OK&quot;. Otherwise, it will revert to &quot;WL&quot;.</p>"); 
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      if (dx.includes('TE')) {  // event has time conflict(s)
        // alert('TE returned: ' + data.substring(3));
        cb.prop("checked", false);
        $("#msgdialogtitle").html("<h3>Event Time Conflict</h3>"); 
        $("#msgdialogcontent").html("<p>This event has a time conflict with another event already selected for this day.</p><p>Carefully review all selected events and choose those whose start and end times do not conflict with other events already selected.</p>"); 
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      if (dx.includes('AO')) {  // event max'ed but admin over rides max
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
      if (dx.includes('TM')) {  // event not registered for multi attendees
        cb.prop("checked", false);    // clear the check box
        var regx = /^\s*.*count:.(\d{1,3})\/cap:.(\d{1,3})\/wl:.(\d{1,3}).*\s*$/;
        var res = data.match(regx);
        // console.log("res1: "+res[1]+", res2: "+res[2]);
        $("#msgdialogtitle").html("<h3 style='color: red;'>Mulitple Event Add Error</h3>"); 
        $("#msgdialogcontent").html("<p>Registration of ALL attendees for this event has failed because the group exceeds the maximum capacity of the event.</p><p>NO EVENTS HAVE BEEN REGISTERED FOR ANYONE!</p><p>Currently registered: "+res[1]+", Event capacity: "+res[2]+", Wait Listed: "+res[3]+"</p>");
        $('#msgdialog').modal('toggle', { keyboard: true });
        return;
        }
      alert("ERROR: " + data);
      });  // end $.post logic 
    }
  
  else {        // handle check box unchecked event
    // console.log("event unchecked");
    var d = $("#SEL").val();    // save current value
    var optArray = [];
    if (d == 'ALLxz') {
      var list =   $("#SEL")[0];       // get select list OBJECT
      for (var i = 0; i < list.length; i++) {
        if (list[i].value == '') continue;
        if (list[i].value == 'ALLxz') continue;
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
          rowtr.find("td:eq(4)").text(newdesc); }
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

<h1>Schedule Events</h1>
<h3>Profile Name: <?=$id?>&nbsp;&nbsp;<a href="proflogin.php" class="btn btn-primary btn-lg">RETURN</a></h1></h3>

<select id=DAY name=day>
<?=$selstring?>
</select>

<span id=filter>
<select id=SEL> 
<option value=''>Attendee</option>
<option value='ALLxz'>ALL</option>
<?=$agendastr?>
</select>
</span>
&nbsp;&nbsp;
<button id =LIST title="list all attendees for all events">List All</button>
&nbsp;&nbsp;
<button id=ADD title="Add or delete events to day and attendee selected">Add/Del Evt</button>
<table class="table" border=0>
<thead>
<tr><th>Sel</th><th>Rowid</th><th>ST</th><th>Evt</th><th>Event Title (Max/Att)</th><th>Start</th><th>End</th><th>FEE</th></tr>
</thead>
<tbody id=TB></tbody>
</table>
<a href="proflogin.php" class="btn btn-primary btn-lg">RETURN</a></h1>

</body>
</html>