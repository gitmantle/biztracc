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
$subscriber = $subid;

$moduledb = $_SESSION['h_sdb'];
mysql_select_db($moduledb) or die(mysql_error());

$filterfile = "ztmp".$user_id."_hs";

$q = "update ".$filterfile." set include = 'N'";
$r = mysql_query($q) or die(mysql_error().' '.$q);

?>
