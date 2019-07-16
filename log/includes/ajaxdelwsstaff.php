<?php
session_start();
$tid = $_REQUEST['tid'];

require("../../db.php");
$moduledb = $_SESSION['s_admindb'];
mysql_select_db($moduledb) or die(mysql_error());

echo 'Deleting user.  Do not tick the box below otherwise you will not get any more warning messages on this page.';
$q = 'delete from users where uid = '.$tid;
$r = mysql_query($q) or die(mysql_error().' '.$q);
$q = "delete from access where staff_id = ".$tid." and module = 'fin'";
$r = mysql_query($q) or die(mysql_error().' '.$q);

?>
