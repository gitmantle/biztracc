<?php
session_start();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Stock Groups and Categories</title>
<script type="text/javascript">

window.name = "updtstkgroups";


</script>
</head>
<body>
  <table width="850" border="0">
    <tr>
      <td>Select, Add or Edit the stock groups from the left grid.</td>
      <td>Add or Edit the categories from the right grid.</td>
    </tr>
    <tr>
      <td><?php include "../fin/getstkgroups.php"; ?></td>
      <td><?php include "../fin/getstkcats.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>
</html>