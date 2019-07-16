<?php
	session_start();

	$qty = $_REQUEST['q'];
	$stkid = $_REQUEST['stkid'];
	$lid = $_REQUEST['locid'];

	$findb = $_SESSION['s_findb'];
	
	include_once("../../includes/DBClass.php");
	$db = new DBClass();
	
	$db->query("select stock from ".$findb.".stkmast where itemcode = :stkid");
	$db->bind(':stkid', $stkid);
	$row = $db->single();
	extract($row);
	$stk = $stock;
	
	if ($stk == 'Stock') {
		$db->query("select onhand-uncosted as avail from ".$findb.".stkmast where itemcode = :stkid");
		$db->bind(':stkid', $stkid);
		$row = $db->single();
		extract($row);
		$available = $avail;
		
		$db->query("select sum(increase - decrease) as atl from ".$findb.".stktrans where itemcode = :stkid and locid = :lid");
		$db->bind(':stkid', $stkid);
		$db->bind(':lid', $lid);
		$row = $db->single();
		extract($row);
		if (is_null($atl)) {
			$atloc = 0;
		} else {
			$atloc = $atl;	
		}
		
		$db->closeDB();

		if ($atloc < $qty) {
			echo 'You requested '.$qty.'. Only '.$atloc.' items in stock at selected location. Total in stock across all locations '.$available;
			return;
		} else {
			return;
		}
		
	} else {
		$db->closeDB();
		return;
	}
?>