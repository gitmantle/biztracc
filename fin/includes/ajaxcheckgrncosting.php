<?php
	session_start();
	
	$purchref = $_REQUEST['purchref'];

	$findb = $_SESSION['s_findb'];
	
	include_once("../../includes/DBClass.php");
	$db = new DBClass();

	$db->query("select debit from ".$findb.".trmain where accountno = 101 and reference = '".$purchref."'");
	$row = $db->single();
	extract($row);

	if ($debit > 0) {
		echo 'N';
	} else {
		echo 'Y';
	}
	return;
?>