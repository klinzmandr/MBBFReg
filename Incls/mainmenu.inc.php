<?php

// used in register.php to hide logout button
// only set on in admmaintframe.php 
unset($_SESSION['admMode']);  

?>
<script src="js/bootstrap-session-timeout.js"></script> 
<script>
$(document).ready(function() { 
  $.sessionTimeout({
      title: 'SESSION TIMEOUT ALERT',
      message: '<h3>Your session is about to expire.</h3>',
      keepAlive: false,
      logoutUrl: 'adminsto.php',
      redirUrl: 'adminsto.php',
      warnAfter:  15*60*1000,
      redirAfter: 20*60*1000,
      countdownMessage: 'Time remaining:',
      countdownBar: true,
      countdownSmart: true,
      showButtons: false
  });
});
</script>

<div class="hidden-print">
<!-- add padding to top of each page for fixed navbar -->
<style>
body { padding-top: 50px; }
.nav a{
    color: black;
    font-size: 1.25em;
    }     
</style>

<!-- start menu bar -->
<!-- set nav bar fix to top of every page -->
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">

<!-- Brand and toggle get grouped for better mobile display -->
<div class="navbar-header">
  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
    <span class="sr-only">Toggle navigation</span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
    <span class="icon-bar"></span>
  </button>
</div>
<!-- end hamburger button-->

<!-- collects all tab defs for collapse -->
<div class="collapse navbar-collapse" id="navbar-collapse-1">

<!-- define the menu bar -->
<ul class="nav nav-tabs">
<!-- home page -->  
  <li class="dropdown">
    <a id="dLabel" role="button" class="btn btn-default" href="admin.php">Home </a></li>
<!-- Financial -->
      <li class="dropdown">
    <a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-default" 
    href="#">Financial<span class="caret"></span></a>
		<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
		  <li><a href="admpayments.php">Payments</a></li>
		  <li><a href="admpendingexempts.php">Current & Pending Exemptions</a></li>
		  <li class="divider"></li>
		  <li><a href="adminvoice.php">Attendee Invoice</a></li>
		  
    </ul>  <!-- dropdown-menu multi-level -->
  </li>  <!-- dropdown  -->
  
<!-- Profile Maintenance -->
  <li class="dropdown">
    <a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-default" 
    href="#">Profiles and Attendees<span class="caret"></span></a>
		<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
		  <li><a href="admprofsummary.php" title="List all events for sekected profile."> Profile Event Summary</a></li>
		  <li><a href="admprofupdate.php" title="Update a selected profile.">Update Profile</a></li>
		  <li><a href="admmaint.php" title="Update a assoicated agendas for a profile using the attendee interface.">Schedule Events</a></li>
		  <li style='cursor: pointer;'><a id=AP title="Create a new attendee profile to be used by the maintenance interface to add agendas and events.">Add new profile</a></li>
		  <li><a href="admprofilewipeout.php" title="Delete an existing profile and ALL associated agendas.">Wipe out an existing profile</a></li>
		  
    </ul>  <!-- dropdown-menu multi-level -->
  </li>  <!-- dropdown  -->

<!-- utilities -->
<li class="dropdown">
    <a id="uLabel" role="button" data-toggle="dropdown" class="btn btn-default" 
    href="#">Utilities <span class="caret"></span></a>
		<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
		<li><a href="emailsend.php" title="Compose an email message or a predefined template message.">Send Email Message</a></li>
		<li><a href="emaileditreplys.php" title="Maintain the predefined message templaes used by the email sending utility.">Edit Email Message Templates</a></li>
		<li><a href="admevtmon.php" title="Start/stop background check on profiles to delete any scheduled events from those that are inactive.">Event Monitor</a></li>
		  <li><a href="admevtlogviewer.php" title="Examine the event monitor log file to review all actions taken by the event monitor utility.">Event Monitor Log</a></li><li><a href="admusersanddates.php">Maintain Users And Dates</a></li>
		<li><a href="admfeeroster.php" title="Establish and maintian fees for all non-event items including the festival registration fee.">Fee Roster</a></li>
		<li><a href="adm_smtp_tester.php" title="Test connections to the mail server for sending mail.">Mail Connecivity Test</a></li>
		<li><a href="admlogviewer.php" title="View registration system activity log.">General Activity Log</a></li>
      </ul>
  </li>  <!-- dropdown -->

