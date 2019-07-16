<?php
session_start();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Uucosted GRNs</title>

<script>

window.name = "costs";

</script>

</head>
<body>
  <table width="960" border="0">
    <tr>
    	<td>Ensure you have all costs and charges available for any GRN you wish to cost</td>
    </tr>
    <tr>
      <td><?php include "getUncostGRNs.php"; ?></td>
    </tr>
    <tr>
      <td><?php include "getCharges.php"; ?></td>
    </tr>
    <tr>
    	<td><input name="bValue" type="button" value="Apportion by Value" onClick="ipostTrdTrans('INV')">&nbsp;&nbsp;&nbsp;&nbsp;<input name="bQty" type="button" value="Apportion by Quantity" onClick="ipostTrdTrans('INV')"></td>
    </tr>
    <tr>
      <td><?php include "getUncostGRNitems.php"; ?></td>
    </tr>
     <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>

</html>