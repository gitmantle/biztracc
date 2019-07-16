<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$bankrectable = 'ztmp'.$user_id.'_bankrec';

$findb = $_SESSION['s_findb'];

$db->query("select uid,temprecon from ".$findb.".trmain where reconciled = 'N'");
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	$db->query("update ".$findb.".".$bankrectable." set reconciled = '".$temprecon."' where uid = ".$id);
	$db->execute();
}

$_SESSION['s_showreconcilled'] = 'Y';

$db->closeDB();

?>
