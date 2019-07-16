<?php
session_start();

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
<title>Update Staff</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

window.name = 'hs_users';

</script>

</head>

<body>
  <table width="950" border="0">
    <tr>
      <td><?php include "getusers.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>

</html>