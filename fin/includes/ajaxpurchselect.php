<?php
session_start();
$id = $_REQUEST['id'];

$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$tradetable = 'ztmp'.$user_id.'_trading';

$findb = $_SESSION['s_findb'];

$db->query("update ".$findb.".".$tradetable." set pay = 'N', tot = 0, value = 0, tax = 0 where uid = ".$id);
$db->execute();

$db->closeDB();

?>
