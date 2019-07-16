<?php
	session_start();
	
	$usersession = $_SESSION['usersession'];
	
	include_once("../../includes/DBClass.php");
	$db = new DBClass();

	$db->query("select * from sessions where session = :vusersession");
	$db->bind(':vusersession', $usersession);
	$row = $db->single();
	extract($row);
	$user_id = $row['user_id'];

	$findb = $_SESSION['s_findb'];
	
	$tradetable = 'ztmp'.$user_id.'_trading';
	
	$db->query('select count(item) as numrows from '.$findb.'.'.$tradetable);
	$row = $db->single();
	extract($row);
	$numrows = $db->rowCount();

	if ($numrows == 0) {
		echo 'Please enter at least one record before posting';
		return;
	} else {
		return;
	}
	
	$db->closeDB();
?>
