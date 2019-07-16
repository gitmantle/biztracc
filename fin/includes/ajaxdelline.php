<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
require("../../db.php");
mysql_select_db($admindb) or die(mysql_error());
$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);

$table = 'ztmp'.$user_id.'_trans';

$tid = $_REQUEST['tid'];

$moduledb = $_SESSION['s_findb'];
mysql_select_db($moduledb) or die(mysql_error());


$q = 'delete from '.$table.' where uid = '.$tid;
$r = mysql_query($q) or die(mysql_error().' '.$q);

?>
