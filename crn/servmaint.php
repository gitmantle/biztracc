<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

require("../db.php");

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select uid,vehicleno from vehicles";
$r = mysql_query($q) or die(mysql_error());
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$vno = $vehicleno;
	
	$qf = "SELECT s.ruckms,s.ruclicence,s.fromkms,s.hubodometer FROM rucs s JOIN (SELECT MAX(ruckms) AS id FROM rucs where vehicleno = '".$vno."') max ON s.ruckms = max.id";
	$rf = mysql_query($qf) or die(mysql_error());
	$row = mysql_fetch_array($rf);
	extract($row);
	$rk = $ruckms;
	$fk = $fromkms;
	$rl = $ruclicence;
	$hb = $hubodometer;
	$qu = "update vehicles set ruclicence = '".$rl."', fromkms = ".$fk.", ruckms = ".$rk.", odometer = ".$hb." where vehicleno = '".$vno."'";
	$ru = mysql_query($qu) or die(mysql_error().' '.$qu);
}

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Servicing and  Mainteneance</title>

<script>

window.name = "servemaint";

function viewsm(uid) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('vmaintenance.php?uid='+uid,'sm','toolbar=0,scrollbars=1,height=600,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}



</script>

</head>
<body>
  <table width="960" border="0">
    <tr>
    <tr>
      <td><?php include "getServeMaint.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>

</html>