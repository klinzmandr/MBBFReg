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
    href="#">Profile and Agendas<span class="caret"></span></a>
		<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
		  <li><a href="admprofsummary.php" title="Update a selected profile."> Profile Summary</a></li>
		  <li><a href="admprofupdate.php" title="Update a selected profile.">Update Profile</a></li>
		  <li><a href="admmaint.php" title="Update a assoicated agendas for a profile using the attendee interface.">Schedule Events</a></li>
		  <li style='cursor: pointer;'><a id=AP title="Create a new attendee profile to be used by the maintenance interface to add agendas and events.">Add new profile</a></li>
		  <li><a href="admprofilewipeout.php" title="Delete an existing profile and ALL associated agendas.">Wipe out an existing profile</a></li>
    </ul>  <!-- dropdown-menu multi-level -->
  </li>  <!-- dropdown  -->

<!-- reports -->
  <li class="dropdown">
    <a id="dLabel" role="button" data-toggle="dropdown" class="btn btn-default" 
    href="#">Reports <span class="caret"></span></a>
		<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
		  <li><a href="admfeesbyprofile.php" title="A complete listing of all charges and payments for all attendees grouped by profile.">Payments History</a></li>
      <li><a href="admcapacity.php" title="Listing of all festival events with each event&apos;s attendance capacity and totals for registered attendees, wait list count and space available.">Capacity Drilldown Report</a></li>
      <li><a href="admshirts.php" title="Listing of all shirt sizes that have been ordered by those that have registered.  Probaby used for vendor ordering.">Shirts Report</a></li> 
      <li><a href="admlunches.php" title="Liting of all lunches that have been ordered by registered attendees by type within day.">Lunches Report</a></li> 
      <li><a href="admfeeroster.php" title="Establish and maintian fees for all non-event fees including the festival registration fee.">Fee Roster</a></li>		  
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
    $("#IN").val(pn);
    $("#FF").submit();
    }
  else {
    event.preventDefault();
    alert("Invalid profile name: "+pn);
    }
  
  });
});
</script>  
<form id=FF action=admaddprofile.php>
<input id=IN type=hidden name=newprofname value=''>
<input type=hidden name=new value='new'>
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
