<?php
session_start();
$usersession = $_SESSION['usersession'];
$tid = $_REQUEST['tid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$bankrectable = 'ztmp'.$user_id.'_bankrec';

$findb = $_SESSION['s_findb'];

$db->query("select reconciled from ".$findb.".$bankrectable where uid = ".$tid);
$row = $db->single();
extract($row);

if ($reconciled == 'N') {

	$db->query('update '.$findb.'.'.$bankrectable.' set reconciled = "Y" where uid = '.$tid);
	$db->execute();
	
	$db->query("select debit,credit from ".$findb.".".$bankrectable." where uid = ".$tid);
	$row = $db->single();
	extract($row);
	echo $debit.'~'.$credit;

} else {
	echo '0~0';
}

$db->closeDB();

?>
