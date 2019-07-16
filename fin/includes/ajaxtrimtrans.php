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

$tradingtable = 'ztmp'.$user_id.'_trading';

$findb = $_SESSION['s_findb'];

$db->query("delete from ".$findb.".".$tradingtable." where pay = 'N'");
$db->execute();

$db->query("update ".$findb.".".$tradingtable." set pay = 'Y', value = quantity * price, tax = quantity * price * (taxpcent / 100) ");
$db->execute();
$db->query("update ".$findb.".".$tradingtable." set tot = value + tax ");
$db->execute();

$db->closeDB();
?>
