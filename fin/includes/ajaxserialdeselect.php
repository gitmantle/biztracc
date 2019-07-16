<?php
session_start();
$id = $_REQUEST['id'];

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];

$serialtable = 'ztmp'.$user_id.'_serialnos';

$findb = $_SESSION['s_findb'];

if ($id == '*') {
	$sql = "update ".$findb.".".$serialtable." set selected = 'N'";
} else {
	$sql = "update ".$findb.".".$serialtable." set selected = 'N' where uid = ".$id;
}
$db->query($sql);
$db->execute();

$db->closeDB();
?>
