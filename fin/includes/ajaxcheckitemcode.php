<?php
	session_start();
	
	$stkcode = trim(strtoupper($_REQUEST['stkid']));

	$findb = $_SESSION['s_findb'];
	
	include_once("../../includes/DBClass.php");
	$db = new DBClass();

	$db->query("select itemcode from ".$findb.".stkmast where itemcode = '".$stkcode."'");
	$rows = $db->resultset();

	if (count($rows) > 0) {
		echo 'Y';
	} else {
		echo 'N';
	}
	return;
?>