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

$table = 'ztmp'.$user_id.'_dn';

$findb = $_SESSION['s_findb'];

$db->query("update ".$findb.".".$table." set picked = (quantity - sent)");
$db->execute();

$db->closeDB();


?>
