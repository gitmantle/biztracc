<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

date_default_timezone_set($_SESSION['s_timezone']);

$edate = date("d/m/Y");
$edateh = date("Y-m-d");

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Road User Charge Refund</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">

<script>

	window.name = "ruc";

</script>

</head>

<body>
<form name="form1" method="post" >
<br>
  <table width="600" border="0" align="center" cellpadding="3" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td><?php include "getrucvehicles.php"; ?></td>
    </tr>
  </table>
</form>
</body>
</html>