<?php
session_start();
require("../db.php");

$moduledb = $_SESSION['s_cltdb'];
mysql_select_db($moduledb) or die(mysql_error());


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Sales Orders</title>
<script type="text/javascript">

window.name = "updtsos";

function viewdn(id) {
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../clt/viewdn.php?id='+id,'vdn','toolbar=0,scrollbars=1,height=570,width=900,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}

function printdn(type,rf) {
	var x = 0, y = 0; // default values	
	x = window.screenX +5;
	y = window.screenY +200;
	if(typeof(rf)=="undefined"){ 
		rf = tradingref;
	}
	window.open('PrintQuote.php?type='+type+'&tradingref='+rf,'plpdf','toolbar=0,scrollbars=1,height=670,width=950,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
}

function createinv() {
	x = window.screenX +5;
	y = window.screenY +200;
	window.open('../clt/createinv.php','cinv','toolbar=0,scrollbars=1,height=500,width=750,resizable=0,left='+x+',screenX='+x+',top='+y+',screenY='+y);
	
}


</script>
</head>
<body>
  <table width="950" border="0">
    <tr>
      <td>Select the Sales Order from the left grid.</td>
      <td>View and print the Delivery Notes from the right grid.</td>
    </tr>
    <tr>
      <td><?php include "getsalesorders.php"; ?></td>
      <td><?php include "getdns.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>
</html>