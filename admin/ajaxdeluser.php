<?php
session_start();
$uid = $_REQUEST['tid'];
$usersession = $_SESSION['usersession'];


include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;

$db->query("update users set active = 'N' where uid = :uid");
$db->bind(':uid', $uid);
$db->execute();

$db->query("select access_id from access where staff_id = :staff_id");
$db->bind(':staff_id', $uid);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$aid = $access_id;
	$db->query("delete from processes where access_id = ".$aid);
	$db->execute();
}

$db->query("delete from access where staff_id = ".$uid);
$db->execute();

$db->closeDB();

?>

