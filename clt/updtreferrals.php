<?php
session_start();
$usersession = $_COOKIE['usersession'];

$ddate = date("d/m/Y");

$dbase = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($dbase) or die(mysql_error());
$query = "select * from sessions where session_id = ".$usersession;
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$subscriber = $sub_id;


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Update Referrals</title>
<script type="text/javascript">

window.name = "updtreferrals";

</script>
</head>
<body>
<form name="updtref" id="updtref" method="post" action="">
  <table width="710" border="0">
    <tr>
      <td><?php include "getReferrals.php" ?></td>
    </tr>
    <tr><td align="left"><img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td></tr>
  </table>
</form>
</body>
</html>