<?php
session_start();
$usersession = $_SESSION['usersession'];

$purchref = $_REQUEST['purchref'];

$_SESSION['s_purchref'] = $purchref;

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];


$table = 'ztmp'.$user_id.'_trading';

	$findb = $_SESSION['s_findb'];
	
	$sql = "select uid,value from ".$findb.".".$table;
	$db->query($sql);
	$db->execute();
	$numrows = $db->rowcount();
	if ($numrows > 0) {
		echo 'Y';
	} else {
		echo 'N';
	}
	$db->closeDB();
	return;


?>