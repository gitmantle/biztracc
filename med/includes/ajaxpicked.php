<?php
session_start();
$rid = $_REQUEST['id'];
$picked = $_REQUEST['toorder'];
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$potable = 'ztmp'.$user_id.'_po';


$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());

$q = "update ".$potable." set toorder = ".$picked." where uid = ".$rid;
$r = mysql_query($q) or die(mysql_error().' '.$q);

?>
