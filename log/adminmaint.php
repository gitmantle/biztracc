<?php
session_start();
date_default_timezone_set($_SESSION['s_timezone']);

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$servicetable = 'ztmp'.$user_id.'_service';

$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$servicetable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$servicetable." ( uid int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, vehicleno varchar(25),branch varchar(4),regno varchar(25),lastserviced date,odometer int(11),servicedue int(11))  engine myisam";
$calc = mysql_query($query) or die(mysql_error().' '.$query);

$q = "select regno,cost_centre from vehicles";
$r = mysql_query($q) or die(mysql_error());
while ($row = mysql_fetch_array($r)) {
	extract($row);
	$v = explode('~',$cost_centre);
	$br = $v[0];
	$vn = $v[1];
	$qi = "insert into ".$servicetable." (vehicleno,branch,regno) values (";
	$qi .= "'".$vn."',";
	$qi .= "'".$br."',";
	$qi .= "'".$regno."')";
	$ri = mysql_query($qi) or die(mysql_error().' '.$qi);
}

require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Administer Mainteneance</title>

<script>

window.name = "routes";

</script>

</head>
<body>
  <table width="960" border="0">
    <tr>
    <tr>
      <td><?php include "getVehicleMaint.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>

</html>