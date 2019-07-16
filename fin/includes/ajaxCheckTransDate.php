<?php
	session_start();
	date_default_timezone_set($_SESSION['s_timezone']);
	
	$dt = $_REQUEST['dt'];
	$dtoday = date("Y-m-d");

	$findb = $_SESSION['s_findb'];
	include_once("../../includes/DBClass.php");
	$db = new DBClass();

	$db->query("select bedate,yrdate,allowtrans from ".$findb.".globals");
	$row = $db->single();
	extract($row);
	
	$db->closeDB();
	
	if ($dt > $dtoday) {
		echo 'Transaction date in the future. If this is incorrect edit this transaction line';
		return;
	}
	if ($dt < $bedate && $allowtrans == 'N') {
		echo 'Transaction date in previous tax year and this posting not permitted';
		return;
	}


?>