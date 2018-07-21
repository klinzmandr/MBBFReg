<?php 
session_start(); 
error_reporting(E_ERROR | E_WARNING | E_PARSE); 

// include 'Incls/vardump.inc.php';
include 'Incls/datautils.inc.php';
include 'Incls/listutils.inc.php';

$id = $_REQUEST['profname'];

// get fee roster
$feesched = readlistreturnarray('Fees');
// echo "<pre>feesched "; print_r($feesched); echo '</pre>';

// get total fees from regeventlog
$sql = "SELECT DISTINCT `RecKey`, COUNT(`RecKey`) as 'regcount', SUM(`FEE`) as 'regfees' FROM `regeventlog` WHERE `ProfName` = '$id' GROUP BY `RecKey`;";
// echo "sql: $sql<br>";
$profres = doSQLsubmitted($sql);
while ($r = $profres->fetch_assoc()) {
  // echo '<pre>r '; print_r($r); echo '</pre>';
  $regtots[$r[RecKey]] = $r[regfees];
  $regcnt[$r[RecKey]] = $r[regcount];
  }
// echo '<pre>tots '; print_r($tots); echo '</pre>';
$evtfees = isset($regtots[Evt]) ? number_format($regtots[Reg]) : '0.00';
$evtaofees = isset($regtots[EvtAO]) ? number_format($regtots[Reg]) : '0.00';
$totproffees = isset($regtots[Reg]) ? number_format($regtots[Reg]) : '0.00';
$totregcnt = isset($regcnt[Reg]) ? number_format($regcnt[Reg]) : '0';
$totpay = isset($regtots[Pay]) ? number_format($regtots[Pay], 2) : '0.00';

// get profile info
$sql = "SELECT * FROM `regprofile` WHERE `ProfileID` = '$id';";
$res = doSQLsubmitted($sql);
$r = $res->fetch_assoc();
// echo "<pre> profile record for $profname"; print_r($r); echo '</pre>';
$totproflunch = 0; $totprofshirts = 0;
$reglist = ''; $lunlist = '';
if ($r[mealFrM]) $lunlist .= "Friday (Meat lunch): $r[mealFrM] @ $$feesched[Lunches] each<br>"; 
if ($r[mealFrV]) $lunlist .= "Friday (Veg lunch): $r[mealFrV] @ $$feesched[Lunches] each<br>"; 
if ($r[mealSaM]) $lunlist .= "Saturday (Meat lunch): $r[mealSaM] @ $$feesched[Lunches] each<br>"; 
if ($r[mealSaV]) $lunlist .= "Saturday (Veg lunch): $r[mealSaV] @ $$feesched[Lunches] each<br>"; 
if ($r[mealSuM]) $lunlist .= "Sunday (Meat lunch): $r[mealSuM] @ $$feesched[Lunches] each<br>"; 
if ($r[mealSuV]) $lunlist .= "Sunday (Veg lunch): $r[mealSuV] @ $$feesched[Lunches] each<br>"; 
$totproflunch += $r[mealFrM] * $feesched[Lunches];
$totproflunch += $r[mealFrV] * $feesched[Lunches];
$totproflunch += $r[mealSaM] * $feesched[Lunches];
$totproflunch += $r[mealSaV] * $feesched[Lunches];
$totproflunch += $r[mealSuM] * $feesched[Lunches];
$totproflunch += $r[mealSuV] * $feesched[Lunches];
if ($totproflunch == 0) $lunlist = 'NO LUNCHES REQUESTED';
$totproflunch = number_format($totproflunch,2);

$shrlist = '';
if ($r[shirtwS]) $shrlist .= "Shirt(s) (Women S): $r[shirtwS]<br>";
if ($r[shirtwM]) $shrlist .= "Shirt(s) (Women M): $r[shirtwM]<br>";
if ($r[shirtwL]) $shrlist .= "Shirt(s) (Women L): $r[shirtwL]<br>";
if ($r[shirtwXL]) $shrlist .= "Shirt(s) (Women XL): $r[shirtwXL]<br>";
if ($r[shirtmS]) $shrlist .= "Shirt(s) (Men S): $r[shirtmS]<br>";
if ($r[shirtmM]) $shrlist .= "Shirt(s) (Men M): $r[shirtmM]<br>";
if ($r[shirtmL]) $shrlist .= "Shirt(s) (Men L): $r[shirtmL]<br>";
if ($r[shirtmXL]) $shrlist .= "Shirt(s) (Men XL): $r[shirtmXL]<br>";
if ($r[shirtmXXL]) $shrlist .= "Shirt(s) (Men XXL): $r[shirtmXXL]<br>";
$totprofshirts += $r[shirtwS] * $feesched[Shirts];
$totprofshirts += $r[shirtwM] * $feesched[Shirts];
$totprofshirts += $r[shirtwL] * $feesched[Shirts];
$totprofshirts += $r[shirtwXL] * $feesched[Shirts];
$totprofshirts += $r[shirtmS] * $feesched[Shirts];
$totprofshirts += $r[shirtmM] * $feesched[Shirts];
$totprofshirts += $r[shirtmL] * $feesched[Shirts];
$totprofshirts += $r[shirtmXL] * $feesched[Shirts];
$totprofshirts += $r[shirtmXXL] * $feesched[Shirts];
$totshirts = $r[shirtwS]+$r[shirtwM]+$r[shirtwL]+$r[shirtwXL]+$r[shirtmS]+$r[shirtmM]+$r[shirtmL]+$r[shirtmXL]+$r[shirtmXXL];

