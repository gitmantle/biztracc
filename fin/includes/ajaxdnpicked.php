<?php
session_start();
$rid = $_REQUEST['rid'];
$picked = $_REQUEST['picked'];
$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tradetable = 'ztmp'.$user_id.'_dn';

$findb = $_SESSION['s_findb'];

$db->query("select itemcode, trackserial,(quantity - sent) as outstanding from ".$findb.".".$tradetable." where uid = ".$rid);
$row = $db->single();
extract($row);

$_SESSION['s_itemcode'] = $itemcode;

if ($picked > $outstanding) {
	echo "You may not pick more than the outstanding quantity";
	return;
} else {
	$db->query("update ".$findb.".".$tradetable." set picked = ".$picked." where uid = ".$rid);
	$db->execute();
	if ($trackserial == 'Yes') {
		echo 'S';
	} else {
		echo "Y";
	}
}

$db->closeDB();

?>
