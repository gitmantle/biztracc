<?php
session_start();
$cid = $_REQUEST['cid'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$db->query("select status from ".$cltdb.".members where member_id = ".$cid);
$row = $db->single();
extract($row);

if ($status == 'Lead') {
	$db->query("update ".$cltdb.".members set status = 'Prospect' where member_id = ".$cid);
	$db->execute();
}

$db->closeDB();

?>

