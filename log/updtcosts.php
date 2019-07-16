<?php
session_start();
require("../db.php");

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Update Costs</title>

<script>

window.name = "costs";

</script>

</head>
<body>
  <table width="960" border="0">
    <tr>
    <tr>
      <td colspan="2"><?php include "getCostheader.php"; ?></td>
    </tr>
    <tr>
      <td>All GST to go against Admin Branch<input type="radio" name="radio" id="bgsta" value="admin" checked/></td>
      <td>GST to go against Branch associated with Docket Truck<input type="radio" name="radio" id="bgstb" value="brch" /></td>
    </tr>
    <tr>
      <td colspan="2"><?php include "getCostlines.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>

</html>