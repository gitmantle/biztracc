<?php
session_start();
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];

$db->query("select subname from subscribers where subid = :subid");
$db->bind(':subid', $subid);
$row = $db->single();
$subname = $row['subname'];

$findb = $_SESSION['s_findb'];

$db->query("select rrraccountno from ".$findb.".globals");
$row = $db->single();
$accountno = $row['rrraccountno'];

$start = date("Y-m-d H:i:s");

$findb = 'infinint_fin45_27';

$db->query("insert into ".$findb.".wip (subid,subscriber,accountno,start) values (:subid,:subscriber,:accountno,:start)");
$db->bind(':subid', $subid);
$db->bind(':subscriber', $subname);		
$db->bind(':start', $start);
$db->bind(':accountno', $accountno);

$db->execute();
$_SESSION['startclockuid'] = $db->lastInsertId();
$db->closeDB();
?>
