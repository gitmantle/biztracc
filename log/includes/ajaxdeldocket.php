<?php
session_start();
$tid = $_REQUEST['tid'];

require("../../db.php");
$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());

	$q = "delete from dockets where docket_id = ".$tid;
	$r = mysql_query($q);

?>
