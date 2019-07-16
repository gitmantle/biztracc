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

$tid = $_REQUEST['tid'];

$findb = $_SESSION['s_findb'];
$table = 'ztmp'.$user_id.'_journal';

$db->query('delete from '.$findb.'.'.$table.' where uid = '.$tid);
$db->execute();
$db->closeDB();
?>
