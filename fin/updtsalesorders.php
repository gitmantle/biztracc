<?php
session_start();

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

function refreshdngrid() {
	jQuery("#dnlist").setGridParam({url:"getdns.php"}).trigger("reloadGrid"); 
}

function refreshsogrid() {
	jQuery("#solist").setGridParam({url:"getsalesorders.php"}).trigger("reloadGrid"); 
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