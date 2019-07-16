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

$db->query("update ".$findb.".".$tradetable." set pay = 'Y', value = quantity * price, tax = quantity * price * (taxpcent / 100) ");
$db->execute();
$db->query("update ".$findb.".".$tradetable." set tot = value + tax ");
$db->execute();

$db->closeDB();
?>
