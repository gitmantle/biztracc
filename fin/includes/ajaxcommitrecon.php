<?php
session_start();
$usersession = $_SESSION['usersession'];
$coyid = $_SESSION['s_coyid'];
$tdate = $_REQUEST['tdate'];

$ac = $_SESSION['s_bankac'];
$br = $_SESSION['s_bankbr'];
$sb = $_SESSION['s_banksb'];

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$bankrectable = 'ztmp'.$user_id.'_bankrec';
$dt = date('Y-m-d');

$findb = $_SESSION['s_findb'];

$db->query("select uid,reconciled from ".$findb.".".$bankrectable);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$id = $uid;
	$db->query("update ".$findb.".trmain set reconciled = '".$reconciled."' where uid = ".$id);
	$db->execute();
}

$db->query('update '.$findb.'.globals set reconciledate = "'.$tdate.'"');
$db->execute();

$db->query("select sum(debit-credit) as unreconciled from ".$findb.".".$bankrectable." where reconciled = 'N'");
$row = $db->single();
extract($row);

$db->query("update ".$findb.".glmast set recondate = '".$tdate."' where accountno = ".$ac." and branch = '".$br."' and sub = ".$sb);
$db->execute();

$db->closeDB();

echo $unreconciled;
?>

