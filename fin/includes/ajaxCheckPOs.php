<?php
	session_start();
	$usersession = $_SESSION['usersession'];

	$ddate = $_REQUEST['ddate'];
	$ref = $_REQUEST['ref'];
	$acc = $_REQUEST['acc'];
	$asb = $_REQUEST['asb'];
	$loc = $_REQUEST['loc'];

	$findb = $_SESSION['s_findb'];
	
	$pos = 'N';
	
	include_once("../../includes/DBClass.php");
	$db = new DBClass();
	
	$db->query("select * from sessions where session = :vusersession");
	$db->bind(':vusersession', $usersession);
	$row = $db->single();
	$subid = $row['subid'];
	$user_id = $row['user_id'];	
	
	$db->query("select uid from ".$findb.".p_olines where supplier = :supplier and sub = :sub and supplied < quantity");
	$db->bind(':supplier', $acc);
	$db->bind(':sub', $asb);
	$rows = $db->resultset();
	$numrows = $db->rowcount();
	if ($numrows > 0 ) {
		$_SESSION['s_ddate'] = $ddate;
		$_SESSION['s_ref'] = $ref;
		$_SESSION['s_acc'] = $acc;
		$_SESSION['s_asb'] = $asb;
		$_SESSION['s_loc'] = $loc;
		$pos = 'Y';
	} else {
		$db->query("delete from ".$findb.".ztmp".$user_id."_trading");
		$db->execute();	
	}

	$db->closeDB();
	echo $pos;

?>