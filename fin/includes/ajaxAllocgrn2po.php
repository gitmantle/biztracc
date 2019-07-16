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

$table = 'ztmp'.$user_id.'_grn2po';

$findb = $_SESSION['s_findb'];

$db->query("select uid,itemcode,thisgrn from ".$findb.".".$table);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	$db->query("update ".$findb.".p_olines set supplied = supplied + ".$thisgrn." where uid = ".$id);
	$db->execute();
}

$db->closeDB();


?>
