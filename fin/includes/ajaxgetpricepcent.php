<?php
	session_start();
	$usersession = $_SESSION['usersession'];
	
	if (isset($_REQUEST['priceband'])) {
		$pb = $_REQUEST['priceband'];
	} else {
		$pb = 1;
	}

	$findb = $_SESSION['s_findb'];
	
	include_once("../../includes/DBClass.php");
	$dbc = new DBClass();
	
	$dbc->query("select * from sessions where session = :vusersession");
	$dbc->bind(':vusersession', $usersession);
	$row = $dbc->single();
	$subid = $row['subid'];
	$user_id = $row['user_id'];
	$subscriber = $subid;
	$sname = $row['uname'];
	
	$dbc->query("select pcent,setprice from ".$findb.".stkpricepcent where uid = :pb");
	$dbc->bind(':pb', $pb);
	$row = $dbc->single();
	extract($row);
	
	$pband = $pcent.'~'.$setprice;
	
	echo $pband;
	
	$dbc->closeDB();

?>