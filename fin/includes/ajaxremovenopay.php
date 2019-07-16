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

$tradetable = 'ztmp'.$user_id.'_trading';

$findb = $_SESSION['s_findb'];

$db->query("delete from ".$findb.".".$tradetable." where pay = 'N'";
$db->execute();
$db->closeDB();
?>