<!-- reports -->
  <li class="dropdown">
    <a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-default" 
    href="#">Reports <span class="caret"></span></a>
		<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
		  <li><a href="admfeesbyprofile.php" title="A complete listing of all charges and payments for all attendees grouped by profile.">Payments History</a></li>
      <li><a href="admcapacity.php" title="Listing of all festival events with each event&apos;s attendance capacity and totals for registered attendees, wait list count and space available.">Capacity Drilldown Report</a></li>
      <li><a href="admshirts.php" title="Listing of all shirt sizes that have been ordered by those that have registered.  Probaby used for vendor ordering.">Shirts Report</a></li> 
      <li><a href="admlunches.php" title="Liting of all lunches that have been ordered by registered attendees by type within day.">Lunches Report</a></li> 
      <li><a href="UC.php" title="Listing of all profiles with outstanding balances." >Payments Due Report</a></li>
      <li><a href="UC.php" title="Print mailing labels" >Print Mailing Label</a></li>
      <li><a href="UC.php" title="Print name tags for speakers, leaders and volunteers." >Print Name Tags</a></li>
      <li><a href="UC.php" title="Print all event information including list of attendees registered." >Event Info and Attendance List</a></li>
      		  
    </ul>  <!-- dropdown-menu multi-level -->
  </li>  <!-- dropdown  -->

<!-- help -->
  <li class="dropdown">
    <a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-default" 
    href="#">Help <span class="caret"></span></a>
		<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
      <li><a href="docs/UserGuide.pdf" target="_blank">User Guide</a></li>
      <li><a href="docs/DataDictionary.pdf" target="_blank">Data Dictionary</a></li>
      <li><a id=about href="#">About Registration Admin</a></li>
    </ul>  <!-- dropdown-menu multi-level -->
  </li>  <!-- dropdown  -->
  
<!-- menu tester -->  
<!--   <li class="dropdown">
    <a id="dLabel" role="button" class="btn btn-default" 
    href="menutester.php">Menu Tester </a>  
  </li> 
 -->
</ul>  <!-- class="nav nav-tabs" -->
</div>  <!-- class="collapse navbar-collapse" -->
</nav>  <!-- class="navbar" -->
<!-- end menu bar -->

<script>
// attach click event to start admaddprofile.php
$(function() {
$("#AP").click(function(event) {
  var pn = prompt("Enter an email address to use for the new profile name");
  pn = pn.toLowerCase();
  var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  if (re.test(String(pn))) {
    $("#IN").val(pn);   // profile name
    }
  else {
    event.preventDefault();
    alert("Invalid profile name: "+pn);
    return;
    }
  // var an = prompt("Enter the first and last name of the new attendee");
  // var error = '';
  // var illegalChars = /^[\w\-\s]+$/gi;
  // if (an.length == 0) {
  //   error += "Missing attendee name.\n";
  //   } 
  // if (!(illegalChars.test(an))) {
  //   error += "The name may only contain letters and numbers.\n";
  //   }
  // if (error.length) {
  //   event.preventDefault();
  //   alert("Invalid attendee:\n\n"+error);
  //   return;
  //   }    
  // an = an.toUpperCase();  
  // $("#AN").val(an);   // agenda name
  
  $("#FF").submit();  // submit form
  });
});
</script>  

<form id=FF action=admaddprofile.php>
<input id=IN type=hidden name=newprofname value=''>
</form>

<script>
$(function() {
$("#about").click(function() {
  // alert("about clicked");
  $("#msgdialogtitle").html("<h3>About Event Registration</h3>"); 
  $("#msgdialogcontent").html('<p>Copyright (C) 2018 by Pragmatic Computing, Morro Bay, CA</p><p>Registration Administration is a system designed for use by the Morro Bay Bird Festival to organize and optimize the registration and scheduling of events for their annual winter bird festival.</p><p>This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.</p><p>This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.</p><p>A copy of this license is available at: <a href="http://www.gnu.org/licenses/gpl.html" target="_blank">http://www.gnu.org/licenses/</a>.</p>');
  $('#msgdialog').modal('toggle', { keyboard: true });
  });
});
</script>

</div>  <!-- class="hidden-print" -->
