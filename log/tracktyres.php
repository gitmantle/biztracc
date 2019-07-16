<?php
session_start();
require("../db.php");

$usersession = $_SESSION['usersession'];
$coyid = $_SESSION['s_coyid'];

$admindb = $_SESSION['s_admindb'];
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$serialtable = 'ztmp'.$user_id.'_serialnos';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "select catid from stkcategory where category = 'Tyres'";
$r = mysql_query($q) or die(mysql_error());
$row = mysql_fetch_array($r);
extract($row);
$cid = $catid;

$q = "select itemcode from stkmast where catid = ".$cid;
$r = mysql_query($q) or die(mysql_error().$q);
$icd = "";
$numrows = mysql_num_rows($r);
if ($numrows > 0) { 
	while ($row = mysql_fetch_array($r)) {
		extract($row);
		$icd .= "'".$itemcode."',";
	}
	$icd = substr($icd,0,-1);
}
//$icd = substr($icd,0,-1);

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$query = "drop table if exists ".$serialtable;
$result = mysql_query($query) or die(mysql_error());

$query = "create table ".$serialtable." ( itemcode varchar(30) default '',item varchar(100) default '',serialno varchar(50) default '')  engine myisam";
$calc = mysql_query($query) or die(mysql_error());

$q = "insert into ".$serialtable." select distinct itemcode,item,serialno from stkserials where stkserials.itemcode in (".$icd.")";
$r = mysql_query($q) or die(mysql_error().' '.$q);

// Add uid
$q = "alter table ".$serialtable." add `uid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY";
$r = mysql_query($q) or die(mysql_error().' '.$q);


require_once("../includes/backgrounds.php");
$theme = $_SESSION['deftheme'];


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Track Tyres</title>

<script>

window.name = "tyres";

</script>

</head>
<body>
  <table width="960" border="0">
    <tr>
    <tr>
      <td><?php include "getTyres.php"; ?></td>
    </tr>
    <tr>
      <td><?php include "getTyreActivity.php"; ?></td>
    </tr>
    <tr>
      <td ><img src="../includes/calculator/calculator.png" width="16" height="16" alt="Calculators" title="Calculators" onclick="showCalculators();">&nbsp; <img src="../images/todo.gif" width="16" height="16" alt="ToDo" title="ToDo List" onClick="todo()"></td>
    </tr>
  </table>
</body>

</html>