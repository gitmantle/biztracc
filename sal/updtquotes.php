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
<title>Update Quotes</title>
<script type="text/javascript">

window.name = "updtquotes";


</script>
</head>
<body>
  <table width="950" border="0">
    <tr>
      <td>Select the prospect/client from the left grid.</td>
      <td>Add, Edit or Delete the quotes from the right grid.</td>
    </tr>
    <tr>
      <td><?php include "getprospects.php"; ?></td>
      <td><?php include "getquotes.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>
</html>