<?php
session_start();
$usersession = $_SESSION['usersession'];

$id = $_REQUEST['id'];
$amt = $_REQUEST['amt'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_grn2po';

$findb = $_SESSION['s_findb'];

$db->query("update ".$findb.".".$table." set thisgrn = ".$amt." where uid = ".$id);
$db->execute();

$db->closeDB();


?>
