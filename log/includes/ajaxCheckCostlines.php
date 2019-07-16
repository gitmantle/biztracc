<?php
	session_start();

	$cid = $_REQUEST['cid'];
	
	require_once("../../db.php");

	$moduledb = $_SESSION['s_logdb'];
	mysql_select_db($moduledb) or die(mysql_error());
	
	$ok = 'Y';
	
	$q = "select total from costlines where costid = ".$cid;
	$r = mysql_query($q) or die($q);
	while ($row = mysql_fetch_array($r)) {
		extract($row);
		if ($total == 0) {
			$ok = 'N';
		}
	}
	
	echo $ok;
	return;
	
?>