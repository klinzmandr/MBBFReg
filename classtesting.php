<?php
$text = isset($_REQUEST['text']) ? $_REQUEST['text'] : '';
$ta = isset($_REQUEST['ta']) ? $_REQUEST['ta'] : '';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Javascript Testing</title>
<link rel="shortcut icon" href="">
<link href="css/bootstrap.min.css" rel="stylesheet" media="all">
<link href="css/font-awesome.min.css" rel="stylesheet">
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<style>
  input[type=checkbox] { transform: scale(1.5); }
</style> 
</head>
<body>
<div class="container">
<h2>Javascript Testing</h2>
<script>
// 'AO ' . 'count: ' . $evtcount . '/' . 'cap: ' . $maxcap
// AO count: 99/cap: 100
$(function() {
  $("#chk").click(function() {
    // var inp = $("#in").val();
    // var inp = "AO count: 99/cap: 100";
    var inp = "AO count: 3/cap: 2";
    var regx = /^.*count:.(\d{1,3})\/cap:.(\d{1,3}).*$/;
    var res = inp.match(regx);
    console.log("result1: "+res[1]+", result2: "+ res[2]);
    //alert("result: "+res);
  });
});
</script>

Input string: <input id=in type=text value=''><br>
<button id=chk>Test string</button>
<br>

<br><br><br>
</div>
</body>
</html>