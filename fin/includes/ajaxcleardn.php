<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subscriber = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_dns';

$findb = $_SESSION['s_findb'];

$sql = "delete from ".$findb.".".$table." where selected = 'Y'";
$db->query($sql);
$db->execute();
$db->closeDB();
return;


?>