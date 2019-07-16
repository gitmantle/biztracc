<?php
session_start();
$tid = $_REQUEST['tid'];
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;

$wagetable = 'ztmp'.$user_id.'_wages';

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

	$q = "delete from ".$wagetable." where uid = ".$tid;
	$r = mysql_query($q);

?>
