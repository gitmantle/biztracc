<?php
	session_start();
	date_default_timezone_set($_SESSION['s_timezone']);
	
	$dt = $_REQUEST['dt'];
	$dtoday = date("Y-m-d");

	require_once("../../db.php");

	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());

	$query = "select bedate,yrdate,allowtrans from globals";
	$result = mysql_query($query) or die($query);
	$row = mysql_fetch_array($result);
	extract($row);
	
	if ($dt > $dtoday) {
		echo 'Transaction date in the future';
		return;
	}
	if ($dt < $bedate && $allowtrans == 'N') {
		echo 'Transaction date in previous tax year and this posting not permitted';
		return;
	}
	
?>