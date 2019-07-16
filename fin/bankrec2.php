<?php
session_start();

$param = $_REQUEST['param'];
$coyid = $_SESSION['coyid'];

$bankarray = explode(",", $_REQUEST['bankarray']);

require_once("../db.php");
mysql_select_db($coyid) or die(mysql_error());

// commit
if ($param == 'c') {
	foreach ($bankarray as $brow) {
		$rowitem = explode('_',$brow);
		if ($rowitem[1] != '') {	
			$sql = "update trmain set reconciled = 'Y' where uid = ".$rowitem[1];
			$result = mysql_query($sql) or die($sql);
		}
	}
	header("Location: bankrec3.php?p=c");
}

// reset
if ($param == 'n') {
	foreach ($bankarray as $brow) {
		$rowitem = explode('_',$brow);
		if ($rowitem[1] != '') {	
			$sql = "update trmain set temprecon = 'N' where uid = ".$rowitem[1];
			$result = mysql_query($sql) or die($sql);
		}
	}
	header("Location: bankrec3.php?p=n");
}

// save for later
if ($param == 's') {
	foreach ($bankarray as $brow) {
		$rowitem = explode('_',$brow);
		if ($rowitem[1] != '') {	
			$sql = "update trmain set temprecon = '".$rowitem[0]."' where uid = ".$rowitem[1];
			$result = mysql_query($sql) or die($sql);
		}
	}
	header("Location: bankrec3.php?p=s");
}




?>