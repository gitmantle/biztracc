<?php
session_start();
$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

include_once("../../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$table = 'ztmp'.$user_id.'_journal';
	
$lineno = $_REQUEST['lineno'];

$findb = $_SESSION['s_findb'];

$db->query("select account,note,debit,credit,accno,subac,brac,ddate,reference,acindex from ".$findb.".".$table." where uid = ".$lineno);
$row = $db->single();
extract($row);
$str = $account."~".$note."~".$debit."~".$credit."~".$accno."~".$subac."~".$brac."~".$ddate."~".$reference."~".$acindex;
echo $str;

$db->closeDB();
?>