$s = $totshirts - $totreg; // figure out extra shirts
if ($s >= 0) $totprofshirts = $s * $feesched[Shirts]; 
if ($totshirts == 0) {
  $shrlist = 'NO SHIRTS ORDERED';
  $totprofshirts = 0;
  }
$totprofshirts = number_format($totprofshirts,2);
// echo "totshirts: $totshirts, totreg: $totreg<br>";
$exemption = $r[Exempt];   // save for later

// get all entries for profile from event log
$sql = "
SELECT `regeventlog`.*, `events`.`Trip`, `events`.`Event`, `events`.`FEE`  
 FROM `regeventlog`, `events` 
WHERE `ProfName` = '$id' 
  AND `regeventlog`.`EvtRowID` = `events`.`RowID`
ORDER BY `events`.`Trip` ASC
;";
// echo "<br>sql: $sql<br>";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
$evtlist = ''; $totevtfees = 0;
while ($r = $res->fetch_assoc()) {
//  echo '<pre>Rec '; print_r($r); echo '</pre>';
    if ($r[RecKey] == 'Reg') {
    $agendacount[] = $r[AgendaName];
    continue;
    }
  if (($r[RecKey] == 'Evt') OR ($r[RecKey] == 'EvtAO')) {
    if ($r[FEE] == 0) $evtlist .= "Event $r[Trip] $r[Event] (Agenda: $r[AgendaName])<br>";
    else $evtlist .= "Event $r[Trip] $r[Event] (Agenda: $r[AgendaName]) @ $r[FEE]<br>";
    $activitycount[] = $r[EvtRowID];
    $totevtfees += $r[FEE];
    continue;
    }  
  }
$totevtfees = number_format($totevtfees,2);

// get payments from event log
$totpay = 0; $paycount = 0;
$sql = "SELECT * FROM `regeventlog` WHERE `ProfName` = '$id' AND `RecKey` = 'Pay';";
$res = doSQLsubmitted($sql);
$rc = $res->num_rows;
// echo "rc: $rc<br>";
while ($r = $res->fetch_assoc()) {
// echo '<pre>Pay rec '; print_r($r); echo '</pre>';
  $paycount += 1;
  $totpay += $r[Payment];
  }
$totpay = number_format($totpay, 2);

$paymentrow = "<tr><td>less total for $paycount payment(s):</td><td align=right>-$$totpay</td><tr>";
if ($totpay == 0) $paymentrow = "";
$grandtotal = number_format(($totevtfees + $totproflunch + $totproffees),2);

$disc = 0;
if ($exemption == 'APPROVED') {
  $disc = $feesched[ExemptPerCent];
  $xmptmsg = "<tr><td>$disc% Registration exemption approved.</td>";
  $disc = $feesched[ExemptPerCent] / 100;
  $discapplied = number_format(($totproffees * $disc), 2);
  $xmptmsg .= "<td align=right>-$$discapplied</td></tr>";
  if ($discapplied == 0) $xmptmsg = '';
  }
if ($exemption == 'YES')
  $xmptmsg = "<tr><td><b>Exemption of fees under review.</b></td></tr>";
$balance = number_format(($grandtotal - $totpay - $discapplied),2);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Attendee Invoice</title>
<!-- Bootstrap -->
<link href="css/bootstrap.min.css " rel="stylesheet" media="all">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jsutils.js"></script>
<script src="js/chksession.js"></script>
<style>
  input[type=checkbox] { transform: scale(1.5); }
</style> 
</head>
<body>
<?php
include 'Incls/mainmenu.inc.php';
?>
<div class=container>
<img src="http://morrobaybirdfestival.net/wp-content/uploads/2016/08/LOGO3.png" width="400" height="100" alt="bird festival logo">
<h1 style="color: blue">Attendee Invoice</h1>
<h2>Profile Name: <?=$id?></h2>
<table class=table>
<tr><td>Registration(s) (<?=$totregcnt?>)</td><td align=right>$<?=$totproffees?></td></tr>
<tr><td colspan=2><ul><?=$reglist?></ul></td></tr>
<tr><td>Lunches</td><td align=right>$<?=$totproflunch?></td></tr>
<tr><td colspan=2><ul><?=$lunlist?></ul></td></tr>
<tr><td>Shirts (Free to registrants)</td><td align=right>&nbsp;</td></tr>
<tr><td colspan=2><ul><?=$shrlist?></ul></td></tr>
<tr><td>Scheduled Events</td><td align=right>$<?=$totevtfees?></td></tr>
<tr><td colspan=2><ul><?=$evtlist?></ul></td></tr>
<tr><td>TOTAL FEES:</td><td align=right>$<?=$grandtotal?></td><tr>
<?=$xmptmsg?>
<?=$paymentrow?>
<tr><td>Balance Due:</td><td align=right>$<?=$balance?></td><tr>

</table>
NOTE: Payments will only be listed after being processed and entered by the Event Registrar.<br><br>
</div>
</body>
</html>